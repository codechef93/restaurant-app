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

use App\Http\Resources\InvoiceResource;

use App\Models\Invoice as InvoiceModel;
use App\Models\InvoiceProduct as InvoiceProductModel;
use App\Models\Supplier as SupplierModel;
use App\Models\Customer as CustomerModel;
use App\Models\Product as ProductModel;
use App\Models\Country as CountryModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\Transaction as TransactionModel;
use App\Models\MasterTransactionType as MasterTransactionTypeModel;
use App\Models\MasterTaxOption as MasterTaxOptionModel;
use App\Models\User as UserModel;

use App\Http\Resources\Collections\InvoiceCollection;

class Invoice extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_INVOICE_LISTING';
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
            
            $query = InvoiceModel::select('invoices.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $invoices = InvoiceResource::collection($query);
           
            $total_count = InvoiceModel::select("id")->get()->count();

            $item_array = [];
            foreach($invoices as $key => $invoice){
                
                $invoice = $invoice->toArray($request);

                $item_array[$key][] = $invoice['invoice_number'];
                $item_array[$key][] = ($invoice['invoice_reference'] != '')?$invoice['invoice_reference']:'-';
                $item_array[$key][] = ($invoice['bill_to'] != '')?$invoice['bill_to']:'-';
                $item_array[$key][] = ($invoice['bill_to_name'] != '')?$invoice['bill_to_name']:'-';
                $item_array[$key][] = ($invoice['invoice_date'] != '')?$invoice['invoice_date']:'-';
                $item_array[$key][] = ($invoice['invoice_due_date'] != '')?$invoice['invoice_due_date']:'-';
                $item_array[$key][] = $invoice['total_order_amount'];
                $item_array[$key][] = (isset($invoice['status']['label']))?view('common.status', ['status_data' => ['label' => $invoice['status']['label'], "color" => $invoice['status']['color']]])->render():'-';
                $item_array[$key][] = $invoice['created_at_label'];
                $item_array[$key][] = $invoice['updated_at_label'];
                $item_array[$key][] = (isset($invoice['created_by']) && isset($invoice['created_by']['fullname']))?$invoice['created_by']['fullname']:'-';
                $item_array[$key][] = view('invoice.layouts.invoice_actions', array('invoice' => $invoice))->render();
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

            if(!check_access(['A_ADD_INVOICE'], true) && !check_access(['A_CREATE_INVOICE_FROM_PO'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            DB::beginTransaction();

            $invoice_data = $this->form_invoice_array($request);
            
            if(!empty($invoice_data['invoice_data'])){
                
                $invoice = $invoice_data['invoice_data'];
                
                $invoice['slack'] = $this->generate_slack("invoices");
                $invoice['invoice_number'] = Str::random(6);
                $invoice['created_at'] = now();
                $invoice['created_by'] = $request->logged_user_id;

                $invoice_id = InvoiceModel::create($invoice)->id;

                $code_start_config = Config::get('constants.unique_code_start.invoice');
                $code_start = (isset($code_start_config))?$code_start_config:100;
                
                $invoice_number = [
                    "invoice_number" => ($code_start+$invoice_id)
                ];
                InvoiceModel::where('id', $invoice_id)
                ->update($invoice_number);
            }
            
            if(!empty($invoice_data['invoice_products'])){
                
                $invoice_products = $invoice_data['invoice_products'];

                array_walk($invoice_products, function (&$item, $key) use ($invoice_id, $request){
                    
                    $item['slack'] = $this->generate_slack("invoice_products");
                    $item['invoice_id'] = $invoice_id; 
                    $item['created_at'] = now();
                    $item['created_by'] = $request->logged_user_id;

                    InvoiceProductModel::insert($item);

                });
            }
            
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Invoice created successfully", 
                    "data"    => $invoice['slack']
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

            if(!check_access(['A_DETAIL_INVOICE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = InvoiceModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new InvoiceResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Invoice loaded successfully", 
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

            if(!check_access(['A_VIEW_INVOICE_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new InvoiceCollection(InvoiceModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Invoices loaded successfully", 
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

            if(!check_access(['A_EDIT_INVOICE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $invoice_details = InvoiceModel::where('slack', $slack)->first();

            DB::beginTransaction();

            $request->invoice_slack = $slack;

            $invoice_data = $this->form_invoice_array($request);
            
            if(!empty($invoice_data['invoice_data'])){
                
                $invoice = $invoice_data['invoice_data'];
            
                $invoice['updated_at'] = now();
                $invoice['updated_by'] = $request->logged_user_id;

                $action_response = InvoiceModel::where('slack', $slack)
                ->update($invoice);
            }
            
            $invoice_id = $invoice_details->id;

            if(!empty($invoice_data['invoice_products'])){
                
                if(count($invoice_data['invoice_products']) > 0){
                    InvoiceProductModel::where('invoice_id', $invoice_id)->delete();
                }

                $invoice_products = $invoice_data['invoice_products'];

                array_walk($invoice_products, function (&$item, $key) use ($invoice_id, $request){

                    $item['slack'] = $this->generate_slack("invoice_products");
                    $item['invoice_id'] = $invoice_id;
                    $item['updated_at'] = now();
                    $item['updated_by'] = $request->logged_user_id;

                    InvoiceProductModel::insert($item);

                });
            }
            
            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Invoice updated successfully", 
                    "data"    => $invoice_details['slack']
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

            if(!check_access(['A_DELETE_INVOICE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $invoice_detail = InvoiceModel::select('id')->where('slack', $slack)->first();
            if (empty($invoice_detail)) {
                throw new Exception("Invalid invoice provided", 400);
            }
            $invoice_id = $invoice_detail->id;

            DB::beginTransaction();

            TransactionModel::where([
                ['bill_to', '=', 'INVOICE'],
                ['bill_to_id', '=', $invoice_id],
            ])->delete();
            InvoiceProductModel::where('invoice_id', $invoice_id)->delete();
            InvoiceModel::where('id', $invoice_id)->delete();

            DB::commit();

            $forward_link = route('invoices');

            return response()->json($this->generate_response(
                array(
                    "message" => "Invoice deleted successfully", 
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

    public function update_invoice_status(Request $request, $slack)
    {
        try {

            if(!check_access(['A_EDIT_STATUS_INVOICE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $status_constant = $request->status;
            
            $invoice_details = InvoiceModel::where('slack', $slack)->first();
            if(empty($invoice_details)){
                throw new Exception("Invalid Invoice selected");
            }

            $invoice_status = MasterStatusModel::select('value')->where([
                ['value_constant', '=', strtoupper($status_constant)],
                ['key', '=', 'INVOICE_STATUS'],
                ['status', '=', '1']
            ])->active()->first();
            if(empty($invoice_status)){
                throw new Exception("Invalid status provided");
            }

            DB::beginTransaction();
            
            $invoice = [];
            $invoice['status'] = $invoice_status->value;
            $invoice['updated_at'] = now();
            $invoice['updated_by'] = $request->logged_user_id;

            $action_response = InvoiceModel::where('slack', $slack)
            ->update($invoice);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Invoice status changed successfully", 
                    "data"    => $invoice_details->slack
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

    public function form_invoice_array($request){
        
        $invoice_slack = $request->invoice_slack;

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

        $invoice_number_details = InvoiceModel::where([
            ['invoice_reference', '=', trim($request->invoice_reference)],
            ['status', '!=', 0],
        ])
        ->when($invoice_slack, function ($query, $invoice_slack) {
            return $query->where('slack', '!=', $invoice_slack);
        })->first();
        if(!empty($invoice_number_details)){
            throw new Exception("Invoice reference no ".trim($request->invoice_reference)." is already used");
        }

        $invoice_ref_details = InvoiceModel::where([
            ['invoice_number', '=', trim($request->invoice_number)],
            ['status', '!=', 0],
        ])->when($invoice_slack, function ($query, $invoice_slack) {
            return $query->where('slack', '!=', $invoice_slack);
        })->first();
        if(!empty($invoice_ref_details)){
            throw new Exception("Invoice no ".trim($request->invoice_number)." is already used");
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
            $tax_type = $product_value['tax_type'];

            if($tax_type == 'INCLUSIVE'){
                $subtotal_amount_including_tax = $unit_price*$quantity;
                $tax_amount = $this->calculate_tax($subtotal_amount_including_tax, $tax_percentage);

                $subtotal_amount_excluding_tax = $subtotal_amount_including_tax-$tax_amount;

                $discount_amount = $this->calculate_discount($subtotal_amount_excluding_tax, $discount_percentage);

                $total_amount_after_discount = ($subtotal_amount_excluding_tax-$discount_amount);
                
                $item_total = $total_amount_after_discount;
            }else{

                $subtotal_amount_excluding_tax = $unit_price*$quantity;

                $discount_amount = $this->calculate_discount($subtotal_amount_excluding_tax, $discount_percentage);

                $total_amount_after_discount = ($subtotal_amount_excluding_tax-$discount_amount);

                $tax_amount = $this->calculate_tax($total_amount_after_discount, $tax_percentage);
                
                $item_total = ($total_amount_after_discount+$tax_amount);
            }

            $tax_components = $tax_option_data['tax_components'];
            if(count($tax_components)>0){
                foreach($tax_components as $key => $tax_component){
                    $tax_eligible_amount = ($tax_type == 'INCLUSIVE')?$subtotal_amount_including_tax:$total_amount_after_discount;
                    $tax_component_percentage = ($tax_percentage/count($tax_components));
                    $tax_component_amount = $this->calculate_tax($tax_eligible_amount, $tax_component_percentage);
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
            
            $invoice_products[] = [
                'invoice_id' => 0,
                'product_slack' => ($product_slack != '')?$product_data->slack:NULL,
                'product_id' => ($product_slack != '')?$product_data->id:NULL,
                'product_code' => ($product_slack != '')?$product_data->product_code:NULL,
                'name' => isset($product_name)?$product_name:NULL,
                
                'quantity' => $quantity,
                'amount_excluding_tax' => $unit_price,
                'subtotal_amount_excluding_tax' => $subtotal_amount_excluding_tax,
                'discount_percentage' => $discount_percentage,
                
                'tax_type' => $tax_type,
                'tax_percentage' => $tax_percentage,
                'discount_amount' => $discount_amount,
                'total_after_discount' => $total_amount_after_discount,

                'tax_amount' => $tax_amount,
                'tax_components' => (count($tax_components)>0)?json_encode($tax_components):'',
                'total_amount' => $item_total,
            ];
        }

        $total_amount_excluding_tax_array = data_get($invoice_products, '*.subtotal_amount_excluding_tax', 0);
        $total_amount_excluding_tax = array_sum($total_amount_excluding_tax_array);

        $total_discount_amount_array = data_get($invoice_products, '*.discount_amount', 0);
        $total_discount_amount = array_sum($total_discount_amount_array);

        $total_after_discount_amount_array = data_get($invoice_products, '*.total_after_discount', 0);
        $total_after_discount_amount = array_sum($total_after_discount_amount_array);

        $total_tax_amount_array = data_get($invoice_products, '*.tax_amount', 0);
        $total_tax_amount = array_sum($total_tax_amount_array);

        $shipping_charge = (isset($request->shipping_charge))?$request->shipping_charge:0.00;
        $packing_charge = (isset($request->packing_charge))?$request->packing_charge:0.00;

        $total_order_amount = ($total_after_discount_amount+$total_tax_amount+$shipping_charge+$packing_charge);

        $invoice_data = [
            "store_id" => $request->logged_user_store_id,
            "invoice_reference" => $request->invoice_reference,
            "invoice_date" => $request->invoice_date,
            "invoice_due_date" => $request->invoice_due_date,
            "parent_po_id" => (isset($request->parent_po_id) && $request->parent_po_id != '')?$request->parent_po_id:NULL,
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
            "terms" => $request->terms,
        ];

        return [
            'invoice_data' => $invoice_data,
            'invoice_products' => $invoice_products
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
            }else if($type == "STAFF"){
                $list_data = UserModel::select('slack', DB::raw("CONCAT(COALESCE(fullname, ''), ',', COALESCE(email, ''), ',', COALESCE(phone, '')) as label"))
                ->where('fullname', 'like', $keywords.'%')
                ->orWhere('email', 'like', $keywords.'%')
                ->orWhere('phone', 'like', $keywords.'%')
                ->active()
                ->hideCurrentLoggedUser($request->logged_user_id)
                ->hideSuperAdminRole()
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

    public function filter_invoices(Request $request){
        try{

            $keyword = $request->keyword;

            $invoice_list = InvoiceModel::select("*")
            ->where('invoice_number', 'like', $keyword.'%')
            ->orWhere('invoice_reference', 'like', $keyword.'%')
            ->orWhere('bill_to_code', 'like', $keyword.'%')
            ->limit(25)
            ->get();
            
            $invoices = InvoiceResource::collection($invoice_list);
           
            return response()->json($this->generate_response(
                array(
                    "message" => "Invoices filtered successfully", 
                    "data" => $invoices
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

    public function get_invoice_pending_payment_data($slack)
    {
        try{
            
            if(!check_access(['A_DETAIL_INVOICE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $invoice = InvoiceModel::where('slack', '=', $slack)->first();

            $transaction_type_data = MasterTransactionTypeModel::select('id')
            ->where('transaction_type_constant', '=', trim('INCOME'))
            ->first();

            $total_amount = $invoice->total_order_amount;

            $paid_amount = TransactionModel::select('id')->where([
                ['bill_to', '=', 'INVOICE'],
                ['bill_to_id', '=', $invoice->id],
                //['transaction_type', '=', $transaction_type_data->id],
            ])->sum('amount');

            $pending_amount = $total_amount-$paid_amount;

            $response = [
                'total_amount' => $total_amount,
                'paid_amount' => $paid_amount,
                'pending_amount' => round($pending_amount, 2),
            ];

            return response()->json($this->generate_response(
                array(
                    "message" => "Transaction amounts calculated successfully", 
                    "data" => $response,
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
            'invoice_reference' => 'max:30',
            'invoice_date' => 'date|required',
            'invoice_due_date' => 'date|required|after_or_equal:invoice_date',
            'terms' => $this->get_validation_rules("text", false),
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
