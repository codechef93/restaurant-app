<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\SmsTemplateResource;

use App\Models\SmsTemplate as SmsTemplateModel;

class SmsTemplate extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_SMS_TEMPLATE_LISTING';
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
            
            $query = SmsTemplateModel::select('sms_templates.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $sms_templates = SmsTemplateResource::collection($query);
           
            $total_count = SmsTemplateModel::select("id")->get()->count();

            $item_array = [];
            foreach($sms_templates as $key => $sms_template){
                
                $sms_template = $sms_template->toArray($request);

                $item_array[$key][] = $sms_template['template_key'];
                $item_array[$key][] = Str::limit($sms_template['message'], 100);
                $item_array[$key][] = (isset($sms_template['status']['label']))?view('common.status', ['status_data' => ['label' => $sms_template['status']['label'], "color" => $sms_template['status']['color']]])->render():'-';
                $item_array[$key][] = $sms_template['created_at_label'];
                $item_array[$key][] = $sms_template['updated_at_label'];
                $item_array[$key][] = (isset($sms_template['created_by']['fullname']))?$sms_template['created_by']['fullname']:'-';
                $item_array[$key][] = view('setting.sms_templates.layouts.sms_template_actions', ['sms_template' => $sms_template])->render();
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

            if(!check_access(['A_EDIT_SMS_TEMPLATE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $sms_template_data_exists = SmsTemplateModel::select('id')
            ->where([
                ['slack', '=', $slack]
            ])
            ->first();
            if (empty($sms_template_data_exists)) {
                throw new Exception("Invalid SMS template", 400);
            }

            DB::beginTransaction();

            $sms_template = [
                "flow_id" => $request->flow_id,
                "message" => $request->message,
                "status" => $request->status,
                'updated_by' => $request->logged_user_id
            ];

            $action_response = SmsTemplateModel::where('slack', $slack)
            ->update($sms_template);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "SMS template updated successfully", 
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
            'flow_id' => $this->get_validation_rules("string", false).'max:100',
            'message' => $this->get_validation_rules("text", true),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
