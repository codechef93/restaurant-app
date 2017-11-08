<?php
namespace App\Exports;
use App\Models\PurchaseOrder;
use App\Http\Resources\PurchaseOrderResource;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

use Carbon\Carbon;

class PurchaseOrderExport implements FromView
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
        

        $query = PurchaseOrder::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('purchase_orders.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('purchase_orders.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('purchase_orders.status', $status);
        }

        $purchase_orders = $query->get();

        $purchase_order_report_data = [];
        
        if(count($purchase_orders)>0){
            foreach($purchase_orders as $key => $purchase_order_item){
                $purchase_order = collect(new PurchaseOrderResource($purchase_order_item));
                $purchase_order_report_data[$key] = [
                    'po_number'=>(isset($order['po_number']))?$order['po_number']:'',
                    'po_reference'=>(isset($order['po_reference']))?$order['po_reference']:'',
                    'order_date'=>(isset($order['order_date']))?$order['order_date']:'',
                    'order_due_date'=>(isset($order['order_due_date']))?$order['order_due_date']:'',
                    'supplier_code'=>(isset($order['supplier_code']))?$order['supplier_code']:'',
                    'supplier_name'=>(isset($order['supplier_name']))?$order['supplier_name']:'',
                    'supplier_address'=>(isset($order['supplier_address']))?$order['supplier_address']:'',
                    'currency_name'=>(isset($order['currency_name']))?$order['currency_name']:'',
                    'currency_code'=>(isset($order['currency_code']))?$order['currency_code']:'',
                    'subtotal_excluding_tax'=>(isset($order['subtotal_excluding_tax']))?$order['subtotal_excluding_tax']:'',
                    'total_discount_amount'=>(isset($order['total_discount_amount']))?$order['total_discount_amount']:'',
                    'total_after_discount'=>(isset($order['total_after_discount']))?$order['total_after_discount']:'',
                    'total_tax_amount'=>(isset($order['total_tax_amount']))?$order['total_tax_amount']:'',
                    'shipping_charge'=>(isset($order['shipping_charge']))?$order['shipping_charge']:'',
                    'packing_charge'=>(isset($order['packing_charge']))?$order['packing_charge']:'',
                    'total_order_amount'=>(isset($order['total_order_amount']))?$order['total_order_amount']:'',
                    'status'=>(isset($order['status']['label']))?$order['status']['label']:'',
                    'created_at'=>(isset($order['created_at_label']))?$order['created_at_label']:'',
                    'created_by'=>(isset($order['created_by']['fullname']))?$order['created_by']['fullname']:'',
                    'updated_at'=>(isset($order['updated_at_label']))?$order['updated_at_label']:'',
                    'updated_by'=>(isset($order['updated_by']['fullname']))?$order['updated_by']['fullname']:''
                 ];
            }
        }
        return view('report.exports.purchase_order_report', [
            'purchase_order_report_data' => $purchase_order_report_data
        ]);
    }
}

            
