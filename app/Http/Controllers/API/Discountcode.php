<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Http\Resources\DiscountcodeResource;
use App\Models\Discountcode as DiscountcodeModel;

use App\Http\Resources\Collections\DiscountcodeCollection;

class Discountcode extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_DISCOUNTCODE_LISTING';
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
            
            $query = DiscountcodeModel::select('discount_codes.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $discount_codes = DiscountcodeResource::collection($query);
           
            $total_count = DiscountcodeModel::select("id")->get()->count();

            $item_array = [];
            foreach($discount_codes as $key => $discount_code){
                
                $discount_code = $discount_code->toArray($request);

                $item_array[$key][] = $discount_code['label'];
                $item_array[$key][] = $discount_code['discount_code'];
                $item_array[$key][] = $discount_code['discount_percentage'];
                $item_array[$key][] = (isset($discount_code['status']['label']))?view('common.status', ['status_data' => ['label' => $discount_code['status']['label'], "color" => $discount_code['status']['color']]])->render():'-';
                $item_array[$key][] = $discount_code['created_at_label'];
                $item_array[$key][] = $discount_code['updated_at_label'];
                $item_array[$key][] = (isset($discount_code['created_by']) && isset($discount_code['created_by']['fullname']))?$discount_code['created_by']['fullname']:'-';
                $item_array[$key][] = view('discount_code.layouts.discount_code_actions', array('discount_code' => $discount_code))->render();
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

            if(!check_access(['A_ADD_DISCOUNTCODE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $discount_code_exists = DiscountcodeModel::select('id')
            ->where('discount_code', '=', trim($request->discount_code))
            ->first();
            if (!empty($discount_code_exists)) {
                throw new Exception("Discount code already exists", 400);
            }

            DB::beginTransaction();
            
            $discount_code = [
                "slack" => $this->generate_slack("discount_codes"),
                "store_id" => $request->logged_user_store_id,
                "label" => $request->label,
                "discount_code" => strtoupper($request->discount_code),
                "discount_percentage" => $request->discount_percentage,
                "description" => $request->description,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];
            
            $discount_code_id = DiscountcodeModel::create($discount_code)->id;

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Discount code created successfully", 
                    "data"    => $discount_code['slack']
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

            if(!check_access(['A_DETAIL_DISCOUNTCODE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = DiscountcodeModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new DiscountcodeResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Discount code loaded successfully", 
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

            if(!check_access(['A_VIEW_DISCOUNTCODE_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new DiscountcodeCollection(DiscountcodeModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Discount codes loaded successfully", 
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

            if(!check_access(['A_EDIT_DISCOUNTCODE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $discount_code_exists = DiscountcodeModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['discount_code', '=', trim($request->discount_code)],
            ])
            ->first();
            if (!empty($discount_code_exists)) {
                throw new Exception("Discount code already exists", 400);
            }

            DB::beginTransaction();
            
            $discount_code = [
                "label" => $request->label,
                "discount_code" => strtoupper($request->discount_code),
                "discount_percentage" => $request->discount_percentage,
                "description" => $request->description,
                "status" => $request->status,
                "updated_by" => $request->logged_user_id
            ];
            
            $action_response = DiscountcodeModel::where('slack', $slack)
            ->update($discount_code);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Discount code updated successfully", 
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
            'label' => $this->get_validation_rules("name_label", true),
            'discount_code' => $this->get_validation_rules("codes", true),
            'discount_percentage' => $this->get_validation_rules("numeric", true),
            'description' => $this->get_validation_rules("text", false),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
