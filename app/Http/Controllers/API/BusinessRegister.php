<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\BusinessRegisterResource;

use App\Models\BusinessRegister as BusinessRegisterModel;
use App\Models\BillingCounter as BillingCounterModel;
use App\Models\Order as OrderModel;

use App\Http\Resources\Collections\BusinessRegisterCollection;

class BusinessRegister extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_BUSINESS_REGISTER_LISTING';
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
            
            $query = BusinessRegisterModel::select('business_registers.*', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
            ->user()
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

            $business_registers = BusinessRegisterResource::collection($query);
           
            $total_count = BusinessRegisterModel::select("id")->get()->count();

            $item_array = [];
            foreach($business_registers as $key => $business_register_item){

                $business_register = $business_register_item->toArray($request);

                $item_array[$key][] = (isset($business_register['user']['fullname']))?$business_register['user']['fullname']:'-';
                $item_array[$key][] = $business_register['opening_date_label'];
                $item_array[$key][] = $business_register['closing_date_label'];
                $item_array[$key][] = $business_register['created_at_label'];
                $item_array[$key][] = $business_register['updated_at_label'];
                $item_array[$key][] = (isset($business_register['created_by']) && isset($business_register['created_by']['fullname']))?$business_register['created_by']['fullname']:'-';
                $item_array[$key][] = view('business_register.layouts.business_register_actions', array('business_register' => $business_register))->render();
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
    public function open_register(Request $request)
    {
        try {

            if(!check_access(['A_ADD_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $business_register_data_exists = BusinessRegisterModel::select('id')
            ->where('user_id', '=', trim($request->logged_user_id))
            ->whereNull('closing_date')
            ->first();
            if (!empty($business_register_data_exists)) {
                throw new Exception("You already have a register open", 400);
            }

            $billing_counter_data = BillingCounterModel::select('id')
            ->where('slack', '=', trim($request->billing_counter))
            ->first();
            if (empty($billing_counter_data)) {
                throw new Exception("Invalid billing counter selected", 400);
            }

            /* $counter_occupied = BusinessRegisterModel::select('id')
            ->where('billing_counter_id', '=', $billing_counter_data->id)
            ->whereNull('closing_date')
            ->first();
            if (!empty($counter_occupied)) {
                throw new Exception("Billing counter already occupied", 400);
            } */
            
            $parent_occupant = BusinessRegisterModel::select('id')
            ->where([
                ['billing_counter_id', '=', $billing_counter_data->id],
                ['current_register', '=', 1]
            ])
            ->whereNull('closing_date')
            ->where(function($query){
                $query->where('parent_register_id', '=', '');
                $query->orWhereNull('parent_register_id');
            })
            ->first();

            DB::beginTransaction();

            if(empty($parent_occupant)){
                BusinessRegisterModel::where([
                    ["billing_counter_id" , "=", $billing_counter_data->id],
                ])
                ->update(['current_register' => 0]);
            }else{
                BusinessRegisterModel::where([
                    ["billing_counter_id" , "=", $billing_counter_data->id],
                    ["user_id", "=", $request->logged_user_id],
                ])
                ->update(['current_register' => 0]);
            }
            
            $business_register = [
                "slack" => $this->generate_slack("business_registers"),
                "store_id" => $request->logged_user_store_id,
                "billing_counter_id" => $billing_counter_data->id,
                "current_register" => 1,
                "user_id" => $request->logged_user_id,
                "opening_date" => NOW(),
                "opening_amount" => trim($request->opening_amount),
                "created_by" => $request->logged_user_id
            ];

            if(!empty($parent_occupant)){
                $business_register["joining_date"] = NOW();
                $business_register["parent_register_id"] = $parent_occupant->id;
            }
            
            $business_register_id = BusinessRegisterModel::create($business_register)->id;

            DB::commit();

            $forward_link = route('add_order');

            return response()->json($this->generate_response(
                array(
                    "message" => "Business register opened successfully", 
                    "data"    => $business_register['slack'],
                    "link"    => $forward_link
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

            if(!check_access(['A_DETAIL_BUSINESS_REGISTER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = BusinessRegisterModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new BusinessRegisterResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Business register loaded successfully", 
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

            if(!check_access(['A_VIEW_BUSINESS_REGISTER_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new BusinessRegisterCollection(BusinessRegisterModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Business Registers loaded successfully", 
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
    public function close_register(Request $request)
    {
        try {

            $action_date = NOW();

            if(!check_access(['A_ADD_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_close_register_request($request);

            $business_register_data_exists = BusinessRegisterModel::select('id', 'slack', 'parent_register_id', 'opening_amount')
            ->where('user_id', '=', trim($request->logged_user_id))
            ->whereNull('closing_date')
            ->first();
            if (empty($business_register_data_exists)) {
                throw new Exception("You dont have any register open", 400);
            }

            DB::beginTransaction();
            
            $business_register = [
                "current_register" => 0,
                "closing_amount" => $request->closing_amount,
                "credit_card_slips" => $request->credit_card_slips,
                "cheques" => $request->cheques,
                "closing_date" => $action_date,
                "exit_date" => $action_date,
                "updated_by" => $request->logged_user_id
            ];

            $action_response = BusinessRegisterModel::where('id', $business_register_data_exists->id)
            ->update($business_register);

            if(isset($business_register_data_exists) && empty($business_register_data_exists->parent_register_id)){

                $sub_business_register_data = BusinessRegisterModel::select('id', 'slack', 'opening_amount')
                ->where('parent_register_id', '=', trim($business_register_data_exists->id))
                ->whereNull('closing_date')
                ->get();

                if(!empty($sub_business_register_data)){
                    foreach($sub_business_register_data as $sub_business_register_data_item){

                        $order_data = OrderModel::select(DB::raw('COUNT(id) as order_count, SUM(total_order_amount) as order_value'))
                        ->where('register_id', $sub_business_register_data_item->id)
                        ->closed()
                        ->first();

                        $closing_amount = $sub_business_register_data_item->opening_amount+$order_data->order_value;
                        $business_register['closing_amount'] = $closing_amount;
                        
                        $action_response = BusinessRegisterModel::where('id', $sub_business_register_data_item->id)
                        ->update($business_register);
                    }
                }
            }

            DB::commit();

            $forward_link = route('register_summary', ['slack' => $business_register_data_exists->slack]);

            return response()->json($this->generate_response(
                array(
                    "message" => (isset($business_register_data_exists) && empty($business_register_data_exists->parent_register_id))?"Business register closed successfully":"Business register exited successfully", 
                    "data"    => $business_register_data_exists->slack,
                    "link"    => (isset($business_register_data_exists) && empty($business_register_data_exists->parent_register_id))?$forward_link:''
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
    public function destroy(Request $request, $slack)
    {
        try{

            if(!check_access(['A_DELETE_BUSINESS_REGISTER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $business_register_detail = BusinessRegisterModel::select('id')->whereNotNull('closing_date')->where('slack', $slack)->first();
            if (empty($business_register_detail)) {
                throw new Exception("Invalid business register provided", 400);
            }
            $business_register_id = $business_register_detail->id;

            DB::beginTransaction();

            BusinessRegisterModel::where('id', $business_register_id)->delete();

            $order = [];
            $order['register_id'] = NULL;
            $order['updated_at'] = now();
            $order['updated_by'] = $request->logged_user_id;

            $action_response = OrderModel::where('register_id', $business_register_id)
            ->update($order);

            DB::commit();

            $forward_link = route('business_registers');

            return response()->json($this->generate_response(
                array(
                    "message" => "Business register deleted successfully", 
                    "data" => $slack,
                    "link" => $forward_link
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

    public function validate_request($request)
    {
        $validator = Validator::make($request->all(), [
            'opening_amount' => $this->get_validation_rules("numeric", true),
            'billing_counter' => $this->get_validation_rules("slack", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }

    public function validate_close_register_request($request)
    {
        $validator = Validator::make($request->all(), [
            'closing_amount' => $this->get_validation_rules("numeric", false),
            'credit_card_slips' => $this->get_validation_rules("numeric", false),
            'cheques' => $this->get_validation_rules("numeric", false),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
