<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\TargetResource;

use App\Models\Target as TargetModel;

use App\Http\Resources\Collections\TargetCollection;

class Target extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_TARGET_LISTING';
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
            
            $query = TargetModel::select('targets.*')
            ->take($limit)
            ->skip($offset)
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

            $targets = TargetResource::collection($query);
           
            $total_count = TargetModel::select("id")->get()->count();

            $item_array = [];
            foreach($targets as $key => $target){
                
                $target = $target->toArray($request);

                $item_array[$key][] = $target['month_label'];
                $item_array[$key][] = $target['income'];
                $item_array[$key][] = $target['expense'];
                $item_array[$key][] = $target['sales'];
                $item_array[$key][] = $target['net_profit'];
                $item_array[$key][] = $target['created_at_label'];
                $item_array[$key][] = $target['updated_at_label'];
                $item_array[$key][] = (isset($target['created_by']['fullname']))?$target['created_by']['fullname']:'-';
                $item_array[$key][] = view('target.layouts.target_actions', ['target' => $target])->render();
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

            if(!check_access(['A_ADD_TARGET'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $month = $request->month.'-01';

            $target_data_exists = TargetModel::select('id')
            ->where('month', 'like', $month.'%')
            ->first();
            if (!empty($target_data_exists)) {
                throw new Exception("Target for the month already exists", 400);
            }

            DB::beginTransaction();
            
            $target = [
                "slack" => $this->generate_slack("targets"),
                "store_id" => $request->logged_user_store_id,
                "month" => $month,
                "income" => $request->income,
                "expense" => $request->expense,
                "sales" => $request->sales,
                "net_profit" => $request->net_profit,
                "created_by" => $request->logged_user_id
            ];
            
            $target_id = TargetModel::create($target)->id;

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Target created successfully", 
                    "data"    => $target['slack']
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

            if(!check_access(['A_DETAIL_TARGET'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = TargetModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new TargetResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Target loaded successfully", 
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

            if(!check_access(['A_VIEW_TARGET_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new TargetCollection(TargetModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Target loaded successfully", 
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

            if(!check_access(['A_EDIT_TARGET'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $month = $request->month.'-01';

            $target_data_exists = TargetModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['month', 'like', $month.'%']
            ])
            ->first();
            if (!empty($target_data_exists)) {
                throw new Exception("Target for the month already exists", 400);
            }

            DB::beginTransaction();

            $target = [
                "month" => $month,
                "income" => $request->income,
                "expense" => $request->expense,
                "sales" => $request->sales,
                "net_profit" => $request->net_profit,
                "updated_by" => $request->logged_user_id
            ];

            $action_response = TargetModel::where('slack', $slack)
            ->update($target);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Target updated successfully", 
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
    public function destroy($slack)
    {
        try{

            if(!check_access(['A_DELETE_TARGET'], true)){
                throw new Exception("Invalid request", 400);
            }

            $target_detail = TargetModel::select('id')->where('slack', $slack)->first();
            if (empty($target_detail)) {
                throw new Exception("Invalid target provided", 400);
            }
            $target_id = $target_detail->id;

            DB::beginTransaction();

            TargetModel::where('id', $target_id)->delete();

            DB::commit();

            $forward_link = route('targets');

            return response()->json($this->generate_response(
                array(
                    "message" => "Target deleted successfully", 
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
            'month' => "date_format:Y-m,required",
            'income' => $this->get_validation_rules("numeric", true),
            'expense' => $this->get_validation_rules("numeric", true),
            'sales' => $this->get_validation_rules("numeric", true),
            'net_profit' => $this->get_validation_rules("numeric", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
