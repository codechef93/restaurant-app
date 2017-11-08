<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use App\Models\Product as ProductModel;
use App\Models\Supplier as SupplierModel;
use App\Models\Category as CategoryModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\TaxcodeType as TaxcodeTypeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\StockTransfer as StockTransferModel;
use App\Models\StockTransferProduct as StockTransferProductModel;
use App\Models\MeasurementUnit as MeasurementUnitModel;
use App\Models\AddonGroup as AddonGroupModel;
use App\Models\VariantOption as VariantOptionModel;
use App\Models\Store as StoreModel;

use App\Http\Resources\ProductResource;
use App\Http\Resources\StockTransferResource;
use App\Http\Resources\StockTransferProductResource;

class Product extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_PRODUCTS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $data['restaurant_mode'] = $request->logged_user_store_restaurant_mode;
        
        return view('product.products', $data);
    }

    //This is the function that loads the add/edit page
    public function add_product($slack = null){

        //check access
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_PRODUCTS';
        $data['action_key'] = ($slack == null)?'A_ADD_PRODUCT':'A_EDIT_PRODUCT';
        check_access(array($data['action_key']));

        $current_route = Route::currentRouteName();
        $data['stock_transfer_data'] = null; 
        $data['stock_transfer_product_data'] = null;
        if($current_route == "add_new_stock_transfer_product"){
            
            $current_selected_store = request()->logged_user_store_id;
            $stock_transfer_product_slack = $slack;
            $slack = null;

            //get stock transfer product details
            $stock_transfer_product_details = StockTransferProductModel::where('slack', $stock_transfer_product_slack)->verifiable()->first();
            if (empty($stock_transfer_product_details)) {
                abort(404);
            }
            $stock_transfer_product_details = new StockTransferProductResource($stock_transfer_product_details);
            
            $stock_transfer_details = StockTransferModel::withoutGlobalScopes()->where('id', $stock_transfer_product_details->stock_transfer_id)->resolveStore($current_selected_store)->first();
            if (empty($stock_transfer_details)) {
                abort(404);
            }
            $stock_transfer_details = new StockTransferResource($stock_transfer_details);

            $data['stock_transfer_data'] = $stock_transfer_details;
            $data['stock_transfer_product_data'] = $stock_transfer_product_details;
        }

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('PRODUCT_STATUS')->active()->sortValueAsc()->get();

        $data['suppliers'] = SupplierModel::select('slack', 'supplier_code', 'name')->sortNameAsc()->active()->get();

        $data['categories'] = CategoryModel::select('slack', 'category_code', 'label')->sortLabelAsc()->active()->get();

        $data['taxcodes'] = TaxcodeModel::select('slack', 'tax_code', 'label', 'tax_type', 'total_tax_percentage')->sortLabelAsc()->active()->get();

        $data['discount_codes'] = DiscountcodeModel::select('slack', 'discount_code', 'label')->sortLabelAsc()->active()->get();

        $data['measurement_units'] = MeasurementUnitModel::select('slack', 'unit_code', 'label')->sortLabelAsc()->active()->get();

        $data['addon_groups'] = AddonGroupModel::select('slack', 'label', 'addon_group_code')->sortLabelAsc()->active()->get();

        $data['variant_options'] = VariantOptionModel::select('slack', 'variant_option_code', 'label')->sortLabelAsc()->active()->get();

        $data['product_data'] = null;
        if(isset($slack)){
            
            $product = ProductModel::where('products.slack', '=', $slack)->first();
            if (empty($product)) {
                abort(404);
            }
            
            $product_data = new ProductResource($product);

            $data['product_data'] = $product_data;
        }

        $data['is_taxcode_inclusive'] = isset($product_data->tax_code)?(($product_data->tax_code['tax_type'] == 'INCLUSIVE')?true:false):false;
        $data['taxcode_percentage'] = isset($product_data->tax_code)?$product_data->tax_code['total_tax_percentage']:0;

        return view('product.add_product', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_PRODUCTS';
        $data['action_key'] = 'A_DETAIL_PRODUCT';
        check_access([$data['action_key']]);

        $product = ProductModel::where('products.slack', '=', $slack)->first();
        
        if (empty($product)) {
            abort(404);
        }

        $product_data = new ProductResource($product);
        
        $data['product_data'] = $product_data;

        return view('product.product_detail', $data);
    }

    //This is the function that loads the barcode generate page
    public function generate_barcode(){
        $data['menu_key'] = 'MM_STOCK';
        $data['sub_menu_key'] = 'SM_PRODUCT_LABEL';
        check_access([$data['menu_key'], $data['sub_menu_key']]);

        return view('product.product_barcode', $data);        
    }
}
