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

use App\Http\Resources\StockReturnResource;

use App\Models\StockReturn as StockReturnModel;
use App\Models\StockReturnProduct as StockReturnProductModel;
use App\Models\Supplier as SupplierModel;
use App\Models\Customer as CustomerModel;
use App\Models\Product as ProductModel;
use App\Models\Country as CountryModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\MasterTaxOption as MasterTaxOptionModel;

use App\Http\Resources\Collections\StockReturnCollection;

class StockReturn extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_STOCK_RETURN_LISTING';
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
            
            $query = StockReturnModel::select('stock_returns.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $stock_returns = StockReturnResource::collection($query);
           
            $total_count = StockReturnModel::select("id")->get()->count();

            $item_array = [];
            foreach($stock_returns as $key => $stock_return){
                
                $stock_return = $stock_return->toArray($request);

                $item_array[$key][] = $stock_return['return_number'];
                $item_array[$key][] = ($stock_return['bill_to'] != '')?$stock_return['bill_to']:'-';
                $item_array[$key][] = ($stock_return['bill_to_name'] != '')?$stock_return['bill_to_name']:'-';
                $item_array[$key][] = ($stock_return['return_date'] != '')?$stock_return['return_date']:'-';
                $item_array[$key][] = $stock_return['total_order_amount'];
                $item_array[$key][] = (isset($stock_return['status']['label']))?view('common.status', ['status_data' => ['label' => $stock_return['status']['label'], "color" => $stock_return['status']['color']]])->render():'-';
                $item_array[$key][] = $stock_return['created_at_label'];
                $item_array[$key][] = $stock_return['updated_at_label'];
                $item_array[$key][] = (isset($stock_return['created_by']) && isset($stock_return['created_by']['fullname']))?$stock_return['created_by']['fullname']:'-';
                $item_array[$key][] = view('stock_return.layouts.stock_return_actions', array('stock_return' => $stock_return))->render();
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

            if(!check_access(['A_ADD_STOCK_RETURN'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            DB::beginTransaction();

            $stock_return_data = $this->form_stock_return_array($request);
            
            if(!empty($stock_return_data['stock_return_data'])){
                
                $stock_return = $stock_return_data['stock_return_data'];
                
                $stock_return['slack'] = $this->generate_slack("stock_returns");
                $stock_return['return_number'] = Str::random(6);
                $stock_return['created_at'] = now();
                $stock_return['created_by'] = $request->logged_user_id;

                $stock_return_id = StockReturnModel::create($stock_return)->id;

                $code_start_config = Config::get('constants.unique_code_start.stock_return');
                $code_start = (isset($code_start_config))?$code_start_config:100;
                
                $stock_return_number = [
                    "return_number" => ($code_start+$stock_return_id)
                ];
                StockReturnModel::where('id', $stock_return_id)
                ->update($stock_return_number);
            }
            
            if(!empty($stock_return_data['stock_return_products'])){
                
                $stock_return_products = $stock_return_data['stock_return_products'];

                array_walk($stock_return_products, function (&$item, $key) use ($stock_return_id, $request){
                    
                    $item['slack'] = $this->generate_slack("stock_return_products");
                    $item['stock_return_id'] = $stock_return_id; 
                    $item['created_at'] = now();
                    $item['created_by'] = $request->logged_user_id;

                    StockReturnProductModel::insert($item);

                });

                $this->update_stock_from_stock_return($request, $stock_return['slack'], false);
            }
            
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Stock return created successfully", 
                    "data"    => $stock_return['slack']
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

            if(!check_access(['A_DETAIL_STOCK_TRANSFER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = StockReturnModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new StockReturnResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Stock Return loaded successfully", 
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

            if(!check_access(['A_VIEW_STOCK_RETURN_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new StockReturnCollection(StockReturnModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Stock Return loaded successfully", 
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

            if(!check_access(['A_EDIT_STOCK_RETURN'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $stock_return_details = StockReturnModel::where('slack', $slack)->first();

            DB::beginTransaction();

            $request->stock_return_slack = $slack;

            $stock_return_data = $this->form_stock_return_array($request);

            $this->update_stock_from_stock_return($request, $slack, true);
            
            if(!empty($stock_return_data['stock_return_data'])){
                
                $stock_return = $stock_return_data['stock_return_data'];
            
                $stock_return['updated_at'] = now();
                $stock_return['updated_by'] = $request->logged_user_id;

                $action_response = StockReturnModel::where('slack', $slack)
                ->update($stock_return);
            }
            
            $stock_return_id = $stock_return_details->id;

            if(!empty($stock_return_data['stock_return_products'])){
                
                if(count($stock_return_data['stock_return_products']) > 0){
                    StockReturnProductModel::where('stock_return_id', $stock_return_id)->delete();
                }

                $stock_return_products = $stock_return_data['stock_return_products'];

                array_walk($stock_return_products, function (&$item, $key) use ($stock_return_id, $request){

                    $item['slack'] = $this->generate_slack("stock_return_products");
                    $item['stock_return_id'] = $stock_return_id;
                    $item['updated_at'] = now();
                    $item['updated_by'] = $request->logged_user_id;

                    StockReturnProductModel::insert($item);

                });

                $this->update_stock_from_stock_return($request, $slack, true);
            }
            
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Stock return updated successfully", 
                    "data"    => $stock_return_details['slack']
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
    public function destroy(Request $request, $slack)
    {
        try{

            if(!check_access(['A_DELETE_STOCK_RETURN'], true)){
                throw new Exception("Invalid request", 400);
            }

            $stock_return_detail = StockReturnModel::select('id')->where('slack', $slack)->first();
            if (empty($stock_return_detail)) {
                throw new Exception("Invalid stock return provided", 400);
            }
            $stock_return_id = $stock_return_detail->id;

            DB::beginTransaction();

            $this->update_stock_from_stock_return($request, $slack, true);

            StockReturnProductModel::where('stock_return_id', $stock_return_id)->delete();
            StockReturnModel::where('id', $stock_return_id)->delete();

            DB::commit();

            $forward_link = route('stock_returns');

            return response()->json($this->generate_response(
                array(
                    "message" => "Stock return deleted successfully", 
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

    public function form_stock_return_array($request){
        
        $stock_return_slack = $request->stock_return_slack;

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
            
            $stock_return_products[] = [
                'stock_return_id' => 0,
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

        $total_amount_excluding_tax_array = data_get($stock_return_products, '*.subtotal_amount_excluding_tax', 0);
        $total_amount_excluding_tax = array_sum($total_amount_excluding_tax_array);

        $total_discount_amount_array = data_get($stock_return_products, '*.discount_amount', 0);
        $total_discount_amount = array_sum($total_discount_amount_array);

        $total_after_discount_amount_array = data_get($stock_return_products, '*.total_after_discount', 0);
        $total_after_discount_amount = array_sum($total_after_discount_amount_array);

        $total_tax_amount_array = data_get($stock_return_products, '*.tax_amount', 0);
        $total_tax_amount = array_sum($total_tax_amount_array);

        $shipping_charge = (isset($request->shipping_charge))?$request->shipping_charge:0.00;
        $packing_charge = (isset($request->packing_charge))?$request->packing_charge:0.00;

        $total_order_amount = ($total_after_discount_amount+$total_tax_amount+$shipping_charge+$packing_charge);

        $stock_return_data = [
            "store_id" => $request->logged_user_store_id,
            "return_date" => $request->stock_return_date,
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
            "update_stock" => ($request->update_stock == true)?1:0,
            "notes" => $request->notes,
        ];

        return [
            'stock_return_data' => $stock_return_data,
            'stock_return_products' => $stock_return_products
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

    public function load_bill_to_list(Request $request){
        try{

            $type = $request->type;
            $keywords = $request->keywords;

            if($type == ""){
                return;
            }
            
            if($type == "SUPPLIER"){
                $list_data = SupplierModel::select('slack', DB::raw("CONCAT(COALESCE(supplier_code, ''),' - ',COALESCE(name, '')) as label"))
                ->where('name', 'like', $keywords.'%')
                ->orWhere('supplier_code', 'like', $keywords.'%')
                ->active()
                ->get();
            }else if($type == "CUSTOMER"){
                $list_data = CustomerModel::select('slack', DB::raw("CONCAT(COALESCE(name, ''), ',', COALESCE(email, ''), ',', COALESCE(phone, '')) as label"))
                ->where('name', 'like', $keywords.'%')
                ->orWhere('email', 'like', $keywords.'%')
                ->orWhere('phone', 'like', $keywords.'%')
                ->active()
                ->skipDefaultCustomer()
                ->get();
            }
           
            return response()->json($this->generate_response(
                array(
                    "message" => "List filtered successfully", 
                    "data" => $list_data
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
            'stock_return_date' => 'date|required',
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

    public function update_stock_from_stock_return(Request $request, $slack, $return_update = false){
        
        $stock_return = StockReturnModel::select('*')->where('slack', $slack)->first();
        if (empty($stock_return)) {
            throw new Exception("Invalid stock return provided", 400);
        }

        if($stock_return->update_stock == 0){
            return false;
        }

        $stock_return_data = new StockReturnResource($stock_return);

        $products = $stock_return_data->products;

        if(count($products)>0){
            foreach($products as $product){

                if($product->product_id != '' && $product->quantity > 0){

                    if($return_update == false){
                        if($product->stock_update == 0){
                            $product_data = ProductModel::find($product->product_id);
                            $product_data->increment('quantity', $product->quantity);

                            $item = [];
                            $item['stock_update'] = 1;
                            $item['updated_at'] = now();
                            $item['updated_by'] = $request->logged_user_id;
                            StockReturnProductModel::where('id', $product->id)
                            ->update($item);
                        }
                    }
                    if($return_update == true){

                        if($product->stock_update == 1){
                            $product_data = ProductModel::find($product->product_id);
                            $product_data->decrement('quantity', $product->quantity);

                            $item = [];
                            $item['stock_update'] = 0;
                            $item['updated_at'] = now();
                            $item['updated_by'] = $request->logged_user_id;
                            StockReturnProductModel::where('id', $product->id)
                            ->update($item);
                        }

                        if($product->stock_update == 0){
                            $product_data = ProductModel::find($product->product_id);
                            $product_data->increment('quantity', $product->quantity);

                            $item = [];
                            $item['stock_update'] = 1;
                            $item['updated_at'] = now();
                            $item['updated_by'] = $request->logged_user_id;
                            StockReturnProductModel::where('id', $product->id)
                            ->update($item);
                        }
                    }
                }

            }
        }

    }
}
