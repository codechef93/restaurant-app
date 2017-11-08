<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\BusinessRegister;
use App\Models\PaymentMethod as PaymentMethodModel;

use Carbon\Carbon;

class BillingCounterExport
{
   
    public function __construct(array $data = [], Request $request)
    {
        $this->data = $data;
        $this->currency_code = $request->logged_user_store_currency;
    }

    // public function view(): View
    // {
    //     $from_date = $this->data['from_date'];
    //     $to_date = $this->data['to_date'];

    //     $payment_methods = PaymentMethodModel::select('label')->orderBy('label', 'asc')->active()->get();

    //     $payment_method_query = [];
    //     foreach($payment_methods as $payment_method){
    //         $payment_method_query[] = "SUM(IF(orders.payment_method = '".$payment_method['label']."',1,0)) AS `".strtolower($payment_method['label'])."_sale_count`";

    //         $payment_method_query[] = "SUM(IF(orders.payment_method = '".$payment_method['label']."',orders.total_order_amount,0)) AS `".strtolower($payment_method['label'])."_sale_amount`";
    //     }

    //     $payment_method_query_string = implode(", ", $payment_method_query);

    //     $query = BusinessRegister::query();
    //     $query->selectRaw("DATE(orders.created_at) as order_day_date, DATE_FORMAT(orders.created_at, '%b %Y') as order_month_date, SUM(total_order_amount) as total_order_amount_sum, CONCAT(billing_counters.billing_counter_code,' - ',billing_counters.counter_name) AS billing_counter_data, currency_code, COUNT(orders.id) as order_count".(($payment_method_query_string != '')?','.$payment_method_query_string:''));
    //     $query->leftJoin('billing_counters', 'billing_counters.id', '=', 'business_registers.billing_counter_id');
    //     $query->leftJoin('orders', 'orders.register_id', '=', 'business_registers.id');
    //     $query->groupBy('billing_counters.id');
    //     $query->groupByRaw('DATE(orders.created_at)');
    //     $query->where('orders.status', 1);
    //     $query->orderBy('orders.created_at', 'asc');

    //     if($from_date != ''){
    //         $from_created_date = strtotime($from_date);
    //         $from_created_date = date(config('app.sql_date_format'), $from_created_date);
    //         $from_created_date = $from_created_date . ' 00:00:00';
    //         $query = $query->where('orders.created_at', '>=', $from_created_date);
    //     }
    //     if($to_date != ''){
    //         $to_created_date = strtotime($to_date);
    //         $to_created_date = date(config('app.sql_date_format'), $to_created_date);
    //         $to_created_date = $to_created_date . ' 23:59:59';
    //         $query = $query->where('orders.created_at', '<=', $to_created_date);
    //     }

    //     $orders = $query->get();
        
    //     $orders_grouped = $orders->mapToGroups(function ($item) {
    //         return [$item['order_day_date'] => $item];
    //     });

    //     $total_order_value = $orders->sum('total_order_amount_sum');
    //     $total_order_count = $orders->sum('order_count');

    //     $date_sequences = generate_date_range($from_date, $to_date);

    //     $business_register_report_data = [];
    //     if(count($date_sequences)>0){
    //         foreach($date_sequences as $date_sequence){
    //             $business_register_report_data[$date_sequence] = (isset($orders_grouped[$date_sequence]))?$orders_grouped[$date_sequence]:[];
    //         }
    //     }

    //     $total_count_array = [
    //         'total_order_value' => $total_order_value,
    //         'total_order_count' => $total_order_count,
    //     ];

    //     $general = [
    //         'title' => 'DATE RANGE : '.Carbon::parse($from_date)->format(config("app.date_format")). ' TO '.Carbon::parse($to_date)->format(config("app.date_format")),
    //         'currency' => $this->currency_code
    //     ];

    //     return view('report.exports.business_register_report', [
    //         'business_register_report_data' => $business_register_report_data,
    //         'total_count_array' => $total_count_array,
    //         'payment_methods' => $payment_methods,
    //         'general' => $general
    //     ]);
    // }
    public function view()
    {
        $from_date = $this->data['from_date'];
        $to_date = $this->data['to_date'];

        $payment_methods = PaymentMethodModel::select('label')->orderBy('label', 'asc')->active()->get();

        $payment_method_query = [];
        foreach($payment_methods as $payment_method){
            $payment_method_query[] = "SUM(IF(orders.payment_method = '".$payment_method['label']."',1,0)) AS `".strtolower($payment_method['label'])."_sale_count`";

            $payment_method_query[] = "SUM(IF(orders.payment_method = '".$payment_method['label']."',orders.total_order_amount,0)) AS `".strtolower($payment_method['label'])."_sale_amount`";
        }

        $payment_method_query_string = implode(", ", $payment_method_query);

        $query = BusinessRegister::query();
        $query->selectRaw("DATE(orders.created_at) as order_day_date, DATE_FORMAT(orders.created_at, '%b %Y') as order_month_date, SUM(total_order_amount) as total_order_amount_sum, CONCAT(billing_counters.billing_counter_code,' - ',billing_counters.counter_name) AS billing_counter_data, currency_code, COUNT(orders.id) as order_count".(($payment_method_query_string != '')?','.$payment_method_query_string:''));
        $query->leftJoin('billing_counters', 'billing_counters.id', '=', 'business_registers.billing_counter_id');
        $query->leftJoin('orders', 'orders.register_id', '=', 'business_registers.id');
        $query->groupBy('billing_counters.id');
        $query->groupByRaw('DATE(orders.created_at)');
        $query->where('orders.status', 1);
        $query->orderBy('orders.created_at', 'asc');

        if($from_date != ''){
            $from_created_date = strtotime($from_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('orders.created_at', '>=', $from_created_date);
        }
        if($to_date != ''){
            $to_created_date = strtotime($to_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('orders.created_at', '<=', $to_created_date);
        }

        $orders = $query->get();
        
        $orders_grouped = $orders->mapToGroups(function ($item) {
            return [$item['order_day_date'] => $item];
        });

        $total_order_value = $orders->sum('total_order_amount_sum');
        $total_order_count = $orders->sum('order_count');

        $date_sequences = generate_date_range($from_date, $to_date);

        $business_register_report_data = [];
        if(count($date_sequences)>0){
            foreach($date_sequences as $date_sequence){
                $business_register_report_data[$date_sequence] = (isset($orders_grouped[$date_sequence]))?$orders_grouped[$date_sequence]:[];
            }
        }

        $total_count_array = [
            'total_order_value' => $total_order_value,
            'total_order_count' => $total_order_count,
        ];

        $general = [
            'title' => 'DATE RANGE : '.Carbon::parse($from_date)->format(config("app.date_format")). ' TO '.Carbon::parse($to_date)->format(config("app.date_format")),
            'currency' => $this->currency_code
        ];

        return view('report.exports.business_register_report', [
            'business_register_report_data' => $business_register_report_data,
            'total_count_array' => $total_count_array,
            'payment_methods' => $payment_methods,
            'general' => $general
        ]);
    }
}
