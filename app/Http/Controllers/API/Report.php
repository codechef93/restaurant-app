<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;

use App\Imports\DataImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Maatwebsite\Excel\Facades\Excel;
use Mpdf\Mpdf;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

use App\Exports\UserExport;
use App\Exports\ProductExport;
use App\Exports\OrderExport;
use App\Exports\CustomerExport;
use App\Exports\CategoryExport;
use App\Exports\DiscountcodeExport;
use App\Exports\StoreExport;
use App\Exports\SupplierExport;
use App\Exports\TaxcodeExport;
use App\Exports\PurchaseOrderExport;
use App\Exports\InvoiceExport;
use App\Exports\QuotationExport;
use App\Exports\TransactionExport;
use App\Exports\SaleTaxExport;
use App\Exports\BillingCounterExport;

use App\Models\OrderProduct as OrderProductModel;
use App\Models\Category as CategoryModel;
use App\Models\Product as ProductModel;

use App\Http\Controllers\Report as ReportController;

use App\Http\Resources\ProductResource;

class Report extends Controller
{

    public function __construct(Request $request) {
        $this->view_path = Config::get('constants.upload.reports.view_path');
        $this->date = ($request->date != '')? $request->date : date("Y-m");
    }

    public function user_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'role' => $request->role,
                'status' => $request->status,
            ];

            // $filename = 'user_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new UserExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );
            
            // $download_link = asset($this->view_path.$filename);
            $type = 'user_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "User report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function product_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'supplier' => $request->supplier,
                'category' => $request->category,
                'tax_code' => $request->tax_code,
                'discount_code' => $request->discount_code,
                'product_type' => $request->product_type,
                'status' => $request->status,
            ];

            // $filename = 'product_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new ProductExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );
            
            // $download_link = asset($this->view_path.$filename);

            $type = 'product_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Product report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function order_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            // $filename = 'order_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new OrderExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );
            
            // $download_link = asset($this->view_path.$filename);
            $type = 'order_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Order report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function purchase_order_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            // $filename = 'purchase_order_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new PurchaseOrderExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );
            
            // $download_link = asset($this->view_path.$filename);
            $type = 'purchase_order_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Purchase Order report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function customer_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            // $filename = 'customer_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new CustomerExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );
            
            // $download_link = asset($this->view_path.$filename);
            $type = 'customer_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Customer report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function store_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            $filename = 'store_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            Excel::store(
                new StoreExport(
                    $params
                ),
                'public/reports/'.$filename
            );
            
            $download_link = asset($this->view_path.$filename);

            return response()->json($this->generate_response(
                array(
                    "message" => "Store report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function taxcode_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            // $filename = 'taxcode_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new TaxcodeExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );
            
            // $download_link = asset($this->view_path.$filename);
            $type = 'taxcode_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Taxcode report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function discountcode_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            // $filename = 'discountcode_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new DiscountcodeExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );
            
            // $download_link = asset($this->view_path.$filename);
            $type = 'discountcode_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Discount code report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function supplier_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            // $filename = 'supplier_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new SupplierExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );
            
            // $download_link = asset($this->view_path.$filename);

            $type = 'supplier_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Supplier code report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function category_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            // $filename = 'category_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new CategoryExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );            
            // $download_link = asset($this->view_path.$filename);

            $type = 'category_report';
            $report_controller = new ReportController();
            
            $file_link = $report_controller->print_report($request, $params, $type);

            $download_link = asset($this->view_path.$file_link);
            
            return response()->json($this->generate_response(
                array(
                    "message" => "Category code report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function invoice_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            // $filename = 'invoice_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new InvoiceExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );
            
            // $download_link = asset($this->view_path.$filename);
            $type = 'invoice_report';
            $report_controller = new ReportController();
            
            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Invoice report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function quotation_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'status' => $request->status,
            ];

            // $filename = 'quotation_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new QuotationExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );
            
            // $download_link = asset($this->view_path.$filename);
            $type = 'quotation_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Quotation report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function transaction_report(Request $request){
        try {
            
            $params = [
                'from_created_date' => $request->from_created_date,
                'to_created_date' => $request->to_created_date,
                'account' => $request->account,
                'transaction_type' => $request->transaction_type,
                'payment_method' => $request->payment_method,
                'bill_to' => $request->bill_to,
            ];

            // $filename = 'transaction_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

            // Excel::store(
            //     new TransactionExport(
            //         $params
            //     ),
            //     'public/reports/'.$filename
            // );
            
            // $download_link = asset($this->view_path.$filename);
            $type = 'transaction_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Transaction report generated successfully",
                    "data" => '',
                    "link" => $download_link
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

    public function get_trending_products(Request $request){
        try {

            $this->current_store = $request->logged_user_store_id;

            $trending_products = OrderProductModel::select(DB::raw("CONCAT(COALESCE(order_products.product_code, ''),' - ',COALESCE(order_products.name, '')) as product"), DB::raw("SUM(order_products.quantity) as quantity"))
            ->orderJoin()
            ->where('orders.store_id', $this->current_store)
            ->where('orders.created_at', 'like', $this->date.'%')
            ->where('orders.status', 1)
            ->orderBy('quantity', 'DESC')
            ->groupBy('product_code')
            ->limit(10)
            ->get()->toArray();

            $x_axis_data = array_column($trending_products, 'product');
            $y_axis_data = array_column($trending_products, 'quantity');
            
            return response()->json($this->generate_response(
                array(
                    "message" => "Trending products loaded successfully",
                    "data" => [
                        "x_axis" => $x_axis_data,
                        "y_axis" => $y_axis_data
                    ],
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

    public function get_category_performance(Request $request){
        try {

            $this->current_store = $request->logged_user_store_id;
            $month = ($request->month)?$request->month:date('Y-m');

            $item_array = array();

            $draw = $request->draw;
            $limit = $request->length;
            $offset = $request->start;
            
            $order_by = $request->order[0]["column"];
            $order_direction = $request->order[0]["dir"];
            $order_by_column =  $request->columns[$order_by]['name'];

            $filter_string = $request->search['value'];
            $filter_columns = array_filter(data_get($request->columns, '*.name'));
            
            $main_query = CategoryModel::select("category.label", "category.category_code", DB::raw("IFNULL(SUM(order_products.quantity), 0) AS sold_quantity"), DB::raw("IFNULL(SUM(order_products.sub_total_purchase_price_excluding_tax),0) AS purchased_amount"), DB::raw("IFNULL(SUM(order_products.sub_total_sale_price_excluding_tax), 0) AS sold_amount"),DB::raw("(IFNULL(SUM(order_products.sub_total_sale_price_excluding_tax), 0) - IFNULL(SUM(order_products.sub_total_purchase_price_excluding_tax),0)) AS profit_loss"))
            ->active()
            ->productJoin()
            ->leftJoin('order_products', function ($join) use ($month){
                $join->on('order_products.product_id', '=', 'products.id');
                $join->where('order_products.created_at', 'like', $month.'%');
            })
            ->leftJoin('orders', function ($join) {
                $join->on('orders.id', '=', 'order_products.order_id');
                $join->where('orders.status', '=', 1);
            })
            ->groupBy('category.id');
            
            $categories = $main_query
            ->take($limit)
            ->skip($offset)

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
           
            $total_count = $main_query->get()->count();

            $item_array = [];
            foreach($categories as $key => $category){

                $item_array[$key][] = $category->label;
                $item_array[$key][] = $category->category_code;
                $item_array[$key][] = $category->sold_quantity;
                $item_array[$key][] = $category->purchased_amount;
                $item_array[$key][] = $category->sold_amount;
                $item_array[$key][] = $category->profit_loss;
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

    
    public function product_alert_report(Request $request){
        try {

            $item_array = array();

            $product_filter = (isset($request->product_type))?$request->product_type:'billing_products';

            $draw = $request->draw;
            $limit = $request->length;
            $offset = $request->start;
            
            $order_by = $request->order[0]["column"];
            $order_direction = $request->order[0]["dir"];
            $order_by_column =  $request->columns[$order_by]['name'];

            $filter_string = $request->search['value'];
            $filter_columns = array_filter(data_get($request->columns, '*.name'));
            
            $query = ProductModel::select('products.*')
            ->take($limit)
            ->skip($offset)
            ->statusJoin()
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->createdUser()
            ->lowStock()
            ->active()

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

            ->when($product_filter == 'billing_products', function ($query) {
                $query->mainProduct();
            })

            ->when($product_filter == 'ingredients', function ($query) {
                $query->isIngredient();
            });

            $count_query = $query;

            $query = $query->get();

            $products = ProductResource::collection($query);

            $count_query->getQuery()->limit = null;
            $count_query->getQuery()->offset = null;
            $total_count = $count_query->get()->count();

            $item_array = [];
            foreach($products as $key => $product){
                
                $product = $product->toArray($request);

                $item_array[$key][] = $product['product_code'];
                $item_array[$key][] = Str::limit($product['name'], 100);
                $item_array[$key][] = $product['alert_quantity'];
                $item_array[$key][] = $product['quantity'];
                
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

    public function store_stock_chart(Request $request){
        try {
            $stock_data = ProductModel::select(DB::raw("IFNULL(SUM(products.quantity), 0) AS total_quantity"), DB::raw("IFNULL(SUM(products.purchase_amount_excluding_tax*products.quantity), 0.00) AS total_purchase_cost"), DB::raw("IFNULL(SUM(products.sale_amount_excluding_tax*products.quantity), 0.00) AS total_sale_price"), DB::raw("IFNULL(SUM(products.sale_amount_excluding_tax*products.quantity), 0.00) -  IFNULL(SUM(products.purchase_amount_excluding_tax*products.quantity), 0.00) AS profit_estimate"))
            ->active()
            ->first();

            $response = [
                "total_quantity" => round($stock_data->total_quantity, 2),
                "total_purchase_cost" => round($stock_data->total_purchase_cost, 2),
                "total_sale_price" => round($stock_data->total_sale_price, 2),
                "profit_estimate" => round($stock_data->profit_estimate, 2)
            ];

            return response()->json($this->generate_response(
                array(
                    "message" => "stock chart data loaded successfully", 
                    "data"    => $response
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

    // public function sale_tax_report(Request $request){
    //     try {
            
    //         $params = [
    //             'from_date' => $request->from_date,
    //             'to_date' => $request->to_date,
    //             'group_type' => $request->group_type,
    //         ];

    //         if($request->from_date == '' || $request->to_date == ''){
    //             throw new Exception("Please choose from date and to date", 400);
    //         }

    //         if($request->to_date != '' && $request->from_date != ''){
    //             $date_diff = strtotime($request->to_date)-strtotime($request->from_date); 
    //             $months = (($date_diff)/2628000);
                
    //             if($months > 3){
    //                 throw new Exception("Please choose 3 months duration", 400);
    //             }
    //         }

    //         $filename = 'sales_tax_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.pdf';

    //         Excel::store(
    //             new SaleTaxExport(
    //                 $params,
    //                 $request
    //             ),
    //             'public/reports/'.$filename
    //         );
            
    //         $download_link = asset($this->view_path.$filename);

    //         return response()->json($this->generate_response(
    //             array(
    //                 "message" => "Sale report generated successfully",
    //                 "data" => '',
    //                 "link" => $download_link
    //             ), 
    //         ));

    //     }catch(Exception $e){
    //         return response()->json($this->generate_response(
    //             array(
    //                 "message" => $e->getMessage(),
    //                 "status_code" => $e->getCode()
    //             )
    //         ));
    //     }
    // }
    public function sale_tax_report(Request $request){
        try {
            
            $params = [
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'group_type' => $request->group_type,
            ];

            if($request->from_date == '' || $request->to_date == ''){
                throw new Exception("Please choose from date and to date", 400);
            }

            if($request->to_date != '' && $request->from_date != ''){
                $date_diff = strtotime($request->to_date)-strtotime($request->from_date); 
                $months = (($date_diff)/2628000);
                
                if($months > 3){
                    throw new Exception("Please choose 3 months duration", 400);
                }
            }

            $type = 'sale_tax_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);
            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Sale report generated successfully",
                    "data" => '',
                    "link" => $download_link
                ), 
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

    // public function billing_counter_report(Request $request){
    //     try {
            
    //         $params = [
    //             'from_date' => $request->from_date,
    //             'to_date' => $request->to_date,
    //         ];
            
    //         if($request->from_date == '' || $request->to_date == ''){
    //             throw new Exception("Please choose from date and to date", 400);
    //         }

    //         if($request->to_date != '' && $request->from_date != ''){
    //             $date_diff = strtotime($request->to_date)-strtotime($request->from_date); 
    //             $months = (($date_diff)/2628000);
                
    //             if($months > 3){
    //                 throw new Exception("Please choose 3 months duration", 400);
    //             }
    //         }

    //         $filename = 'billing_counter_report_'.date('Y_m_d_h_i_s').'_'.uniqid().'.xlsx';

    //         Excel::store(
    //             new BillingCounterExport(
    //                 $params,
    //                 $request
    //             ),
    //             'public/reports/'.$filename
    //         );
            
    //         $download_link = asset($this->view_path.$filename);

    //         return response()->json($this->generate_response(
    //             array(
    //                 "message" => "Billing counter report generated successfully",
    //                 "data" => '',
    //                 "link" => $download_link
    //             ), 'SUCCESS'
    //         ));

    //     }catch(Exception $e){
    //         return response()->json($this->generate_response(
    //             array(
    //                 "message" => $e->getMessage(),
    //                 "status_code" => $e->getCode()
    //             )
    //         ));
    //     }
    // }

    public function billing_counter_report(Request $request){
        try {
            
            $params = [
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
            ];
            
            if($request->from_date == '' || $request->to_date == ''){
                throw new Exception("Please choose from date and to date", 400);
            }

            if($request->to_date != '' && $request->from_date != ''){
                $date_diff = strtotime($request->to_date)-strtotime($request->from_date); 
                $months = (($date_diff)/2628000);
                
                if($months > 3){
                    throw new Exception("Please choose 3 months duration", 400);
                }
            }
            $type = 'billing_counter_report';
            $report_controller = new ReportController();

            $file_link = $report_controller->print_report($request, $params, $type);

            $download_link = asset($this->view_path.$file_link);

            return response()->json($this->generate_response(
                array(
                    "message" => "Billing counter report generated successfully",
                    "data" => '',
                    "link" => $download_link
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
}