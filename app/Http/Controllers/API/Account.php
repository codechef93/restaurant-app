<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Http\Resources\AccountResource;

use App\Models\Account as AccountModel;
use App\Models\MasterAccountType as MasterAccountTypeModel;

use App\Http\Resources\Collections\AccountCollection;

class Account extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_ACCOUNT_LISTING';
            if(check_access(array($data['action_key']), true) == false){
                $response = $this->no_access_response_for_listing_table();
                return $response;
            }

            $item_array = array();

            $draw = $request->draw;
            $limit = $request->length;
            $offset = $request->start;
            
            $order_by = $request->order[0]["column"];
            $order_direction = $request->order[0]["dir"];
            $order_by_column =  $request->columns[$order_by]['name'];

            $filter_string = $request->search['value'];
            $filter_columns = array_filter(data_get($request->columns, '*.name'));
            
            $query = AccountModel::select('accounts.*', 'master_account_type.label as account_type_label', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
            ->masterAccountTypeJoin()
            ->statusJoin()
            ->createdUser()

            ->when($order_by_column, function ($query, $order_by_column) use ($order_direction) {
                $query->orderBy($order_by_column, $order_direction);
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })

            ->when($filter_string, function ($query, $filter_string) use ($filter_columns) {
                $query->where(function ($query) use ($filter_string, $filter_columns){
                    foreach($filter_columns as $filter_column){
                        $query->orWhere($filter_column, 'like', '%'.$filter_string.'%');
                    }
                });
            })

            ->get();

            $accounts = AccountResource::collection($query);
           
            $total_count = AccountModel::select("id")->get()->count();

            $item_array = [];
            foreach($accounts as $key => $account){
                
                $account = $account->toArray($request);
                
                $item_array[$key][] = $account['account_code'];
                $item_array[$key][] = $account['label'];
                $item_array[$key][] = (isset($account['account_type_data']['label']))?$account['account_type_data']['label']:'-';
                $item_array[$key][] = ($account['pos_default'] == '1')?'Yes':'No';
                $item_array[$key][] = view('common.status', ['status_data' => ['label' => $account['status']['label'], "color" => $account['status']['color']]])->render();
                $item_array[$key][] = $account['created_at_label'];
                $item_array[$key][] = $account['updated_at_label'];
                $item_array[$key][] = (isset($account['created_by']['fullname']))?$account['created_by']['fullname']:'-';
                $item_array[$key][] = view('account.layouts.account_actions', ['account' => $account])->render();
            }

            $response = [
                'draw' => $draw,
                'recordsTotal' => $total_count,
                'recordsFiltered' => $total_count,
                'data' => $item_array
            ];
            
            return response()->json($response);
        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            if(!check_access(['A_ADD_ACCOUNT'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $account_data_exists = AccountModel::select('id')
            ->where('label', '=', trim($request->account_name))
            ->first();
            if (!empty($account_data_exists)) {
                throw new Exception("Account already exists", 400);
            }

            $account_type_data_exists = MasterAccountTypeModel::select('id')
            ->where('account_type_constant', '=', trim($request->account_type))
            ->first();
            if (empty($account_type_data_exists)) {
                throw new Exception("Invalid account type selected", 400);
            }

            DB::beginTransaction();
            
            $account = [
                "slack" => $this->generate_slack("accounts"),
                "store_id" => $request->logged_user_store_id,
                "account_code" => Str::random(6),
                "account_type" => $account_type_data_exists->id,
                "label" => $request->account_name,
                "description" => $request->description,
                "initial_balance" => $request->initial_balance,
                "pos_default" => $request->pos_default,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];
            
            $account_id = AccountModel::create($account)->id;

            $code_start_config = Config::get('constants.unique_code_start.account');
            $code_start = (isset($code_start_config))?$code_start_config:100;
            
            $account_code = [
                "account_code" => ($code_start+$account_id)
            ];
            AccountModel::where('id', $account_id)
            ->update($account_code);

            if($request->pos_default == 1){
                AccountModel::where([
                    ['id', '!=', $account_id]
                ])
                ->update(['pos_default' => 0]);
            }

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Account created successfully", 
                    "data"    => $account['slack']
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function show($slack)
    { 
        try {

            if(!check_access(['A_DETAIL_ACCOUNT'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = AccountModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new AccountResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Account loaded successfully", 
                    "data"    => $item_data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }  
    }

    /**
     * list all the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {

            if(!check_access(['A_VIEW_ACCOUNT_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new AccountCollection(AccountModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Accounts loaded successfully", 
                    "data"    => $list
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slack)
    {
        try {

            if(!check_access(['A_EDIT_ACCOUNT'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $account_data_exists = AccountModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['label', '=', trim($request->account_name)],
            ])
            ->first();
            if (!empty($account_data_exists)) {
                throw new Exception("Account already exists", 400);
            }

            $account_type_data_exists = MasterAccountTypeModel::select('id')
            ->where('account_type_constant', '=', trim($request->account_type))
            ->first();
            if (empty($account_type_data_exists)) {
                throw new Exception("Invalid account type selected", 400);
            }

            DB::beginTransaction();
            
            $account = [
                "account_type" => $account_type_data_exists->id,
                "label" => $request->account_name,
                "description" => $request->description,
                "initial_balance" => $request->initial_balance,
                "pos_default" => $request->pos_default,
                "status" => $request->status,
                "updated_by" => $request->logged_user_id
            ];
            
            $action_response = AccountModel::where('slack', $slack)
            ->update($account);

            if($request->pos_default == 1){
                AccountModel::where([
                    ['slack', '!=', $slack]
                ])
                ->update(['pos_default' => 0]);
            }

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Account updated successfully", 
                    "data"    => $slack
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function validate_request($request)
    {
        $validator = Validator::make($request->all(), [
            'account_type' => $this->get_validation_rules("string", true),
            'account_name' => $this->get_validation_rules("name_label", true),
            'initial_balance' => $this->get_validation_rules("numeric", true),
            'pos_default' => 'boolean|required',
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
