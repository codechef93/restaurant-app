<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Http\Resources\PrinterResource;

use App\Models\Printer as PrinterModel;
use App\Models\Store as StoreModel;
use App\Models\Order as OrderModel;
use App\Models\Invoice as InvoiceModel;
use App\Models\PurchaseOrder as PurchaseOrderModel;
use App\Models\Quotation as QuotationModel;
use App\Models\BusinessRegister as BusinessRegisterModel;

use App\Http\Resources\Collections\PrinterCollection;

use App\Http\Controllers\Order as OrderController;
use App\Http\Controllers\Invoice as InvoiceController;
use App\Http\Controllers\PurchaseOrder as PurchaseOrderController;
use App\Http\Controllers\Quotation as QuotationController;
use App\Http\Controllers\BusinessRegister as BusinessRegisterController;

use PrintNode;

class Printer extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_PRINTER_LISTING';
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
            
            $query = PrinterModel::select('printers.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
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

            $printers = PrinterResource::collection($query);
           
            $total_count = PrinterModel::select("id")->get()->count();

            $item_array = [];
            foreach($printers as $key => $printer){
                
                $printer = $printer->toArray($request);
                
                $item_array[$key][] = $printer['printer_code'];
                $item_array[$key][] = $printer['printer_name'];
                
                $item_array[$key][] = view('common.status', ['status_data' => ['label' => $printer['status']['label'], "color" => $printer['status']['color']]])->render();
                $item_array[$key][] = $printer['created_at_label'];
                $item_array[$key][] = $printer['updated_at_label'];
                $item_array[$key][] = (isset($printer['created_by']['fullname']))?$printer['created_by']['fullname']:'-';
                $item_array[$key][] = view('printer.layouts.printer_actions', ['printer' => $printer])->render();
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

            if(!check_access(['A_ADD_PRINTER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $printer_data_exists = PrinterModel::select('id')
            ->where('printer_name', '=', trim($request->printer_name))
            ->first();
            if (!empty($printer_data_exists)) {
                throw new Exception("Printer already exists", 400);
            }

            DB::beginTransaction();
            
            $printer = [
                "slack" => $this->generate_slack("printers"),
                "store_id" => $request->logged_user_store_id,
                "printer_code" => Str::random(6),
                "printer_id" => $request->printer_id,
                "printer_name" => $request->printer_name,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];
            
            $printer_id = PrinterModel::create($printer)->id;

            $code_start_config = Config::get('constants.unique_code_start.printer');
            $code_start = (isset($code_start_config))?$code_start_config:100;
            
            $printer_code = [
                "printer_code" => ($code_start+$printer_id)
            ];
            PrinterModel::where('id', $printer_id)
            ->update($printer_code);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Printer created successfully", 
                    "data"    => $printer['slack']
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

            if(!check_access(['A_DETAIL_PRINTER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = PrinterModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new PrinterResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Printer loaded successfully", 
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

            if(!check_access(['A_VIEW_PRINTER_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new PrinterCollection(PrinterModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Printers loaded successfully", 
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

            if(!check_access(['A_EDIT_PRINTER'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $printer_data_exists = PrinterModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['printer_name', '=', trim($request->printer_name)],
            ])
            ->first();
            if (!empty($printer_data_exists)) {
                throw new Exception("Printer already exists", 400);
            }

            DB::beginTransaction();
            
            $printer = [
                "printer_id" => $request->printer_id,
                "printer_name" => $request->printer_name,
                "status" => $request->status,
                "updated_by" => $request->logged_user_id
            ];
            
            $action_response = PrinterModel::where('slack', $slack)
            ->update($printer);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Printer updated successfully", 
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

    public function print_with_printnode(Request $request)
    {
        try {

            $print_type = $request->print_type;
            $slack = $request->slack;

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
                case "INVOICE":

                    if(empty($store_data->other_printer_id)){
                        throw new Exception("Invoice printer not configured", 400);
                    }

                    $printer = PrinterModel::select('printer_id')->where('id', $store_data->other_printer_id)->active()->first();
                    if(empty($printer)){
                        throw new Exception("Printer is not available/inactive", 400);
                    }
                    
                    $printer_id = $printer->printer_id;

                    $invoice = InvoiceModel::select('invoices.invoice_number')->where('slack', $slack)->first();
                    if(empty($invoice)){
                        throw new Exception("Invalid Invoice", 400);
                    }

                    $title = $invoice->invoice_number;
                    $source = $source.$invoice->invoice_number;

                    $invoice_controller = new InvoiceController();
                    $file_link = $invoice_controller->print_invoice($request, $slack, 'FILE', true);
                break;
                case "PURCHASE_ORDER":

                    if(empty($store_data->other_printer_id)){
                        throw new Exception("Purchase Order printer not configured", 400);
                    }

                    $printer = PrinterModel::select('printer_id')->where('id', $store_data->other_printer_id)->active()->first();
                    if(empty($printer)){
                        throw new Exception("Printer is not available/inactive", 400);
                    }
                    
                    $printer_id = $printer->printer_id;

                    $purchase_order = PurchaseOrderModel::select('po_number')->where('slack', $slack)->first();
                    if(empty($purchase_order)){
                        throw new Exception("Invalid Purchase Order", 400);
                    }

                    $title = $purchase_order->po_number;
                    $source = $source.$purchase_order->po_number;

                    $purchase_order_controller = new PurchaseOrderController();
                    $file_link = $purchase_order_controller->print_purchase_order($request, $slack, 'FILE', true);
                break;
                case "QUOTATION":

                    if(empty($store_data->other_printer_id)){
                        throw new Exception("Quotation printer not configured", 400);
                    }

                    $printer = PrinterModel::select('printer_id')->where('id', $store_data->other_printer_id)->active()->first();
                    if(empty($printer)){
                        throw new Exception("Printer is not available/inactive", 400);
                    }
                    
                    $printer_id = $printer->printer_id;

                    $quotation = QuotationModel::select('quotation_number')->where('slack', $slack)->first();
                    if(empty($quotation)){
                        throw new Exception("Invalid Quotation", 400);
                    }

                    $title = $quotation->quotation_number;
                    $source = $source.$quotation->quotation_number;

                    $quotation_controller = new QuotationController();
                    $file_link = $quotation_controller->print_quotation($request, $slack, 'FILE', true);
                break;
                case "BUSINESS_REGISTER_REPORT":

                    if(empty($store_data->other_printer_id)){
                        throw new Exception("Register report printer not configured", 400);
                    }

                    $printer = PrinterModel::select('printer_id')->where('id', $store_data->other_printer_id)->active()->first();
                    if(empty($printer)){
                        throw new Exception("Printer is not available/inactive", 400);
                    }
                    
                    $printer_id = $printer->printer_id;

                    $business_register = BusinessRegisterModel::select('closing_date')->where('slack', $slack)->first();
                    if(empty($business_register)){
                        throw new Exception("Invalid Register", 400);
                    }

                    $title = 'register_report_'.$business_register->closing_date;
                    $source = $source.'register_report_'.$business_register->closing_date;

                    $business_register_controller = new BusinessRegisterController();
                    $file_link = $business_register_controller->print_register_report($request, $slack, 'FILE', true);
                break;
            }
            
            $print_job->title = $print_type.'_'.$title;
            $print_job->source = $source;
            $print_job->printer = $printer_id;
            $print_job->contentType = 'pdf_base64';
            $print_job->addPdfFile($file_link);
            
            $print_job_id = $client->createPrintJob($print_job);

            return response()->json($this->generate_response(
                array(
                    "message" => "Print job sent successfully", 
                    "data"    => $print_job_id
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
        $validator = Validator::make($request->all(), [
            'printer_id' => $this->get_validation_rules("string", true),
            'printer_name' => $this->get_validation_rules("name_label", true),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
