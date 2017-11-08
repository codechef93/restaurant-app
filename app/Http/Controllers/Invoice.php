<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

use App\Models\Invoice as InvoiceModel;
use App\Models\Country as CountryModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\MasterTransactionType as MasterTransactionTypeModel;
use App\Models\Account as AccountModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Store as StoreModel;
use App\Models\MasterTaxOption as MasterTaxOptionModel;

use App\Http\Resources\InvoiceResource;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

use Mpdf\Mpdf;

class Invoice extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_INVOICES';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('invoice.invoices', $data);
    }

    //This is the function that loads the add/edit page
    public function add_invoice($slack = null){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_INVOICES';
        $data['action_key'] = ($slack == null)?'A_ADD_INVOICE':'A_EDIT_INVOICE';
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

        $data['invoice_data'] = null;
        if(isset($slack)){
            
            $invoice = InvoiceModel::where('slack', '=', $slack)->first();
            if (empty($invoice)) {
                abort(404);
            }
            
            $invoice_data = new InvoiceResource($invoice);
            $data['invoice_data'] = $invoice_data;
        }

        return view('invoice.add_invoice', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_INVOICES';
        $data['action_key'] = 'A_DETAIL_INVOICE';
        check_access([$data['action_key']]);

        $invoice = InvoiceModel::where('slack', '=', $slack)->first();
        
        if (empty($invoice)) {
            abort(404);
        }

        $data['transaction_type'] = MasterTransactionTypeModel::select('transaction_type_constant', 'label')
        ->active()
        ->get();

        $income_transaction_type_data = MasterTransactionTypeModel::select('transaction_type_constant')
        ->where('transaction_type_constant', '=', trim('INCOME'))
        ->first();

        $expense_transaction_type_data = MasterTransactionTypeModel::select('transaction_type_constant')
        ->where('transaction_type_constant', '=', trim('EXPENSE'))
        ->first();

        $data['default_transaction_type'] = (isset($invoice->parent_po_id) && $invoice->parent_po_id != '')?$expense_transaction_type_data->transaction_type_constant:$income_transaction_type_data->transaction_type_constant;

        $data['accounts'] = AccountModel::select('accounts.slack', 'accounts.label', 'master_account_type.label as account_type_label')
        ->masterAccountTypeJoin()
        ->active()
        ->get();

        $data['payment_methods'] = PaymentMethodModel::select('slack', 'label')
        ->active()
        ->skipPaymentGateway()
        ->get();

        $store_data = StoreModel::select('currency_name', 'currency_code', 'printnode_enabled')
        ->where([
            ['stores.id', '=', request()->logged_user_store_id]
        ])
        ->active()
        ->first();

        $invoice_data = new InvoiceResource($invoice);
        
        $data['invoice_data'] = $invoice_data;
        
        $invoice_statuses = [];
        
        if(check_access(['A_EDIT_STATUS_INVOICE'] ,true)){
            $invoice_statuses = MasterStatusModel::select('label','value_constant')->where([
                ['value_constant', '!=', strtoupper('NEW')],
                ['key', '=', 'INVOICE_STATUS'],
                ['status', '=', '1']
            ])->active()->orderBy('value', 'asc')->get();
        }

        $data['invoice_statuses'] = $invoice_statuses;

        $data['currency_codes'] = [
            'store_currency' => $store_data->currency_code,
            'invoice_currency' => $invoice_data->currency_code
        ];

        $data['delete_invoice_access'] = check_access(['A_DELETE_INVOICE'] ,true);

        $data['make_payment_access'] = check_access(['A_MAKE_PAYMENT_INVOICE'] ,true);

        $data['printnode_enabled'] = (isset($store_data->printnode_enabled) && $store_data->printnode_enabled == 1)?true:false;

        return view('invoice.invoice_detail', $data);
    }

    //This is the function that loads the print purchase order page
    public function print_invoice(Request $request, $slack, $type = 'INLINE', $full_path = false){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_INVOICES';
        check_access([$data['sub_menu_key']]);

        $invoice = InvoiceModel::where('slack', '=', $slack)->first();
        
        if (empty($invoice)) {
            abort(404);
        }

        $invoice_data = new InvoiceResource($invoice);

        $print_logo_path = config("app.invoice_print_logo");
       
        $print_data = view('invoice.invoice.invoice_print', ['data' => json_encode($invoice_data), 'logo_path' => $print_logo_path])->render();

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

        $stylesheet = File::get(public_path('css/invoice_print_invoice.css'));
        $mpdf = new Mpdf($mpdf_config);
        $mpdf->SetDisplayMode('real');
        $mpdf->WriteHTML($stylesheet,\Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->SetHTMLFooter('<div class="footer">Page: {PAGENO}/{nb}</div>');
        $mpdf->WriteHTML($print_data);
        header('Content-Type: application/pdf');

        $filename = 'invoice_'.$invoice_data['invoice_number'].'.pdf';

        Storage::disk('invoice')->delete(
            [
                $filename
            ]
        );

        if($type == 'INLINE'){
            $mpdf->Output($filename.$cache_params, \Mpdf\Output\Destination::INLINE);
        }else{
            $view_path = Config::get('constants.upload.invoice.view_path');
            $upload_dir = Storage::disk('invoice')->getAdapter()->getPathPrefix();

            $mpdf->Output($upload_dir.$filename, \Mpdf\Output\Destination::FILE);

            $download_link = ($full_path == false)?$view_path.$filename.$cache_params:$upload_dir.$filename;
            return $download_link; 
        }
    }
    
}
