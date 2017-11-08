<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\AddonGroupResource;

use App\Models\AddonGroup as AddonGroupModel;
use App\Models\AddonGroupProduct as AddonGroupProductModel;
use App\Models\Product as ProductModel;

use App\Http\Resources\Collections\AddonGroupCollection;

class AddonGroup extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_ADDON_GROUP_LISTING';
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
            
            $query = AddonGroupModel::select('addon_groups.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $addon_groups = AddonGroupResource::collection($query);
           
            $total_count = AddonGroupModel::select("id")->get()->count();

            $item_array = [];
            foreach($addon_groups as $key => $addon_group){
                
                $addon_group = $addon_group->toArray($request);

                $item_array[$key][] = $addon_group['addon_group_code'];
                $item_array[$key][] = $addon_group['label'];
                $item_array[$key][] = (isset($addon_group['status']['label']))?view('common.status', ['status_data' => ['label' => $addon_group['status']['label'], "color" => $addon_group['status']['color']]])->render():'-';
                $item_array[$key][] = $addon_group['created_at_label'];
                $item_array[$key][] = $addon_group['updated_at_label'];
                $item_array[$key][] = (isset($addon_group['created_by']['fullname']))?$addon_group['created_by']['fullname']:'-';
                $item_array[$key][] = view('addon_group.layouts.addon_group_actions', ['addon_group' => $addon_group])->render();
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

            if(!check_access(['A_ADD_ADDON_GROUP'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $addon_group_data_exists = AddonGroupModel::select('id')
            ->where('label', '=', trim($request->addon_group_name))
            ->first();
            if (!empty($addon_group_data_exists)) {
                throw new Exception("Add-on group already exists", 400);
            }

            DB::beginTransaction();
            
            $addon_group = [
                "slack" => $this->generate_slack("addon_groups"),
                "store_id" => $request->logged_user_store_id,
                "addon_group_code" => Str::random(6),
                "label" => $request->addon_group_name,
                "multiple_selection" => $request->multiple_selection,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];
            
            $addon_group_id = AddonGroupModel::create($addon_group)->id;

            $code_start_config = Config::get('constants.unique_code_start.addon_group');
            $code_start = (isset($code_start_config))?$code_start_config:100;
            
            $addon_group_code = [
                "addon_group_code" => 'AOG'.($code_start+$addon_group_id)
            ];
            AddonGroupModel::where('id', $addon_group_id)
            ->update($addon_group_code);

            $addon_products = $request->addon_products;
            
            foreach($addon_products as $key => $addon_product){

                if(!empty($addon_product['addon_product_slack'])){
                    $addon_product_data = ProductModel::select('id')
                    ->where('slack', '=', trim($addon_product['addon_product_slack']))
                    ->active()
                    ->first();

                    if (empty($addon_product_data)) {
                        throw new Exception("Invalid add-on product selected at line ". ($key+1), 400);
                    }

                    $addon_product_data_array[] = [
                        "addon_group_id" => $addon_group_id,
                        "product_id" => $addon_product_data->id,
                        "created_by" => $request->logged_user_id
                    ];
                }
            }

            if(!empty($addon_product_data_array) && count($addon_product_data_array)>0){
                foreach($addon_product_data_array as $addon_product_data_array_item){
                    AddonGroupProductModel::create($addon_product_data_array_item);
                }
            }

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Add-on group created successfully", 
                    "data"    => $addon_group['slack']
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

            if(!check_access(['A_DETAIL_ADDON_GROUP'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = AddonGroupModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new AddonGroupResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Add-on group loaded successfully", 
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

            if(!check_access(['A_VIEW_ADDON_GROUP_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new AddonGroupCollection(AddonGroupModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Add-on group loaded successfully", 
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

            if(!check_access(['A_EDIT_ADDON_GROUP'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $addon_group_data_exists = AddonGroupModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['label', '=', trim($request->addon_group_name)],
            ])
            ->first();
            if (!empty($addon_group_data_exists)) {
                throw new Exception("Add-on group already exists", 400);
            }

            $addon_group_data = AddonGroupModel::select('id')
            ->where('slack', '=', trim($slack))
            ->first();

            DB::beginTransaction();

            $addon_group = [
                "label" => $request->addon_group_name,
                "multiple_selection" => $request->multiple_selection,
                "status" => $request->status,
                'updated_by' => $request->logged_user_id
            ];

            $action_response = AddonGroupModel::where('slack', $slack)
            ->update($addon_group);

            AddonGroupProductModel::where('addon_group_id', $addon_group_data->id)->delete();

            $addon_products = $request->addon_products;
            
            foreach($addon_products as $key => $addon_product){

                if(!empty($addon_product['addon_product_slack'])){
                    $addon_product_data = ProductModel::select('id')
                    ->where('slack', '=', trim($addon_product['addon_product_slack']))
                    ->active()
                    ->first();

                    if (empty($addon_product_data)) {
                        throw new Exception("Invalid add-on product selected at line ". ($key+1), 400);
                    }

                    $addon_product_data_array[] = [
                        "addon_group_id" => $addon_group_data->id,
                        "product_id" => $addon_product_data->id,
                        "created_by" => $request->logged_user_id
                    ];
                }
            }

            if(!empty($addon_product_data_array) && count($addon_product_data_array)>0){
                foreach($addon_product_data_array as $addon_product_data_array_item){
                    AddonGroupProductModel::create($addon_product_data_array_item);
                }
            }

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Add-on group updated successfully", 
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
        $request->merge(['addon_products' => json_decode($request->addon_products, true)]);

        $validator = Validator::make($request->all(), [
            'addon_group_name' => $this->get_validation_rules("name_label", true),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
