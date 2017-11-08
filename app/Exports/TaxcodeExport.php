<?php
namespace App\Exports;
use App\Models\Taxcode;
use App\Http\Resources\TaxcodeResource;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

use Carbon\Carbon;

class TaxcodeExport implements FromView
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

        $query = Taxcode::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('tax_codes.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('tax_codes.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('tax_codes.status', $status);
        }

        $tax_codes = $query->get();
        $taxcode_report_data = [];
        
        if(count($tax_codes)>0){
            foreach($tax_codes as $key => $tax_code_item){
                $tax_code = collect(new TaxcodeResource($tax_code_item));
                $taxcode_report_data[$key] = [
                    'tax_code'=>(isset($tax_code['tax_code']))?$tax_code['tax_code']:'',
                    'name'=>(isset($tax_code['label']))?$tax_code['label']:'',
                    'tax_percentage'=>(isset($tax_code['total_tax_percentage']))?$tax_code['total_tax_percentage']:'',
                    'description'=>(isset($tax_code['description']))?$tax_code['description']:'',            
                    'status'=>(isset($tax_code['status']['label']))?$tax_code['status']['label']:'',
                    'created_at'=>(isset($tax_code['created_at_label']))?$tax_code['created_at_label']:'',
                    'created_by'=>(isset($tax_code['created_by']['fullname']))?$tax_code['created_by']['fullname']:'',
                    'updated_at'=>(isset($tax_code['updated_at_label']))?$tax_code['updated_at_label']:'',
                    'updated_by'=>(isset($tax_code['updated_by']['fullname']))?$tax_code['updated_by']['fullname']:''
                 ];
            }
        }
        return view('report.exports.taxcode_report', [
            'taxcode_report_data' => $taxcode_report_data
        ]);
    }
}
