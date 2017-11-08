<?php
namespace App\Exports;
use App\Models\Discountcode;
use App\Http\Resources\DiscountcodeResource;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;

use Carbon\Carbon;

class DiscountcodeExport implements FromView
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

        $query = Discountcode::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('discount_codes.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('discount_codes.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('discount_codes.status', $status);
        }

        $discount_codes = $query->get();

        $discountcode_report_data = [];
        
        if(count($discount_codes)>0){
            foreach($discount_codes as $key => $discount_code_item){
                $discount_code = collect(new DiscountcodeResource($discount_code_item));
                $discountcode_report_data[$key] = [
                    'discount_code'=>(isset($discount_code['discount_code']))?$discount_code['discount_code']:'',
                    'name'=>(isset($discount_code['label']))?$discount_code['label']:'',
                    'discount_percentage'=>(isset($discount_code['discount_percentage']))?$discount_code['discount_percentage']:'',
                    'description'=>(isset($discount_code['description']))?$discount_code['description']:'',            
                    'status'=>(isset($discount_code['status']['label']))?$discount_code['status']['label']:'',
                    'created_at'=>(isset($discount_code['created_at_label']))?$discount_code['created_at_label']:'',
                    'created_by'=>(isset($discount_code['created_by']['fullname']))?$discount_code['created_by']['fullname']:'',
                    'updated_at'=>(isset($discount_code['updated_at_label']))?$discount_code['updated_at_label']:'',
                    'updated_by'=>(isset($discount_code['updated_by']['fullname']))?$discount_code['updated_by']['fullname']:''
                 ];
            }
        }
        return view('report.exports.discountcode_report', [
            'discountcode_report_data' => $discountcode_report_data
        ]);
    }
}
