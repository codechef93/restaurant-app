<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\StockReturn as StockReturnModel;
use App\Models\Country as CountryModel;
use App\Models\MasterTaxOption as MasterTaxOptionModel;

use App\Http\Resources\StockReturnResource;

use Mpdf\Mpdf;

class StockReturn extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_RETURNS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('stock_return.stock_returns', $data);
    }

    //This is the function that loads the add/edit page
    public function add_stock_return($slack = null){
        
        //check access
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_RETURNS';
        $data['action_key'] = ($slack == null)?'A_ADD_STOCK_RETURN':'A_EDIT_STOCK_RETURN';
        check_access(array($data['action_key']));

        $data['currency_list'] = CountryModel::select('currency_code', 'currency_name')
        ->where('currency_code', '!=', '')
        ->whereNotNull('currency_code')
        ->active()
        ->groupBy('currency_code')
        ->get();

        $data['tax_options'] = MasterTaxOptionModel::select('tax_option_constant', 'label')
        ->active()
        ->get();

        $data['stock_return_data'] = null;
        if(isset($slack)){
            
            $stock_return_data = StockReturnModel::where('slack', '=', $slack)->first();
            if (empty($stock_return_data)) {
                abort(404);
            }
            
            $stock_return_data = new StockReturnResource($stock_return_data);
            $data['stock_return_data'] = $stock_return_data;
        }

        return view('stock_return.add_stock_return', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_RETURNS';
        $data['action_key'] = 'A_DETAIL_STOCK_RETURN';
        check_access([$data['action_key']]);

        $stock_return = StockReturnModel::where('slack', '=', $slack)->first();
        
        if (empty($stock_return)) {
            abort(404);
        }

        $stock_data = new StockReturnResource($stock_return);
        
        $data['stock_return_data'] = $stock_data;

        $data['delete_stock_return_access'] = check_access(['A_DELETE_STOCK_RETURN'] ,true);

        return view('stock_return.stock_return_detail', $data);
    }

    //This is the function that loads the print purchase order page
    public function print_stock_return(Request $request, $slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_RETURNS';
        check_access([$data['sub_menu_key']]);

        $stock_return = StockReturnModel::where('slack', '=', $slack)->first();
        
        if (empty($stock_return)) {
            abort(404);
        }

        $stock_return_data = new StockReturnResource($stock_return);

        $print_logo_path = config("app.invoice_print_logo");
       
        $print_data = view('stock_return.invoice.stock_return_print', ['data' => json_encode($stock_return_data), 'logo_path' => $print_logo_path])->render();

        $mpdf_config = [
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'orientation'   => 'P',
            'margin_left'   => 7,
            'margin_right'  => 7,
            'margin_top'    => 7,
            'margin_bottom' => 7,
            'tempDir' => storage_path()."/pdf_temp" 
        ];

        $stylesheet = File::get(public_path('css/invoice_print_invoice.css'));
        $mpdf = new Mpdf($mpdf_config);
        $mpdf->SetDisplayMode('real');
        $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->SetHTMLFooter('<div class="footer">Page: {PAGENO}/{nb}</div>');
        $mpdf->WriteHTML($print_data);
        header('Content-Type: application/pdf'); 
        $mpdf->Output('stock_return_'.$stock_return_data['return_number'].'.pdf', \Mpdf\Output\Destination::INLINE);
    }

}
