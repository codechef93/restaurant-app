<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Product as ProductModel;
use App\Models\Category as CategoryModel;
use App\Models\Store as StoreModel;
use App\Models\MasterOrderType as MasterOrderTypeModel;
use App\Models\MasterBillingType as MasterBillingTypeModel;
use App\Models\Table as TableModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\Language as LanguageModel;
use App\Models\PaymentMethod as PaymentMethodModel;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;

use App\Http\Resources\Collections\ProductCollection;

use Carbon\Carbon;

class RestaurantMenu extends Controller
{

    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_RESTAURANT';
        $data['sub_menu_key'] = 'SM_RESTAURANT_MENU';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $data['menu_link'] = route('our_menu', ['store_slack' => $request->logged_user_store_slack]);

        return view('kitchen.restaurant_menu.restaurant_menu_qr', $data);
    }

    public function our_menu(Request $request, $store_slack, $table_slack = null){
        $data = [];
        
        $category_array = [];

        $store_data = StoreModel::select('id', 'slack', 'name', 'address', 'currency_code', 'tax_code_id', 'discount_code_id', 'restaurant_billing_type_id', 'restaurant_mode', 'enable_digital_menu_otp_verification', 'menu_language_id', 'enable_variants_popup', 'digital_menu_enabled', 'menu_open_time', 'menu_close_time', 'store_logo', 'store_logo_base64')
        ->where('slack', $store_slack)
        ->active()
        ->first();

        if (empty($store_data)) {
            return response()->view('errors.404_public', [], 500);exit;
        }

        $language = LanguageModel::select("language_code")->where('id', '=', trim($store_data->menu_language_id))->active()->first();

        $data['table'] = [];
        if($table_slack != null){
            $table = TableModel::withoutGlobalScopes()->select('slack', 'table_number')
            ->where('slack', '=', $table_slack)
            ->where('store_id', $store_data->id)
            ->active()
            ->first();
            if (empty($table)) {
                return response()->view('errors.404_public', [], 500);exit;
            }
            $data['table'] = $table;
        }

        $data['store'] = $store_data;

        $data['store_restaurant_mode'] = ($store_data->restaurant_mode == 1)?true:false;

        if ($data['store_restaurant_mode'] == false) {
            return response()->view('errors.404_public', [], 500);exit;
        }

        $data['menu_enabled'] = $store_data->digital_menu_enabled == 1?true:false;

        $data['inside_menu_schedule'] = $this->check_menu_timing($store_data->menu_open_time, $store_data->menu_close_time);

        $data['menu_schedule'] = ($store_data->menu_open_time != '')?Carbon::parse($store_data->menu_open_time)->format(config("app.display_time_format")).' - '.Carbon::parse($store_data->menu_close_time)->format(config("app.display_time_format")):'';

        $data['language'] = (isset($language->language_code) && !empty($language->language_code))?$language->language_code:'en';
        
        if($data['menu_enabled'] == false || $data['inside_menu_schedule']  == false){
            return response()->view('kitchen.restaurant_menu.disabled_menu', $data, '200');exit;
        }

        $data['store_tax_percentage'] = null;
        $data['store_discount_percentage'] = null;
        $data['restaurant_order_types'] = null;
        $data['billing_types']  = null;
        $data['store_billing_type'] = null;

        if(isset($store_data->tax_code_id)){
            $taxcode_data = TaxcodeModel::withoutGlobalScopes()->select('total_tax_percentage')
            ->where('id', '=', $store_data->tax_code_id)
            ->where('store_id', $store_data->id)
            ->active()
            ->first();
            $data['store_tax_percentage'] = (isset($taxcode_data->total_tax_percentage))?$taxcode_data->total_tax_percentage:0.00;
        }

        if(isset($store_data->discount_code_id)){
            $discountcode_data = DiscountcodeModel::withoutGlobalScopes()->select('discount_percentage')
            ->where('id', '=', trim($store_data->discount_code_id))
            ->where('store_id', $store_data->id)
            ->active()
            ->first();
            $data['store_discount_percentage'] = (isset($discountcode_data->discount_percentage))?$discountcode_data->discount_percentage:0.00;
        }

        $data['restaurant_order_types'] = MasterOrderTypeModel::select('order_type_constant', 'label')->where('restaurant', 1)->active()->get();

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


        $categories = CategoryModel::withoutGlobalScopes()
        ->select('id', 'slack', 'label')
        ->where('store_id', $store_data->id)
        ->active()
        ->showQrMenu()
        ->orderBy('category.label', 'asc')
        ->get();

        $category_array = $categories->map(function ($item, $key) {
            return [
                'slack' => $item->slack,
                'label' => $item->label,
            ];
        });
        
        foreach($categories as $key => $category){
            
            $products_data = ProductModel::withoutGlobalScopes()->select("products.*")
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->categoryActive()
            ->supplierActive()
            ->taxcodeActive()
            ->mainProduct()
            ->quantityCheck(1)
            ->active()
            ->where([
                ['products.category_id', '=', $category->id],
                ['products.store_id', '=', $store_data->id],
            ])
            ->orderBy('products.name', 'asc')
            ->get();
            
            $products = ProductResource::collection($products_data);

            if(!$products_data->isEmpty()){
                $categories[$key]->products = $products;
            }else{
                unset($categories[$key]);
            }
        }

        $data['category_products'] = $categories;

        $data['category_array'] = $category_array;

        $data['enable_digital_menu_otp_verification'] = ($store_data->enable_digital_menu_otp_verification == 1)?true:false;

        $data['company_logo'] = config('app.company_logo');

        $data['navbar_logo'] = $store_data->store_logo_base64;

        $payment_methods = PaymentMethodModel::select('slack', 'label', 'payment_constant')
        ->active()
        ->activeOnDigitalMenu()
        ->get();
        $data['payment_methods'] = (!empty($payment_methods))?$payment_methods:[];

        $data['base_url'] = url('/');

        return view('kitchen.restaurant_menu.restaurant_menu', $data);
    }

    public function check_menu_timing($menu_open_time, $menu_close_time){
        $inside_menu_schedule = false;
        if($menu_open_time != '' && $menu_close_time != ''){

            $now = date("H:i:s");
            $menu_open_time = Carbon::parse($menu_open_time)->format("H:i:s");
            $menu_close_time = Carbon::parse($menu_close_time)->format("H:i:s");
            
            if($menu_close_time<$menu_open_time){

                $menu_open_time_1 = $menu_open_time;
                $menu_close_time_1 = "23:59:59";
                if($now < $menu_close_time_1 && $now > $menu_open_time_1){
                    $inside_menu_schedule = true;
                }

                $menu_open_time_2 = "00:00:00";
                $menu_close_time_2 = $menu_close_time;
                if($now < $menu_close_time_2 && $now > $menu_open_time_2){
                    $inside_menu_schedule = true;
                }
                
            }else{
                if($now < $menu_close_time && $now > $menu_open_time){
                    $inside_menu_schedule = true;
                }
            }
        }else{
            $inside_menu_schedule = true;
        }
        return $inside_menu_schedule;
    }
}