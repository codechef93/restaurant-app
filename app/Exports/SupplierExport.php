<?php
namespace App\Exports;
use App\Models\Supplier;
use App\Http\Resources\SupplierResource;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

use Carbon\Carbon;

class SupplierExport implements FromView
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
        

        $query = Supplier::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('suppliers.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('suppliers.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('suppliers.status', $status);
        }

        $suppliers = $query->get();

        $supplier_report_data = [];
        
        if(count($suppliers)>0){
            foreach($suppliers as $key => $supplier_item){
                $supplier = collect(new SupplierResource($supplier_item));
                $supplier_report_data[$key] = [
                    'supplier_code'=>(isset($supplier['supplier_code']))?$supplier['supplier_code']:'',
                    'name'=>(isset($supplier['name']))?$supplier['name']:'',
                    'email'=>(isset($supplier['email']))?$supplier['email']:'',
                    'phone'=>(isset($supplier['phone']))?$supplier['phone']:'',
                    'address'=>(isset($supplier['address']))?$supplier['address']:'',
                    'pincode'=>(isset($supplier['pincode']))?$supplier['pincode']:'',
                    'status'=>(isset($supplier['status']['label']))?$supplier['status']['label']:'',
                    'created_at'=>(isset($supplier['created_at_label']))?$supplier['created_at_label']:'',
                    'created_by'=>(isset($supplier['created_by']['fullname']))?$supplier['created_by']['fullname']:'',
                    'updated_at'=>(isset($supplier['updated_at_label']))?$supplier['updated_at_label']:'',
                    'updated_by'=>(isset($supplier['updated_by']['fullname']))?$supplier['updated_by']['fullname']:''
                    
                 ];
            }
        }
        return view('report.exports.supplier_report', [
            'supplier_report_data' => $supplier_report_data
        ]);
    }
}
