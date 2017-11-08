<?php
namespace App\Exports;
use App\Models\Order;
use App\Http\Resources\OrderResource;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

use Carbon\Carbon;

class OrderExport implements FromView
{
   
    public function __construct(array $data = [], Request $request)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        $from_created_date = $this->data['from_created_date'];
        $to_created_date = $this->data['to_created_date'];
        $status = $this->data['status'];
        

        $query = Order::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('orders.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('orders.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('orders.status', $status);
        }

        $orders = $query->get();

        $order_report_data = [];
        
        if(count($orders)>0){
            foreach($orders as $key => $order_item){
                $order = collect(new OrderResource($order_item));
                $order_report_data[$key] = [
                    'order_number'=>(isset($order['order_number']))?$order['order_number']:'',
                    'customer_phone'=>(isset($order['customer_phone']))?$order['customer_phone']:'',
                    'customer_email'=>(isset($order['customer_email']))?$order['customer_email']:'',
                    'order_level_discount_code'=>(isset($order['order_level_discount_code']))?$order['order_level_discount_code']:'',
                    'order_level_discount_percentage'=>(isset($order['order_level_discount_percentage']))?$order['order_level_discount_percentage']:'',
                    'order_level_discount_amount'=>(isset($order['order_level_discount_amount']))?$order['order_level_discount_amount']:'',
                    'product_level_total_discount'=>(isset($order['product_level_total_discount']))?$order['product_level_total_discount']:'',
                    'order_level_tax_code'=>(isset($order['order_level_tax_code']))?$order['order_level_tax_code']:'',
                    'order_level_tax_percentage'=>(isset($order['order_level_tax_percentage']))?$order['order_level_tax_percentage']:'',
                    'order_level_tax_amount'=>(isset($order['order_level_tax_amount']))?$order['order_level_tax_amount']:'',
                    'order_level_tax_components'=>(isset($order['order_level_tax_components']))?$order['order_level_tax_components']:'',
                    'product_level_total_tax'=>(isset($order['product_level_total_tax']))?$order['product_level_total_tax']:'',
                    'purchase_amount_subtotal_excluding_tax'=>(isset($order['purchase_amount_subtotal_excluding_tax']))?$order['purchase_amount_subtotal_excluding_tax']:'',
                    'sale_amount_subtotal_excluding_tax'=>(isset($order['sale_amount_subtotal_excluding_tax']))?$order['sale_amount_subtotal_excluding_tax']:'',
                    'total_discount_amount'=>(isset($order['total_discount_amount']))?$order['total_discount_amount']:'',
                    'total_after_discount'=>(isset($order['total_after_discount']))?$order['total_after_discount']:'',
                    'total_tax_amount'=>(isset($order['total_tax_amount']))?$order['total_tax_amount']:'',
                    'total_order_amount'=>(isset($order['total_order_amount']))?$order['total_order_amount']:'',
                    'payment_method'=>(isset($order['payment_method']))?$order['payment_method']:'',
                    'status'=>(isset($order['status']['label']))?$order['status']['label']:'',
                    'created_at'=>(isset($order['created_at_label']))?$order['created_at_label']:'',
                    'created_by'=>(isset($order['created_by']['fullname']))?$order['created_by']['fullname']:'',
                    'updated_at'=>(isset($order['updated_at_label']))?$order['updated_at_label']:'',
                    'updated_by'=>(isset($order['updated_by']['fullname']))?$order['updated_by']['fullname']:''
                    
                 ];
            }
        }
        return view('report.exports.order_report', [
            'order_report_data' => $order_report_data
        ]);
    }
}