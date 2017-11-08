<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\CategoryResource;

use App\Models\Category as CategoryModel;

use App\Http\Resources\Collections\CategoryCollection;

class Category extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_CATEGORY_LISTING';
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
            
            $query = CategoryModel::select('category.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $categories = CategoryResource::collection($query);
           
            $total_count = CategoryModel::select("id")->get()->count();

            $item_array = [];
            foreach($categories as $key => $category){
                
                $category = $category->toArray($request);

                $item_array[$key][] = $category['label'];
                $item_array[$key][] = $category['category_code'];
                $item_array[$key][] = ($category['display_on_pos_screen'] == 1)?'Yes':'No';
                $item_array[$key][] = ($category['display_on_qr_menu'] == 1)?'Yes':'No';
                $item_array[$key][] = (isset($category['status']['label']))?view('common.status', ['status_data' => ['label' => $category['status']['label'], "color" => $category['status']['color']]])->render():'-';
                $item_array[$key][] = $category['created_at_label'];
                $item_array[$key][] = $category['updated_at_label'];
                $item_array[$key][] = (isset($category['created_by']['fullname']))?$category['created_by']['fullname']:'-';
                $item_array[$key][] = view('category.layouts.category_actions', ['category' => $category])->render();
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

            if(!check_access(['A_ADD_CATEGORY'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $category_data_exists = CategoryModel::select('id')
            ->where('label', '=', trim($request->category_name))
            ->first();
            if (!empty($category_data_exists)) {
                throw new Exception("Category already exists", 400);
            }

            DB::beginTransaction();
            
            $category = [
                "slack" => $this->generate_slack("category"),
                "store_id" => $request->logged_user_store_id,
                "category_code" => Str::random(6),
                "label" => Str::title($request->category_name),
                "description" => $request->description,
                "display_on_pos_screen" => (isset($request->display_on_pos_screen))?$request->display_on_pos_screen:0,
                "display_on_qr_menu" => (isset($request->display_on_qr_menu))?$request->display_on_qr_menu:0,
                "status" => $request->status,
                "created_by" => $request->logged_user_id,
                "type" => $request->type,
            ];
            
            $category_id = CategoryModel::create($category)->id;

            $code_start_config = Config::get('constants.unique_code_start.category');
            $code_start = (isset($code_start_config))?$code_start_config:100;
            
            $category_code = [
                "category_code" => "CAT".($code_start+$category_id)
            ];
            CategoryModel::where('id', $category_id)
            ->update($category_code);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Category created successfully", 
                    "data"    => $category['slack']
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

            if(!check_access(['A_DETAIL_CATEGORY'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = CategoryModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new CategoryResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Category loaded successfully", 
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

            if(!check_access(['A_VIEW_CATEGORY_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new CategoryCollection(CategoryModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Categories loaded successfully", 
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

            if(!check_access(['A_EDIT_CATEGORY'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $category_data_exists = CategoryModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['label', '=', trim($request->category_name)],
            ])
            ->first();
            if (!empty($category_data_exists)) {
                throw new Exception("Category already exists", 400);
            }

            DB::beginTransaction();

            $category = [
                "label" => Str::title($request->category_name),
                "description" => $request->description,
                "display_on_pos_screen" => (isset($request->display_on_pos_screen))?$request->display_on_pos_screen:0,
                "display_on_qr_menu" => (isset($request->display_on_qr_menu))?$request->display_on_qr_menu:0,
                "status" => $request->status,
                'updated_by' => $request->logged_user_id,
                "type" => $request->type
            ];

            $action_response = CategoryModel::where('slack', $slack)
            ->update($category);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Category updated successfully", 
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
            'category_name' => $this->get_validation_rules("name_label", true),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
