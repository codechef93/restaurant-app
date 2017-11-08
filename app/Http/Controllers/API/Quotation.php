<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\QuotationResource;

use App\Models\Quotation as QuotationModel;
use App\Models\QuotationProduct as QuotationProductModel;
use App\Models\Supplier as SupplierModel;
use App\Models\Customer as CustomerModel;
use App\Models\Product as ProductModel;
use App\Models\Country as CountryModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\MasterTaxOption as MasterTaxOptionModel;

use App\Http\Resources\Collections\QuotationCollection;

class Quotation extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_QUOTATION_LISTING';
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
            
            $query = QuotationModel::select('quotations.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $quotations = QuotationResource::collection($query);
           
            $total_count = QuotationModel::select("id")->get()->count();

            $item_array = [];
            foreach($quotations as $key => $quotation){
                
                $quotation = $quotation->toArray($request);

                $item_array[$key][] = $quotation['quotation_number'];
                $item_array[$key][] = ($quotation['quotation_reference'] != '')?$quotation['quotation_reference']:'-';
                $item_array[$key][] = ($quotation['bill_to'] != '')?$quotation['bill_to']:'-';
                $item_array[$key][] = ($quotation['bill_to_name'] != '')?$quotation['bill_to_name']:'-';
                $item_array[$key][] = ($quotation['quotation_date'] != '')?$quotation['quotation_date']:'-';
                $item_array[$key][] = ($quotation['quotation_due_date'] != '')?$quotation['quotation_due_date']:'-';
                $item_array[$key][] = $quotation['total_order_amount'];
                $item_array[$key][] = (isset($quotation['status']['label']))?view('common.status', ['status_data' => ['label' => $quotation['status']['label'], "color" => $quotation['status']['color']]])->render():'-';
                $item_array[$key][] = $quotation['created_at_label'];
                $item_array[$key][] = $quotation['updated_at_label'];
                $item_array[$key][] = (isset($quotation['created_by']) && isset($quotation['created_by']['fullname']))?$quotation['created_by']['fullname']:'-';
                $item_array[$key][] = view('quotation.layouts.quotation_actions', array('quotation' => $quotation))->render();
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

            if(!check_access(['A_ADD_QUOTATION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            DB::beginTransaction();

            $quotation_data = $this->form_quotation_array($request);
            
            if(!empty($quotation_data['quotation_data'])){
                
                $quotation = $quotation_data['quotation_data'];
                
                $quotation['slack'] = $this->generate_slack("quotations");
                $quotation['quotation_number'] = Str::random(6);
                $quotation['created_at'] = now();
                $quotation['created_by'] = $request->logged_user_id;

                $quotation_id = QuotationModel::create($quotation)->id;

                $code_start_config = Config::get('constants.unique_code_start.quotation');
                $code_start = (isset($code_start_config))?$code_start_config:100;
                
                $quotation_number = [
                    "quotation_number" => ($code_start+$quotation_id)
                ];
                QuotationModel::where('id', $quotation_id)
                ->update($quotation_number);
            }
            
            if(!empty($quotation_data['quotation_products'])){
                
                $quotation_products = $quotation_data['quotation_products'];

                array_walk($quotation_products, function (&$item, $key) use ($quotation_id, $request){
                    
                    $item['slack'] = $this->generate_slack("quotation_products");
                    $item['quotation_id'] = $quotation_id; 
                    $item['created_at'] = now();
                    $item['created_by'] = $request->logged_user_id;

                    QuotationProductModel::insert($item);

                });
            }
            
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Quotation created successfully", 
                    "data"    => $quotation['slack']
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

            if(!check_access(['A_DETAIL_QUOTATION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = QuotationModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new QuotationResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Quotation loaded successfully", 
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

            if(!check_access(['A_VIEW_QUOTATION_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new QuotationCollection(QuotationModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Quotations loaded successfully", 
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

            if(!check_access(['A_EDIT_QUOTATION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $quotation_details = QuotationModel::where('slack', $slack)->first();

            DB::beginTransaction();

            $request->quotation_slack = $slack;

            $quotation_data = $this->form_quotation_array($request);
            
            if(!empty($quotation_data['quotation_data'])){
                
                $quotation = $quotation_data['quotation_data'];
            
                $quotation['updated_at'] = now();
                $quotation['updated_by'] = $request->logged_user_id;

                $action_response = QuotationModel::where('slack', $slack)
                ->update($quotation);
            }
            
            $quotation_id = $quotation_details->id;

            if(!empty($quotation_data['quotation_products'])){
                
                if(count($quotation_data['quotation_products']) > 0){
                    QuotationProductModel::where('quotation_id', $quotation_id)->delete();
                }

                $quotation_products = $quotation_data['quotation_products'];

                array_walk($quotation_products, function (&$item, $key) use ($quotation_id, $request){

                    $item['slack'] = $this->generate_slack("quotation_products");
                    $item['quotation_id'] = $quotation_id;
                    $item['updated_at'] = now();
                    $item['updated_by'] = $request->logged_user_id;

                    QuotationProductModel::insert($item);

                });
            }
            
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Quotation updated successfully", 
                    "data"    => $quotation_details['slack']
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
    public function destroy($slack)
    {
        try{

            if(!check_access(['A_DELETE_QUOTATION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $quotation_detail = QuotationModel::select('id')->where('slack', $slack)->first();
            if (empty($quotation_detail)) {
                throw new Exception("Invalid quotation provided", 400);
            }
            $quotation_id = $quotation_detail->id;

            DB::beginTransaction();

            QuotationProductModel::where('quotation_id', $quotation_id)->delete();
            QuotationModel::where('id', $quotation_id)->delete();

            DB::commit();

            $forward_link = route('quotations');

            return response()->json($this->generate_response(
                array(
                    "message" => "Quotation deleted successfully", 
                    "data" => $slack,
                    "link" => $forward_link
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

    public function update_quotation_status(Request $request, $slack)
    {
        try {

            if(!check_access(['A_EDIT_STATUS_QUOTATION'], true)){
                throw new Exception("Invalid request", 400);
            }

            $status_constant = $request->status;
            
            $quotation_details = QuotationModel::where('slack', $slack)->first();
            if(empty($quotation_details)){
                throw new Exception("Invalid quotation selected");
            }

            $quotation_status = MasterStatusModel::select('value')->where([
                ['value_constant', '=', strtoupper($status_constant)],
                ['key', '=', 'quotation_STATUS'],
                ['status', '=', '1']
            ])->active()->first();
            if(empty($quotation_status)){
                throw new Exception("Invalid status provided");
            }

            DB::beginTransaction();
            
            $quotation = [];
            $quotation['status'] = $quotation_status->value;
            $quotation['updated_at'] = now();
            $quotation['updated_by'] = $request->logged_user_id;

            $action_response = QuotationModel::where('slack', $slack)
            ->update($quotation);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "quotation status changed successfully", 
                    "data"    => $quotation_details->slack
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

    public function form_quotation_array($request){
        
        $quotation_slack = $request->quotation_slack;

        $products = $request->products;

        if( empty((array) $products) ){
            throw new Exception("Product list cannot be empty");
        }

        if($request->bill_to == 'SUPPLIER'){
            $supplier_data = SupplierModel::select('id', 'name', 'supplier_code', 'email', 'phone', 'address', 'pincode')
            ->where('slack', '=', trim($request->bill_to_slack))
            ->active()
            ->first();
            if (empty($supplier_data)) {
                throw new Exception("Invalid supplier selected", 400);
            }

            $bill_to_id = $supplier_data->id;
            $bill_to_code = $supplier_data->supplier_code;
            $bill_to_name = $supplier_data->name;
            $bill_to_email = $supplier_data->email;
            $bill_to_contact = $supplier_data->phone;
            $bill_to_address = $supplier_data->address . ','.$supplier_data->pincode;

        }else if($request->bill_to == 'CUSTOMER'){
            $customer_data = CustomerModel::select('id', 'name', 'email', 'address', 'phone')
            ->where('slack', '=', trim($request->bill_to_slack))
            ->active()
            ->first();
            if (empty($customer_data)) {
                throw new Exception("Invalid customer selected", 400);
            }

            $bill_to_id = $customer_data->id;
            $bill_to_code = '';
            $bill_to_name = $customer_data->name;
            $bill_to_email = $customer_data->email;
            $bill_to_contact = $customer_data->phone;
            $bill_to_address = $customer_data->address;
        }

        $quotation_number_details = QuotationModel::where([
            ['quotation_reference', '=', trim($request->quotation_reference)],
            ['status', '!=', 0],
        ])
        ->when($quotation_slack, function ($query, $quotation_slack) {
            return $query->where('slack', '!=', $quotation_slack);
        })->first();
        if(!empty($quotation_number_details)){
            throw new Exception("quotation reference no ".trim($request->quotation_reference)." is already used");
        }

        $quotation_ref_details = QuotationModel::where([
            ['quotation_number', '=', trim($request->quotation_number)],
            ['status', '!=', 0],
        ])->when($quotation_slack, function ($query, $quotation_slack) {
            return $query->where('slack', '!=', $quotation_slack);
        })->first();
        if(!empty($quotation_ref_details)){
            throw new Exception("quotation no ".trim($request->quotation_number)." is already used");
        }

        $currency_data = CountryModel::select('currency_code', 'currency_name')
        ->where('currency_code', '=', trim($request->currency))
        ->active()
        ->first();
        if (empty($currency_data)) {
            throw new Exception("Invalid currency selected", 400);
        }

        $tax_option_data = $this->calculate_tax_component($request->tax_option);

        foreach($products as $product_key => $product_value){

            $product_slack = $product_value['slack'];
            $product_name = $product_value['name'];
            $unit_price = (isset($product_value['unit_price']) && $product_value['unit_price'] != '')?$product_value['unit_price']:0.00;
            $quantity = (isset($product_value['quantity']) && $product_value['quantity'] != '')?$product_value['quantity']:0.00;
            $discount_percentage = (isset($product_value['discount_percentage']) && $product_value['discount_percentage'] != '')?$product_value['discount_percentage']:0.00;
            $tax_percentage = (isset($product_value['tax_percentage']) && $product_value['tax_percentage'] != '')?$product_value['tax_percentage']:0.00;

            $subtotal_amount_excluding_tax = $unit_price*$quantity;

            $discount_amount = $this->calculate_discount($subtotal_amount_excluding_tax, $discount_percentage);

            $total_amount_after_discount = ($subtotal_amount_excluding_tax-$discount_amount);

            $tax_amount = $this->calculate_tax($total_amount_after_discount, $tax_percentage);
            
            $item_total = ($total_amount_after_discount+$tax_amount);

            $tax_components = $tax_option_data['tax_components'];
            if(count($tax_components)>0){
                foreach($tax_components as $key => $tax_component){
                    $tax_component_percentage = ($tax_percentage/count($tax_components));
                    $tax_component_amount = $this->calculate_tax($total_amount_after_discount, $tax_component_percentage);
                    $tax_components[$key]['tax_percentage'] = $tax_component_percentage;
                    $tax_components[$key]['tax_amount'] = number_format((float)$tax_component_amount, 2, '.', '');
                }
            }

            if($product_slack != ''){
                $product_data = ProductModel::select('products.id', 'products.slack', 'products.product_code')
                ->where('products.slack', '=', $product_slack)
                ->categoryJoin()
                ->supplierJoin()
                ->taxcodeJoin()
                ->discountcodeJoin()
                ->categoryActive()
                ->supplierActive()
                ->taxcodeActive()
                ->first();
                if (empty($product_data)) {
                    throw new Exception("Product ".$product_name." is not currently available", 400);
                }
            }
            
            $quotation_products[] = [
                'quotation_id' => 0,
                'product_slack' => ($product_slack != '')?$product_data->slack:NULL,
                'product_id' => ($product_slack != '')?$product_data->id:NULL,
                'product_code' => ($product_slack != '')?$product_data->product_code:NULL,
                'name' => isset($product_name)?$product_name:NULL,
                
                'quantity' => $quantity,
                'amount_excluding_tax' => $unit_price,
                'subtotal_amount_excluding_tax' => $subtotal_amount_excluding_tax,
                'discount_percentage' => $discount_percentage,
                
                'tax_percentage' => $tax_percentage,
                'discount_amount' => $discount_amount,
                'total_after_discount' => $total_amount_after_discount,

                'tax_amount' => $tax_amount,
                'tax_components' => (count($tax_components)>0)?json_encode($tax_components):'',
                'total_amount' => $item_total,
            ];
        }

        $total_amount_excluding_tax_array = data_get($quotation_products, '*.subtotal_amount_excluding_tax', 0);
        $total_amount_excluding_tax = array_sum($total_amount_excluding_tax_array);

        $total_discount_amount_array = data_get($quotation_products, '*.discount_amount', 0);
        $total_discount_amount = array_sum($total_discount_amount_array);

        $total_after_discount_amount_array = data_get($quotation_products, '*.total_after_discount', 0);
        $total_after_discount_amount = array_sum($total_after_discount_amount_array);

        $total_tax_amount_array = data_get($quotation_products, '*.tax_amount', 0);
        $total_tax_amount = array_sum($total_tax_amount_array);

        $shipping_charge = (isset($request->shipping_charge))?$request->shipping_charge:0.00;
        $packing_charge = (isset($request->packing_charge))?$request->packing_charge:0.00;

        $total_order_amount = ($total_after_discount_amount+$total_tax_amount+$shipping_charge+$packing_charge);

        $quotation_data = [
            "store_id" => $request->logged_user_store_id,
            "quotation_reference" => $request->quotation_reference,
            "quotation_date" => $request->quotation_date,
            "quotation_due_date" => $request->quotation_due_date,
            "bill_to" => $request->bill_to,
            "bill_to_id" => $bill_to_id,
            "bill_to_code" => $bill_to_code,
            "bill_to_name" => $bill_to_name,
            "bill_to_email" => $bill_to_email,
            "bill_to_contact" => $bill_to_contact,
            "bill_to_address" => $bill_to_address,
            "currency_name" => $currency_data->currency_name,
            "currency_code" => $currency_data->currency_code,
            "subtotal_excluding_tax" => $total_amount_excluding_tax,
            "total_discount_amount" => $total_discount_amount,
            "total_after_discount" => $total_after_discount_amount,
            "total_tax_amount" => $total_tax_amount,
            "shipping_charge" => $shipping_charge,
            "packing_charge" => $packing_charge,
            "total_order_amount" => $total_order_amount,
            "tax_option_id" => $tax_option_data['tax_option_id'],
            "notes" => $request->notes,
        ];

        return [
            'quotation_data' => $quotation_data,
            'quotation_products' => $quotation_products
        ];
    }

    public function calculate_tax($item_total, $tax_percentage){
        $tax_amount = ($tax_percentage/100)*$item_total;
        return $tax_amount;
    }

    public function calculate_discount($item_total, $discount_percentage){
        $discount_amount = ($discount_percentage/100)*$item_total;
        return $discount_amount;
    }

    public function calculate_tax_component($tax_option_constant){
        
        $response = [];

        $response['tax_option_id'] = '';
        $response['tax_components'] = [];
        
        $tax_components = [];

        if(isset($tax_option_constant) && $tax_option_constant != ''){
           
            $tax_option_data = MasterTaxOptionModel::select('id', 'component_count', 'component_1', 'component_2', 'component_3')
            ->where('tax_option_constant', '=', $tax_option_constant)
            ->active()
            ->first();

            $response['tax_option_id'] = $tax_option_data->id;

            if($tax_option_data->component_count>0){
                for($i=1; $i<=$tax_option_data->component_count; $i++){
                    $component_name = 'component_'.$i;
                    $tax_components[]['name'] = $tax_option_data->$component_name;
                }

                $response['tax_components'] = $tax_components;
            }

        }
        return $response;
    }

    public function filter_quotations(Request $request){
        try{

            $keyword = $request->keyword;

            $quotation_list = QuotationModel::select("*")
            ->where('quotation_number', 'like', $keyword.'%')
            ->orWhere('bill_to_code', 'like', $keyword.'%')
            ->limit(25)
            ->get();
            
            $quotations = quotationResource::collection($quotation_list);
           
            return response()->json($this->generate_response(
                array(
                    "message" => "Quotations filtered successfully", 
                    "data" => $quotations
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

    public function validate_request($request)
    {
        $request->merge(['products' => json_decode($request->products, true)]);

        $validator = Validator::make($request->all(), [
            'bill_to_slack' => $this->get_validation_rules("slack", true),
            'quotation_reference' => 'max:30',
            'quotation_date' => 'date|nullable',
            'quotation_due_date' => 'date|nullable|after_or_equal:quotation_date',
            'notes' => $this->get_validation_rules("text", false),
            'products.*.name' => $this->get_validation_rules("name_label", true),
            'products.*.quantity' => 'min:1|'.$this->get_validation_rules("numeric", true),
            'products.*.unit_price' => $this->get_validation_rules("numeric", true),
            'products.*.discount_percentage' => $this->get_validation_rules("numeric", false),
            'products.*.tax_percentage' => $this->get_validation_rules("numeric", false)
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
