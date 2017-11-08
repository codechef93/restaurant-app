<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Carbon\Carbon;

use App\Http\Resources\OrderResource;

use App\Models\Order as OrderModel;
use App\Models\Printer as PrinterModel;
use App\Models\OrderProduct as OrderProductModel;
use App\Models\Product as ProductModel;
use App\Models\Customer as CustomerModel;
use App\Models\Category as CategoryModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\Store as StoreModel;
use App\Models\TaxcodeType as TaxcodeTypeModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Transaction as TransactionModel;
use App\Models\MasterTransactionType as MasterTransactionTypeModel;
use App\Models\Account as AccountModel;
use App\Models\MasterOrderType as MasterOrderTypeModel;
use App\Models\Table as TableModel;
use App\Models\BusinessRegister as BusinessRegisterModel;
use App\Models\User as UserModel;
use App\Models\MasterBillingType as MasterBillingTypeModel;
use App\Models\ProductIngredient as ProductIngredientModel;
use App\Models\MasterStatus;

use App\Http\Resources\Collections\OrderCollection;

use App\Http\Controllers\Order as OrderController;

use App\Http\Controllers\API\Notification as NotificationAPI;
use App\Http\Controllers\API\Otp as OtpAPI;

use App\Events\NewOrderReceived;

use PrintNode;

