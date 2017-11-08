<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod as PaymentMethodModel;

use App\Http\Resources\Collections\PaymentMethodCollection;

class PaymentMethod extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_PAYMENT_METHOD_LISTING';
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
            
            $query = PaymentMethodModel::select('payment_methods.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $PaymentMethods = PaymentMethodResource::collection($query);
           
            $total_count = PaymentMethodModel::select("id")->get()->count();

            $item_array = [];
            foreach($PaymentMethods as $key => $payment_method){

                $payment_method = $payment_method->toArray($request);

                $item_array[$key][] = $payment_method['label'];
                $item_array[$key][] = (isset($payment_method['status']['label']))?view('common.status', ['status_data' => ['label' => $payment_method['status']['label'], "color" => $payment_method['status']['color']]])->render():'-';
                $item_array[$key][] = $payment_method['created_at_label'];
                $item_array[$key][] = $payment_method['updated_at_label'];
                $item_array[$key][] = (isset($payment_method['created_by']) && isset($payment_method['created_by']['fullname']))?$payment_method['created_by']['fullname']:'-';
                $item_array[$key][] = view('payment_method.layouts.payment_method_actions', array('payment_method' => $payment_method))->render();
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

            if(!check_access(['A_ADD_PAYMENT_METHOD'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $payment_method_data_exists = PaymentMethodModel::select('id')
            ->where('label', '=', trim($request->payment_method_name))
            ->first();
            if (!empty($payment_method_data_exists)) {
                throw new Exception("Payment method already exists", 400);
            }

            DB::beginTransaction();
            
            $payment_method = [
                "slack" => $this->generate_slack("payment_methods"),
                "label" => Str::title($request->payment_method_name),
                "key_1" => trim($request->key_1),
                "key_2" => trim($request->key_2),
                "description" => $request->description,
                "activate_on_digital_menu" => (isset($request->activate_on_digital_menu))?$request->activate_on_digital_menu:0,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];
            
            $payment_method_id = PaymentMethodModel::create($payment_method)->id;

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Payment method created successfully", 
                    "data"    => $payment_method['slack']
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

            if(!check_access(['A_DETAIL_PAYMENT_METHOD'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = PaymentMethodModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new PaymentMethodResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Payment method loaded successfully", 
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

            if(!check_access(['A_VIEW_PAYMENT_METHOD_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new PaymentMethodCollection(PaymentMethodModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Payment method loaded successfully", 
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

            if(!check_access(['A_EDIT_PAYMENT_METHOD'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);
            
            $payment_method_data_exists = PaymentMethodModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['label', '=', trim($request->payment_method_name)],
            ])
            ->first();
            if (!empty($payment_method_data_exists)) {
                throw new Exception("Payment method already exists", 400);
            }

            DB::beginTransaction();
            
            $payment_method = [
                "label" => Str::title($request->payment_method_name),
                "key_1" => trim($request->key_1),
                "key_2" => trim($request->key_2),
                "description" => $request->description,
                "activate_on_digital_menu" => (isset($request->activate_on_digital_menu))?$request->activate_on_digital_menu:0,
                "status" => $request->status,
                "updated_by" => $request->logged_user_id
            ];

            $action_response = PaymentMethodModel::where('slack', $slack)
            ->update($payment_method);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Payment method updated successfully", 
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
            'payment_method_name' => $this->get_validation_rules("name_label", true),
            'key_1' => $this->get_validation_rules("text", false),
            'key_2' => $this->get_validation_rules("text", false),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
