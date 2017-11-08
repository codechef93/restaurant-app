<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\VariantOptionResource;

use App\Models\VariantOption as VariantOptionModel;

use App\Http\Resources\Collections\VariantOptionCollection;

class VariantOption extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_VARIANT_OPTION_LISTING';
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
            
            $query = VariantOptionModel::select('variant_options.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $variant_options = VariantOptionResource::collection($query);
           
            $total_count = VariantOptionModel::select("id")->get()->count();

            $item_array = [];
            foreach($variant_options as $key => $variant_option){
                
                $variant_option = $variant_option->toArray($request);

                $item_array[$key][] = $variant_option['variant_option_code'];
                $item_array[$key][] = $variant_option['label'];
                $item_array[$key][] = (isset($variant_option['status']['label']))?view('common.status', ['status_data' => ['label' => $variant_option['status']['label'], "color" => $variant_option['status']['color']]])->render():'-';
                $item_array[$key][] = $variant_option['created_at_label'];
                $item_array[$key][] = $variant_option['updated_at_label'];
                $item_array[$key][] = (isset($variant_option['created_by']['fullname']))?$variant_option['created_by']['fullname']:'-';
                $item_array[$key][] = view('variant_option.layouts.variant_option_actions', ['variant_option' => $variant_option])->render();
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

            if(!check_access(['A_ADD_VARIANT_OPTION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $variant_option_data_exists = VariantOptionModel::select('id')
            ->where('label', '=', trim($request->variant_option_name))
            ->first();
            if (!empty($variant_option_data_exists)) {
                throw new Exception("Variant option already exists", 400);
            }

            DB::beginTransaction();
            
            $variant_option = [
                "slack" => $this->generate_slack("variant_options"),
                "store_id" => $request->logged_user_store_id,
                "variant_option_code" => Str::random(6),
                "label" => $request->variant_option_name,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];
            
            $variant_option_id = VariantOptionModel::create($variant_option)->id;

            $code_start_config = Config::get('constants.unique_code_start.variant_option');
            $code_start = (isset($code_start_config))?$code_start_config:100;
            
            $variant_option_code = [
                "variant_option_code" => 'VO'.($code_start+$variant_option_id)
            ];
            VariantOptionModel::where('id', $variant_option_id)
            ->update($variant_option_code);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Variant option created successfully", 
                    "data"    => $variant_option['slack']
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

            if(!check_access(['A_DETAIL_VARIANT_OPTION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = VariantOptionModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new VariantOptionResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Variant option loaded successfully", 
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

            if(!check_access(['A_VIEW_VARIANT_OPTION_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new VariantOptionCollection(VariantOptionModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Variant options loaded successfully", 
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

            if(!check_access(['A_EDIT_VARIANT_OPTION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $variant_option_data_exists = VariantOptionModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['label', '=', trim($request->variant_option_name)],
            ])
            ->first();
            if (!empty($variant_option_data_exists)) {
                throw new Exception("Variant option already exists", 400);
            }

            $variant_option_data = VariantOptionModel::select('id')
            ->where('slack', '=', trim($slack))
            ->first();

            DB::beginTransaction();

            $variant_option = [
                "label" => $request->variant_option_name,
                "status" => $request->status,
                'updated_by' => $request->logged_user_id
            ];

            $action_response = VariantOptionModel::where('slack', $slack)
            ->update($variant_option);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Variant option updated successfully", 
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
            'variant_option_name' => $this->get_validation_rules("name_label", true),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
