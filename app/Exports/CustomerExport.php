<?php
namespace App\Exports;
use App\Models\Customer;
use App\Http\Resources\CustomerResource;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

use Carbon\Carbon;

class CustomerExport implements FromView
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

        $query = Customer::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('customers.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('customers.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('customers.status', $status);
        }

        $customers = $query->get();

        $customer_report_data = [];
        
        if(count($customers)>0){
            foreach($customers as $key => $customer_item){
                $customer = collect(new CustomerResource($customer_item));
                $customer_report_data[$key] = [
                    'name'=>(isset($customer['name']))?$customer['name']:'',
                    'email'=>(isset($customer['email']))?$customer['email']:'',
                    'phone'=>(isset($customer['phone']))?$customer['phone']:'',
                    'address'=>(isset($customer['address']))?$customer['address']:'',            
                    'status'=>(isset($customer['status']['label']))?$customer['status']['label']:'',
                    'created_at'=>(isset($customer['created_at_label']))?$customer['created_at_label']:'',
                    'created_by'=>(isset($customer['created_by']['fullname']))?$customer['created_by']['fullname']:'',
                    'updated_at'=>(isset($customer['updated_at_label']))?$customer['updated_at_label']:'',
                    'updated_by'=>(isset($customer['updated_by']['fullname']))?$customer['updated_by']['fullname']:''
                 ];
            }
        }
        return view('report.exports.customer_report', [
            'customer_report_data' => $customer_report_data
        ]);
    }
}
