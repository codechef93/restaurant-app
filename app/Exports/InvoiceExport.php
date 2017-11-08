<?php
namespace App\Exports;
use App\Models\Invoice;
use App\Http\Resources\InvoiceResource;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

use Carbon\Carbon;

class InvoiceExport implements FromView
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
        

        $query = Invoice::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('invoices.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('invoices.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('invoices.status', $status);
        }

        $invoices = $query->get();

        $invoice_report_data = [];
        
        if(count($invoices)>0){
            foreach($invoices as $key => $invoice_item){
                $invoice = collect(new InvoiceResource($invoice_item));
                $invoice_report_data[$key] = [
                    'bill_to'=>(isset($order['bill_to']))?$order['bill_to']:'',
                    'invoice_number'=>(isset($order['invoice_number']))?$order['invoice_number']:'',
                    'invoice_reference'=>(isset($order['invoice_reference']))?$order['invoice_reference']:'',
                    'invoice_date'=>(isset($order['invoice_date']))?$order['invoice_date']:'',
                    'invoice_due_date'=>(isset($order['invoice_due_date']))?$order['invoice_due_date']:'',
                    'bill_to_code'=>(isset($order['bill_to_code']))?$order['bill_to_code']:'',
                    'bill_to_name'=>(isset($order['bill_to_name']))?$order['bill_to_name']:'',
                    'bill_to_contact'=>(isset($order['bill_to_contact']))?$order['bill_to_contact']:'',
                    'bill_to_email'=>(isset($order['bill_to_email']))?$order['bill_to_email']:'',
                    'bill_to_address'=>(isset($order['bill_to_address']))?$order['bill_to_address']:'',
                    'currency_name'=>(isset($order['currency_name']))?$order['currency_name']:'',
                    'currency_code'=>(isset($order['currency_code']))?$order['currency_code']:'',
                    'subtotal_excluding_tax'=>(isset($order['subtotal_excluding_tax']))?$order['subtotal_excluding_tax']:'',
                    'total_discount_amount'=>(isset($order['total_discount_amount']))?$order['total_discount_amount']:'',
                    'total_after_discount'=>(isset($order['total_after_discount']))?$order['total_after_discount']:'',
                    'total_tax_amount'=>(isset($order['total_tax_amount']))?$order['total_tax_amount']:'',
                    'shipping_charge'=>(isset($order['shipping_charge']))?$order['shipping_charge']:'',
                    'packing_charge'=>(isset($order['packing_charge']))?$order['packing_charge']:'',
                    'total_amount'=>(isset($order['total_order_amount']))?$order['total_order_amount']:'',
                    'status'=>(isset($order['status']['label']))?$order['status']['label']:'',
                    'created_at'=>(isset($order['created_at_label']))?$order['created_at_label']:'',
                    'created_by'=>(isset($order['created_by']['fullname']))?$order['created_by']['fullname']:'',
                    'updated_at'=>(isset($order['updated_at_label']))?$order['updated_at_label']:'',
                    'updated_by'=>(isset($order['updated_by']['fullname']))?$order['updated_by']['fullname']:'' 
                 ];
            }
        }
        return view('report.exports.invoice_report', [
            'invoice_report_data' => $invoice_report_data
        ]);
    }
}
