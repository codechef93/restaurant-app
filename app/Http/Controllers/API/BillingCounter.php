<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\BillingCounterResource;

use App\Models\BillingCounter as BillingCounterModel;

use App\Http\Resources\Collections\BillingCounterCollection;

class BillingCounter extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_BILLING_COUNTER_LISTING';
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
            
            $query = BillingCounterModel::select('billing_counters.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
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

            $billing_counters = BillingCounterResource::collection($query);
           
            $total_count = BillingCounterModel::select("id")->get()->count();

            $item_array = [];
            foreach($billing_counters as $key => $billing_counter){
                
                $billing_counter = $billing_counter->toArray($request);

                $item_array[$key][] = $billing_counter['billing_counter_code'];
                $item_array[$key][] = $billing_counter['counter_name'];
                $item_array[$key][] = (isset($billing_counter['status']['label']))?view('common.status', ['status_data' => ['label' => $billing_counter['status']['label'], "color" => $billing_counter['status']['color']]])->render():'-';
                $item_array[$key][] = $billing_counter['created_at_label'];
                $item_array[$key][] = $billing_counter['updated_at_label'];
                $item_array[$key][] = (isset($billing_counter['created_by']['fullname']))?$billing_counter['created_by']['fullname']:'-';
                $item_array[$key][] = view('billing_counter.layouts.billing_counter_actions', ['billing_counter' => $billing_counter])->render();
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

            if(!check_access(['A_ADD_BILLING_COUNTER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $billing_counter_code_data_exists = BillingCounterModel::select('id')
            ->where('billing_counter_code', '=', trim($request->billing_counter_code))
            ->first();
            if (!empty($billing_counter_code_data_exists)) {
                throw new Exception("Billing counter code already exists", 400);
            }

            $billing_counter_data_exists = BillingCounterModel::select('id')
            ->where('counter_name', '=', trim($request->counter_name))
            ->first();
            if (!empty($billing_counter_data_exists)) {
                throw new Exception("Billing counter name already exists", 400);
            }

            DB::beginTransaction();
            
            $billing_counter = [
                "slack" => $this->generate_slack("billing_counters"),
                "store_id" => $request->logged_user_store_id,
                "billing_counter_code" => $request->billing_counter_code,
                "counter_name" => $request->billing_counter_name,
                "description" => $request->description,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];
            
            $billing_counter_id = BillingCounterModel::create($billing_counter)->id;

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Billing counter created successfully", 
                    "data"    => $billing_counter['slack']
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

            if(!check_access(['A_DETAIL_BILLING_COUNTER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = BillingCounterModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new BillingCounterResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Billing Counter loaded successfully", 
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

            if(!check_access(['A_VIEW_BILLING_COUNTER_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new BillingCounterCollection(BillingCounterModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Billing Counter loaded successfully", 
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

            if(!check_access(['A_EDIT_BILLING_COUNTER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $billing_counter_code_data_exists = BillingCounterModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['billing_counter_code', '=', trim($request->billing_counter_code)]
            ])
            ->first();
            if (!empty($billing_counter_code_data_exists)) {
                throw new Exception("Billing counter code already exists", 400);
            }

            $billing_counter_data_exists = BillingCounterModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['counter_name', '=', trim($request->billing_counter_name)],
            ])
            ->first();
            if (!empty($billing_counter_data_exists)) {
                throw new Exception("Billing counter name already exists", 400);
            }

            DB::beginTransaction();

            $billing_counter = [
                "billing_counter_code" => $request->billing_counter_code,
                "counter_name" => $request->billing_counter_name,
                "description" => $request->description,
                "status" => $request->status,
                "updated_by" => $request->logged_user_id
            ];

            $action_response = BillingCounterModel::where('slack', $slack)
            ->update($billing_counter);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Billing counter updated successfully", 
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
            'billing_counter_code' => $this->get_validation_rules("codes", true),
            'billing_counter_name' => 'max:150|required',
            'description' => $this->get_validation_rules("text", false),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
