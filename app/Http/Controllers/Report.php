<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Role as RoleModel;

use App\Models\Supplier as SupplierModel;
use App\Models\Category as CategoryModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\TaxcodeType as TaxcodeTypeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\MasterTransactionType as MasterTransactionTypeModel;
use App\Models\Account as AccountModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Store as StoreModel;

use Mpdf\Mpdf;
use Illuminate\Support\Facades\Storage;

use App\Exports\UserExport;
use App\Exports\ProductExport;
use App\Exports\OrderExport;
use App\Exports\CustomerExport;
use App\Exports\CategoryExport;
use App\Exports\DiscountcodeExport;
use App\Exports\StoreExport;
use App\Exports\SupplierExport;
use App\Exports\TaxcodeExport;
use App\Exports\PurchaseOrderExport;
use App\Exports\InvoiceExport;
use App\Exports\QuotationExport;
use App\Exports\TransactionExport;
use App\Exports\SaleTaxExport;
use App\Exports\BillingCounterExport;

class Report extends Controller
{
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_DOWNLOAD_REPORT';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        //user
        $data['user_statuses'] = MasterStatus::select('value', 'label')->filterByKey('USER_STATUS')->active()->sortValueAsc()->get();

        $data['roles'] = RoleModel::select('slack', 'role_code', 'label')->resolveSuperAdminRole()->active()->sortLabelAsc()->get();

        //product
        $data['product_statuses'] = MasterStatus::select('value', 'label')->filterByKey('PRODUCT_STATUS')->active()->sortValueAsc()->get();

        $data['suppliers'] = SupplierModel::select('slack', 'supplier_code', 'name')->sortNameAsc()->get();

        $data['categories'] = CategoryModel::select('slack', 'category_code', 'label')->sortLabelAsc()->get();

        $data['taxcodes'] = TaxcodeModel::select('slack', 'tax_code', 'label')->sortLabelAsc()->get();

        $data['discountcodes'] = DiscountcodeModel::select('slack', 'discount_code', 'label')->sortLabelAsc()->get();

        //order
        $data['order_statuses'] = MasterStatus::select('value', 'label')->filterByKey('ORDER_STATUS')->active()->sortValueAsc()->get();

        //purchase order
        $data['purchase_order_statuses'] = MasterStatus::select('value', 'label')->filterByKey('PURCHASE_ORDER_STATUS')->active()->sortValueAsc()->get();

        //store
        $data['store_statuses'] = MasterStatus::select('value', 'label')->filterByKey('STORE_STATUS')->active()->sortValueAsc()->get();

        //customer
        $data['customer_statuses'] = MasterStatus::select('value', 'label')->filterByKey('CUSTOMER_STATUS')->active()->sortValueAsc()->get();

        //category
        $data['category_statuses'] = MasterStatus::select('value', 'label')->filterByKey('CATEGORY_STATUS')->active()->sortValueAsc()->get();

        //supplier
        $data['supplier_statuses'] = MasterStatus::select('value', 'label')->filterByKey('SUPPLIER_STATUS')->active()->sortValueAsc()->get();

        //tax code
        $data['taxcode_statuses'] = MasterStatus::select('value', 'label')->filterByKey('TAX_CODE_STATUS')->active()->sortValueAsc()->get();

        //discount code
        $data['discountcode_statuses'] = MasterStatus::select('value', 'label')->filterByKey('DISCOUNTCODE_STATUS')->active()->sortValueAsc()->get();
        
        //invoice code
        $data['invoice_statuses'] = MasterStatus::select('value', 'label')->filterByKey('INVOICE_STATUS')->active()->sortValueAsc()->get();

        //quotation code
        $data['quotation_statuses'] = MasterStatus::select('value', 'label')->filterByKey('QUOTATION_STATUS')->active()->sortValueAsc()->get();

        //transaction
        $data['transaction_types'] = MasterTransactionTypeModel::select('transaction_type_constant', 'label')->active()->get();
        $data['accounts'] = AccountModel::select('accounts.slack', 'accounts.label', 'master_account_type.label as account_type_label')->masterAccountTypeJoin()->active()->get();
        $data['payment_methods'] = PaymentMethodModel::select('slack', 'label')->active()->get();

        return view('report.report', $data);
    }

    
    public function best_seller_report(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_BEST_SELLER';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        return view('report.best_seller_report', $data);
    }

    public function day_wise_sale_report(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_DAY_WISE_SALE';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        return view('report.day_wise_sale_report', $data);
    }

    public function catgeory_report(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_CATEGORY_REPORT';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $data['store'] = StoreModel::select('currency_name', 'currency_code')
        ->where('id', $request->logged_user_store_id)
        ->first();

        return view('report.catgeory_report', $data);
    }

    public function product_quantity_alert(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_PRODUCT_QUANTITY_ALERT';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        return view('report.product_quantity_alert', $data);
    }

    public function store_stock_chart(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_STORE_STOCK_CHART';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $data['store'] = StoreModel::select('currency_name', 'currency_code')
        ->where('id', $request->logged_user_store_id)
        ->first();
        
        return view('report.store_stock_chart', $data);
    }

    // public function print_report(Request $request, $slack, $type='INLINE', $full_path=false) {
    public function print_report(Request $request, $params, $type) {
        switch ($type){
            case "sale_tax_report":
                $export_view =  new SaleTaxExport(
                    $params,
                    $request
                );
                break;
            case "billing_counter_report":
                $export_view =  new BillingCounterExport(
                    $params,
                    $request
                );
                break;
            case "category_report":
                $export_view =  new CategoryExport(
                    $params,
                    $request
                );
                break;
            case "user_report":
                $export_view =  new UserExport(
                    $params,
                    $request
                );
                break;
            case "product_report":
                $export_view =  new ProductExport(
                    $params,
                    $request
                );
                break;
            case "order_report":
                $export_view =  new OrderExport(
                    $params,
                    $request
                );
                break;
            case "customer_report":
                $export_view =  new CustomerExport(
                    $params,
                    $request
                );
                break;
            case "discountcode_report":
                $export_view =  new DiscountcodeExport(
                    $params,
                    $request
                );
                break;
            case "supplier_report":
                $export_view =  new SupplierExport(
                    $params,
                    $request
                );
                break;
            case "taxcode_report":
                $export_view =  new TaxcodeExport(
                    $params,
                    $request
                );
                break;
            case "purchase_order_report":
                $export_view =  new PurchaseOrderExport(
                    $params,
                    $request
                );
                break;
            case "invoice_report":
                $export_view =  new InvoiceExport(
                    $params,
                    $request
                );
                break;
            case "quotation_report":
                $export_view =  new QuotationExport(
                    $params,
                    $request
                );
                break;
            case "transaction_report":
                $export_view =  new TransactionExport(
                    $params,
                    $request
                );
                break;
            default:
                break;
        }        

        $print_data = $export_view->view()->render();

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

        $mpdf = new Mpdf($mpdf_config);
        $mpdf->SetDisplayMode('real');
        $mpdf->SetHTMLFooter('<div class="footer">Page: {PAGENO}/{nb}</div>');
        $mpdf->WriteHTML($print_data);
        header('Content-Type: application/pdf');

        $filename = $type.'_'.date('Y_m_d_h_i_s').uniqid().'.pdf';

        $upload_dir = Storage::disk('reports')->getAdapter()->getPathPrefix();

        $mpdf->Output($upload_dir.$filename, \Mpdf\Output\Destination::FILE);
        return $filename;
    }
}
