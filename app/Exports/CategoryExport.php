<?php
namespace App\Exports;
use App\Models\Order;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;

use Carbon\Carbon;

class CategoryExport implements FromView
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
        

        $query = Category::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('category.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('category.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('category.status', $status);
        }

        $categories = $query->get();
        $category_report_data = [];
        
        if(count($categories)>0){
            foreach($categories as $key => $category_item){
                $category = collect(new CategoryResource($category_item));
                $category_report_data[$key] = [
                    'category_code' => (isset($category['category_code']))?$category['category_code']:'',
                    'label' => (isset($category['label']))?$category['label']:'',
                    'description' =>(isset($category['description']))?$category['description']:'',            
                    'status' =>(isset($category['status']['label']))?$category['status']['label']:'',
                    'created_at_label' =>(isset($category['created_at_label']))?$category['created_at_label']:'',
                    'created_by' =>(isset($category['created_by']['fullname']))?$category['created_by']['fullname']:'',
                    'updated_at_label' =>(isset($category['updated_at_label']))?$category['updated_at_label']:'',
                    'updated_by' =>(isset($category['updated_by']['fullname']))?$category['updated_by']['fullname']:''
                 ];
            }
        }
        return view('report.exports.category_report', [
            'category_report_data' => $category_report_data
        ]);
    }
}

