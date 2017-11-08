<?php

namespace App\Exports;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

use Carbon\Carbon;

class SaleTaxExport implements FromView
{
   
    public function __construct(array $data = [], Request $request)
    {
        $this->data = $data;
        $this->currency_code = $request->logged_user_store_currency;
    }

    public function view(): View
    {
        $from_date = $this->data['from_date'];
        $to_date = $this->data['to_date'];
        $group_type = $this->data['group_type'];

        $query = Order::query();
        $query->selectRaw("DATE(orders.created_at) as order_day_date, DATE_FORMAT(orders.created_at, '%b %Y') as order_month_date, SUM(total_order_amount) as total_order_amount_sum, SUM(total_after_discount) as total_after_discount_sum, SUM(total_tax_amount) as total_tax_amount_sum, currency_code");
        $query->when($group_type == 'DAILY', function ($query) {
            return $query->groupByRaw('DATE(orders.created_at)');
        });
        $query->when($group_type == 'MONTHLY', function ($query) {
            return $query->groupByRaw('YEAR(orders.created_at), MONTH(orders.created_at)');
        })
        ->where('orders.status', 1);

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

        if($group_type == 'DAILY'){
            $orders_grouped = $orders->mapWithKeys(function ($item) {
                return [$item['order_day_date'] => $item];
            });
            $date_sequences = generate_date_range($from_date, $to_date);
        }

        if($group_type == 'MONTHLY'){
            $orders_grouped = $orders->mapWithKeys(function ($item) {
                return [$item['order_month_date'] => $item];
            });
            $date_sequences = generate_date_range($from_date, $to_date, '+1 month', 'M Y');
        }

        $sale_tax_report_data = [];
        if(count($date_sequences)>0){
            foreach($date_sequences as $date_sequence){
                $sale_tax_report_data[$date_sequence] = [
                    'order_date' => $date_sequence,
                    'total_order_amount_sum' => (isset($orders_grouped[$date_sequence]->total_order_amount_sum)?$orders_grouped[$date_sequence]->total_order_amount_sum:0),
                    'total_after_discount_sum' => (isset($orders_grouped[$date_sequence]->total_after_discount_sum)?$orders_grouped[$date_sequence]->total_after_discount_sum:0),
                    'total_tax_amount_sum' => (isset($orders_grouped[$date_sequence]->total_tax_amount_sum)?$orders_grouped[$date_sequence]->total_tax_amount_sum:0),
                ];
            }
        }

        $total_sale_tax_report_data = [
            'grand_total_order_amount_sum' => array_sum(array_column($sale_tax_report_data, 'total_order_amount_sum')),
            'grand_total_after_discount_sum' => array_sum(array_column($sale_tax_report_data, 'total_after_discount_sum')),
            'grand_total_tax_amount_sum' => array_sum(array_column($sale_tax_report_data, 'total_tax_amount_sum')),
        ];

        $general = [
            'title' => 'DATE RANGE : '.Carbon::parse($from_date)->format(config("app.date_format")). ' TO '.Carbon::parse($to_date)->format(config("app.date_format")),
            'currency' => $this->currency_code
        ];

        return view('report.exports.sales_tax', [
            'sales_tax_data' => $sale_tax_report_data,
            'total_sales_tax_data' => $total_sale_tax_report_data,
            'general' => $general
        ]);
    }
}
