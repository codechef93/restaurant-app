<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Models\SettingSms as SettingSmsModel;

use App\Http\Resources\SettingSmsResource;

use App\Http\Resources\Collections\SettingSmsCollection;

class SmsSetting extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_SMS_SETTING_LISTING';
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
            
            $query = SettingSmsModel::select('setting_sms_gateways.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $sms_settings = SettingSmsResource::collection($query);
           
            $total_count = SettingSmsModel::select("id")->get()->count();

            $item_array = [];
            foreach($sms_settings as $key => $sms_setting){

                $sms_setting = $sms_setting->toArray($request);

                $item_array[$key][] = $sms_setting['gateway_type'];
                $item_array[$key][] = (isset($sms_setting['status']['label']))?view('common.status', ['status_data' => ['label' => $sms_setting['status']['label'], "color" => $sms_setting['status']['color']]])->render():'-';
                $item_array[$key][] = $sms_setting['created_at_label'];
                $item_array[$key][] = $sms_setting['updated_at_label'];
                $item_array[$key][] = (isset($sms_setting['created_by']) && isset($sms_setting['created_by']['fullname']))?$sms_setting['created_by']['fullname']:'-';
                $item_array[$key][] = view('setting.sms.layouts.sms_setting_actions', array('sms_setting' => $sms_setting))->render();
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
        //
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

            if(!check_access(['A_DETAIL_SMS_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = SettingSmsModel::where('slack', '=', $slack)->first();

            $item_data = new SettingSmsResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "SMS Setting loaded successfully", 
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

            if(!check_access(['A_VIEW_SMS_SETTING_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new SettingSmsCollection(SettingSmsModel::select('*')->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "SMS Settings loaded successfully", 
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

            if(!check_access(['A_EDIT_SMS_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }
            
            $this->validate_request($request);

            $sms_setting_data_exists = SettingSmsModel::select('id')
            ->where([
                ['slack', '=', $slack],
                ['gateway_type', '=', $request->gateway_type]
            ])
            ->first();
            if (empty($sms_setting_data_exists)) {
                throw new Exception("Trying to update invalid SMS setting", 400);
            }

            DB::beginTransaction();

            switch ($request->gateway_type) {
                case 'TWILIO':
                    $sms_setting = [
                        "account_id" => trim($request->account_id),
                        "token" => trim($request->token),
                        "twilio_number" => trim($request->twilio_number),
                        "status" => $request->status,
                        "updated_by" => $request->logged_user_id
                    ];
                    break;
                
                case 'MSG91':
                    $sms_setting = [
                        "auth_key" => trim($request->auth_key),
                        "sender_id" => trim($request->sender_id),
                        "status" => $request->status,
                        "updated_by" => $request->logged_user_id
                    ];
                    break;
                case 'TEXTLOCAL':
                    $sms_setting = [
                        "auth_key" => trim($request->api_key),
                        "sender_id" => trim($request->sender_id),
                        "status" => $request->status,
                        "updated_by" => $request->logged_user_id
                    ];
                    break;
            }
            
            $action_response = SettingSmsModel::where('slack', $slack)
            ->update($sms_setting);

            if($request->status == 1){
                SettingSmsModel::where([
                    ['slack', '!=', $slack],
                    ['gateway_type', '!=', $request->gateway_type]
                ])
                ->update(['status' => 0]);
            }

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "SMS settings updated successfully", 
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
        switch($request->gateway_type){
            case 'TWILIO':
                $validator = Validator::make($request->all(), [
                    'account_id' => 'max:150|required',
                    'token' => 'max:150|required',
                    'twilio_number' => 'max:50|required',
                    'status' => $this->get_validation_rules("status", true),
                ]);
            break;
            case 'MSG91':
                $validator = Validator::make($request->all(), [
                    'auth_key' => 'max:100|required',
                    'sender_id' => 'min:6|max:10|required',
                    'status' => $this->get_validation_rules("status", true),
                ]);
            break;
            case 'TEXTLOCAL':
                $validator = Validator::make($request->all(), [
                    'api_key' => 'max:100|required',
                    'sender_id' => 'min:6|max:6|required',
                    'status' => $this->get_validation_rules("status", true),
                ]);
            break;
        }
        
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
