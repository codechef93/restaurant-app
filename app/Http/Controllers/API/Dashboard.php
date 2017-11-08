<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;
use Illuminate\Support\Arr;

use App\Models\Customer as CustomerModel;
use App\Models\Order as OrderModel;
use App\Models\User as UserModel;
use App\Models\Store as StoreModel;
use App\Models\Product as ProductModel;
use App\Models\Supplier as SupplierModel;
use App\Models\PurchaseOrder as PurchaseOrderModel;
use App\Models\Transaction as TransactionModel;
use App\Models\MasterTransactionType as MasterTransactionTypeModel;
use App\Models\Invoice as InvoiceModel;
use App\Models\Quotation as QuotationModel;
use App\Models\Target as TargetModel;
use App\Models\OrderProduct as OrderProductModel;
use App\Models\BillingCounter as BillingCounterModel;
use App\Models\BusinessRegister as BusinessRegisterModel;
use App\Models\PaymentMethod as PaymentMethodModel;

use App\Http\Resources\TransactionResource;
use App\Http\Resources\BillingCounterResource;

class Dashboard extends Controller
{
    public function __construct(Request $request) { 
        $this->date = ($request->date != '')? $request->date : date("Y-m");
    }

    public function get_dashboard_stats(){
        try {
            $data = [];
            $data['todays_order_count'] = $this->get_today_order_count();
            $data['todays_order_value'] = $this->get_today_order_value();

            $data['order_count'] = $this->get_order_count();
            $data['order_value'] = $this->get_order_value();
            $data['customer_count'] = $this->get_customer_count();
            $data['revenue_value'] = $this->get_total_revenue();
            $data['expense'] = $this->get_total_expense();
            $data['net_profit_value'] = $this->get_net_profit();
            $data['purchase_order_count'] = $this->get_po_count();
            $data['invoices_count'] = $this->get_invoice_count();

            $data['targets'] = $this->get_target_values();

            return response()->json($this->generate_response(
                array(
                    "message" => "Dashboard stats calculated successfully",
                    "data" => $data,
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


    public function get_order_chart_stats(Request $request){
        try {

            $dates = $this->get_chart_dates();
            $order_count_array = $this->get_monthly_order_count();
            $order_value_array = $this->get_monthly_order_value();
            
            $result = [];
            foreach($dates as $key => $date){
                $order_count = 0;
                $order_value = 0;
                if(array_key_exists($date, $order_count_array)){
                    $order_count = $order_count_array[$date];
                }
                if(array_key_exists($date, $order_value_array)){
                    $order_value = $order_value_array[$date];
                }
                $result[] = [
                    "order_count" => $order_count,
                    "order_value" => $order_value,
                    "date" => $date
                ];
            }

            $x_axis_data = array_column($result, 'date');
            $y_axis_order_count_data = array_column($result, 'order_count');
            $y_axis_order_value_data = array_column($result, 'order_value');
            
            return response()->json($this->generate_response(
                array(
                    "message" => "Monthly order matrix calculated successfully",
                    "data" => [
                        "x_axis" => $x_axis_data,
                        "y_axis" => [
                            'order_count' => $y_axis_order_count_data,
                            'order_value' => $y_axis_order_value_data
                        ]
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

    private function get_chart_dates(){
        $year = date("Y", strtotime($this->date));
        $month = date("n", strtotime($this->date));
        $current_month = date("n");
        $current_day = date("j");

        $number_of_days = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $dates = [];
        for($i = 1; $i <= $number_of_days; $i++){
            if($current_month == $month && $i > $current_day){
                continue;                    
            }
            $loop_date = $i;
            $dates[] = $loop_date;
        }
        return $dates;
    }

    public function get_monthly_order_count(){
        $order_count_data = OrderModel::selectRaw("COUNT(id) as order_count, DATE_FORMAT(created_at, '%e') as order_date")
        ->closed()
        ->where('created_at', 'like', $this->date.'%')
        ->groupBy('order_date')
        ->get()
        ->toArray();

        $order_count_data_array = [];
        if (!empty($order_count_data)) {
            foreach($order_count_data as $order_count_data_item){
                $order_count_data_array[$order_count_data_item['order_date']] = $order_count_data_item['order_count'];
            }
        }
        return $order_count_data_array;
    }

    public function get_monthly_order_value(){
        $order_count_data = OrderModel::selectRaw("SUM(total_order_amount) as order_value, DATE_FORMAT(created_at, '%e') as order_date")
        ->closed()
        ->where('created_at', 'like', $this->date.'%')
        ->groupBy('order_date')
        ->get()
        ->toArray();

        $order_count_data_array = [];
        if (!empty($order_count_data)) {
            foreach($order_count_data as $order_count_data_item){
                $order_count_data_array[$order_count_data_item['order_date']] = $order_count_data_item['order_value'];
            }
        }
        return $order_count_data_array;
    }

    public function get_order_count(){
        $count = OrderModel::closed()
        ->where('created_at', 'like', $this->date.'%')
        ->count();

        $count_formatted = number_format_short($count);

        $response = [
            "count_raw" => $count,
            "count_formatted" => $count_formatted
        ];

        return $response;
    }

    public function get_today_order_count(){

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime(Carbon::now()->subDays(1)));
        $day_before_yesterday = date('Y-m-d', strtotime(Carbon::now()->subDays(2)));

        $count = OrderModel::closed()
        ->where('created_at', 'like', $today.'%')
        ->count();

        $count_formatted = number_format_short($count);

        $yesterday_count = OrderModel::closed()
        ->where('created_at', 'like', $yesterday.'%')
        ->count();

        $day_before_yesterday_count = OrderModel::closed()
        ->where('created_at', 'like', $day_before_yesterday.'%')
        ->count();

        $sale_count_chart = [
            'x_axis' => ['Day Before Yest.', 'Yesterday', 'Today'],
            'y_axis' => [$day_before_yesterday_count, $yesterday_count, $count]
        ];

        $response = [
            "count_raw" => $count,
            "count_formatted" => $count_formatted,
            "chart" => $sale_count_chart
        ];

        return $response;
    }

    public function get_order_value(){
        $sum = OrderModel::closed()
        ->where('created_at', 'like', $this->date.'%')
        ->sum('total_order_amount');

        $sum_formatted = number_format_short($sum);

        $response = [
            "count_raw" => round($sum, 2),
            "count_formatted" => $sum_formatted
        ];

        return $response;
    }

    public function get_today_order_value(){

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime(Carbon::now()->subDays(1)));
        $day_before_yesterday = date('Y-m-d', strtotime(Carbon::now()->subDays(2)));

        $order_value = OrderModel::closed()
        ->where('created_at', 'like', $today.'%')
        ->sum('total_order_amount');

        $order_value_formatted = number_format_short($order_value);

        $yesterday_value= OrderModel::closed()
        ->where('created_at', 'like', $yesterday.'%')
        ->sum('total_order_amount');

        $day_before_yesterday_value= OrderModel::closed()
        ->where('created_at', 'like', $day_before_yesterday.'%')
        ->sum('total_order_amount');

        $sale_value_chart = [
            'x_axis' => ['Day Before Yest.', 'Yesterday', 'Today'],
            'y_axis' => [$day_before_yesterday_value, $yesterday_value, $order_value]
        ];

        $response = [
            "count_raw" => $order_value,
            "count_formatted" => $order_value_formatted,
            "chart" => $sale_value_chart
        ];

        return $response;
    }

    public function get_customer_count(){
        $count = CustomerModel::active()
        ->where('created_at', 'like', $this->date.'%')
        ->count();

        $count_formatted = number_format_short($count);

        $response = [
            "count_raw" => $count,
            "count_formatted" => $count_formatted
        ];

        return $response;
    }

    public function get_total_revenue(){
        $sum = OrderModel::closed()
        ->where('created_at', 'like', $this->date.'%')
        ->sum('total_order_amount');

        $transaction_type_data = MasterTransactionTypeModel::select('id')
        ->where('transaction_type_constant', '=', trim('INCOME'))
        ->first();

        $transaction_income = TransactionModel::select('id')->where([
            ['transaction_type', '=', $transaction_type_data->id],
        ])
        ->whereIn('bill_to', ['INVOICE', 'CUSTOMER', 'SUPPLIER'])
        ->where('transaction_date', 'like', $this->date.'%')
        ->notMerged()
        ->sum('amount');

        $sum = $sum+$transaction_income;
        $sum_formatted = number_format_short($sum);

        $response = [
            "count_raw" => round($sum, 2),
            "count_formatted" => $sum_formatted
        ];

        return $response;
    }

    public function get_total_expense(){
        $transaction_type_data = MasterTransactionTypeModel::select('id')
        ->where('transaction_type_constant', '=', trim('EXPENSE'))
        ->first();

        $transaction_expense = TransactionModel::select('id')->where([
            ['transaction_type', '=', $transaction_type_data->id],
        ])
        ->whereIn('bill_to', ['INVOICE', 'CUSTOMER', 'SUPPLIER'])
        ->where('transaction_date', 'like', $this->date.'%')
        ->notMerged()
        ->sum('amount');

        $sum_formatted = number_format_short($transaction_expense);

        $response = [
            "count_raw" => round($transaction_expense, 2),
            "count_formatted" => $sum_formatted
        ];

        return $response;
    }

    public function get_net_profit(){
        $revenue = $this->get_total_revenue();
        $revenue_value = $revenue['count_raw'];

        $transaction_type_data = MasterTransactionTypeModel::select('id')
        ->where('transaction_type_constant', '=', trim('EXPENSE'))
        ->first();

        $transaction_expense = TransactionModel::select('id')->where([
            ['transaction_type', '=', $transaction_type_data->id],
        ])
        ->whereIn('bill_to', ['INVOICE', 'CUSTOMER', 'SUPPLIER'])
        ->where('transaction_date', 'like', $this->date.'%')
        ->notMerged()
        ->sum('amount');

        $net_profit = $revenue_value-$transaction_expense;

        $net_profit_formatted = number_format_short($net_profit);

        $response = [
            "count_raw" => round($net_profit, 2),
            "count_formatted" => $net_profit_formatted
        ];

        return $response;
    }

    public function get_po_count(){
        $po_count = PurchaseOrderModel::select('id')
        ->statusJoin()
        ->where('purchase_orders.created_at', 'like', $this->date.'%')
        ->whereIn('value_constant', ['CREATED', 'APPROVED', 'RELEASED_TO_SUPPLIER', 'CLOSED'])
        ->count();

        $po_count_formatted = number_format_short($po_count);
        
        $response = [
            "count_raw" => $po_count,
            "count_formatted" => $po_count_formatted
        ];

        return $response;
    }

    public function get_invoice_count(){
        $invoice_count = InvoiceModel::select('id')
        ->statusJoin()
        ->where('invoices.created_at', 'like', $this->date.'%')
        ->whereIn('value_constant', ['NEW','SENT','PAID','OVERDUE', 'WRITEOFF'])->count();

        $invoice_count_formatted = number_format_short($invoice_count);
        
        $response = [
            "count_raw" => $invoice_count,
            "count_formatted" => $invoice_count_formatted
        ];

        return $response;
    }

    public function get_target_values(){
        $target = TargetModel::select('income', 'expense', 'sales', 'net_profit')
        ->where('month', 'like', $this->date.'%')
        ->first();

        $income = ($target)?$target->income:0;
        $expense = ($target)?$target->expense:0;
        $sales = ($target)?$target->sales:0;
        $net_profit = ($target)?$target->net_profit:0;
        
        $response = [
            "income" => $income,
            "expense" => $expense,
            "sales" => $sales,
            "net_profit" => $net_profit
        ];

        return $response;
    }

    public function get_recent_trasactions(Request $request){
        try {

            $transactions_list = TransactionModel::select('*')
            ->where('transactions.transaction_date', 'like', $this->date.'%')
            ->orderBy('created_at', 'DESC')
            ->notMerged()
            ->limit(10)
            ->get();

            $transactions = TransactionResource::collection($transactions_list);
            
            return response()->json($this->generate_response(
                array(
                    "message" => "Recent transactions loaded successfully",
                    "data" => $transactions,
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

    public function get_billing_counter_stats(Request $request){
        
        try {
            $date = date("Y-m-d");

            $payment_methods = PaymentMethodModel::select('id', 'label')
            ->active()
            ->get();

            $billing_counters = BillingCounterModel::select("*")
            ->active()
            ->get();

            $billing_counters_data = BillingCounterResource::collection($billing_counters);

            $billing_counters_data = $billing_counters_data->map(function ($billing_counter, $key) use ($date, $payment_methods){

                $business_registers = BusinessRegisterModel::select('id')
                ->where('billing_counter_id', '=', $billing_counter->id)
                ->where('created_at', 'like', $date.'%')
                ->pluck('id')->toArray();
                
                $business_register_ids = array_values($business_registers);
                
                $order_data = [];
                $payment_method_array= [];

                if(count($business_register_ids)>0){
                    
                    $order_data = OrderModel::select(DB::raw('COUNT(id) as order_count, SUM(total_order_amount) as order_value'))
                    ->where('created_at', 'like', $date.'%')
                    ->whereIn('register_id', $business_register_ids)
                    ->closed()
                    ->first();
                }

                $billing_counter =  collect(['order_data' => $order_data])->merge($billing_counter);

                foreach($payment_methods as $payment_method){

                    $payment_method_order_amount = OrderModel::select(DB::raw('COUNT(id) as order_count, SUM(total_order_amount) as order_value'))
                    ->where([
                        ['created_at', 'like', $date.'%'],
                        ['payment_method_id', '=', $payment_method->id]
                    ])
                    ->whereIn('register_id', $business_register_ids)
                    ->closed()
                    ->first();

                    $payment_method_array[] = [
                        'payment_method' => $payment_method['label'],
                        'value' => $payment_method_order_amount->order_value,
                        'order_count' => $payment_method_order_amount->order_count
                    ];
                }

                $billing_counter =  collect(['payment_method_data' => $payment_method_array])->merge($billing_counter);

                return $billing_counter;
            });

            return response()->json($this->generate_response(
                array(
                    "message" => "Billing counter stats loaded successfully",
                    "data" => $billing_counters_data,
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