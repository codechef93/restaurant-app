<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

use App\Models\Table as TableModel;
use App\Models\User as UserModel;
use App\RestoArea;

use App\Http\Resources\TableResource;

use App\Http\Resources\Collections\TableCollection;

class Table extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_RESTAURANT_TABLE_LISTING';
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
            
            $query = TableModel::select('restaurant_tables.*')
            ->take($limit)
            ->skip($offset)
            ->waiterJoin()
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

            $tables = TableResource::collection($query);
           
            $total_count = TableModel::select("id")->get()->count();

            $item_array = [];
            foreach($tables as $key => $table){
                
                $table = $table->toArray($request);

                $item_array[$key][] = $table['table_number'];
                $item_array[$key][] = $table['no_of_occupants'];
                $item_array[$key][] = RestoArea::where('id', $table['restoarea_id'])->first()->name;
                $item_array[$key][] = (isset($table['waiter']['fullname']))?$table['waiter']['user_code'].' - '.$table['waiter']['fullname']:'-';
                $item_array[$key][] = (isset($table['status']['label']))?view('common.status', ['status_data' => ['label' => $table['status']['label'], "color" => $table['status']['color']]])->render():'-';
                $item_array[$key][] = $table['created_at_label'];
                $item_array[$key][] = $table['updated_at_label'];
                $item_array[$key][] = (isset($table['created_by']['fullname']))?$table['created_by']['fullname']:'-';
                $item_array[$key][] = view('table.layouts.table_actions', ['table' => $table])->render();
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

            if(!check_access(['A_ADD_RESTAURANT_TABLE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $table_data_exists = TableModel::select('id')
            ->where('table_number', '=', trim($request->table_number))
            ->first();
            if (!empty($table_data_exists)) {
                throw new Exception("Table already exists", 400);
            }

            if(trim($request->waiter) != ''){
                $waiter_data_exists = UserModel::select('id')
                ->where('slack', '=', trim($request->waiter))
                ->first();
                if (empty($waiter_data_exists)) {
                    throw new Exception("Invalid waiter selected", 400);
                }
            }

            DB::beginTransaction();
            
            $table = [
                "slack" => $this->generate_slack("restaurant_tables"),
                "store_id" => $request->logged_user_store_id,
                "table_number" => $request->table_number,
                "no_of_occupants" => $request->no_of_occupants,
                "waiter_user_id" => (isset($waiter_data_exists) && $waiter_data_exists->id != '')?$waiter_data_exists->id:NULL,
                "status" => $request->status,
                "restoarea_id" => $request->restoarea_id,
                "created_by" => $request->logged_user_id
            ];
            
            $table_id = TableModel::create($table)->id;

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Table created successfully", 
                    "data"    => $table['slack']
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

            if(!check_access(['A_DETAIL_RESTAURANT_TABLE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = TableModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new TableResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Table loaded successfully", 
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

            if(!check_access(['A_VIEW_RESTAURANT_TABLE_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new TableCollection(TableModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Table loaded successfully", 
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

            if(!check_access(['A_EDIT_RESTAURANT_TABLE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $table_data_exists = TableModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['table_number', '=', trim($request->table_number)],
            ])
            ->first();
            if (!empty($table_data_exists)) {
                throw new Exception("Table already exists", 400);
            }

            if(trim($request->waiter) != ''){
                $waiter_data_exists = UserModel::select('id')
                ->where('slack', '=', trim($request->waiter))
                ->first();
                if (empty($waiter_data_exists)) {
                    throw new Exception("Invalid waiter selected", 400);
                }
            }

            DB::beginTransaction();

            $table = [
                "table_number" => $request->table_number,
                "no_of_occupants" => $request->no_of_occupants,
                "waiter_user_id" => (isset($waiter_data_exists) && $waiter_data_exists->id != '')?$waiter_data_exists->id:NULL,
                "status" => $request->status,
                "restoarea_id" => $request->restoarea_id,
                'updated_by' => $request->logged_user_id
            ];

            $action_response = TableModel::where('slack', $slack)
            ->update($table);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Table updated successfully", 
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
            'table_number' => $this->get_validation_rules("name_label", true),
            'no_of_occupants' => $this->get_validation_rules("numeric", true),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
