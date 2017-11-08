<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\MasterStatus;
use App\Models\PurchaseOrder as PurchaseOrderModel;
use App\Models\Country as CountryModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\MasterTaxOption as MasterTaxOptionModel;
use App\Models\Store as StoreModel;

use App\Http\Resources\PurchaseOrderResource;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

use Mpdf\Mpdf;

class PurchaseOrder extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_PURCHASE_ORDERS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('purchase_order.purchase_orders', $data);
    }

    //This is the function that loads the add/edit page
    public function add_purchase_order($slack = null){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_PURCHASE_ORDERS';
        $data['action_key'] = ($slack == null)?'A_ADD_PURCHASE_ORDER':'A_EDIT_PURCHASE_ORDER';
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

        $data['purchase_order_data'] = null;
        if(isset($slack)){
            
            $purchase_order = PurchaseOrderModel::where('slack', '=', $slack)->first();
            if (empty($purchase_order)) {
                abort(404);
            }
            
            $purchase_order_data = new PurchaseOrderResource($purchase_order);
            $data['purchase_order_data'] = $purchase_order_data;
        }

        return view('purchase_order.add_purchase_order', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_PURCHASE_ORDERS';
        $data['action_key'] = 'A_DETAIL_PURCHASE_ORDER';
        check_access([$data['action_key']]);

        $purchase_order = PurchaseOrderModel::where('slack', '=', $slack)->first();
        
        if (empty($purchase_order)) {
            abort(404);
        }

        $purchase_order_data = new PurchaseOrderResource($purchase_order);
        
        $data['purchase_order_data'] = $purchase_order_data;
        
        $po_statuses = [];
        
        if(check_access(['A_EDIT_STATUS_PURCHASE_ORDER'] ,true)){
            $po_statuses = MasterStatusModel::select('label','value_constant')->where([
                ['value_constant', '!=', strtoupper('CREATED')],
                ['key', '=', 'PURCHASE_ORDER_STATUS'],
                ['status', '=', '1']
            ])->active()->orderBy('value', 'asc')->get();
        }

        $data['po_statuses'] = $po_statuses;

        $data['delete_po_access'] = check_access(['A_DELETE_PURCHASE_ORDER'] ,true);

        $data['create_invoice_from_po_access'] = check_access(['A_CREATE_INVOICE_FROM_PO'] ,true);

        $store_data = StoreModel::select('printnode_enabled')
        ->where([
            ['stores.id', '=', request()->logged_user_store_id]
        ])
        ->active()
        ->first();
        
        $data['printnode_enabled'] = (isset($store_data->printnode_enabled) && $store_data->printnode_enabled == 1)?true:false;

        return view('purchase_order.purchase_order_detail', $data);
    }

    //This is the function that loads the print purchase order page
    public function print_purchase_order(Request $request, $slack, $type = 'INLINE', $full_path = false){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_PURCHASE_ORDERS';
        check_access([$data['sub_menu_key']]);

        $purchase_order = PurchaseOrderModel::where('slack', '=', $slack)->first();
        
        if (empty($purchase_order)) {
            abort(404);
        }

        $purchase_order_data = new PurchaseOrderResource($purchase_order);

        $print_logo_path = config("app.invoice_print_logo");
       
        $print_data = view('purchase_order.invoice.po_print', ['data' => json_encode($purchase_order_data), 'logo_path' => $print_logo_path])->render();

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

        $stylesheet = File::get(public_path('css/purchase_order_print_invoice.css'));
        $mpdf = new Mpdf($mpdf_config);
        $mpdf->SetDisplayMode('real');
        $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->SetHTMLFooter('<div class="footer">Page: {PAGENO}/{nb}</div>');
        $mpdf->WriteHTML($print_data);
        header('Content-Type: application/pdf');
       
        $filename = 'purchase_order_'.$purchase_order_data['po_number'].'.pdf';

        Storage::disk('purchase_order')->delete(
            [
                $filename
            ]
        );

        if($type == 'INLINE'){
            $mpdf->Output($filename.$cache_params, \Mpdf\Output\Destination::INLINE);
        }else{
            $view_path = Config::get('constants.upload.purchase_order.view_path');
            $upload_dir = Storage::disk('purchase_order')->getAdapter()->getPathPrefix();

            $mpdf->Output($upload_dir.$filename, \Mpdf\Output\Destination::FILE);

            $download_link = ($full_path == false)?$view_path.$filename.$cache_params:$upload_dir.$filename;
            return $download_link; 
        }
    }
}
