<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Quotation as QuotationModel;
use App\Models\Country as CountryModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\MasterTaxOption as MasterTaxOptionModel;
use App\Models\Store as StoreModel;

use App\Http\Resources\QuotationResource;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

use Mpdf\Mpdf;

class Quotation extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_QUOTATIONS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('quotation.quotations', $data);
    }

    //This is the function that loads the add/edit page
    public function add_quotation($slack = null){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_QUOTATIONS';
        $data['action_key'] = ($slack == null)?'A_ADD_QUOTATION':'A_EDIT_QUOTATION';
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

        $data['quotation_data'] = null;
        if(isset($slack)){
            
            $quotation = QuotationModel::where('slack', '=', $slack)->first();
            if (empty($quotation)) {
                abort(404);
            }
            
            $quotation_data = new QuotationResource($quotation);
            $data['quotation_data'] = $quotation_data;
        }

        return view('quotation.add_quotation', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_QUOTATIONS';
        $data['action_key'] = 'A_DETAIL_QUOTATION';
        check_access([$data['action_key']]);

        $quotation = QuotationModel::where('slack', '=', $slack)->first();
        
        if (empty($quotation)) {
            abort(404);
        }

        $quotation_data = new QuotationResource($quotation);
        
        $data['quotation_data'] = $quotation_data;
        
        $quotation_statuses = [];
        
        if(check_access(['A_EDIT_STATUS_QUOTATION'] ,true)){
            $quotation_statuses = MasterStatusModel::select('label','value_constant')->where([
                ['value_constant', '!=', strtoupper('PENDING')],
                ['key', '=', 'QUOTATION_STATUS'],
                ['status', '=', '1']
            ])->active()->orderBy('value', 'asc')->get();
        }

        $data['quotation_statuses'] = $quotation_statuses;

        $data['delete_quotation_access'] = check_access(['A_DELETE_QUOTATION'] ,true);

        $store_data = StoreModel::select('printnode_enabled')
        ->where([
            ['stores.id', '=', request()->logged_user_store_id]
        ])
        ->active()
        ->first();

        $data['printnode_enabled'] = (isset($store_data->printnode_enabled) && $store_data->printnode_enabled == 1)?true:false;

        return view('quotation.quotation_detail', $data);
    }

    //This is the function that loads the print purchase order page
    public function print_quotation(Request $request, $slack, $type = 'INLINE', $full_path = false){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_QUOTATIONS';
        check_access([$data['sub_menu_key']]);

        $quotation = QuotationModel::where('slack', '=', $slack)->first();
        
        if (empty($quotation)) {
            abort(404);
        }

        $quotation_data = new QuotationResource($quotation);

        $print_logo_path = config("app.invoice_print_logo");
       
        $print_data = view('quotation.invoice.quotation_print', ['data' => json_encode($quotation_data), 'logo_path' => $print_logo_path])->render();

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

        $cache_params = '?='.uniqid();

        $stylesheet = File::get(public_path('css/quotation_print_invoice.css'));
        $mpdf = new Mpdf($mpdf_config);
        $mpdf->SetDisplayMode('real');
        $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->SetHTMLFooter('<div class="footer">Page: {PAGENO}/{nb}</div>');
        $mpdf->WriteHTML($print_data);
        header('Content-Type: application/pdf');

        $filename = 'quotation_'.$quotation_data['quotation_number'].'.pdf';

        Storage::disk('quotation')->delete(
            [
                $filename
            ]
        );

        if($type == 'INLINE'){
            $mpdf->Output($filename.$cache_params, \Mpdf\Output\Destination::INLINE);
        }else{
            $view_path = Config::get('constants.upload.quotation.view_path');
            $upload_dir = Storage::disk('quotation')->getAdapter()->getPathPrefix();

            $mpdf->Output($upload_dir.$filename, \Mpdf\Output\Destination::FILE);

            $download_link = ($full_path == false)?$view_path.$filename.$cache_params:$upload_dir.$filename;
            return $download_link; 
        }
    }
    
}
