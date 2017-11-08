<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\MeasurementUnitResource;

use App\Models\MeasurementUnit as MeasurementUnitModel;

use App\Http\Resources\Collections\MeasurementUnitCollection;

class MeasurementUnit extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_MEASUREMENT_UNIT_LISTING';
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
            
            $query = MeasurementUnitModel::select('measurement_units.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $measurement_units = MeasurementUnitResource::collection($query);
           
            $total_count = MeasurementUnitModel::select("id")->get()->count();

            $item_array = [];
            foreach($measurement_units as $key => $measurement_unit){
                
                $measurement_unit = $measurement_unit->toArray($request);

                $item_array[$key][] = $measurement_unit['unit_code'];
                $item_array[$key][] = $measurement_unit['label'];
                $item_array[$key][] = (isset($measurement_unit['status']['label']))?view('common.status', ['status_data' => ['label' => $measurement_unit['status']['label'], "color" => $measurement_unit['status']['color']]])->render():'-';
                $item_array[$key][] = $measurement_unit['created_at_label'];
                $item_array[$key][] = $measurement_unit['updated_at_label'];
                $item_array[$key][] = (isset($measurement_unit['created_by']['fullname']))?$measurement_unit['created_by']['fullname']:'-';
                $item_array[$key][] = view('measurement_unit.layouts.measurement_unit_actions', ['measurement_unit' => $measurement_unit])->render();
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

            if(!check_access(['A_ADD_MEASUREMENT_UNIT'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $measurement_unit_data_exists = MeasurementUnitModel::select('id')
            ->where('unit_code', '=', trim($request->unit_code))
            ->first();
            if (!empty($measurement_unit_data_exists)) {
                throw new Exception("Measurement unit already exists", 400);
            }

            DB::beginTransaction();
            
            $measurement_unit = [
                "slack" => $this->generate_slack("measurement_units"),
                "unit_code" => $request->unit_code,
                "label" => $request->label,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];
            
            $measurement_unit_id = MeasurementUnitModel::create($measurement_unit)->id;

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Measurement unit created successfully", 
                    "data"    => $measurement_unit['slack']
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

            if(!check_access(['A_DETAIL_MEASUREMENT_UNIT'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = MeasurementUnitModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new MeasurementUnitResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Measurement unit loaded successfully", 
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

            if(!check_access(['A_VIEW_MEASUREMENT_UNIT_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new MeasurementUnitCollection(MeasurementUnitModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Measurement units loaded successfully", 
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

            if(!check_access(['A_EDIT_MEASUREMENT_UNIT'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $measurement_unit_data_exists = MeasurementUnitModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['unit_code', '=', trim($request->unit_code)],
            ])
            ->first();
            if (!empty($measurement_unit_data_exists)) {
                throw new Exception("Measurement unit already exists", 400);
            }

            DB::beginTransaction();

            $measurement_unit = [
                "unit_code" => trim($request->unit_code),
                "label" => $request->label,
                "status" => $request->status,
                'updated_by' => $request->logged_user_id
            ];

            $action_response = MeasurementUnitModel::where('slack', $slack)
            ->update($measurement_unit);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Measurement unit updated successfully", 
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
            'unit_code' => $this->get_validation_rules("codes", true).'|max:30',
            'label' => $this->get_validation_rules("name_label", true),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}