class Order extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_ORDER_LISTING';
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
            
            if($request->filter_by == "All"){
                $query = OrderModel::select('orders.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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
            }
            else if($request->filter_by == ''){
                $query = OrderModel::select('orders.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
                ->take($limit)
                ->skip($offset)
                ->statusJoin()
                ->createdUser()
                ->inKitchen()

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
            }
            else{                
                $query = OrderModel::select('orders.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
                ->take($limit)
                ->skip($offset)
                ->statusJoin()
                ->createdUser()
                ->filter($request->filter_by)

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
            }
            
            $request->skip_products = true;
            $orders = OrderResource::collection($query);
           
            $total_count = OrderModel::select("id")->get()->count();

            $item_array = [];
            foreach($orders as $key => $order){

                $order = $order->toArray($request);
                // $amount = $order['total_after_discount']."(".$order['total_tax_amount'].")"; 

                $item_array[$key][] = $order['order_number'];
                $item_array[$key][] = $order['table'];
                $item_array[$key][] = (!empty($order['customer_phone']))?$order['customer_name']:'-';
                $item_array[$key][] = (!empty($order['customer_name']))?$order['customer_phone']:'-';
                $item_array[$key][] = $order['total_order_amount'];
                $item_array[$key][] = (isset($order['status']['label']))?view('common.status', ['status_data' => ['label' => $order['status']['label'], "color" => $order['status']['color']]])->render():'-';
                $item_array[$key][] = $order['created_at_label'];
                $item_array[$key][] = $order['updated_at_label'];
                $item_array[$key][] = (isset($order['created_by']) && isset($order['created_by']['fullname']))?$order['created_by']['fullname']:'-';
                $item_array[$key][] = 1;
                $item_array[$key][] = view('order.layouts.order_actions', ['order' => $order])->render();
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function update_category_order(Request $request)
    {
        $category_list = json_decode($request->category_list);

        foreach ($category_list as $key => $value) {
            CategoryModel::where('slack', $value->slack)->update([
                'category_order' => $key
            ]);
        }

        return $request;
    }

    public function store(Request $request)
    {
        try {
            $slack = '';
            $validation_required = ($request->order_status == "CLOSE")?true:false;
            $validator = Validator::make($request->all(), [
                'order_status' => $this->get_validation_rules("order_status", true),
                'payment_method' => $this->get_validation_rules("slack", $validation_required),
                'business_account' => $this->get_validation_rules("slack", $validation_required),
                'contact_number' => $this->get_validation_rules("phone", false),
                'address' => $this->get_validation_rules("text", false),
            ]);
            $validation_status = $validator->fails();
            if($validation_status){
                throw new Exception($validator->errors());
            }

            if(!check_access(['A_ADD_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $cart = json_decode($request->cart);

            DB::beginTransaction();

            if(!empty($cart)){

                $business_register_data = BusinessRegisterModel::select('id', 'slack')
                ->where('user_id', '=', trim($request->logged_user_id))
                ->whereNull('closing_date')
                ->first();

                if (empty($business_register_data) && $request->order_status != "CUSTOMER_ORDER") {
                    throw new Exception("You dont have any register open", 400);
                }

                $order_data = $this->form_order_array($request);
                
                if(!empty($order_data['order_data']) && !empty($order_data['order_products_data'])){
                    if(!empty($order_data['order_data'])){
                        
                        $order = $order_data['order_data'];
                        
                        $order['slack'] = $this->generate_slack("orders");
                        $slack = $order['slack'];
                        $order['store_id'] = $request->logged_user_store_id;
                        $order['order_number'] = uniqid();
                        $order['additional_note'] = $request->additional_note == NULL ? '' : $request->additional_note;
                        $order['register_id'] = (isset($business_register_data->id))?$business_register_data->id:NULL;
                        $order['order_origin'] = ($request->order_status == "CUSTOMER_ORDER")?'DIGITAL_MENU':'POS_WEB';
                        $order['created_at'] = now();
                        $order['created_by'] = $request->logged_user_id;

                        $order_id = OrderModel::create($order)->id;

                        $code_start_config = Config::get('constants.unique_code_start.order');
                        $code_start = (isset($code_start_config))?$code_start_config:100;
                        
                        $order_number = [
                            "order_number" => $code_start+$order_id
                        ];
                        OrderModel::where('id', $order_id)
                        ->update($order_number);
                    }
                    
                    if(!empty($order_data['order_products_data'])){
                        
                        $order_products = $order_data['order_products_data'];

                        $this->update_order_products($request, $order_id, $order_products);
                    }
                }
            }

            DB::commit();

            $forward_link = '';
            $print_node_enabled = false;
            $new_tab = false;
            $print_job_id = '';

            if($request->order_status == "CLOSE"){
                try {

                    $print_type = ($request->print_type == NULL || $request->print_type == '') ? "POS_INVOICE" : $request->print_type;
                    if($request->print_type != NULL || $request->print_type != '')
                        $print_node_enabled = true;       
                    $store_data = StoreModel::select('store_code', 'printnode_enabled', 'printnode_api_key', 'pos_printer_id', 'kot_printer_id', 'other_printer_id')
                    ->where([
                        ['stores.slack', '=', $request->logged_user_store_slack]
                    ])
                    ->active()
                    ->first();
        
                    if($store_data->printnode_enabled == 0){
                        throw new Exception("PrintNode is not enabled", 400);
                    }
        
                    $credentials = new \PrintNode\Credentials\ApiKey($store_data->printnode_api_key);
                    $client = new \PrintNode\Client($credentials);
                    $print_job = new \PrintNode\Entity\PrintJob($client);
        
                    $title = '';
                    $source = $print_type.'|'.$store_data->store_code.'|';
                    $printer_id = '';
                    switch($print_type){
                        case "KOT":
        
                            if(empty($store_data->kot_printer_id)){
                                throw new Exception("KOT printer not configured", 400);
                            }
        
                            $printer = PrinterModel::select('printer_id')->where('id', $store_data->kot_printer_id)->active()->first();
                            if(empty($printer)){
                                throw new Exception("Printer is not available/inactive", 400);
                            }
                            
                            $printer_id = $printer->printer_id;
        
                            $order_temp = OrderModel::select('orders.order_number')->where('orders.slack', $slack)->first();
                            if(empty($order_temp)){
                                throw new Exception("Invalid Order", 400);
                            }
        
                            $title = $order_temp->order_number;
                            $source = $source.$order_temp->order_number;
        
                            $order_controller = new OrderController();
                            $file_link = $order_controller->print_kot($request,  $order['slack'], 'FILE', true);
                        break;
                        case "POS_INVOICE":
        
                            if(empty($store_data->pos_printer_id)){
                                throw new Exception("Invoice printer not configured", 400);
                            }
        
                            $printer = PrinterModel::select('printer_id')->where('id', $store_data->pos_printer_id)->active()->first();
                            if(empty($printer)){
                                throw new Exception("Printer is not available/inactive", 400);
                            }
                            
                            $printer_id = $printer->printer_id;
        
                            $order_temp = OrderModel::select('orders.order_number')->where('orders.slack', $slack)->first();
                            if(empty($order_temp)){
                                throw new Exception("Invalid Order", 400);
                            }
        
                            $title = $order_temp->order_number;
                            $source = $source.$order_temp->order_number;
        
                            $order_controller = new OrderController();
                            $file_link = $order_controller->print_order($request, $order['slack'], 'FILE', true);
                        break;
                    }
                    
                    $print_job->title = $print_type.'_'.$title;
                    $print_job->source = $source;
                    $print_job->printer = $printer_id;
                    $print_job->contentType = 'pdf_base64';
                    $print_job->addPdfFile($file_link);
                    
                    $print_job_id = $client->createPrintJob($print_job);
                    
        
                }catch(Exception $e){
                    return response()->json($this->generate_response(
                        array(
                            "message" => $e->getMessage(),
                            "status_code" => $e->getCode()
                        )
                    ));
                }
                if(strtoupper($order['payment_method']) == "STRIPE"){
                    $forward_link = route('payment_gateway', ['type' => 'stripe', 'slack' => $order['slack']]);
                }else if(strtoupper($order['payment_method']) == "PAYPAL"){
                    $forward_link = route('payment_gateway', ['type' => 'paypal', 'slack' => $order['slack']]);
                }else if(strtoupper($order['payment_method']) == "RAZORPAY"){
                    $forward_link = route('payment_gateway', ['type' => 'razorpay', 'slack' => $order['slack']]);
                }else{
                    // if(!$print_node_enabled)
                    //     $forward_link = route('order_summary', ['slack' => $order['slack']]);
                    // else{
                        $forward_link = route('waiter');
                    // }
                    $this->record_order_payment_transaction($order['slack']);

                    $notification_api = new NotificationAPI();
                    $notification_api->send_sms('POS_SALE_BILL_MESSAGE', $order['slack']);
                }
            }
            else if($request->order_status == "IN_KITCHEN"){                
                $forward_link = route('add_order');
            }
            try{

                if(!empty($order_data['order_data']) && $order['restaurant_mode'] == 1 && in_array($request->order_status, ['CLOSE', 'IN_KITCHEN'])){
                    $order_event_data = [
                        'waiter_slack' => ($request->waiter != NULL)?$request->waiter:'',
                        'order_slack' => $order['slack'],
                        'order_number' => $order_number['order_number'],
                        'order_type' => $order['order_type'],
                        'created_at' => $order['created_at'],
                    ];
                    broadcast(new NewOrderReceived($request->logged_user_store_slack, $order_event_data));
                }

            }catch(Exception $e){
                //skip
            }
            
            return response()->json($this->generate_response(
                array(
                    "message" => "Order created successfully", 
                    "data" => $order['slack'],
                    "link" => $forward_link,
                    "print_job_id"    => $print_job_id,
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

            if(!check_access(['A_DETAIL_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = OrderModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new OrderResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Order loaded successfully", 
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

            if(!check_access(['A_VIEW_ORDER_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $query = OrderModel::select('*');

            if(isset($request->customer_slack)){
                $customer = CustomerModel::select('id')->where('customers.slack', $request->customer_slack)
                ->first();
                $query->where('customer_id', '=', $customer->id);
            }

            $query = $query->orderBy('created_at', 'desc')->paginate();

            $list = new OrderCollection($query);

            return response()->json($this->generate_response(
                array(
                    "message" => "Orders loaded successfully", 
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slack)
    {
        try {

            $validation_required = ($request->order_status == "CLOSE")?true:false;
            $validator = Validator::make($request->all(), [
                'order_status' => $this->get_validation_rules("order_status", true),
                'payment_method' => $this->get_validation_rules("slack", $validation_required),
                'business_account' => $this->get_validation_rules("slack", $validation_required),
                'contact_number' => $this->get_validation_rules("phone", false),
                'address' => $this->get_validation_rules("text", false),
            ]);

            $validation_status = $validator->fails();
            if($validation_status){
                throw new Exception($validator->errors());
            }

            if(!check_access(['A_EDIT_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            
            $order_details = OrderModel::where('slack', $slack)->first();

            $cart = json_decode($request->cart);
            
            DB::beginTransaction();
            
            if(!empty($cart)){

                $business_register_data = BusinessRegisterModel::select('id', 'slack')
                ->where('user_id', '=', trim($request->logged_user_id))
                ->whereNull('closing_date')
                ->first();

                if (empty($business_register_data) && $request->order_status != "CUSTOMER_ORDER") {
                    throw new Exception("You dont have any register open", 400);
                }

                $order_data = $this->form_order_array($request, $slack);
                
                if(!empty($order_data['order_data']) && !empty($order_data['order_products_data'])){
                    if(!empty($order_data['order_data'])){
                        
                        $order = $order_data['order_data'];
                        
                        $order['register_id'] = (isset($business_register_data->id))?$business_register_data->id:NULL;
                        $order['updated_at'] = now();
                        $order['updated_by'] = $request->logged_user_id;

                        $action_response = OrderModel::where('slack', $slack)
                        ->update($order);
                    }
                    
                    $order_id = $order_details->id;
                  
                    if(!empty($order_data['order_products_data'])){
                        
                        //$current_order_products = OrderProductModel::where('order_id', $order_id)->get()->toArray();
                        
                        if(count($order_data['order_products_data']) > 0){
                            OrderProductModel::where('order_id', $order_id)->delete();
                        }

                        $order_products = $order_data['order_products_data'];
                      
                        $this->update_order_products($request, $order_id, $order_products);
                        
                    }
                }
            }

            DB::commit();

            $forward_link = '';
            $orders_link = '';
            $new_tab = false;
            $print_node_enabled = false;
            $print_job_id = '';
            if($request->order_status == "CLOSE"){
                try {

                    $print_type = ($request->print_type == NULL || $request->print_type == '') ? "POS_INVOICE" : $request->print_type;
                    if($request->print_type != NULL || $request->print_type != '')
                        $print_node_enabled = true;       
                    $store_data = StoreModel::select('store_code', 'printnode_enabled', 'printnode_api_key', 'pos_printer_id', 'kot_printer_id', 'other_printer_id')
                    ->where([
                        ['stores.slack', '=', $request->logged_user_store_slack]
                    ])
                    ->active()
                    ->first();
        
                    if($store_data->printnode_enabled == 0){
                        throw new Exception("PrintNode is not enabled", 400);
                    }
        
                    $credentials = new \PrintNode\Credentials\ApiKey($store_data->printnode_api_key);
                    $client = new \PrintNode\Client($credentials);
                    $print_job = new \PrintNode\Entity\PrintJob($client);
        
                    $title = '';
                    $source = $print_type.'|'.$store_data->store_code.'|';
                    $printer_id = '';
                    switch($print_type){
                        case "KOT":
        
                            if(empty($store_data->kot_printer_id)){
                                throw new Exception("KOT printer not configured", 400);
                            }
        
                            $printer = PrinterModel::select('printer_id')->where('id', $store_data->kot_printer_id)->active()->first();
                            if(empty($printer)){
                                throw new Exception("Printer is not available/inactive", 400);
                            }
                            
                            $printer_id = $printer->printer_id;
        
                            $order = OrderModel::select('orders.order_number')->where('orders.slack', $slack)->first();
                            if(empty($order)){
                                throw new Exception("Invalid Order", 400);
                            }
        
                            $title = $order->order_number;
                            $source = $source.$order->order_number;
        
                            $order_controller = new OrderController();
                            $file_link = $order_controller->print_kot($request, $slack, 'FILE', true);
                        break;
                        case "POS_INVOICE":
        
                            if(empty($store_data->pos_printer_id)){
                                throw new Exception("Invoice printer not configured", 400);
                            }
        
                            $printer = PrinterModel::select('printer_id')->where('id', $store_data->pos_printer_id)->active()->first();
                            if(empty($printer)){
                                throw new Exception("Printer is not available/inactive", 400);
                            }
                            
                            $printer_id = $printer->printer_id;
        
                            $order = OrderModel::select('orders.order_number')->where('orders.slack', $slack)->first();
                            if(empty($order)){
                                throw new Exception("Invalid Order", 400);
                            }
        
                            $title = $order->order_number;
                            $source = $source.$order->order_number;
        
                            $order_controller = new OrderController();
                            $file_link = $order_controller->print_order($request, $slack, 'FILE', true);
                        break;
                    }
                    
                    $print_job->title = $print_type.'_'.$title;
                    $print_job->source = $source;
                    $print_job->printer = $printer_id;
                    $print_job->contentType = 'pdf_base64';
                    $print_job->addPdfFile($file_link);
                    
                    $print_job_id = $client->createPrintJob($print_job);
                    
        
                }catch(Exception $e){
                    return response()->json($this->generate_response(
                        array(
                            "message" => $e->getMessage(),
                            "status_code" => $e->getCode()
                        )
                    ));
                }
                if(strtoupper($order['payment_method']) == "STRIPE"){
                    $forward_link = route('payment_gateway', ['type' => 'stripe', 'slack' => $slack]);
                }else if(strtoupper($order['payment_method']) == "PAYPAL"){
                    $forward_link = route('payment_gateway', ['type' => 'paypal', 'slack' => $slack]);
                }else if(strtoupper($order['payment_method']) == "RAZORPAY"){
                    $forward_link = route('payment_gateway', ['type' => 'razorpay', 'slack' => $slack]);
                }else{
                    if(!$print_node_enabled)
                        $forward_link = route('order_summary', ['slack' => $slack]);
                    else{
                        $forward_link = route('waiter');
                    }
                        
                    $this->record_order_payment_transaction($slack);

                    $notification_api = new NotificationAPI();
                    $notification_api->send_sms('POS_SALE_BILL_MESSAGE', $slack);
                }
                $orders_link = route('waiter');
            }
            return response()->json($this->generate_response(
                array(
                    "message" => "Order updated successfully", 
                    "data"    => $slack,
                    "print_job_id"    => $print_job_id,
                    "link"    => $forward_link,
                    "new_tab" => $new_tab,
                    "orders_link" => $orders_link
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

            if(!check_access(['A_DELETE_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $order_detail = OrderModel::select('id', 'order_merged')->where('slack', $slack)->first();
            if (empty($order_detail)) {
                throw new Exception("Invalid order provided", 400);
            }
            $order_id = $order_detail->id;
            $order_products = OrderProductModel::where('order_id', $order_id)->get()->toArray();

            DB::beginTransaction();

            array_walk($order_products, function (&$item, $key) use ($request, $order_detail){

                if($order_detail->order_merged == 0){
                    $product = ProductModel::find($item['product_id']);
                    $product->increment('quantity', $item['quantity']);
                    
                    $this->update_ingredient_quantity($request, $item['product_id'], $item['quantity'], 'increment');
                }
            });

            TransactionModel::where([
                ['bill_to', '=', 'POS_ORDER'],
                ['bill_to_id', '=', $order_id],
            ])->delete();
            OrderProductModel::where('order_id', $order_id)->delete();
            OrderModel::where('id', $order_id)->delete();

            DB::commit();

            $forward_link = route('orders');

            return response()->json($this->generate_response(
                array(
                    "message" => "Order deleted successfully", 
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

    public function form_order_array($request, $slack = ''){

        $cart = json_decode($request->cart);
        if( empty((array) $cart) ){
            throw new Exception("Cart cannot be empty");
        }

        if(!empty($cart)){     

            $validate_response_data = $this->validate_order_close_request($request, $slack);
            
            $store_data = StoreModel::select('tax_code_id', 'discount_code_id', 'tax_codes.tax_code', 'discount_codes.discount_code', 'tax_codes.total_tax_percentage', 'discount_codes.discount_percentage', 'currency_name', 'currency_code', 'digital_menu_send_order_to_kitchen','delivery_tax')
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->where([
                ['stores.id', '=', $request->logged_user_store_id]
            ])
            ->active()
            ->first();
            if (empty($store_data)) {
                throw new Exception("Invalid store selected");
            }

            $payment_status_pending_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_PAYMENT_STATUS', 'PAYMENT_PENDING')->first();
            $payment_status = $payment_status_pending_data->value;
            switch($request->order_status){
                case 'HOLD':
                    $status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'HOLD')->first();
                    $order_status = $status_data->value;
                break;
                case 'CLOSE':

                    $status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'CLOSED')->first();
                    $order_status = $status_data->value;

                    $payment_status_success_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_PAYMENT_STATUS', 'PAYMENT_SUCCESS')->first();
                    $payment_status = $payment_status_success_data->value;

                    $order_status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'PAYMENT_ATTEMPTED')->first();
                    switch(strtoupper($validate_response_data['payment_method']->payment_constant)){
                        case "STRIPE":
                        case "PAYPAL":
                        case "RAZORPAY":
                            $order_status = $order_status_data->value;
                            $payment_status = $payment_status_pending_data->value;
                        break;
                    }
                break;
                case 'IN_KITCHEN':
                    $status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'IN_KITCHEN')->first();
                    $order_status = $status_data->value;
                break;
                case 'CUSTOMER_ORDER':
                    if($store_data->digital_menu_send_order_to_kitchen == 0){
                        $status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'CUSTOMER_ORDER')->first();
                        $order_status = $status_data->value;
                    }else{
                        $status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'IN_KITCHEN')->first();
                        $order_status = $status_data->value;
                    }
                break;
            }

            $restaurant_mode = (isset($request->restaurant_mode))?($request->restaurant_mode == 1)?1:0:0;

            if($request->restaurant_order_type == 'DELIVERY'){
                $store_level_total_tax_percentage = (isset($store_data->delivery_tax) && $store_data->delivery_tax != NULL)?$store_data->delivery_tax:0.00;
            }
            else{
                $store_level_total_tax_percentage = isset($store_data->total_tax_percentage)?$store_data->total_tax_percentage:0.00;
            }
            
            $store_level_total_discount_percentage = isset($store_data->discount_percentage)?$store_data->discount_percentage:0.00;

            $additional_discount_percentage = isset($request->additional_discount_percentage)?$request->additional_discount_percentage:0.00;
            
            $cart_key = 1;
            foreach($cart as $cart_item_key => $cart_item){

                $product_data = ProductModel::select('products.*','tax_codes.id as tax_code_id', 'discount_codes.id as discount_code_id', 'tax_codes.tax_code', 'discount_codes.discount_code','tax_codes.total_tax_percentage as tax_percentage', 'tax_codes.tax_type as tax_type', 'discount_codes.discount_percentage as discount_percentage')
                ->where('products.slack', '=', $cart_item->product_slack)
                ->categoryJoin()
                ->supplierJoin()
                ->taxcodeJoin()
                ->discountcodeJoin()
                ->categoryActive()
                ->supplierActive()
                //->taxcodeActive()
                ->quantityCheck($cart_item->quantity)
                ->first();
                if (empty($product_data)) {
                    throw new Exception("Product code: ".$cart_item->product_code." is out of stock", 400);
                }

                $prices = $this->calculate_prices($cart_item->quantity, $product_data);
                $sub_total_purchase_price_excluding_tax = $prices['sub_total_purchase_price_excluding_tax'];
                $total_amount = $prices['total_amount'];
                $discount_amount = $prices['discount_amount'];
                $total_amount_after_discount = $prices['total_amount_after_discount'];
                $tax_amount = $prices['tax_amount'];
                $item_total = $prices['item_total'];
                $product_tax_component_data = $prices['product_tax_component_data'];

                $is_ready_to_serve = 0;
                if($restaurant_mode == 1 && $slack != ''){
                    
                    $order_data = OrderModel::select('orders.id')->where('orders.slack', $slack)
                    ->first();

                    $kitchen_order_product_item = OrderProductModel::select('quantity', 'is_ready_to_serve')->where([
                        ['order_products.order_id', '=', $order_data->id],
                        ['order_products.product_slack', '=', $cart_item->product_slack]
                    ])
                    ->first();
                    if(!empty($kitchen_order_product_item)){
                        $is_ready_to_serve = ($cart_item->quantity>$kitchen_order_product_item->quantity)?0:$kitchen_order_product_item->is_ready_to_serve;
                    }
                }

                $order_products[$cart_key] = [
                    'order_id' => 0,
                    'product_slack' => $product_data->slack,
                    'product_id' => $product_data->id,
                    'product_code' => $product_data->product_code,
                    'name' => $product_data->name,
                    
                    'quantity' => $cart_item->quantity,
                    'purchase_amount_excluding_tax' => $product_data->purchase_amount_excluding_tax,
                    'sale_amount_excluding_tax' => $product_data->sale_amount_excluding_tax,
                    'sale_amount_including_tax' => $product_data->sale_amount_including_tax,
                    
                    'discount_code_id' => isset($product_data->discount_code_id)?$product_data->discount_code_id:NULL,
                    'discount_code' => isset($product_data->discount_code)?$product_data->discount_code:NULL,
                    'discount_percentage' => isset($product_data->discount_percentage)?$product_data->discount_percentage:0,

                    'tax_code_id' => isset($product_data->tax_code_id)?$product_data->tax_code_id:NULL,
                    'tax_code' => isset($product_data->tax_code)?$product_data->tax_code:NULL,
                    'tax_percentage' => $product_data->tax_percentage,
                    'tax_components' => ($product_data->tax_percentage>0)?$product_tax_component_data:NULL,

                    'sub_total_purchase_price_excluding_tax' => $sub_total_purchase_price_excluding_tax,
                    'sub_total_sale_price_excluding_tax' => $total_amount,
                    'discount_amount' => $discount_amount,
                    'total_after_discount' => $total_amount_after_discount,
                    'tax_amount' => $tax_amount,
                    'total_amount' => $item_total,

                    'is_ready_to_serve' => (isset($is_ready_to_serve))?$is_ready_to_serve:0,
                    'parent_id' => 0
                ]; 

                if(isset($cart_item->selected_addon_products) && !empty($cart_item->selected_addon_products)){
                    $addon_key = 1;
                    foreach($cart_item->selected_addon_products as $cart_addon_item_key => $cart_addon_item){

                        $product_data = ProductModel::select('products.*','tax_codes.id as tax_code_id', 'discount_codes.id as discount_code_id', 'tax_codes.tax_code', 'discount_codes.discount_code','tax_codes.total_tax_percentage as tax_percentage', 'tax_codes.tax_type as tax_type', 'discount_codes.discount_percentage as discount_percentage')
                        ->where('products.slack', '=', $cart_addon_item->product_slack)
                        ->categoryJoin()
                        ->supplierJoin()
                        ->taxcodeJoin()
                        ->discountcodeJoin()
                        ->categoryActive()
                        ->supplierActive()
                        //->taxcodeActive()
                        ->quantityCheck($cart_addon_item->quantity)
                        ->first();
                        if (empty($product_data)) {
                            throw new Exception("Product code: ".$cart_addon_item->product_code." is out of stock", 400);
                        }

                        $prices = $this->calculate_prices($cart_addon_item->quantity, $product_data);

                        $sub_total_purchase_price_excluding_tax = $prices['sub_total_purchase_price_excluding_tax'];
                        $total_amount = $prices['total_amount'];
                        $discount_amount = $prices['discount_amount'];
                        $total_amount_after_discount = $prices['total_amount_after_discount'];
                        $tax_amount = $prices['tax_amount'];
                        $item_total = $prices['item_total'];
                        $product_tax_component_data = $prices['product_tax_component_data'];
                        $tax_type = $prices['tax_type'];
                        
                        $is_ready_to_serve = 0;
                        if($restaurant_mode == 1 && $slack != ''){
                            
                            $order_data = OrderModel::select('orders.id')->where('orders.slack', $slack)
                            ->first();
                        
                            $kitchen_order_product_item = OrderProductModel::select('quantity', 'is_ready_to_serve')->where([
                                ['order_products.order_id', '=', $order_data->id],
                                ['order_products.product_slack', '=', $cart_addon_item->product_slack]
                            ])
                            ->first();
                            if(!empty($kitchen_order_product_item)){
                                $is_ready_to_serve = ($cart_addon_item->quantity>$kitchen_order_product_item->quantity)?0:$kitchen_order_product_item->is_ready_to_serve;
                            }
                        }
                        
                        $order_products['addon_'.$cart_key.'_'.$addon_key] = [
                            'order_id' => 0,
                            'product_slack' => $product_data->slack,
                            'product_id' => $product_data->id,
                            'product_code' => $product_data->product_code,
                            'name' => $product_data->name,
                            
                            'quantity' => $cart_addon_item->quantity,
                            'purchase_amount_excluding_tax' => $product_data->purchase_amount_excluding_tax,
                            'sale_amount_excluding_tax' => $product_data->sale_amount_excluding_tax,
                            'sale_amount_including_tax' => $product_data->sale_amount_including_tax,
                            
                            'discount_code_id' => isset($product_data->discount_code_id)?$product_data->discount_code_id:NULL,
                            'discount_code' => isset($product_data->discount_code)?$product_data->discount_code:NULL,
                            'discount_percentage' => isset($product_data->discount_percentage)?$product_data->discount_percentage:0,
                        
                            'tax_code_id' => isset($product_data->tax_code_id)?$product_data->tax_code_id:NULL,
                            'tax_code' => isset($product_data->tax_code)?$product_data->tax_code:NULL,
                            'tax_percentage' => $product_data->tax_percentage,
                            'tax_components' => ($product_data->tax_percentage>0)?$product_tax_component_data:NULL,
                        
                            'sub_total_purchase_price_excluding_tax' => $sub_total_purchase_price_excluding_tax,
                            'sub_total_sale_price_excluding_tax' => $total_amount,
                            'discount_amount' => $discount_amount,
                            'total_after_discount' => $total_amount_after_discount,
                            'tax_amount' => $tax_amount,
                            'total_amount' => $item_total,
                        
                            'is_ready_to_serve' => (isset($is_ready_to_serve))?$is_ready_to_serve:0,
                            'parent_id' => $cart_key
                        ]; 

                        $addon_key += 1;
                    }
                }

                $cart_key += 1;
            }
            
            $total_purchase_amount_excluding_tax_array = data_get($order_products, '*.sub_total_purchase_price_excluding_tax', 0);
            $total_purchase_amount_excluding_tax = array_sum($total_purchase_amount_excluding_tax_array);

            $total_sale_amount_excluding_tax_array = data_get($order_products, '*.sub_total_sale_price_excluding_tax', 0);
            $total_sale_amount_excluding_tax = array_sum($total_sale_amount_excluding_tax_array);

            $store_level_total_discount_amount = $this->calculate_discount($total_sale_amount_excluding_tax, $store_level_total_discount_percentage);

            $total_discount_amount_array = data_get($order_products, '*.discount_amount', 0);
            $total_discount_amount = array_sum($total_discount_amount_array);
            $product_level_total_discount_amount = $total_discount_amount;
            $total_discount_amount = $total_discount_amount+$store_level_total_discount_amount;

            $total_discount_before_additional_discount = $total_discount_amount;
            
            $total_amount_before_additional_discount = ($total_sale_amount_excluding_tax-$total_discount_amount);

            $additional_discount_amount = $this->calculate_discount($total_amount_before_additional_discount, $additional_discount_percentage);

            $total_discount_amount = ($total_discount_amount+$additional_discount_amount);

            $total_amount_after_discount = ($total_amount_before_additional_discount-$additional_discount_amount);

            $store_level_total_tax_amount = $this->calculate_tax($total_amount_after_discount, $store_level_total_tax_percentage);

            $total_tax_amount_array = data_get($order_products, '*.tax_amount', 0);
            $total_tax_amount = array_sum($total_tax_amount_array);
            $product_level_total_tax_amount = $total_tax_amount;
            $total_tax_amount = $total_tax_amount+$store_level_total_tax_amount;

            if(isset($store_data->tax_code_id)){
                $store_tax_component_data = TaxcodeTypeModel::select('tax_type', 'tax_percentage')->where("tax_code_id", $store_data->tax_code_id)->get()->toArray();
                foreach($store_tax_component_data as $key => $store_tax_component_data_item){
                    $tax_component_amount = $this->calculate_tax($total_amount_after_discount, $store_tax_component_data_item['tax_percentage']);
                    $store_tax_component_data[$key]['tax_amount'] = $tax_component_amount;
                }
                $store_tax_component_data = json_encode($store_tax_component_data);
            }
            
            //$total_order_amount_array = data_get($order_products, '*.total_amount', 0);
            //$total_order_amount = array_sum($total_order_amount_array);
            $total_order_amount = ($total_amount_after_discount+$total_tax_amount);

            $customer_data = json_decode($request->customer, true);
            $customer = [
                'customer_slack'    => (isset($customer_data['slack']))?$customer_data['slack']:'',
                'customer_number'   => (isset($customer_data['phone']))?$customer_data['phone']:'',
                'customer_email'    => (isset($customer_data['email']))?$customer_data['email']:'',
                'customer_name'    => (isset($customer_data['name']))?$customer_data['name']:'',
            ];
            $customer = $this->handle_customer($customer);

            $order = [
                "customer_id" => $customer['customer_id'],
                "customer_name" => $customer['name'],
                "customer_phone" => $customer['phone'],
                "customer_email" => $customer['email'],
                'additional_note' => isset($request->additional_note)?$request->additional_note:'',

                "contact_number" => isset($request->contact_number)?$request->contact_number:NULL,
                "address" => isset($request->address)?$request->address:NULL,

                "store_level_discount_code_id" => isset($store_data->discount_code_id)?$store_data->discount_code_id:NULL,
                "store_level_discount_code" => isset($store_data->discount_code)?$store_data->discount_code:NULL,
                "store_level_total_discount_percentage" => $store_level_total_discount_percentage,
                "store_level_total_discount_amount" => $store_level_total_discount_amount,
                "product_level_total_discount_amount" => $product_level_total_discount_amount,

                "store_level_tax_code_id" => isset($store_data->tax_code_id)?$store_data->tax_code_id:NULL,
                "store_level_tax_code" => isset($store_data->tax_code)?$store_data->tax_code:NULL,
                "store_level_total_tax_percentage" => $store_level_total_tax_percentage,
                "store_level_total_tax_amount" => $store_level_total_tax_amount,
                'store_level_total_tax_components' => ($store_level_total_tax_percentage>0 && isset($store_data->tax_code_id))?$store_tax_component_data:NULL,
                "product_level_total_tax_amount" => $product_level_total_tax_amount,

                "purchase_amount_subtotal_excluding_tax" => $total_purchase_amount_excluding_tax,
                "sale_amount_subtotal_excluding_tax" => $total_sale_amount_excluding_tax,

                "total_discount_before_additional_discount" => $total_discount_before_additional_discount,
                "total_amount_before_additional_discount" => $total_amount_before_additional_discount,
                
                "additional_discount_percentage" => $additional_discount_percentage,
                "additional_discount_amount" => $additional_discount_amount,

                "total_discount_amount" => $total_discount_amount,
                "total_after_discount" => $total_amount_after_discount,
                "total_tax_amount" => $total_tax_amount,
                "total_order_amount" => $total_order_amount,
                "total_order_amount_rounded" => round($total_order_amount),

                'payment_method_id' => (isset($validate_response_data['payment_method']))?$validate_response_data['payment_method']->id:'',
                'payment_method_slack' => (isset($validate_response_data['payment_method']))?$validate_response_data['payment_method']->slack:'',
                'payment_method' => (isset($validate_response_data['payment_method']))?$validate_response_data['payment_method']->label:'',

                "currency_name" => $store_data->currency_name,
                "currency_code" => $store_data->currency_code,

                'business_account_id' => (isset($validate_response_data['business_account']))?$validate_response_data['business_account']->id:'',
                
                'restaurant_mode' => $restaurant_mode,

                'order_type_id' => (isset($validate_response_data['order_type']))?$validate_response_data['order_type']->id:'',
                'order_type' => (isset($validate_response_data['order_type']))?$validate_response_data['order_type']->label:'',

                'table_id' => (isset($validate_response_data['restaurant_table']))?$validate_response_data['restaurant_table']->id:'',
                'table_number' => (isset($validate_response_data['restaurant_table']))?$validate_response_data['restaurant_table']->table_number:'',
                'waiter_id' => (isset($validate_response_data['waiter']))?$validate_response_data['waiter']->id:'',

                'bill_type_id' => (isset($validate_response_data['billing_type']))?$validate_response_data['billing_type']->id:'',
                'bill_type' => (isset($validate_response_data['billing_type']))?$validate_response_data['billing_type']->label:'',
                
                'status' => $order_status,
                'payment_status' => $payment_status
            ];

            if($restaurant_mode == 1 && isset($slack)){
                $order_products_collection = collect($order_products);

                $ready_to_serve_array = $order_products_collection->map(function ($item, $key) {
                    return $item['is_ready_to_serve'];
                })->toArray();
                $not_ready_to_serve_exists = in_array(0, $ready_to_serve_array)?true:false;

                if($not_ready_to_serve_exists == true){
                    $kitchen_status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_KITCHEN_STATUS', 'STARTED_PREPARING')->first();
                    if(!empty($kitchen_status_data) && $request->order_status == 'IN_KITCHEN'){
                        $order['kitchen_status'] = $kitchen_status_data->value;
                        $order['kitchen_screen_dismissed'] = 0;
                        $order['waiter_screen_dismissed'] = 0;
                    }else if($request->order_status == 'CLOSE'){
                        $kitchen_status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_KITCHEN_STATUS', 'CONFIRMED')->first();
                        $order['kitchen_status'] = $kitchen_status_data->value;
                    }
                }
            }

        }

        return [
            'order_data' => $order,
            'order_products_data' => $order_products
        ];
    }

    public function calculate_prices($quantity, $product_data){
        $tax_type = ($product_data->tax_type != null)?$product_data->tax_type:'EXCLUSIVE';
        
        $inclusive_total_price = 0;
        if($tax_type == 'INCLUSIVE'){
            $sub_total_purchase_price_excluding_tax = $quantity*$product_data->purchase_amount_excluding_tax;
            
            $inclusive_total_price = $quantity*$product_data->sale_amount_including_tax;
            $inclusive_total_tax = $this->calculate_tax($inclusive_total_price, $product_data->tax_percentage);
            
            $total_amount = $inclusive_total_price-$inclusive_total_tax;
            
            $discount_amount = $this->calculate_discount($total_amount, $product_data->discount_percentage);
    
            $total_amount_after_discount = ($total_amount-$discount_amount);
    
            $tax_amount = $inclusive_total_tax;

            $item_total = ($total_amount_after_discount+$tax_amount);
        }else{
            $sub_total_purchase_price_excluding_tax = $quantity*$product_data->purchase_amount_excluding_tax;

            $total_amount = $quantity*$product_data->sale_amount_excluding_tax;
            
            $discount_amount = $this->calculate_discount($total_amount, $product_data->discount_percentage);
    
            $total_amount_after_discount = ($total_amount-$discount_amount);
    
            $tax_amount = $this->calculate_tax($total_amount_after_discount, $product_data->tax_percentage);

            $item_total = ($total_amount_after_discount+$tax_amount);
        }
        
        $product_tax_component_data = '';
        if(isset($product_data->tax_code_id)){
            $tax_calculation_amount = ($tax_type == 'INCLUSIVE')?$inclusive_total_price:$total_amount_after_discount;
            $product_tax_component_data = TaxcodeTypeModel::select('tax_type', 'tax_percentage')->where("tax_code_id", $product_data->tax_code_id)->get()->toArray();
            foreach($product_tax_component_data as $key => $product_tax_component_data_item){
                $tax_component_amount = $this->calculate_tax($tax_calculation_amount, $product_tax_component_data_item['tax_percentage']);
                $product_tax_component_data[$key]['tax_amount'] = $tax_component_amount;
            }
            $product_tax_component_data = json_encode($product_tax_component_data);
        }

        return [
            "sub_total_purchase_price_excluding_tax" => $sub_total_purchase_price_excluding_tax,
            "total_amount" => $total_amount,
            "discount_amount" => $discount_amount,
            "total_amount_after_discount" => $total_amount_after_discount,
            "tax_amount" => $tax_amount,
            "item_total" => $item_total,
            "product_tax_component_data" => $product_tax_component_data,
            "tax_inclusive_amount" => $inclusive_total_price,
            "tax_type" => $tax_type
        ];
    }

    private function validate_order_close_request($request, $slack = ""){
        $response = [];
        
        if($request->order_status == 'CLOSE'){
            $payment_method = PaymentMethodModel::select('id', 'slack', 'label', 'payment_constant')
            ->where([
                ['payment_methods.slack', '=', $request->payment_method]
            ])
            ->active()
            ->first();
            if (empty($payment_method)) {
                throw new Exception("Invalid Payment method selected");
            }
            $response['payment_method'] = $payment_method;

            $business_account = AccountModel::select('id')
            ->where([
                ['accounts.slack', '=', $request->business_account]
            ])
            ->active()
            ->first();
            if (empty($business_account)) {
                throw new Exception("Invalid Business Account selected");
            }
            $response['business_account'] = $business_account;
        }

        if(in_array($request->order_status, ['HOLD', 'IN_KITCHEN', 'CLOSE', 'CUSTOMER_ORDER']) && $request->restaurant_mode == 1){
            $order_type = MasterOrderTypeModel::select('id', 'label', 'order_type_constant')
            ->active()
            ->where([
                ['restaurant', '=', 1],
                ['order_type_constant', '=', $request->restaurant_order_type],
            ])->first();
            if (empty($order_type)) {
                throw new Exception("Invalid Order Type selected");
            }
            $response['order_type'] = $order_type;


            $restaurant_table = TableModel::select('id', 'table_number', 'waiter_user_id')
            ->active()
            ->where([
                ['slack', '=', $request->restaurant_table],
            ])->first();
            if (empty($restaurant_table) && $request->restaurant_order_type == 'DINEIN' && $request->order_status != "CUSTOMER_ORDER") {
                throw new Exception("Invalid Table selected");
            }
            $response['restaurant_table'] = $restaurant_table;

            if(!empty($restaurant_table)){

                if(isset($restaurant_table->waiter_user_id) && $restaurant_table->waiter_user_id != ''){
                    $waiter = UserModel::select('id')
                    ->active()
                    ->where([
                        ['id', '=', $restaurant_table->waiter_user_id],
                    ])->first();
                    if (!empty($waiter)) {
                        $response['waiter'] = $waiter;
                    }
                }
            }

            if(!empty($request->waiter)){
                $waiter = UserModel::select('id')
                ->active()
                ->where([
                    ['slack', '=', $request->waiter],
                ])->first();
                if (empty($waiter)) {
                    throw new Exception("Invalid Waiter selected");
                }
                $response['waiter'] = $waiter;
            }
            

            $billing_type = MasterBillingTypeModel::select('id', 'billing_type_constant', 'label')
            ->active()
            ->where([
                ['billing_type_constant', '=', $request->billing_type]
            ])
            ->first();
            if (empty($billing_type)) {
                throw new Exception("Invalid Billing Type selected");
            }
            $response['billing_type'] = $billing_type;
        }

        return $response;
    }

    public function calculate_tax($item_total, $tax_percentage){
        $tax_amount = ($tax_percentage/100)*$item_total;
        return $tax_amount;
    }

    public function calculate_discount($item_total, $discount_percentage){
        $discount_amount = ($discount_percentage/100)*$item_total;
        return $discount_amount;
    }

    public function handle_customer($customer){
        $customer_name = trim($customer['customer_name']);
        $customer_phone = trim($customer['customer_number']);
        $customer_email = trim($customer['customer_email']);
        $customer_slack = trim($customer['customer_slack']);
        
        if($customer_phone != '' || $customer_email != '' || $customer_slack != ''){
            $customer_data = CustomerModel::select('id', 'name', 'email', 'phone')
            ->where(function ($query) use ($customer_email, $customer_phone, $customer_slack) {
                $query->when($customer_email != '', function ($query) use ($customer_email){
                    $query->where('email', '=', $customer_email);
                });
                $query->when($customer_phone != '', function ($query) use ($customer_phone){
                    $query->orWhere('phone', '=', $customer_phone);
                });
                $query->when($customer_slack != '', function ($query) use ($customer_slack){
                    $query->orWhere('slack', '=', $customer_slack);
                });
            })
            ->first();

            if(empty($customer_data)){
                $customer = [
                    'slack'         => $this->generate_slack("customers"),
                    'customer_type' => 'WALKIN',
                    'name'          => (isset($customer_name) && ($customer_name != '' && $customer_name != null))?$customer_name:'',
                    'email'         => (isset($customer_email) && ($customer_email != '' && $customer_email != null))?$customer_email:'',
                    'phone'         => (isset($customer_phone) && ($customer_phone != '' && $customer_phone != null))?$customer_phone:'',
                    'status'        => 1,
                    "created_by"    => request()->logged_user_id
                ];
                $customer_id = CustomerModel::create($customer)->id;
            }else{
                $customer_id = $customer_data->id;
            }

            $customer_data = CustomerModel::select('id', 'name', 'email', 'phone')
            ->where('id', $customer_id)
            ->first();
            
        }else{
            $customer_data = CustomerModel::select('id', 'name', 'email', 'phone')
            ->where('customer_type', '=', 'DEFAULT')
            ->active()
            ->first();
            $customer_id = $customer_data->id;
        }

        $customer_data->name = (isset($customer_name) && ($customer_name != '' && $customer_name != null))?$customer_name:$customer_data->name;

        $customer = [
            'customer_id' => $customer_id,
            'name'        => (isset($customer_data->name))?$customer_data->name:NULL,
            'email'       => (isset($customer_data->email))?$customer_data->email:NULL,
            'phone'       => (isset($customer_data->phone))?$customer_data->phone:NULL,
        ];
        
        return $customer;
    }

    public function filter_orders(Request $request){
        try{

            $keyword = $request->keyword;

            $order_list = OrderModel::select("*")
            ->where('order_number', 'like', $keyword.'%')
            ->orWhere('customer_email', 'like', $keyword.'%')
            ->orWhere('customer_phone', 'like', $keyword.'%')
            ->limit(25)
            ->get();
            
            $request->skip_products = true;
            $orders = OrderResource::collection($order_list);
           
            return response()->json($this->generate_response(
                array(
                    "message" => "Order filtered successfully", 
                    "data" => $orders
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

    public function record_order_payment_transaction($order_slack){
        
        $order_detail = OrderModel::select('*')->where('slack', $order_slack)->first();

        $transaction_type_data = MasterTransactionTypeModel::select('id')
        ->where('transaction_type_constant', '=', 'INCOME')
        ->first();
        if (empty($transaction_type_data)) {
            throw new Exception("Invalid transaction type selected", 400);
        }

        $customer_data = CustomerModel::select('id', 'name', 'email', 'phone', 'address')
        ->where('id', '=', $order_detail->customer_id)
        ->first();

        $transaction = [
            "slack" => $this->generate_slack("transactions"),
            "store_id" => $order_detail->store_id,
            "transaction_code" => Str::random(6),
            "account_id" => $order_detail->business_account_id,
            "transaction_type" => $transaction_type_data->id,
            "payment_method_id" => $order_detail->payment_method_id,
            "payment_method" => $order_detail->payment_method,
            "bill_to" => 'POS_ORDER',
            "bill_to_id" => $order_detail->id,
            "bill_to_name" => (isset($customer_data->name))?$customer_data->name:'Walkin Customer',
            "bill_to_contact" => $order_detail->customer_phone,
            "bill_to_address" => (isset($customer_data->address))?$customer_data->address:'',
            "currency_code" => $order_detail->currency_code,
            "amount" => $order_detail->total_order_amount,
            "pg_transaction_id" => '',
            "pg_transaction_status" => '',
            "notes" => '',
            "transaction_date" => date('Y-m-d'),
            "created_by" => request()->logged_user_id
        ];
        
        $transaction_id = TransactionModel::create($transaction)->id;

        $code_start_config = Config::get('constants.unique_code_start.transaction');
        $code_start = (isset($code_start_config))?$code_start_config:100;
        
        $transaction_code = [
            "transaction_code" => ($code_start+$transaction_id)
        ];
        TransactionModel::where('id', $transaction_id)
        ->update($transaction_code);
    }

    public function get_hold_list(Request $request){
        try{
            if(!check_access(['MM_ORDERS'], true) || !check_access(['SM_POS_ORDERS'], true)){
                throw new Exception("Invalid request", 400);
            }

            $hold_order_list = OrderModel::select('*')
            ->hold()
            ->orderBy('created_at', 'desc')
            ->whereDate('created_at', '>', Carbon::now()->subDays(2))
            ->get();

            $request->skip_products = true;
            $hold_orders = OrderResource::collection($hold_order_list);

            return response()->json($this->generate_response(
                array(
                    "message" => "Order list loaded successfully", 
                    "data" => $hold_orders,
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

    public function get_in_kitchen_order_list(Request $request){
        try{
            
            if(!check_access(['MM_RESTAURANT'], true) || !check_access(['SM_RESTAURANT_KITCHEN'], true)){
                throw new Exception("Invalid request", 400);
            }

            if(!check_access(['A_VIEW_KITCHEN_ORDER_LISTING'], true)){
                throw new Exception("You Don't Currently Have Permission to Access Listing. Please Request for Access.", 400);
            }
            $in_kitchen_order_list = OrderModel::select('*')
            //->inkitchen()
            ->inkitchenOrClosed()
            ->kitchenNonDismissed()
            ->orderBy('created_at', 'desc')
            ->whereDate('created_at', '>', Carbon::now()->subDays(5))
            ->get();
            
            $request->skip_products = true;
            $in_kitchen_orders = OrderResource::collection($in_kitchen_order_list);

            return response()->json($this->generate_response(
                array(
                    "message" => "Kitchen order tickets loaded successfully", 
                    "data" => $in_kitchen_orders,
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

    public function get_running_order_list(Request $request){
        try{
            if(!check_access(['MM_ORDERS'], true) || !check_access(['SM_POS_ORDERS'], true)){
                throw new Exception("Invalid request", 400);
            }

            $page = $request->page;
            $request->skip_products = true;
            
            $running_order_list = new OrderCollection(OrderModel::select('*')
            ->inkitchenOrClosed()
            ->kitchenNonDismissed()
            ->whereDate('created_at', '>', Carbon::now()->subDays(5))
            ->orderBy('created_at', 'desc')
            ->paginate(10));

            return response()->json($this->generate_response(
                array(
                    "message" => "Running orders loaded successfully", 
                    "data" => $running_order_list,
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

    public function get_running_order_list_waiter(Request $request){
        try{            
            $running_order_list = new OrderCollection(OrderModel::select('*')
            ->inkitchenOrClosed()
            ->kitchenNonDismissed()
            ->whereDate('created_at', '>', Carbon::now()->subDays(5))
            ->orderBy('created_at', 'desc')
            ->paginate(10000));

            return response()->json($this->generate_response(
                array(
                    "message" => "Running orders loaded successfully", 
                    "data" => $running_order_list,
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

    public function update_kitchen_order_status(Request $request){
        try{
            if(!check_access(['A_CHANGE_KITCHEN_ORDER_STATUS'], true)){
                throw new Exception("Invalid request", 400);
            }

            $validator = Validator::make($request->all(), [
                'order_slack' => $this->get_validation_rules("slack", true),
                'kitchen_status' => $this->get_validation_rules("string", true),
            ]);

            $validation_status = $validator->fails();
            if($validation_status){
                throw new Exception($validator->errors());
            }

            $kitchen_status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_KITCHEN_STATUS', $request->kitchen_status)->first();
            if(empty($kitchen_status_data)){
                throw new Exception("Invalid kitchen status provided", 400);
            }

            DB::beginTransaction();
            
            $order = [];
            $order['kitchen_status'] = $kitchen_status_data->value;
            $order['updated_at'] = now();
            $order['updated_by'] = $request->logged_user_id;

            $action_response = OrderModel::where('slack', $request->order_slack)
            ->update($order);

            $order = OrderModel::select('orders.*')->where('orders.slack', $request->order_slack)
            ->first();

            $order_items = OrderProductModel::select('id', 'is_ready_to_serve')->where('order_products.order_id', $order->id)
            ->get();

            if($request->kitchen_status == 'ORDER_READY'){

                $ready_to_serve_array = $order_items->map(function ($item, $key) use ($request){
                    
                    $order_product = [];
                    $order_product['is_ready_to_serve'] = 1;
                    $order_product['updated_at'] = now();
                    $order_product['updated_by'] = $request->logged_user_id;

                    $action_response = OrderProductModel::where('id', $item->id)
                    ->update($order_product);

                    return $action_response;

                })->toArray();
            }

            $order_data = new OrderResource($order);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Kitchen order status changed successfully", 
                    "data" => ['order_data' => $order_data],
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

    public function get_register_order_amount(Request $request){
        try{
            if(!check_access(['A_ADD_ORDER'], true) || !check_access(['A_EDIT_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $business_register_data = BusinessRegisterModel::select('id', 'slack', 'opening_amount')
            ->where('user_id', '=', trim($request->logged_user_id))
            ->whereNull('closing_date')
            ->first();
            if (empty($business_register_data)) {
                throw new Exception("You dont have any register open", 400);
            }

            $business_register_id = $business_register_data->id;

            $all_registers = [];
            $all_registers[] = $business_register_id;

            $order_value = OrderModel::closed()
            ->whereIn('register_id', $all_registers)
            ->sum('total_order_amount');

            $total_amount = $business_register_data->opening_amount+$order_value;

            return response()->json($this->generate_response(
                array(
                    "message" => "Register order value calculated successfully", 
                    "data" => $total_amount,
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

    public function share_invoice_sms(Request $request, $slack){
        try{
            if(!check_access(['A_SHARE_INVOICE_SMS'], true)){
                throw new Exception("Invalid request", 400);
            }

            $notification_api = new NotificationAPI();
            $response = $notification_api->send_sms('POS_SALE_BILL_MESSAGE', $slack);

            $response_decoded = json_decode(json_encode($response), true);
            if($response_decoded['original']['status_code'] != 200){
                if($response_decoded['original']['status_code'] == 400){
                    throw new Exception($response_decoded['original']['msg'], 400);
                }else{
                    throw new Exception('Twilio response: '.$response_decoded['original']['msg']." (status code: ".$response_decoded['original']['status_code'].")", 400);
                }
            }

            return response()->json($this->generate_response(
                array(
                    "message" => "Invoice has been sent via SMS successfully!", 
                    "data" => $slack,
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

    public function update_ingredient_quantity(Request $request, $product_id, $cart_quantity, $type = 'decrement'){
        
        $restaurant_mode = $request->logged_user_store_restaurant_mode;
        
        if(!empty($product_id) && $restaurant_mode == 1){

            $product_data = ProductModel::withoutGlobalScopes()->select('id', 'product_code', 'name', 'is_ingredient')
            ->where('id', '=', trim($product_id))
            ->first();
            
            if($product_data->is_ingredient == 0){
                $ingredient_list = ProductIngredientModel::select('*')
                ->where('product_id', '=', trim($product_id))
                ->get();

                if(!empty($ingredient_list)){
                    foreach($ingredient_list as $ingredient_list_item){
                        
                        $total_ingredient_quantity = $cart_quantity*$ingredient_list_item->quantity;

                        if($type == "decrement"){
                            $ingredient_data = ProductModel::withoutGlobalScopes()->select('products.id as product_id')
                            ->where('products.id', '=', trim($ingredient_list_item['ingredient_product_id']))
                            ->categoryJoin()
                            ->supplierJoin()
                            ->taxcodeJoin()
                            ->discountcodeJoin()
                            ->categoryActive()
                            ->supplierActive()
                            ->taxcodeActive()
                            ->quantityCheck($total_ingredient_quantity)
                            ->active()
                            ->first();
                            if (empty($ingredient_data)) {
                                throw new Exception("Ingredient for product ".$product_data->product_code." - ".$product_data->name." is currently out of stock", 400);
                            }

                            ProductModel::withoutGlobalScopes()->where('id', $ingredient_data['product_id'])->decrement('quantity', $total_ingredient_quantity);
                        }else{
                            $ingredient_data = ProductModel::withoutGlobalScopes()->select('products.id as product_id')
                            ->where('products.id', '=', trim($ingredient_list_item['ingredient_product_id']))
                            ->categoryJoin()
                            ->supplierJoin()
                            ->taxcodeJoin()
                            ->discountcodeJoin()
                            ->categoryActive()
                            ->supplierActive()
                            ->taxcodeActive()
                            ->active()
                            ->first();

                            if (!empty($ingredient_data)) {
                                ProductModel::withoutGlobalScopes()->where('id', $ingredient_data['product_id'])->increment('quantity', $total_ingredient_quantity);
                            }
                        }
                    }
                }
            }
        }
    }

    public function get_waiter_order_list(Request $request){
        try{
            if(!check_access(['MM_RESTAURANT'], true) || !check_access(['SM_RESTAURANT_WAITER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $waiter_id = $request->logged_user_id;
            $waiter_slack = $request->waiter_slack;

            if(isset($waiter_slack) && $waiter_slack != ''){
                $user_detail = UserModel::select('id')->where('slack', $waiter_slack)->first();
                $waiter_id = $user_detail->id;
            }

            $waiter_order_list = OrderModel::select('*')
            ->inkitchenOrClosed()
            ->waiterNonDismissed()
            ->where('waiter_id', '=', $waiter_id)
            ->orderBy('created_at', 'desc')
            ->whereDate('created_at', '>', Carbon::now()->subDays(2))
            ->get();

            $request->skip_products = true;
            $waiter_orders = OrderResource::collection($waiter_order_list);

            return response()->json($this->generate_response(
                array(
                    "message" => "Waiter orders loaded successfully", 
                    "data" => $waiter_orders,
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

    public function update_kitchen_item_status(Request $request){
        try{
            if(!check_access(['A_CHANGE_KITCHEN_ORDER_STATUS'], true)){
                throw new Exception("Invalid request", 400);
            }

            $validator = Validator::make($request->all(), [
                'item_slack' => $this->get_validation_rules("slack", true)
            ]);

            $validation_status = $validator->fails();
            if($validation_status){
                throw new Exception($validator->errors());
            }

            $item_slack = $request->item_slack;

            DB::beginTransaction();

            $order_item = OrderProductModel::select('order_products.order_id', 'order_products.is_ready_to_serve')->where('order_products.slack', $item_slack)
            ->first();

            $order_data = OrderModel::select('orders.id')->where('orders.id', $order_item->order_id)
            ->first();
            
            $order_product = [];
            $order_product['is_ready_to_serve'] = ($order_item->is_ready_to_serve == 0)?1:0;
            $order_product['updated_at'] = now();
            $order_product['updated_by'] = $request->logged_user_id;

            $action_response = OrderProductModel::where('slack', $item_slack)
            ->update($order_product);

            $order_items = OrderProductModel::select('order_products.is_ready_to_serve')->where('order_products.order_id', $order_data->id)
            ->get();

            $ready_to_serve_array = $order_items->map(function ($item, $key) {
                return $item['is_ready_to_serve'];
            })->toArray();
            $not_ready_to_serve_exists = in_array(0, $ready_to_serve_array)?true:false;
            
            if($not_ready_to_serve_exists == true){
                $kitchen_status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_KITCHEN_STATUS', 'STARTED_PREPARING')->first();
                if(!empty($kitchen_status_data)){
                    $order = [];
                    $order['kitchen_status'] = $kitchen_status_data->value;
                    $order['updated_at'] = now();
                    $order['updated_by'] = $request->logged_user_id;

                    $action_response = OrderModel::where('id', $order_data->id)
                    ->update($order);
                }
            }else{
                $kitchen_status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_KITCHEN_STATUS', 'ORDER_READY')->first();
                if(!empty($kitchen_status_data)){
                    $order = [];
                    $order['kitchen_status'] = $kitchen_status_data->value;
                    $order['updated_at'] = now();
                    $order['updated_by'] = $request->logged_user_id;

                    $action_response = OrderModel::where('id', $order_data->id)
                    ->update($order);
                }
            }

            $order = OrderModel::select('orders.*')->where('orders.id', $order_data->id)
            ->first();

            $order_data = new OrderResource($order);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Kitchen item status changed successfully", 
                    "data" => ['order_data' => $order_data],
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

    public function store_customer_order(Request $request){
        try {
            $store = StoreModel::select('stores.id', 'stores.enable_digital_menu_otp_verification')
            ->where([['slack', '=', $request->store_slack]])
            ->first();

            $user = UserModel::select('users.id')
            ->where([['user_code', '=', 'CUSTOMER_USER']])
            ->active()
            ->first();

            if(!empty($user)){

                if($store->enable_digital_menu_otp_verification == true){
                    $otp_api = new OtpAPI();
                    $request->merge([
                        'event_type' => 'QR_CUSTOMER_ORDER',
                    ]);
                    $otp_response = $otp_api->validate_otp($request);
                    $otp_response = json_decode($otp_response->content(), true);
                }else{
                    $otp_response['status_code'] = 200;
                }

                if($otp_response['status_code'] == 200){
                    
                    $request->merge([
                        'logged_user_store_id' => $store->id,
                        'logged_user_id' => $user->id,
                    ]);
                    
                    $response = $this->store($request);
                    $response = json_decode($response->content(), true);
                    
                    if($response['status_code'] == 200){

                        $order_slack = $response['data'];
                        $item = OrderModel::select('*')
                        ->where('slack', $order_slack)
                        ->first();

                        $item_data = new OrderResource($item);

                        return response()->json($this->generate_response(
                            array(
                                "message" => "Order submitted successfully", 
                                "data" => $item_data,
                            ), 'SUCCESS'
                        ));
                    }else{
                        throw new Exception($response['msg'], 400); 
                    }
                }else{
                    throw new Exception($otp_response['msg'], 400); 
                }
            }

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }
    
    public function get_digital_menu_orders_list(Request $request){
        try{
            if(!check_access(['MM_ORDERS'], true) || !check_access(['SM_POS_ORDERS'], true)){
                throw new Exception("Invalid request", 400);
            }

            $page = $request->page;
            $request->skip_products = true;

            $digital_menu_order_list = new OrderCollection(OrderModel::select('*')
            ->digitalMenuOrders()
            ->whereDate('created_at', '>', Carbon::now()->subDays(1))
            ->orderBy('created_at', 'desc')
            ->paginate(10));

            return response()->json($this->generate_response(
                array(
                    "message" => "Digital menu orders loaded successfully", 
                    "data" => $digital_menu_order_list,
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

    public function get_digital_menu_orders_list_waiter(Request $request){
        try{
            $digital_menu_order_list = new OrderCollection(OrderModel::select('*')
            ->digitalMenuOrders()
            ->whereDate('created_at', '>', Carbon::now()->subDays(1))
            ->orderBy('created_at', 'desc')
            ->paginate(10));

            return response()->json($this->generate_response(
                array(
                    "message" => "Digital menu orders loaded successfully", 
                    "data" => $digital_menu_order_list,
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

    public function get_digital_menu_orders(Request $request){
        try{
            if(!check_access(['MM_ORDERS'], true) || !check_access(['SM_DIGITAL_MENU_ORDERS'], true)){
                throw new Exception("Invalid request", 400);
            }

            if(!check_access(['A_VIEW_DIGITAL_MENU_ORDER_LISTING'], true)){
                throw new Exception("You Don't Currently Have Permission to Access Listing. Please Request for Access.", 400);
            }

            $digital_menu_order_list = OrderModel::select('*')
            ->digitalMenuOrders()
            ->orderBy('created_at', 'desc')
            ->whereDate('created_at', '>', Carbon::now()->subDays(2))
            ->get();

            $request->skip_products = true;

            $digital_menu_orders = OrderResource::collection($digital_menu_order_list);

            return response()->json($this->generate_response(
                array(
                    "message" => "Digital menu orders loaded successfully", 
                    "data" => $digital_menu_orders,
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

    public function send_order_to_kitchen(Request $request){
        try{
            if(!check_access(['MM_ORDERS'], true) || !check_access(['EDIT_DIGITAL_MENU_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $order_slack = $request->order_slack;

            $order_detail = OrderModel::select('id')->where('slack', $order_slack)->first();

            if (empty($order_detail)) {
                throw new Exception("Invalid request");
            }

            $status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'IN_KITCHEN')->first();
            $order_status = $status_data->value;

            DB::beginTransaction();

            $order = [];
            $order['status'] = $order_status;
            $order['updated_at'] = now();
            $order['updated_by'] = $request->logged_user_id;

            $action_response = OrderModel::where('id', $order_detail->id)
            ->update($order);

            DB::commit();

            $order = OrderModel::select('orders.*')->where('orders.id', $order_detail->id)
            ->first();

            $order_data = new OrderResource($order);

            return response()->json($this->generate_response(
                array(
                    "message" => "Order send to kitchen successfully", 
                    "data" => $order_data,
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

    public function update_order_products($request, $order_id, $order_products){
        $addon_parent_array = [];
        $order_master_products = Arr::where($order_products, function ($value, $key){
            return (isset($value['parent_id']) && $value['parent_id'] == 0);
        });

        $order_addon_products = Arr::where($order_products, function ($value, $key){
            return (isset($value['parent_id']) && $value['parent_id'] != 0);
        });

        

        array_walk($order_master_products, function (&$item, $key) use ($order_id, $request, &$addon_parent_array){

            $item['slack'] = $this->generate_slack("order_products");
            $item['order_id'] = $order_id;
            $item['parent_order_product_id'] = NULL;
            $item['created_at'] = now();
            $item['created_by'] = $request->logged_user_id;

            $order_product_id = OrderProductModel::create($item)->id;

            if($request->order_status == 'CLOSE' && $item['product_id'] != '' && $item['quantity'] > 0){
                $product = ProductModel::find($item['product_id']);
                $product->decrement('quantity', $item['quantity']);

                $this->update_ingredient_quantity($request, $item['product_id'], $item['quantity']);
            }

            $addon_parent_array[$key] = $order_product_id;
        });

        array_walk($order_addon_products, function (&$item, $key) use ($order_id, $request, $addon_parent_array){

            $item['slack'] = $this->generate_slack("order_products");
            $item['order_id'] = $order_id;
            $item['parent_order_product_id'] = $addon_parent_array[$item['parent_id']];
            $item['created_at'] = now();
            $item['created_by'] = $request->logged_user_id;

            $order_product_id = OrderProductModel::create($item)->id;

            if($request->order_status == 'CLOSE' && $item['product_id'] != '' && $item['quantity'] > 0){
                $product = ProductModel::find($item['product_id']);
                $product->decrement('quantity', $item['quantity']);

                $this->update_ingredient_quantity($request, $item['product_id'], $item['quantity']);
            }
        });
    }

    public function get_qr_order_history(Request $request){
        try{
            
            $orders = $request->orders;
            $request->skip_products = true;

            $order_slack = explode(',', $orders);

            $qr_order_list = new OrderCollection(OrderModel::withoutGlobalScopes()->select('*')
            ->whereIn('slack', $order_slack)
            ->whereDate('created_at', '>', Carbon::now()->subDays(2))
            ->orderBy('created_at', 'desc')
            ->paginate(25));
            
            return response()->json($this->generate_response(
                array(
                    "message" => "QR customer orders loaded successfully", 
                    "data" => $qr_order_list,
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

    public function update_order_product_inventory(Request $request, $order_slack){
        
        $order_data = OrderModel::withoutGlobalScopes()->select('id', 'store_id')
        ->where('slack', '=', $order_slack)
        ->closed()
        ->first();
        if(!isset($request->logged_user_store_restaurant_mode)){
            
            $store_data = StoreModel::select('restaurant_mode')
            ->where([
                ['stores.id', '=', $order_data->store_id]
            ])
            ->active()
            ->first();
            $request->logged_user_store_restaurant_mode = $store_data->restaurant_mode;
        }
        if(!empty($order_data)){

            $order_products = OrderProductModel::select('parent_order_product_id', 'product_id', 'quantity')
            ->where('order_id', '=', $order_data->id)
            ->active()->get();

            foreach ($order_products as $order_product) {
                if($order_product['product_id'] != '' && $order_product['quantity'] > 0){
                    
                    ProductModel::withoutGlobalScopes()->where('id', $order_product['product_id'])->decrement('quantity', $order_product['quantity']);
                    
                    $this->update_ingredient_quantity($request, $order_product['product_id'], $order_product['quantity']);
                }
            }
        }
    }

    public function toggle_order_dismissed_from_screen_status(Request $request){
        try{

            $validator = Validator::make($request->all(), [
                'order_slack' => $this->get_validation_rules("slack", true),
            ]);

            $validation_status = $validator->fails();
            if($validation_status){
                throw new Exception($validator->errors());
            }

            $screen = $request->screen;

            $order_data = OrderModel::select('orders.*')->where('orders.slack', $request->order_slack)
            ->first();

            DB::beginTransaction();
            
            $order = [];
            switch($screen){
                case 'KITCHEN_SCREEN':
                    $order['kitchen_screen_dismissed'] = ($order_data->kitchen_screen_dismissed == 1)?0:1;
                break;
                case 'WAITER_SCREEN':
                    $order['waiter_screen_dismissed'] = ($order_data->waiter_screen_dismissed == 1)?0:1;
                break;
            }

            $action_response = OrderModel::where('slack', $request->order_slack)
            ->update($order);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Order from screen updated successfully", 
                    "data" => [],
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

    public function get_merge_order_list(Request $request){
        try{

            $keyword = $request->keywords;
            $parent_order_slack = $request->parent_order_slack;

            $merge_order_list = OrderModel::select('*')
            ->notMerged()
            ->notClosed()
            ->where('slack' , '!=', $parent_order_slack)
            ->where('order_number', 'like', $keyword.'%')
            ->orWhere('customer_email', 'like', $keyword.'%')
            ->orWhere('customer_phone', 'like', $keyword.'%')
            ->orderBy('created_at', 'desc')
            ->limit(15)
            ->get();

            $request->skip_products = true;
            $merge_orders = OrderResource::collection($merge_order_list);

            return response()->json($this->generate_response(
                array(
                    "message" => "Merge orders loaded successfully", 
                    "data" => $merge_orders,
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

    public function merge_order(Request $request){
        try{

            if(!check_access(['A_MERGE_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            if($request->parent_order_number) {
                $parent_order_number = $request->parent_order_number;

                $parent_order = OrderModel::select('id', 'slack')
                ->notMerged()
                ->notClosed()
                ->where("order_number", $parent_order_number)
                ->first();

                $parent_order_slack = $parent_order->slack;

                $children = isset($request->children_order_numbers)? $request->children_order_numbers:[];
                if(count($children) == 0){
                    throw new Exception("Please choose the orders to merge");
                }

                $children = OrderModel::whereIn('order_number', $children)->pluck('slack')->toArray();
                
            } else {
                $parent_order_slack = $request->parent_slack;

                $parent_order = OrderModel::select('id')
                ->notMerged()
                ->notClosed()
                ->where("slack", $parent_order_slack)
                ->first();

                $children = isset($request->children)?explode(',', $request->children):[];
                if(count($children) == 0){
                    throw new Exception("Please choose the orders to merge");
                }
            }            

            if(empty($parent_order)){
                throw new Exception("Parent order is invalid");
            }

            $order_id = $parent_order->id;

            $order_id_array = [];
            foreach($children as $child){
                
                $child_order = OrderModel::select('id')
                ->notMerged()
                ->notClosed()
                ->where([
                    ["slack", '=', $child],
                    ["slack", '!=', $parent_order_slack]
                ])
                ->first();

                if(empty($child_order)){
                    
                    $child_order_data = OrderModel::select('order_number')
                    ->where("slack", $child)
                    ->first();

                    throw new Exception("Unable to merge order ".$child_order_data->order_number);
                }

                array_push($order_id_array, $child_order->id);
            }

            $order_products = OrderProductModel::select('*')
            ->whereIn('order_id', $order_id_array)
            ->fetchParent()->active()->orderBy('id', 'asc')->get()->makeVisible(['id', 'order_id'])->toArray();
            
            if(empty($order_products)){
                throw new Exception("Unable to fetch order products");
            }
            
            $order_products_array = [];
            foreach($order_products as $order_product){
                $order_product['slack'] = "";
                $order_product_id = $order_product['id'];
                unset($order_product['id']);
                $order_products_array[$order_product_id] = $order_product;
            }

            $order_products_addons = OrderProductModel::select('*')
            ->whereIn('order_id', $order_id_array)
            ->fetchAddon()->active()->orderBy('id', 'asc')->get()->makeVisible(['id', 'order_id'])->toArray();
            
            if(!empty($order_products_addons)){
                foreach($order_products_addons as $order_product_addon){
                    $order_product_addon['slack'] = "";
                    if(isset($order_products_array[$order_product_addon['parent_order_product_id']])){
                        unset($order_product_addon['id']);
                        $order_products_array[$order_product_addon['parent_order_product_id']]['add_ons'][] = $order_product_addon;
                    }
                }
            }

            DB::beginTransaction();

            array_walk($order_products_array, function (&$item, $key) use ($order_id, $request){

                $item['slack'] = $this->generate_slack("order_products");
                $item['merged_from'] = $item['order_id'];
                $item['order_id'] = $order_id;
                $item['parent_order_product_id'] = NULL;
                $item['created_at'] = now();
                $item['created_by'] = $request->logged_user_id;
                
                $order_product_id = OrderProductModel::create($item)->id;

                if(isset($item['add_ons']) && count($item['add_ons'])>0){
                    array_walk($item['add_ons'], function (&$addon_item, $key) use ($order_id, $request, $order_product_id){
                        
                        $addon_item['slack'] = $this->generate_slack("order_products");
                        $addon_item['merged_from'] = $addon_item['order_id'];
                        $addon_item['order_id'] = $order_id;
                        $addon_item['parent_order_product_id'] = $order_product_id;
                        $addon_item['created_at'] = now();
                        $addon_item['created_by'] = $request->logged_user_id;

                        $order_product_id = OrderProductModel::create($addon_item)->id;
                    
                    });
                }
            });

            $merge_from_old_products = array_column($order_products, 'id');
            OrderProductModel::whereIn('id', $merge_from_old_products)
            ->update(['merged_to' => $order_id]);

            if(!empty($order_products_addons)){
                $merge_from_old_products_addon = array_column($order_products_addons, 'id');
                OrderProductModel::whereIn('id', $merge_from_old_products_addon)
                ->update(['merged_to' => $order_id]);
            }

            $status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'MERGED')->first();
            $order_status = $status_data->value;

            OrderModel::whereIn('id', $order_id_array)
            ->update(['status' => $order_status, 'order_merged' => 1, 'order_merge_parent_id' => $order_id]);

            $this->calculate_and_update_order_amount($request, $order_id);

            $child_order_transactions = TransactionModel::where([
                ['bill_to', '=', 'POS_ORDER'],
            ])
            ->whereIn('bill_to_id', $order_id_array)->get()->makeVisible(['bill_to_id', 'store_id'])->toArray();

            if(!empty($child_order_transactions)){
                array_walk($child_order_transactions, function (&$item, $key) use ($order_id, $request){
                    
                    $bill_to_id = $item['bill_to_id'];
                    $transaction_slack = $item['slack'];

                    $item['slack'] = $this->generate_slack("transactions");
                    $item['transaction_code'] = Str::random(6);
                    $item['merged_from'] = $bill_to_id;
                    $item['bill_to_id'] = $order_id;
                    $item['created_at'] = now();
                    $item['created_by'] = $request->logged_user_id;
                    
                    $order_transaction_id = TransactionModel::create($item)->id;

                    $code_start_config = Config::get('constants.unique_code_start.transaction');
                    $code_start = (isset($code_start_config))?$code_start_config:100;
                    
                    $transaction_code = [
                        "transaction_code" => ($code_start+$order_transaction_id)
                    ];
                    TransactionModel::where('id', $order_transaction_id)
                    ->update($transaction_code);

                    TransactionModel::where('slack', $transaction_slack)
                    ->update(['transaction_merged' => 1, 'merged_to' => $order_id]);
                });
            }

            DB::commit();

            $order = OrderModel::select('orders.*')->where('orders.id', $order_id)
            ->first();

            $order_data = new OrderResource($order);

            return response()->json($this->generate_response(
                array(
                    "message" => "Order(s) merged successfully", 
                    "data" => $order_data,
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

    public function calculate_and_update_order_amount(Request $request, $order_id){
        $order_data = OrderModel::select('store_level_total_discount_percentage', 'additional_discount_percentage', 'store_level_total_tax_percentage', 'store_level_total_tax_components')
        ->where("id", $order_id)
        ->first();

        $order_products = OrderProductModel::select('*')
        ->where('order_id', $order_id)
        ->active()->get()->toArray();

        $total_purchase_amount_excluding_tax_array = data_get($order_products, '*.sub_total_purchase_price_excluding_tax', 0);
        
        $total_purchase_amount_excluding_tax = array_sum($total_purchase_amount_excluding_tax_array);

        $total_sale_amount_excluding_tax_array = data_get($order_products, '*.sub_total_sale_price_excluding_tax', 0);
        $total_sale_amount_excluding_tax = array_sum($total_sale_amount_excluding_tax_array);

        $store_level_total_discount_amount = $this->calculate_discount($total_sale_amount_excluding_tax, $order_data->store_level_total_discount_percentage);

        $total_discount_amount_array = data_get($order_products, '*.discount_amount', 0);
        $total_discount_amount = array_sum($total_discount_amount_array);
        $product_level_total_discount_amount = $total_discount_amount;
        $total_discount_amount = $total_discount_amount+$store_level_total_discount_amount;

        $total_discount_before_additional_discount = $total_discount_amount;

        $total_amount_before_additional_discount = ($total_sale_amount_excluding_tax-$total_discount_amount);

        $additional_discount_amount = $this->calculate_discount($total_amount_before_additional_discount, $order_data->additional_discount_percentage);

        $total_discount_amount = ($total_discount_amount+$additional_discount_amount);

        $total_amount_after_discount = ($total_amount_before_additional_discount-$additional_discount_amount);

        $store_level_total_tax_amount = $this->calculate_tax($total_amount_after_discount, $order_data->store_level_total_tax_percentage);

        $total_tax_amount_array = data_get($order_products, '*.tax_amount', 0);
        $total_tax_amount = array_sum($total_tax_amount_array);
        $product_level_total_tax_amount = $total_tax_amount;
        $total_tax_amount = $total_tax_amount+$store_level_total_tax_amount;
        
        if(isset($order_data->store_level_total_tax_components) && $order_data->store_level_total_tax_components != NULL){
            $store_tax_component_data = json_decode($order_data->store_level_total_tax_components, true);
            foreach($store_tax_component_data as $key => $store_tax_component_data_item){
                $tax_component_amount = $this->calculate_tax($total_amount_after_discount, $store_tax_component_data_item['tax_percentage']);
                $store_tax_component_data[$key]['tax_amount'] = $tax_component_amount;
            }
            $store_tax_component_data = json_encode($store_tax_component_data);
        }

        $total_order_amount = ($total_amount_after_discount+$total_tax_amount);

        $order = [
            "store_level_total_discount_amount" => $store_level_total_discount_amount,
            "product_level_total_discount_amount" => $product_level_total_discount_amount,
            
            "store_level_total_tax_amount" => $store_level_total_tax_amount,
            'store_level_total_tax_components' => ($order_data->store_level_total_tax_percentage>0)?$store_tax_component_data:NULL,
            "product_level_total_tax_amount" => $product_level_total_tax_amount,

            "purchase_amount_subtotal_excluding_tax" => $total_purchase_amount_excluding_tax,
            "sale_amount_subtotal_excluding_tax" => $total_sale_amount_excluding_tax,

            "total_discount_before_additional_discount" => $total_discount_before_additional_discount,
            "total_amount_before_additional_discount" => $total_amount_before_additional_discount,
            
            "additional_discount_amount" => $additional_discount_amount,

            "total_discount_amount" => $total_discount_amount,
            "total_after_discount" => $total_amount_after_discount,
            "total_tax_amount" => $total_tax_amount,
            "total_order_amount" => $total_order_amount,
            "total_order_amount_rounded" => round($total_order_amount),
        ];

        $order['updated_at'] = now();
        $order['updated_by'] = $request->logged_user_id;

        $action_response = OrderModel::where('id', $order_id)
        ->update($order);
    }

    public function unmerge_order(Request $request){
        try{

            if(!check_access(['A_UNMERGE_ORDER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $order_slack = $request->order_slack;

            $order_data = OrderModel::select('id', 'order_merge_parent_id')
            ->merged()
            ->where("slack", $order_slack)
            ->first();

            if(empty($order_data)){
                throw new Exception("Invalid Order, Unable to unmerge");
            }

            $parent_order_merged = OrderModel::select('id')
            ->merged()
            ->where("id", $order_data->order_merge_parent_id)
            ->first();

            if(!empty($parent_order_merged)){
                throw new Exception("Parent order is merged with other order, Unable to unmerge");
            }

            DB::beginTransaction();

            OrderProductModel::where([
                ['order_id', '=', $order_data->order_merge_parent_id],
                ['merged_from', '=', $order_data->id]
            ])->delete();

            TransactionModel::where([
                ['bill_to', '=', 'POS_ORDER'],
                ['bill_to_id', '=', $order_data->order_merge_parent_id],
                ['merged_from', '=', $order_data->id]
            ])->delete();

            OrderProductModel::where([
                ['order_id', '=', $order_data->id],
            ])->update(['merged_to' => NULL]);

            TransactionModel::where([
                ['bill_to', '=', 'POS_ORDER'],
                ['bill_to_id', '=', $order_data->id],
                ['merged_to', '=', $order_data->order_merge_parent_id]
            ])
            ->update(['transaction_merged' => 0,'merged_to' => NULL]);

            $status_data = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'CLOSED')->first();
            $order_status = $status_data->value;

            OrderModel::where('id', $order_data->id)
            ->update(['status' => $order_status, 'order_merged' => 0, 'order_merge_parent_id' => NULL]);

            $this->calculate_and_update_order_amount($request, $order_data->order_merge_parent_id);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Order unmerged successfully", 
                    "data" => '',
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

    public function testing(Request $request) {
        // $orders = OrderModel::select("*")->get();

        // foreach($orders as $val){
        //     $order = OrderModel::where('slack', $val->slack)->first();
        //     $order->customer_email = str_replace("appsthing.com", "posmds.online", $order->customer_email);
           
        //     $order->save();
        // }

        // $customers = CustomerModel::select("*")->get();

        // foreach($customers as $val){
        //     $customer = CustomerModel::where('slack', $val->slack)->first();
        //     // if(!$customer) return response()->json(['111'=>$customer]);
        //     $customer->email = str_replace("appsthing.com", "posmds.online", $customer->email);
           
        //     $customer->save();
        // }
        dd(env('APP_URL').'/storage/product');
        dd(storage_path('app/public/product'));
        // $status = MasterStatus::select('value_constant', 'label')
        //         ->FilterByValueConstant('ORDER_KITCHEN_STATUS', 'CONFIRMED')
        //         ->update([
        //             'label' => 'Starters Ready'
        //         ]);

        return response()->json($status);
    }
}
