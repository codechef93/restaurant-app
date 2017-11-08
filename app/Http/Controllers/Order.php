<?php

namespace App\Http\Controllers;


use App\Http\Resources\OrderResource;
use App\Http\Resources\SettingAppResource;
use App\Http\Resources\UserResource;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

use App\Models\Order as OrderModel;
use App\Models\OrderProduct as OrderProductModel;
use App\Models\Customer as CustomerModel;
use App\Models\Store as StoreModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Category as CategoryModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\Account as AccountModel;
use App\Models\MasterOrderType as MasterOrderTypeModel;
use App\Models\Table as TableModel;
use App\Models\BusinessRegister as BusinessRegisterModel;
use App\Models\MasterBillingType as MasterBillingTypeModel;
use App\Models\Role as RoleModel;
use App\Models\SettingApp as SettingAppModel;
use App\Models\KeyboardShortcut as KeyboardShortcutModel;
use App\Models\User as UserModel;

use Mpdf\Mpdf;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;

use Razorpay\Api\Api;

class Order extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_POS_ORDERS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('order.orders', $data);
    }

    //This is the function that loads the add/edit page
    public function add_order(Request $request, $slack_temp = null){
        //check access

        $slack = null;
        $table_id = null;

        if(isset($slack_temp)) {
            if(strpos($slack_temp, 'area_') !== false) {
                $table_id = intval(trim(str_replace('area_','',$slack_temp)));
            } else {
                $slack = $slack_temp;
            }
        }

        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_POS_ORDERS';
        $data['action_key'] = ($slack == null)?'A_ADD_ORDER':'A_EDIT_ORDER';
        check_access(array($data['action_key']));

        $business_register_data = BusinessRegisterModel::select('slack', 'parent_register_id')
        ->where('user_id', '=', trim($request->logged_user_id))
        ->whereNull('closing_date')
        ->first();

        if(empty($business_register_data)){
            return redirect('add_business_register');
        }
        
        $data['parent_register'] = ($business_register_data->parent_register_id == '')?true:false;

        $data['new_order_link'] = route('add_order');

        $data['store_tax_percentage'] = null;
        $data['store_discount_percentage'] = null;
        $data['enable_customer_detail_popup'] = true;

        $store_data = StoreModel::select('id', 'tax_code_id', 'discount_code_id', 'currency_code', 'restaurant_billing_type_id', 'restaurant_waiter_role_id', 'enable_customer_popup', 'enable_variants_popup')
        ->where([
            ['id', '=', $request->logged_user_store_id],
            ['status', '=', 1]
        ])
        ->first();
        if (empty($store_data)) {
            return redirect('select_store');
        }

        $data['store_currency'] = $store_data->currency_code;

        if(isset($store_data->tax_code_id)){
            $taxcode_data = TaxcodeModel::select('total_tax_percentage')
            ->where('id', '=', $store_data->tax_code_id)
            ->active()
            ->first();
            $data['store_tax_percentage'] = (isset($taxcode_data->total_tax_percentage))?$taxcode_data->total_tax_percentage:0.00;
        }

        if(isset($store_data->discount_code_id)){
            $discountcode_data = DiscountcodeModel::select('discount_percentage')
            ->where('id', '=', trim($store_data->discount_code_id))
            ->active()
            ->first();
            $data['store_discount_percentage'] = (isset($discountcode_data->discount_percentage))?$discountcode_data->discount_percentage:0.00;
        }

        $data['enable_customer_detail_popup'] = ($store_data->enable_customer_popup == 0)?false:true;
        $data['enable_vairants_popup'] = ($store_data->enable_variants_popup == 0)?false:true;

        // $categories = CategoryModel::select('slack', 'category_code', 'label')->showPosScreen()->sortLabelAsc()->get();
        $categories = CategoryModel::select('slack', 'category_code', 'label')->where('status','=',1)->showPosScreen()->sortCategoryOrderAsc()->get();
        $data['categories'] = (!empty($categories))?$categories:[];

        $payment_methods = PaymentMethodModel::select('slack', 'label')
        ->active()
        ->get();
        $data['payment_methods'] = (!empty($payment_methods))?$payment_methods:[];

        $default_business_account = AccountModel::select('slack', 'account_code', 'label')
        ->where('pos_default', '=', 1)
        ->active()
        ->first();
        $data['default_business_account'] = $default_business_account;

        $business_accounts = AccountModel::select('accounts.slack', 'accounts.account_code', 'accounts.label', 'master_account_type.label as account_type_label')
        ->masterAccountTypeJoin()
        ->active()
        ->get();
        $data['business_accounts'] = (!empty($business_accounts))?$business_accounts:[];

        $data['store_restaurant_mode'] = ($request->logged_user_store_restaurant_mode == 1)?true:false;

        $data['customer_status'] = MasterStatus::select('value', 'label')->filterByKey('CUSTOMER_STATUS')->where('value_constant', 'ACTIVE')->active()->first();

        $data['restaurant_order_types'] = null;
        $data['vacant_tables'] = null;
        $data['billing_types']  = null;
        $data['store_billing_type'] = null;
        $data['store_waiter_role_slack'] = null;
        $data['waiters'] = null;
        $data['order_area_data'] = null;

        if($request->logged_user_store_restaurant_mode == true){

            $data['restaurant_order_types'] = MasterOrderTypeModel::select('order_type_constant', 'label')->where('restaurant', 1)->active()->get();
            $tables = TableModel::select('id', 'slack', 'table_number', 'no_of_occupants')
            ->active()
            ->get();
            $data['vacant_tables'] = $tables;

            $data['billing_types'] = MasterBillingTypeModel::select('billing_type_constant', 'label')
            ->active()
            ->get();

            if($store_data->restaurant_billing_type_id != ''){
                $store_billing_type = MasterBillingTypeModel::select('billing_type_constant')
                ->where('id', '=', $store_data->restaurant_billing_type_id)
                ->active()
                ->first();

                $data['store_billing_type'] = (!empty($store_billing_type))?$store_billing_type->billing_type_constant:'FINE_DINE';
            }

            if ($store_data->restaurant_waiter_role_id != ''){
                $waiter_role_id = RoleModel::select('id')
                ->where('id', '=', $store_data->restaurant_waiter_role_id)
                ->active()
                ->first();
                
                $user_list = UserModel::select('*', 'user_stores.id as user_store_access')
                ->hideSuperAdminRole()
                ->userStoreAccessData()
                ->active()
                ->where('role_id', '=', $waiter_role_id->id)
                ->where('user_stores.store_id', $store_data->id)
                ->whereNotNull('user_stores.id')
                ->groupBy('users.id')
                ->get();
                
                $users = UserResource::collection($user_list);
                $data['waiters'] = $users;
            }
        }

        $data['order_data'] = null;
        if(isset($slack)){
            $order = OrderModel::where('slack', $slack)
            ->first();

            $order_data = new OrderResource($order);
            // $order_data = $this->get_order_data($slack);

            $order_status = MasterStatusModel::select('value_constant')->where([
                ['key', '=', 'ORDER_STATUS'],
                ['value', '=', $order->status],
            ])
            ->first();

            $order_data_decoded = json_decode(json_encode($order_data, true));
            
            $data['order_data']['slack'] = $order->slack;
            
            $data['order_data']['order'] = [
                'additional_note'=> $order->additional_note,
                'order_number' => $order->order_number,
                'store_level_total_tax_percentage' => $order->store_level_total_tax_percentage,
                'store_level_total_discount_percentage' => $order->store_level_total_discount_percentage,
                'sub_total' => $order->sale_amount_subtotal_excluding_tax,
                'tax_total' => $order->total_tax_amount,
                'total' => $order->total_order_amount,
                'contact_number' => $order->contact_number,
                'address' => $order->address,
                'payment_method' => $order->payment_method_slack,
                'current_status' => $order_status,
                'restaurant_mode' => $order->restaurant_mode,
                'order_type' => ($order_data->order_type_data != null)?$order_data->order_type_data->order_type_constant:'',
                'table' => ($order_data->restaurant_table_data != null)?$order_data->restaurant_table_data->slack:'',
                'waiter' => ($order_data_decoded->waiter_data != null)?$order_data_decoded->waiter_data->slack:'',
                'waiter_name' => ($order_data_decoded->waiter_data != null)?$order_data_decoded->waiter_data->fullname:'',
                'billing_type' => ($order_data_decoded->billing_type_data != null)?$order_data_decoded->billing_type_data->billing_type_constant:'',
                'additional_discount_percentage' => ($order_data_decoded->additional_discount_percentage != null)?$order_data_decoded->additional_discount_percentage:0,
                'customer' => ($order_data_decoded->customer_formatted != null)?$order_data_decoded->customer_formatted:'',
            ];

            $order_products = $order_data_decoded->products;
            $cart = [];
            // $cart = $order_products;
            if(count($order_products)>0){
                foreach($order_products as $order_product){
                    if($order_product->parent_order_product == false){
                        continue;
                    }
                    
                    $cart_item_key = $order_product->product_slack;

                    if(!empty($order_product->addon_products)){
                        $order_addon_product_slack_array = Arr::pluck($order_product->addon_products, 'product_slack');
                        sort($order_addon_product_slack_array);
                        $cart_item_key = $cart_item_key.'_'.implode('_', $order_addon_product_slack_array);
                    }
                    
                    if(isset($cart[$cart_item_key])) {
                        $quantity1 = $cart[$cart_item_key]['quantity'];
                        $cart[$cart_item_key]['quantity'] = floatval($quantity1) + floatval($order_product->quantity);
                    }
                    else{
                        $cart[$cart_item_key] = [
                            "product_slack"     => $order_product->product_slack,
                            "product_code"      => $order_product->product_code,
                            "name"              => $order_product->name,
                            "price"             => $order_product->price,
                            "quantity"          => $order_product->quantity,
                            "tax_percentage"    => $order_product->tax_percentage,
                            "discount_percentage" => $order_product->discount_percentage,
                            "total_price"       => $order_product->total_price,
                            "product"           => $order_product->product,
                            "customizable"      => $order_product->product->customizable?1:0
                        ];
                    }
                    if(!empty($order_product->addon_products)){
                        foreach($order_product->addon_products as $addon_product_data){
                            if(isset($cart[$cart_item_key]['selected_addon_products'][$addon_product_data->product_slack])) {
                                $addon_quantity1 = $cart[$cart_item_key]['selected_addon_products'][$addon_product_data->product_slack]['quantity'];
                                $cart[$cart_item_key]['selected_addon_products'][$addon_product_data->product_slack]['quantity'] = floatval($addon_product_data->quantity) + floatval($addon_quantity1);
                            } else {
                                $cart[$cart_item_key]['selected_addon_products'][$addon_product_data->product_slack] = [
                                    "product_slack"     => $addon_product_data->product_slack,
                                    "product_code"      => $addon_product_data->product_code,
                                    "name"              => $addon_product_data->name,
                                    "price"             => $addon_product_data->price,
                                    "quantity"          => $addon_product_data->quantity,
                                    "tax_percentage"    => $addon_product_data->tax_percentage,
                                    "discount_percentage" => $addon_product_data->discount_percentage,
                                    "total_price"       => $addon_product_data->total_price
                                ];
                            }
                        }
                    }
                }
            }
            $data['order_data']['cart'] = json_encode($cart);
        } else if(isset($table_id)) {
            $restaurant_table = TableModel::where('id', $table_id)->first();
            if(isset($restaurant_table)) {
                $data['order_area_data']['order_type'] = 'DINEIN';
                $data['order_area_data']['table'] = $restaurant_table->slack;
            }
        }

        $data['keyboard_shortcuts'] = KeyboardShortcutModel::select('keyboard_constant', 'keyboard_shortcut', 'keyboard_shortcut_label', 'description')
        ->active()
        ->sortAsc()
        ->get();

        $keyboard_shortcuts_formatted = $data['keyboard_shortcuts']->mapWithKeys(function ($item) {
            $keyboard_shortcut = null;
            $shortcuts = explode(',', $item['keyboard_shortcut']);
            if(count($shortcuts)>1){
                $keyboard_shortcut = $shortcuts;
            }else{
                $keyboard_shortcut =  $shortcuts;
            }
            return [$item['keyboard_constant'] => $keyboard_shortcut];
        });
        $data['keyboard_shortcuts_formatted'] = $keyboard_shortcuts_formatted;

        return view('order.add_order', $data);
    }

    //This is the function that loads the detail page
    public function detail(Request $request, $slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_POS_ORDERS';
        $data['action_key'] = 'A_DETAIL_ORDER';
        check_access([$data['action_key']]);

        $order_data = $this->get_order_data($slack);

        $data['logged_user_id'] = $request->logged_user_id;
        
        $data['order_data'] = $order_data;

        $data['print_order_link'] = route('print_order', ['slack' => $slack]);

        $data['delete_order_access'] = check_access(['A_DELETE_ORDER'], true);

        $data['share_invoice_sms_access'] = check_access(['A_SHARE_INVOICE_SMS'], true);

        $data['merge_order_access'] = check_access(['A_MERGE_ORDER'], true);

        $data['unmerge_order_access'] = check_access(['A_UNMERGE_ORDER'], true);

        $data['print_kot_link'] = (isset($order_data['restaurant_mode']) && $order_data['restaurant_mode'] == 1)?route('print_kot', ['slack' => $slack]):'';
        
        $data['printnode_enabled'] = (isset($order_data['store']['printnode_enabled']) && $order_data['store']['printnode_enabled'] == 1)?true:false;

        return view('order.order_detail', $data);
    }

    //This is the function that loads the print order page
    public function print_order(Request $request, $slack, $type = 'INLINE', $full_path = false){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_POS_ORDERS';
        check_access([$data['sub_menu_key']]);
        
        $order_data = $this->get_order_data($slack);
        if(in_array($order_data['status']['value'], [1, 5] ) != 1){
            abort(404);
        }
        $invoice_print_type = $order_data['store']['invoice_type'];
        
        switch($invoice_print_type){
            case 'A4':
                $view_file = 'order.invoice.a4_print';
                $css_file = 'css/order_a4_print_invoice.css';
                $format = 'A4';
                $print_logo_path = config("app.invoice_print_logo");
            break;
            case 'SMALL':
                $view_file = 'order.invoice.thermal_print';
                $css_file = 'css/order_thermal_print_invoice.css';
                $format = [80, 297];
                $print_logo_path = '';
            break;
            case 'SMALL_LITE':
                $view_file = 'order.invoice.thermal_print_lite';
                $css_file = 'css/order_thermal_print_invoice.css';
                $format = [80, 297];
                $print_logo_path = '';
            break;
            case 'SMALL_V2':
                $view_file = 'order.invoice.thermal_print_v2';
                $css_file = 'css/order_thermal_print_invoice.css';
                $format = [84, 297];
                $print_logo_path = '';
            break;
            default:
                $view_file = 'order.invoice.thermal_print';
                $css_file = 'css/order_thermal_print_invoice.css';
                $format = [80, 297];
                $print_logo_path = '';
            break;
        }

        $print_data = view($view_file, ['data' => json_encode($order_data), 'logo_path' => $print_logo_path])->render();

        $mpdf_config = [
            'mode'          => 'utf-8',
            'format'        => $format,
            'orientation'   => 'P',
            'margin_left'   => 3,
            'margin_right'  => 3,
            'margin_top'    => 3,
            'margin_bottom' => 3,
            'tempDir' => storage_path()."/pdf_temp" 
        ];
        
        $stylesheet = File::get(public_path($css_file));
        $mpdf = new Mpdf($mpdf_config);
        $mpdf->SetDisplayMode('real');
        $mpdf->showImageErrors = true;
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($print_data);

        $filename = 'order_'.$order_data['order_number'].'.pdf';

        Storage::disk('order')->delete(
            [
                $filename
            ]
        );

        $cache_params = '?='.uniqid();

        if($type == 'INLINE'){
            $mpdf->Output($filename, 'D');
        }else{

            $view_path = Config::get('constants.upload.order.view_path');
            $upload_dir = Storage::disk('order')->getAdapter()->getPathPrefix();
            $mpdf->Output($upload_dir.$filename, \Mpdf\Output\Destination::FILE);
            $download_link = ($full_path == false)?$view_path.$filename:$upload_dir.$filename;
            return $download_link; 
        }
    }

    public function print_kot(Request $request, $slack, $type = 'INLINE', $full_path = false){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_POS_ORDERS';
        check_access([$data['sub_menu_key']]);
        
        $order_data = $this->get_order_data($slack);
        if(in_array($order_data['status']['value'], [1, 5] ) != 1){
            abort(404);
        }

        $view_file = 'order.kot.kot_print';
        $css_file = 'css/order_thermal_print_invoice.css';
        $format = [80, 297];

        $print_data = view($view_file, ['data' => json_encode($order_data)])->render();

        $mpdf_config = [
            'mode'          => 'utf-8',
            'format'        => $format,
            'orientation'   => 'P',
            'margin_left'   => 3,
            'margin_right'  => 3,
            'margin_top'    => 3,
            'margin_bottom' => 3,
            'tempDir' => storage_path()."/pdf_temp" 
        ];
        
        $stylesheet = File::get(public_path($css_file));
        $mpdf = new Mpdf($mpdf_config);
        $mpdf->SetDisplayMode('real');
        $mpdf->showImageErrors = true;
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($print_data);

        $filename = 'kot_'.$order_data['order_number'].'.pdf';

        Storage::disk('order')->delete(
            [
                $filename
            ]
        );

        $cache_params = '?='.uniqid();

        if($type == 'INLINE'){
            $mpdf->Output($filename.$cache_params, \Mpdf\Output\Destination::INLINE);
        }else{

            $view_path = Config::get('constants.upload.order.view_path');
            $upload_dir = Storage::disk('order')->getAdapter()->getPathPrefix();

            $mpdf->Output($upload_dir.$filename, \Mpdf\Output\Destination::FILE);

            $download_link = ($full_path == false)?$view_path.$filename.$cache_params:$upload_dir.$filename;
            return $download_link; 
        }
    }

    public function get_order_data($slack){
        $data['order_data'] = null;

        if(isset($slack)){

            $order = OrderModel::withoutGlobalScopes()->select('orders.*')->where('orders.slack', $slack)
            ->first();

            if (empty($order)) {
                abort(404);
            }

            $order_data = new OrderResource($order);

            $order_products_array = collect($order_data->products)->toArray();
            
            $order_data = collect($order_data);
            $data = $order_data->all();
        }
        return $data;
    }

    public function payment_gateway($type, $slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_POS_ORDERS';
        check_access([$data['sub_menu_key']]);

        $order = OrderModel::select('orders.*')->where('orders.slack', $slack)->first();
        $order_status_master = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'CLOSED')->first();

        $data['new_order_link'] = route('add_order');

        if($order_status_master->value == $order->status){
            return redirect($data['new_order_link']);
        }

        $order_data = new OrderResource($order);

        $zero_decimal_currencies = ["BIF", "CLP", "DJF", "GNF", "JPY", "KMF", "KRW", "MGA", "PYG", "RWF", "UGX", "VND", "VUV", "XAF", "XOF", "XPF"];

        $data['order_slack'] = $slack;
        $data['order_detail_link'] = route('order_detail', ['slack' => $slack]);
        $data['order_print_link'] = route('order_summary', ['slack' => $slack]);
        $data['order_number'] = $order->order_number;
        $data['order_amount'] = $order->total_order_amount;
        $data['order_currency'] = $order->currency_code;
        $data['order_currency_round_note'] = (in_array(strtoupper($order->currency_code), $zero_decimal_currencies))?"Amount will be rounded in case of these currencies :".implode(", ", $zero_decimal_currencies):'';
        $data['public_order'] = false;

        switch(strtolower($type)){
            case "stripe":
                if(in_array(strtoupper($order->currency_code), $zero_decimal_currencies)){
                    $data['order_amount'] = $order->total_order_amount_rounded;
                }else{
                    $data['order_amount'] = ($order->total_order_amount);
                }
                $view_file = 'payment.stripe';
            break;
            case "paypal":

                $payment_method = PaymentMethodModel::where('payment_constant', '=', 'PAYPAL')->first();
                $client_secret = $payment_method->key_1;
                $client_id = $payment_method->key_2;
                $data['client_id'] = $client_id;

                $view_file = 'payment.paypal';
            break;
            case "razorpay":

                $payment_method = PaymentMethodModel::where('payment_constant', '=', 'RAZORPAY')->first();
                $key_id = $payment_method->key_1;
                $key_secret = $payment_method->key_2;
                
                $api = new Api($key_id, $key_secret);

                $razorpay_order_data = [
                    'receipt'         => $order->order_number,
                    'amount'          => $order->total_order_amount * 100,
                    'currency'        => $order->currency_code,
                    'payment_capture' => 1 // auto capture
                ];
                
                $razorpay_order = $api->order->create($razorpay_order_data);
                $razorpay_order_id = $razorpay_order['id'];
                $_SESSION['razorpay_order_id'] = $razorpay_order_id;
                $display_amount = $amount = $razorpay_order_data['amount'];
                
                $razorpay_data['razorpay_array'] = [
                    "key"               => $key_id,
                    "amount"            => $amount,
                    "name"              => "",
                    "description"       => "",
                    "image"             => "",
                    "prefill"           => [
                        "name"              => (isset($order_data->customer))?$order_data->customer->name:'',
                        "email"             => (isset($order_data->customer))?$order_data->customer->email:'',
                        "contact"           => (isset($order_data->customer))?$order_data->customer->phone:'',
                    ],
                    "notes"             => [
                        "address"           => "",
                        "merchant_order_id" => $order->order_number,
                    ],
                    "theme"             => [
                        "color"             => "#F37254"
                    ],
                    "order_id"          => $razorpay_order_id,
                    "display_currency"  => $order->currency_code,
                    "display_amount"  => $order->total_order_amount,
                ];

                $data = array_merge($data, $razorpay_data);
                $view_file = 'payment.razorpay';
            break;
        }

        return view($view_file, $data);
    }

    public function get_order_data_public($slack){
        $data['order_data'] = null;

        if(isset($slack)){

            $order = OrderModel::withoutGlobalScopes()->select('orders.*')->where('orders.slack', $slack)
            ->closed()->first();

            if (empty($order)) {
                abort(404);
            }

            $order_data = new OrderResource($order);

            $order_products_array = collect($order_data->products)->toArray();
            $total_qty_array = data_get($order_products_array, '*.quantity', 0);
            $total_quantity = array_sum($total_qty_array);
            
            $order_data = collect($order_data);
            $order_data->put('total_quantity', $total_quantity);
            $data = $order_data->all();
        }
        return $data;
    }

    public function detail_public_view($slack){
        
        $order_data = $this->get_order_data_public($slack);

        $data['order_data'] = $order_data;

        $data['company_logo'] = config('app.company_logo');

        return view('order.order_detail_public', $data);
    }

    public function order_summary(Request $request, $slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_POS_ORDERS';
        check_access([$data['sub_menu_key']]);

        $order_data = $this->get_order_data($slack);
        $data['order_data'] = $order_data;

        $data['pdf_print'] = $this->print_order($request, $slack, 'FILE');

        $data['new_order_link'] = route('add_order');

        $data['order_detail_link'] = route('order_detail', ['slack' => $slack]);

        $data['print_order_link'] = route('print_order', ['slack' => $slack]);

        $data['print_kot_link'] = (isset($order_data['restaurant_mode']) && $order_data['restaurant_mode'] == 1)?route('print_kot', ['slack' => $slack]):'';

        $data['order_detail_access'] = check_access(['A_DETAIL_ORDER'] ,true);

        $data['new_order_access'] = check_access(['A_ADD_ORDER'] ,true);

        $data['edit_order_link'] = route('edit_order', ['slack' => $slack]);

        $data['edit_order_access'] = check_access(['A_EDIT_ORDER'] ,true);

        $data['printnode_enabled'] = (isset($order_data['store']['printnode_enabled']) && $order_data['store']['printnode_enabled'] == 1)?true:false;

        // return view('order.order_summary', $data);
        return redirect()->route('print_order',['slack' => $slack]);
        
    }

    public function digital_menu_orders(){
        //check access
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_DIGITAL_MENU_ORDERS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $data['edit_order_link'] = route('edit_order', ['slack' => '']);
        
        $data['edit_order_access'] = check_access(['A_EDIT_ORDER'] ,true);

        return view('order.digital_menu.digital_menu_orders', $data);
    }

    public function payment_gateway_public(Request $request, $type, $slack){

        $store_slack = $request->store;
        $table_slack = $request->table;
        
        $order = OrderModel::withoutGlobalScopes()->select('orders.*')->where('orders.slack', $slack)->first();
        if (empty($order)) {
            abort(404);
        }

        $order_status_master = MasterStatusModel::select('value')->filterByValueConstant('ORDER_STATUS', 'CLOSED')->first();

        $data['new_order_link'] = route('our_menu', ['store_slack' => $store_slack, 'table_slack' => $table_slack]);

        if($order_status_master->value == $order->status){
            return redirect($data['new_order_link']);
        }
        
        $order_data = new OrderResource($order);

        $zero_decimal_currencies = ["BIF", "CLP", "DJF", "GNF", "JPY", "KMF", "KRW", "MGA", "PYG", "RWF", "UGX", "VND", "VUV", "XAF", "XOF", "XPF"];

        $data['order_slack'] = $slack;
        $data['order_detail_link'] =  route('our_menu', ['store_slack' => $store_slack, 'table_slack' => $table_slack]);
        $data['order_print_link'] = route('print_order_public', ['slack' => $slack]);
        $data['order_number'] = $order->order_number;
        $data['order_amount'] = $order->total_order_amount;
        $data['order_currency'] = $order->currency_code;
        $data['order_currency_round_note'] = (in_array(strtoupper($order->currency_code), $zero_decimal_currencies))?"Amount will be rounded in case of these currencies :".implode(", ", $zero_decimal_currencies):'';
        $data['public_order'] = true;

        switch(strtolower($type)){
            case "stripe":
                if(in_array(strtoupper($order->currency_code), $zero_decimal_currencies)){
                    $data['order_amount'] = $order->total_order_amount_rounded;
                }else{
                    $data['order_amount'] = ($order->total_order_amount);
                }
                $view_file = 'payment.stripe';
            break;
            case "paypal":

                $payment_method = PaymentMethodModel::where('payment_constant', '=', 'PAYPAL')->first();
                $client_secret = $payment_method->key_1;
                $client_id = $payment_method->key_2;
                $data['client_id'] = $client_id;

                $view_file = 'payment.paypal';
            break;
            case "razorpay":

                $payment_method = PaymentMethodModel::where('payment_constant', '=', 'RAZORPAY')->first();
                $key_id = $payment_method->key_1;
                $key_secret = $payment_method->key_2;
                
                $api = new Api($key_id, $key_secret);

                $razorpay_order_data = [
                    'receipt'         => $order->order_number,
                    'amount'          => $order->total_order_amount * 100,
                    'currency'        => $order->currency_code,
                    'payment_capture' => 1 // auto capture
                ];
                
                $razorpay_order = $api->order->create($razorpay_order_data);
                $razorpay_order_id = $razorpay_order['id'];
                $_SESSION['razorpay_order_id'] = $razorpay_order_id;
                $display_amount = $amount = $razorpay_order_data['amount'];
                
                $razorpay_data['razorpay_array'] = [
                    "key"               => $key_id,
                    "amount"            => $amount,
                    "name"              => "",
                    "description"       => "",
                    "image"             => "",
                    "prefill"           => [
                        "name"              => (isset($order_data->customer))?$order_data->customer->name:'',
                        "email"             => (isset($order_data->customer))?$order_data->customer->email:'',
                        "contact"           => (isset($order_data->customer))?$order_data->customer->phone:'',
                    ],
                    "notes"             => [
                        "address"           => "",
                        "merchant_order_id" => $order->order_number,
                    ],
                    "theme"             => [
                        "color"             => "#F37254"
                    ],
                    "order_id"          => $razorpay_order_id,
                    "display_currency"  => $order->currency_code,
                    "display_amount"  => $order->total_order_amount,
                ];

                $data = array_merge($data, $razorpay_data);
                $view_file = 'payment.razorpay';
            break;
        }

        return view($view_file, $data);
    }

    public function print_order_public(Request $request, $slack){
        $this->print_order($request, $slack, 'INLINE');
    }
}
