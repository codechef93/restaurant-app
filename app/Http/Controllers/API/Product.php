<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;

use App\Models\Product as ProductModel;
use App\Models\Supplier as SupplierModel;
use App\Models\Category as CategoryModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\MasterStatus;
use App\Models\StockTransfer as StockTransferModel;
use App\Models\StockTransferProduct as StockTransferProductModel;
use App\Models\ProductImages as ProductImagesModel;
use App\Models\MeasurementUnit as MeasurementUnitModel;
use App\Models\ProductIngredient as ProductIngredientModel;
use App\Models\AddonGroup as AddonGroupModel;
use App\Models\ProductAddonGroup as ProductAddonGroupModel;
use App\Models\Store as StoreModel;
use App\Models\User as UserModel;
use App\Models\ProductVariant as ProductVariantModel;
use App\Models\VariantOption as VariantOptionModel;
use App\Models\Customer as CustomerModel;

use App\Http\Controllers\API\StockTransfer as StockTransferAPI;
use App\Http\Controllers\API\Product as ProductAPI;

use App\Http\Resources\ProductResource;
use App\Http\Resources\AddonGroupResource;

use App\Http\Resources\Collections\ProductCollection;

use Mpdf\Mpdf;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;

class Product extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_PRODUCT_LISTING';
            if(check_access(array($data['action_key']), true) == false){
                $response = $this->no_access_response_for_listing_table();
                return $response;
            }

            $item_array = array();

            $product_filter = (isset($request->product_filter))?$request->product_filter:'billing_products';

            $draw = $request->draw;
            $limit = $request->length;
            $offset = $request->start;
            
            $order_by = $request->order[0]["column"];
            $order_direction = $request->order[0]["dir"];
            $order_by_column =  $request->columns[$order_by]['name'];

            $filter_string = $request->search['value'];
            $filter_columns = array_filter(data_get($request->columns, '*.name'));
            
            $query = ProductModel::select('products.*', 'master_status.label as status_label', 'master_status.color as status_color', 'suppliers.name as supplier_name', 'suppliers.status as supplier_status', 'category.label as category_label', 'category.status as category_status', 'tax_codes.tax_code as tax_code_label', 'tax_codes.status as tax_code_status', 'discount_codes.discount_code as discount_code_label', 'discount_codes.status as discount_code_status', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
            ->statusJoin()
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->createdUser()

            ->when($order_by_column, function ($query, $order_by_column) use ($order_direction) {
                $query->orderBy($order_by_column, $order_direction);
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })

            ->when($filter_string, function ($query, $filter_string) use ($filter_columns) {
                $query->where(function ($query) use ($filter_string, $filter_columns){
                    foreach($filter_columns as $filter_column){
                        $query->orWhere($filter_column, 'like', '%'.$filter_string.'%');
                    }
                });
            })

            ->when($product_filter == 'billing_products', function ($query) {
                $query->mainProduct();
            })

            ->when($product_filter == 'ingredients', function ($query) {
                $query->isIngredient();
            })

            ->when($product_filter == 'addon_products', function ($query) {
                $query->addonProduct();
            });

            $count_query = $query;

            $query = $query->get();

            $products = ProductResource::collection($query);
           
            $count_query->getQuery()->limit = null;
            $count_query->getQuery()->offset = null;
            $total_count = $count_query->get()->count();

            $item_array = [];
            foreach($products as $key => $product){
                
                $product = $product->toArray($request);

                $item_array[$key][] = $product['product_code'];
                $item_array[$key][] = Str::limit($product['name'], 50);
                $item_array[$key][] = (isset($product['supplier']['status']))?(view('common.status_indicators', ['status' => $product['supplier']['status']])->render()).Str::limit($product['supplier']['name'], 50)." (".$product['supplier']['supplier_code'].")":'-';
                $item_array[$key][] = (isset($product['category']['status']))?(view('common.status_indicators', ['status' => $product['category']['status']])->render()).Str::limit($product['category']['label'], 50)." (".$product['category']['category_code'].")":'-';
                $item_array[$key][] = (isset($product['tax_code']['status']))?(view('common.status_indicators', ['status' => $product['tax_code']['status']])->render()).Str::limit($product['tax_code']['label'], 50)." (".$product['tax_code']['tax_code'].")":'-';
                $item_array[$key][] = (isset($product['discount_code']['status']) && $product['discount_code']['status'] != null)?(view('common.status_indicators', ['status' => $product['discount_code']['status']])->render().Str::limit($product['discount_code']['label'], 50)." (".$product['discount_code']['discount_code'].")"):'-';
                $item_array[$key][] = (isset($product['quantity']))?$product['quantity']:'-';
                $item_array[$key][] = (isset($product['sale_amount_excluding_tax']))?$product['sale_amount_excluding_tax']:'-';
                $item_array[$key][] = (isset($product['status']['label']))?view('common.status', ['status_data' => ['label' => $product['status']['label'], "color" => $product['status']['color']]])->render():'-';
                $item_array[$key][] = (isset($product['created_at_label']))?$product['created_at_label']:'-';
                $item_array[$key][] = (isset($product['updated_at_label']))?$product['updated_at_label']:'-';
                $item_array[$key][] = (isset($product['created_by']) && $product['created_by']['fullname'] != '')?$product['created_by']['fullname']:'-';
                $item_array[$key][] = view('product.layouts.product_actions', array('product' => $product))->render();
            }

            $response = [
                'draw' => $draw,
                'recordsTotal' => $total_count,
                'recordsFiltered' => $total_count,
                'data' => $item_array
            ];
            
            return response()->json($response);
        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            if(!check_access(['A_ADD_PRODUCT'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $product_data_exists = ProductModel::select('id')
            ->where('product_code', '=', trim($request->product_code))
            ->first();
            if (!empty($product_data_exists)) {
                throw new Exception("Product code already assigned to a product", 400);
            }

            $supplier_data = SupplierModel::select('id')
            ->where('slack', '=', trim($request->supplier))
            //->active()
            ->first();
            if (empty($supplier_data)) {
                throw new Exception("Supplier not found or inactive in the system", 400);
            }

            $category_data = CategoryModel::select('id')
            ->where('slack', '=', trim($request->category))
            //->active()
            ->first();
            if (empty($category_data)) {
                throw new Exception("Category not found or inactive in the system", 400);
            }

            $sale_price = 0;
            $sale_amount_including_tax = 0;

            $taxcode_data = TaxcodeModel::select('id', 'tax_type', 'total_tax_percentage')
            ->where('slack', '=', trim($request->tax_code))
            //->active()
            ->first();
            if (empty($taxcode_data)) {
                throw new Exception("Taxcode not found or inactive in the system", 400);
            }else{
                if($taxcode_data->tax_type == 'INCLUSIVE'){
                    $sale_amount_including_tax = $request->sale_amount_including_tax;
                    $tax_amount = calculate_tax($taxcode_data->total_tax_percentage, $sale_amount_including_tax);
                    // $sale_price = $request->sale_amount_including_tax-$tax_amount;
                    $sale_price = $request->sale_price;
                }else{
                    $sale_price = $request->sale_price;
                    $tax_amount = calculate_tax($taxcode_data->total_tax_percentage, $sale_price);
                    $sale_amount_including_tax = $sale_price+$tax_amount;
                }
            }

            $discount_code_id = NULL;
            if(isset($request->discount_code)){
                $discount_code_data = DiscountcodeModel::select('id')
                ->where('slack', '=', trim($request->discount_code))
                ->active()
                ->first();
                if (empty($discount_code_data)) {
                    throw new Exception("Discount code not found or inactive in the system", 400);
                }
                $discount_code_id = $discount_code_data->id;
            }

            if(isset($request->stock_transfer_product_slack) && $request->stock_transfer_product_slack != ''){
                $stock_transfer_api = new StockTransferAPI();
                $validate_response = $stock_transfer_api->validate_verify_stock_transfer($request, $request->stock_transfer_product_slack, $request->quantity);
                $stock_transfer_details = $validate_response['stock_transfer_details'];
                $stock_transfer_product_details = $validate_response['stock_transfer_product_details'];
                $stock_transfer_status = $validate_response['stock_transfer_status'];
                $stock_transfer_product_status = $validate_response['stock_transfer_product_status'];
            }

            DB::beginTransaction();
            
            $product = [
                "slack" => $this->generate_slack("products"),
                "store_id" => $request->logged_user_store_id,
                "name" => $request->product_name,
                "product_code" => strtoupper($request->product_code),
                "description" => $request->description,
                "category_id" => $category_data->id,
                "supplier_id" => $supplier_data->id,
                "tax_code_id" => $taxcode_data->id,
                "discount_code_id" => $discount_code_id,
                "quantity" => $request->quantity,
                "alert_quantity" => (!isset($request->alert_quantity))?0.00:$request->alert_quantity,
                "purchase_amount_excluding_tax" => $request->purchase_price,
                "sale_amount_excluding_tax" => $sale_price,
                "sale_amount_including_tax" => $sale_amount_including_tax,
                "is_ingredient_price" => ($request->is_ingredient_price == true)?1:0,
                "is_ingredient" => ($request->is_ingredient == true)?1:0,
                "is_addon_product" => ($request->is_addon_product == true)?1:0,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];
            
            $product_id = ProductModel::create($product)->id;

            $this->add_ingredients($request, $product['slack']);
            
            $this->upload_product_images($request, $product['slack']);

            $forward_link = '';

            if(isset($request->stock_transfer_product_slack) && $request->stock_transfer_product_slack != ''){
                
                $source_store_product = ProductModel::withoutGlobalScopes()->where('id', $stock_transfer_product_details->product_id);
                $source_store_product->decrement('quantity', $request->quantity);

                $stock_transfer = [];
                $stock_transfer['status'] = $stock_transfer_status->value;
                $stock_transfer['updated_at'] = now();
                $stock_transfer['updated_by'] = $request->logged_user_id;

                $action_response = StockTransferModel::withoutGlobalScopes()->where('id', $stock_transfer_product_details->stock_transfer_id)
                ->update($stock_transfer);

                $stock_transfer_product = [];

                $stock_transfer_product['inward_type'] = 'NEW';
                $stock_transfer_product['accepted_quantity'] = $request->quantity;
                $stock_transfer_product['destination_product_id'] = $product_id;
                $stock_transfer_product['destination_product_slack'] = $product['slack'];
                $stock_transfer_product['destination_product_code'] = $product['product_code'];
                $stock_transfer_product['destination_product_name'] = $product['name'];

                $stock_transfer_product['status'] = $stock_transfer_product_status->value;
                $stock_transfer_product['updated_at'] = now();
                $stock_transfer_product['updated_by'] = $request->logged_user_id;

                $action_response = StockTransferProductModel::where('slack', $request->stock_transfer_product_slack)
                ->update($stock_transfer_product);

                $stock_transfer_api->check_and_update_stock_transfer_status($request, $stock_transfer_details->slack);

                $forward_link = route('verify_stock_transfer', ['slack' => $stock_transfer_details->slack]);
            }

            $this->add_addon_groups($request, $product['slack']);

            $this->add_variants($request, $product['slack']);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Product created successfully", 
                    "data"    => $product['slack'],
                    "link"    => $forward_link
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function show($slack)
    { 
        try {

            if(!check_access(['A_DETAIL_PRODUCT'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = ProductModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new ProductResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Product loaded successfully", 
                    "data"    => $item_data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }  
    }

    /**
     * list all the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        try {

            if(!check_access(['A_VIEW_PRODUCT_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new ProductCollection(ProductModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Products loaded successfully", 
                    "data"    => $list
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slack)
    {
        try {

            if(!check_access(['A_EDIT_PRODUCT'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $product_data_exists = ProductModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['product_code', '=', trim($request->product_code)],
            ])
            ->first();
            if (!empty($product_data_exists)) {
                throw new Exception("Product code already assigned to a product", 400);
            }

            $supplier_data = SupplierModel::select('id')
            ->where('slack', '=', trim($request->supplier))
            //->active()
            ->first();
            if (empty($supplier_data)) {
                throw new Exception("Supplier not found or inactive in the system", 400);
            }

            $category_data = CategoryModel::select('id')
            ->where('slack', '=', trim($request->category))
            //->active()
            ->first();
            if (empty($category_data)) {
                throw new Exception("Category not found or inactive in the system", 400);
            }

            $sale_price = 0;
            $sale_amount_including_tax = 0;

            $taxcode_data = TaxcodeModel::select('id', 'tax_type', 'total_tax_percentage')
            ->where('slack', '=', trim($request->tax_code))
            //->active()
            ->first();
            if (empty($taxcode_data)) {
                throw new Exception("Taxcode not found or inactive in the system", 400);
            }else{
                if($taxcode_data->tax_type == 'INCLUSIVE'){
                    $sale_amount_including_tax = $request->sale_amount_including_tax;
                    $tax_amount = calculate_tax($taxcode_data->total_tax_percentage, $sale_amount_including_tax);
                    // $sale_price = $sale_amount_including_tax-$tax_amount;
                    $sale_price = $request->sale_price;
                    $request->is_ingredient_price = false;
                }else{
                    $sale_price = $request->sale_price;
                    $tax_amount = calculate_tax($taxcode_data->total_tax_percentage, $sale_price);
                    $sale_amount_including_tax = $sale_price+$tax_amount;
                }
            }

            $discount_code_id = NULL;
            if(isset($request->discount_code)){
                $discount_code_data = DiscountcodeModel::select('id')
                ->where('slack', '=', trim($request->discount_code))
                ->active()
                ->first();
                if (empty($discount_code_data)) {
                    throw new Exception("Discount code not found or inactive in the system", 400);
                }
                $discount_code_id = $discount_code_data->id;
            }

            DB::beginTransaction();
            
            $product = [
                "name" => $request->product_name,
                "product_code" => strtoupper($request->product_code),
                "description" => $request->description,
                "category_id" => $category_data->id,
                "supplier_id" => $supplier_data->id,
                "tax_code_id" => $taxcode_data->id,
                "discount_code_id" => $discount_code_id,
                "quantity" => $request->quantity,
                "alert_quantity" => (!isset($request->alert_quantity))?0.00:$request->alert_quantity,
                "purchase_amount_excluding_tax" => $request->purchase_price,
                "sale_amount_excluding_tax" => $sale_price,
                "sale_amount_including_tax" => $sale_amount_including_tax,
                "is_ingredient_price" => ($request->is_ingredient_price == true)?1:0,
                "is_ingredient" => ($request->is_ingredient == true)?1:0,
                "is_addon_product" => ($request->is_addon_product == true)?1:0,
                "status" => $request->status,
                "updated_by" => $request->logged_user_id
            ];

            $action_response = ProductModel::where('slack', $slack)
            ->update($product);

            $this->add_ingredients($request, $slack);

            $this->add_addon_groups($request, $slack);

            $this->add_variants($request, $slack);
            
            $this->upload_product_images($request, $slack);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Product updated successfully", 
                    "data"    => $slack
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function validate_request($request)
    {
        $request->merge(['ingredients' => json_decode($request->ingredients, true)]);
        $request->merge(['addon_group_values' => json_decode($request->addon_group_values, true)]);
        $request->merge(['variants' => json_decode($request->variants, true)]);

        $validation_array = [
            'product_name' => $this->get_validation_rules("name_label", true),
            'product_code' => $this->get_validation_rules("codes", true),
            'purchase_price' => $this->get_validation_rules("numeric", true),
            'quantity' => $this->get_validation_rules("numeric", true),
            'alert_quantity' => $this->get_validation_rules("numeric", false),
            'supplier' => $this->get_validation_rules("slack", true),
            'category' => $this->get_validation_rules("slack", true),
            'tax_code' => $this->get_validation_rules("slack", true),
            'description' => $this->get_validation_rules("text", false),
            'status' => $this->get_validation_rules("status", true),
            'product_images.*' => $this->get_validation_rules("product_image", false)
        ];

        $taxcode_data = TaxcodeModel::select('tax_type')
        ->where('slack', '=', trim($request->tax_code))
        ->first();
        if (!empty($taxcode_data)) {
            if($taxcode_data->tax_type == 'INCLUSIVE'){
                $validation_array['sale_amount_including_tax'] = $this->get_validation_rules("numeric", true);
            }else{
                $validation_array['sale_price'] = $this->get_validation_rules("numeric", true);
            }
        }

        if(!empty($request->variants) && count($request->variants)>0){
            $validation_array['variants.*.variant_option_slack'] = $this->get_validation_rules("slack", true);
            $validation_array['parent_variant_option'] = $this->get_validation_rules("slack", true);
        }

        $validator = Validator::make($request->all(), $validation_array);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }

    /**
     * get products from order page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_product(Request $request)
    {
        try {

            $product_code = $request->barcode;
            $product_title = $request->product_title;
            $product_category = $request->product_category;
            $customer_slack = $request->customer_slack;
            // return response()->json($product_title);
            $query = ProductModel::select('products.*')
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->categoryActive()
            ->supplierActive()
            ->taxcodeActive()
            ->quantityCheck()
            ->active()
            ->mainProduct();

            if(isset($product_code) && $product_code != ''){
                $query->where([
                    ['products.product_code', 'like', '%'.trim($product_code).'%']
                ]);
            }
            if(isset($product_title) && $product_title != ''){
                $query->where([
                    ['products.name', 'like', '%'.trim($product_title).'%']
                ]);
                
            }
            if(isset($product_category) && $product_category != ''){
                $query->where([
                    ['category.slack', '=', trim($product_category)]
                ]);
            }
            if($product_code == '' && $product_title == '' && $product_category == '' && $customer_slack == ''){
                $query->orderProduct()
                ->orderJoin()
                ->where('orders.status', 1) //closed orders
                ->orderBy('order_products.quantity', 'DESC')
                ->groupBy('product_code')
                ->limit(10);
            }
            if($customer_slack != ''){

                $customer = CustomerModel::select('id')->where('customers.slack', $customer_slack)
                ->first();

                $query->orderProduct()
                ->orderJoin()
                ->where('orders.customer_id', '=', $customer->id)
                ->where('orders.status', 1) //closed orders
                ->orderBy('order_products.quantity', 'DESC')
                ->groupBy('product_code')
                ->limit(10);
            }

            $product_data = $query->get();

            $request->skip_products = true;
            $product_data = ProductResource::collection($product_data);

            if (empty($product_data)) {
                throw new Exception("Product not available", 400);
            }

            return response()->json($this->generate_response(
                array(
                    "message" => "Product listed successfully", 
                    "data"    => $product_data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function generate_barcodes(Request $request){
        try {
            
            $request->merge(['products' => json_decode($request->products, true)]);

            $validator = Validator::make($request->all(), [
                'products.*.quantity' => 'min:1|integer',
            ]);
            $validation_status = $validator->fails();
            if($validation_status){
                throw new Exception($validator->errors());
            }

            $product_array = $request->products;

            $upload_folder = Config::get('constants.upload.barcode.dir');
            $upload_path = Config::get('constants.upload.barcode.upload_path');
            $view_path = Config::get('constants.upload.barcode.view_path');
            $generator  = new \Picqer\Barcode\BarcodeGeneratorJPG();
            $barcode_type = $generator::TYPE_CODE_128;
            
            $barcode_array = [];
            $remove_file_array = [];
            $download_link = '';
            
            if (empty((array)$product_array)) {
                throw new Exception("Product list should not be empty", 400);
            }

            foreach($product_array as $product_array_item){
                $product_data = ProductModel::select('slack', 'product_code', 'name', 'sale_amount_excluding_tax')
                ->where('slack', '=', $product_array_item['slack'])
                ->active()
                ->first();

                if (empty($product_data)) {
                    throw new Exception("Invalid product provided", 400);
                }

                $product_slack = $product_data->slack;
                $product_code = $product_data->product_code;
                $price = $product_data->sale_amount_excluding_tax;
                    
                $barcode_data = $generator->getBarcode($product_code, $barcode_type);
                
                $filename = $product_slack.".jpg";

                Storage::disk('public')->put($upload_folder.$filename, $barcode_data);
                
                $barcode_path = $upload_path.$filename;
                $image_resize = Image::make($barcode_path); 
                $image_resize->resize(300, 100);
                $image_resize->save($barcode_path);
                    
                $barcode_array[] = [
                    'product_code' => $product_code,
                    'count' => $product_array_item['quantity'],
                    'product_name' => Str::limit($product_array_item['name'], 28),
                    'price' => $request->logged_user_store_currency.' '.$price,
                    'product_barcode' => $barcode_path,
                ];

                $remove_file_array[] = $upload_folder.$filename;
            }
            
            if(count($barcode_array) >0){
                
                $date = Carbon::now();
                $current_date = $date->format('d-m-Y H:i');
                $store = $request->logged_user_store_code.'-'.$request->logged_user_store_name;

                $print_barcode_page = view('product.barcode.barcode_print', ['data' => $barcode_array, 'store' => $store, 'date' => $current_date])->render();

                $pdf_filename = "barcode_export_".date('Y_m_d_h_i_s')."_".uniqid().".pdf";
                
                ini_set("pcre.backtrack_limit", "5000000");
                set_time_limit(180);

                $mpdf_config = [
                    'mode'          => 'utf-8',
                    'format'        => 'A4',
                    'orientation'   => 'L',
                    'margin_left'   => 0,
                    'margin_right'  => 0,
                    'margin_top'    => 0,
                    'margin_bottom' => 0,
                    'margin_footer' => 1,
                    'tempDir' => storage_path()."/pdf_temp" 
                ];

                $css_file = 'css/barcode_print.css';
                $stylesheet = File::get(public_path($css_file));
                $mpdf = new Mpdf($mpdf_config);
                $mpdf->SetDisplayMode('real');
                $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
                $mpdf->SetHTMLFooter('<div class="footer">store: '.$store.' | generated on: '.$current_date.' | page: {PAGENO}/{nb}</div>');
                $mpdf->WriteHTML($print_barcode_page);
                $mpdf->Output(public_path('storage/barcode').'/'.$pdf_filename, \Mpdf\Output\Destination::FILE);

                $download_link = asset($view_path.$pdf_filename);
            }

            Storage::disk('public')->delete($remove_file_array);

            return response()->json($this->generate_response(
                array(
                    "message" => "Barcodes generated successfully",
                    'link' => ($download_link != '')?$download_link:''
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }
    
    /**
     * get products for po page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function load_product_for_po(Request $request)
    {
        try {

            $keywords = $request->keywords;
            $supplier_slack = $request->supplier;

            $query = ProductModel::select('products.slack as product_slack', 'products.product_code as product_code', 'products.name as label', 'products.purchase_amount_excluding_tax', 'tax_codes.total_tax_percentage as tax_percentage', 'tax_codes.tax_type as tax_type', 'discount_codes.discount_percentage as discount_percentage')
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->categoryActive()
            ->supplierActive()
            ->taxcodeActive()
            ->where('suppliers.slack', $supplier_slack)
            ->where(function($query) use ($keywords){
                $query->where('products.product_code', 'like', $keywords.'%')
                ->orWhere('products.name', 'like', $keywords.'%');
            });
            
            $product_data = $query->get();

            return response()->json($this->generate_response(
                array(
                    "message" => "Product listed successfully", 
                    "data"    => $product_data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function load_product_for_stock_transfer(Request $request)
    {
        try {

            $keywords = $request->keywords;

            $query = ProductModel::select('products.slack as product_slack', 'products.product_code as product_code', 'products.name as label', 'products.quantity')
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->categoryActive()
            ->supplierActive()
            ->taxcodeActive()
            ->where(function($query) use ($keywords){
                $query->where('products.product_code', 'like', $keywords.'%')
                ->orWhere('products.name', 'like', $keywords.'%');
            });
            
            $product_data = $query->get();

            return response()->json($this->generate_response(
                array(
                    "message" => "Product listed successfully", 
                    "data"    => $product_data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function upload_product_images(Request $request, $product_slack){
        if($request->hasFile('product_images')){

            $product_data_exists = ProductModel::select('id')
            ->where('slack', '=', trim($product_slack))
            ->first();

            if(!empty($product_data_exists)){

                $product_id = $product_data_exists->id;

                $upload_dir = Config::get('constants.upload.product.upload_path');
                $product_images_array = $request->product_images;

                foreach($product_images_array as $product_images_array_item){

                    $extension = $product_images_array_item->getClientOriginalExtension();
                    $file_name = $product_slack.'_'.uniqid().'.'.$extension;
                    $path = Storage::disk('product')->putFileAs('/', $product_images_array_item, $file_name);
                    $file_name = basename($path);

                    $image = Image::make($product_images_array_item);
                    $file_path = $upload_dir.'thumb_'.$file_name;
                    $image->fit(150);
                    $image->fit(150, 150, function ($constraint) {
                        $constraint->upsize();
                    });
                    $image->save($file_path);
                    $image->destroy();
                    
                    $status_data = MasterStatus::select('value')->filterByValueConstant('PRODUCT_IMAGE_STATUS', 'ACTIVE')->first();

                    $product_image_array = [
                        "slack" => $this->generate_slack("product_images"),
                        "product_id" => $product_id,
                        "filename" => $file_name,
                        "status" => $status_data->value,
                        "created_by" => $request->logged_user_id
                    ];
                    
                    $product_image_id = ProductImagesModel::create($product_image_array)->id;
                 
                }
            }
        }
    }

    public function delete_product_image(Request $request){
        try {
            $image_slack = $request->image_slack;

            $product_image_data = ProductImagesModel::select('filename')
            ->where('slack', '=', trim($image_slack))
            ->first();
            
            DB::beginTransaction();
            
            ProductImagesModel::where('slack', $image_slack)
            ->delete();

            DB::commit();

            if (!empty($product_image_data)) {
                Storage::disk('product')->delete(
                    [
                        $product_image_data->filename,
                        'thumb_'.$product_image_data->filename
                    ]
                );
            }

            return response()->json($this->generate_response(
                array(
                    "message" => "Product image deleted successfully", 
                    "data"    => $image_slack
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function load_ingredients(Request $request)
    {
        try {

            $keywords = $request->keywords;

            $query = ProductModel::select('products.*')
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->categoryActive()
            ->supplierActive()
            ->taxcodeActive()
            ->quantityCheck()
            ->isIngredient()
            ->where(function($query) use ($keywords){
                $query->where('products.product_code', 'like', $keywords.'%')
                ->orWhere('products.name', 'like', $keywords.'%');
            });
            
            $product_data = $query->get();

            $product_data = ProductResource::collection($product_data);

            if (empty($product_data)) {
                throw new Exception("Ingredient not available", 400);
            }

            return response()->json($this->generate_response(
                array(
                    "message" => "Ingredient list loaded successfully", 
                    "data"    => $product_data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function add_ingredients(Request $request, $product_slack){
        $product_ingredient_array = [];
      
        $product_data = ProductModel::select('id', 'is_ingredient')
        ->where('slack', '=', trim($product_slack))
        ->first();

        $restaurant_mode = $request->logged_user_store_restaurant_mode;

        if(!empty($product_data) && $restaurant_mode == 1){
            if($request->is_ingredient == false && !empty($request->ingredients)){ 
                
                $ingredients = $request->ingredients;
                $is_ingredient_price = $request->is_ingredient_price;

                $item_sale_price = 0;
                $item_sale_price_including_tax = 0;
                $item_purchase_price = 0;

                ProductIngredientModel::where('product_id', $product_data->id)->delete();

                foreach($ingredients as $key => $ingredient){

                    if(!empty($ingredient['ingredient_slack'])){
                        $ingredient_data = ProductModel::select('id', 'sale_amount_excluding_tax', 'sale_amount_including_tax', 'purchase_amount_excluding_tax')
                        ->where('slack', '=', trim($ingredient['ingredient_slack']))
                        ->active()
                        ->first();

                        if (empty($ingredient_data)) {
                            throw new Exception("Invalid ingredient selected at line ". ($key+1), 400);
                        }

                        $measurement_unit_data = MeasurementUnitModel::select('id')
                        ->where('slack', '=', trim($ingredient['unit_slack']))
                        ->active()
                        ->first();

                        if (empty($ingredient_data)) {
                            throw new Exception("Invalid ingredient selected at ". ($key+1), 400);
                        }

                        $product_ingredient_array[] = [
                            "slack" => $this->generate_slack("product_ingredients"),
                            "product_id" => $product_data->id,
                            "ingredient_product_id" => $ingredient_data->id,
                            "quantity" => $ingredient['quantity'],
                            "measurement_unit_id" => (isset($measurement_unit_data))?$measurement_unit_data->id:'',
                            "created_by" => $request->logged_user_id
                        ];

                        $individual_sale_price = ($ingredient['quantity']*$ingredient_data->sale_amount_excluding_tax);
                        $individual_sale_price_including_tax = ($ingredient['quantity']*$ingredient_data->sale_amount_including_tax);
                        $individual_purchase_price = ($ingredient['quantity']*$ingredient_data->purchase_amount_excluding_tax);

                        $item_sale_price = $item_sale_price + $individual_sale_price;
                        $item_sale_price_including_tax = $item_sale_price_including_tax + $individual_sale_price_including_tax;
                        $item_purchase_price = $item_purchase_price + $individual_purchase_price;
                    }
                }

                if(!empty($product_ingredient_array) && count($product_ingredient_array)>0){
                    foreach($product_ingredient_array as $product_ingredient_array_item){
                        ProductIngredientModel::create($product_ingredient_array_item);
                    }
                }

                if($is_ingredient_price == true){
                    $product = [
                        "sale_amount_excluding_tax" => $item_sale_price,
                        "purchase_amount_excluding_tax" => $item_purchase_price,
                        "is_ingredient_price" => 1
                    ];
                    ProductModel::where('id', $product_data->id)
                    ->update($product);
                }

            }else if($request->is_ingredient == true){
                ProductIngredientModel::where('product_id', $product_data->id)->delete();
                ProductAddonGroupModel::where('product_id', $product_data->id)->delete();
                $product = [
                    "is_ingredient_price" => 0
                ];
                ProductModel::where('id', $product_data->id)
                ->update($product);
            }
        }else if($restaurant_mode == 0){
            ProductIngredientModel::where('product_id', $product_data->id)->delete();
            
            $product = [
                "is_ingredient" => 0,
                "is_ingredient_price" => 0
            ];
            ProductModel::where('id', $product_data->id)
            ->update($product);

        }
    }

    public function load_addon_group_product(Request $request)
    {
        try {

            $keywords = $request->keywords;

            $query = ProductModel::select('products.slack as product_slack', 'products.product_code as product_code', 'products.name as label', 'products.quantity', 'products.sale_amount_excluding_tax')
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->categoryActive()
            ->supplierActive()
            ->taxcodeActive()
            ->addonProduct()
            ->where(function($query) use ($keywords){
                $query->where('products.product_code', 'like', $keywords.'%')
                ->orWhere('products.name', 'like', $keywords.'%');
            });
            
            $product_data = $query->get();

            return response()->json($this->generate_response(
                array(
                    "message" => "Product listed successfully", 
                    "data"    => $product_data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function add_addon_groups(Request $request, $product_slack){
        
        $product_addon_group_array = [];
        
        $addon_group_data_array = $request->addon_group_values;

        $product_data = ProductModel::select('id', 'is_ingredient')
        ->where('slack', '=', trim($product_slack))
        ->first();

        if(!empty($product_data)){
            if(empty($addon_group_data_array)){
                ProductAddonGroupModel::where('product_id', $product_data->id)->delete();
            }
            if(!empty($addon_group_data_array) && $request->is_addon_product == 0){

                ProductAddonGroupModel::where('product_id', $product_data->id)->delete();

                foreach($addon_group_data_array as $key => $addon_group_data_array_item){

                    
                    if(!empty($addon_group_data_array_item['slack'])){

                        $addon_group_data = AddonGroupModel::select('id')
                        ->where('slack', '=', trim($addon_group_data_array_item['slack']))
                        ->active()
                        ->first();

                        if (empty($addon_group_data)) {
                            throw new Exception("Invalid add-on group selected", 400);
                        }

                        $product_addon_group_array[] = [
                            "product_id" => $product_data->id,
                            "addon_group_id" => $addon_group_data->id,
                            "created_by" => $request->logged_user_id
                        ];
                    }
                }

                if(!empty($product_addon_group_array) && count($product_addon_group_array)>0){
                    foreach($product_addon_group_array as $product_addon_group_array_item){
                        ProductAddonGroupModel::create($product_addon_group_array_item);
                    }
                }
            }else if($request->is_addon_product == 1){
                ProductAddonGroupModel::where('product_id', $product_data->id)->delete();
                ProductIngredientModel::where('product_id', $product_data->id)->delete();
            }
        }
    }

    /**
     * get add on groups for order page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get_product_addon_groups(Request $request)
    {
        try {

            $product_slack = $request->product_slack;
      
            $product_data = ProductModel::select('id')
            ->where('slack', '=', trim($product_slack))
            ->first();

            if(!empty($product_data)){

                $product_addon_groups = ProductAddonGroupModel::select('addon_group_id')->where('product_id', $product_data->id)->get();

                if(!empty($product_addon_groups)){

                    $product_addon_groups_ids_array = $product_addon_groups->pluck('addon_group_id')->toArray();

                    $addon_group_data = AddonGroupModel::select('*')
                    ->whereIn('id', $product_addon_groups_ids_array)
                    ->active()
                    ->get();

                    $addon_groups = AddonGroupResource::collection($addon_group_data);
                }
            }

            return response()->json($this->generate_response(
                array(
                    "message" => "Add-on group listed successfully", 
                    "data"    => $addon_groups
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function get_customer_order_product_addon_groups(Request $request){
        try {
            $store = StoreModel::select('stores.id', 'stores.enable_digital_menu_otp_verification')
            ->where([['slack', '=', $request->store_slack]])
            ->first();

            $user = UserModel::select('users.id')
            ->where([['user_code', '=', 'CUSTOMER_USER']])
            ->active()
            ->first();

            if(!empty($user)){

                $request->merge([
                    'logged_user_store_id' => $store->id,
                ]);

                $product_api = new ProductAPI();
                $response = $product_api->get_product_addon_groups($request);
                $response = json_decode($response->content(), true);

                if($response['status_code'] == 200){
                    return response()->json($this->generate_response(
                        array(
                            "message" => $response['msg'], 
                            "data" => $response['data'],
                        ), 'SUCCESS'
                    ));
                }else{
                    throw new Exception($response['msg'], 400); 
                }
            }
        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function load_variant_products(Request $request)
    {
        try {

            $keywords = $request->keywords;
            $current_product = (isset($request->current_product) && $request->current_product !='')?$request->current_product:'';

            $query = ProductModel::select('products.*')
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->discountcodeJoin()
            ->categoryActive()
            ->supplierActive()
            ->taxcodeActive()
            ->quantityCheck()
            ->mainProduct()
            ->productVariantJoin()
            ->whereNull('product_variants.variant_code')
            ->where(function($query) use ($keywords){
                $query->where('products.product_code', 'like', $keywords.'%')
                ->orWhere('products.name', 'like', $keywords.'%');
            });

            if(isset($current_product) && $current_product != ''){
                $query->where([
                    ['products.slack', '!=', trim($current_product)]
                ]);
            }
            
            $product_data = $query->get();

            $product_data = ProductResource::collection($product_data);

            if (empty($product_data)) {
                throw new Exception("Product not available", 400);
            }

            return response()->json($this->generate_response(
                array(
                    "message" => "Vairant product list loaded successfully", 
                    "data"    => $product_data
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function generate_variant_code(){
        do{
            $variant_code = str_random(25);
            $exist = ProductVariantModel::where("variant_code", $variant_code)->first();
        }while($exist);
        return $variant_code;
    }

    public function add_variants(Request $request, $product_slack){
        $variants_array = $request->variants;
        $parent_variant_option = $request->parent_variant_option;
        
        if($request->is_addon_product == 0 && $request->is_ingredient == 0){
            
            $product_data = ProductModel::select('products.id', 'variant_code')
            ->productVariantJoin()
            ->where('products.slack', '=', trim($product_slack))
            ->first();

            if(!empty($variants_array) && count($variants_array)>0){

                $product_variant_code = ($product_data->variant_code != '')?$product_data->variant_code:$this->generate_variant_code();

                if($product_data->variant_code != ''){
                    ProductVariantModel::where("variant_code", $product_data->variant_code)->delete();
                }
                
                $variant_product_array = [];
                foreach($variants_array as $variants_array_item){
                    
                    $variant_product_data = ProductModel::select('products.id', 'product_code', 'name', 'variant_code')
                    ->productVariantJoin()
                    ->where('products.slack', '=', trim($variants_array_item['variant_slack']))
                    ->first();

                    if($variant_product_data->variant_code != ''){
                        throw new Exception($variant_product_data->product_code .' - '. $variant_product_data->name . ' is already added as a variant for other product', 400);
                    }

                    $variant_option_data = VariantOptionModel::select('id')
                    ->where('slack', '=', trim($variants_array_item['variant_option_slack']))
                    ->active()
                    ->first();

                    if(empty($variant_option_data)){
                        throw new Exception('Variant option is not available', 400);
                    }

                    $variant_product_array[] = [
                        "variant_code"      => $product_variant_code,
                        "product_id"        => $variant_product_data->id,
                        "variant_option_id" => $variant_option_data->id,
                        "created_by"        => $request->logged_user_id
                    ];
                }

                if(!empty($variant_product_array) && count($variant_product_array)>0){

                    $parent_variant_option_data = VariantOptionModel::select('id')
                    ->where('slack', '=', trim($parent_variant_option))
                    ->active()
                    ->first();

                    $variant_product_array[] = [
                        "variant_code"      => $product_variant_code,
                        "product_id"        => $product_data->id,
                        "variant_option_id" => $parent_variant_option_data->id,
                        "created_by"        => $request->logged_user_id
                    ];
                    foreach($variant_product_array as $variant_product_array_item){
                        $variant_product_array_item['slack'] = $this->generate_slack("product_variants");
                        ProductVariantModel::create($variant_product_array_item);
                    }
                }

            }else{
                ProductVariantModel::where('product_id', '=', $product_data->id)->delete();
            }
        }else{
            $product_data = ProductModel::select('id')
            ->where('slack', '=', trim($product_slack))
            ->first();

            ProductVariantModel::where('product_id', '=', $product_data->id)->delete();
        }
    }

    public function add_variants_from_import(Request $request){
        $variants_array = $request->variants;
   
        if(!empty($variants_array) && count($variants_array)>0){

            $product_variant_code = $this->generate_variant_code();
            
            $variant_product_array = [];
            $remove_product_array = [];
            foreach($variants_array as $variants_array_item){
                
                $variant_product_data = ProductModel::select('products.id', 'product_code', 'name')
                ->where('products.slack', '=', trim($variants_array_item['variant_slack']))
                ->first();

                $variant_option_data = VariantOptionModel::select('id')
                ->where('slack', '=', trim($variants_array_item['variant_option_slack']))
                ->active()
                ->first();

                $variant_product_array[] = [
                    "variant_code"      => $product_variant_code,
                    "product_id"        => $variant_product_data->id,
                    "variant_option_id" => $variant_option_data->id,
                    "created_by"        => $request->logged_user_id
                ];

                $remove_product_array[] = $variant_product_data->id;
            }
            
            if(!empty($variant_product_array) && count($variant_product_array)>0){
                
                ProductVariantModel::whereIn('product_id', $remove_product_array)->delete();
                
                foreach($variant_product_array as $variant_product_array_item){
                    $variant_product_array_item['slack'] = $this->generate_slack("product_variants");
                    ProductVariantModel::create($variant_product_array_item);
                }
            }

        }
    }

    public function remove_variant_product(Request $request)
    {
        try {

            $variant_slack = $request->variant_slack;

            ProductVariantModel::where('slack', '=', $variant_slack)->delete();

            return response()->json($this->generate_response(
                array(
                    "message" => "Vairant removed successfully", 
                    "data"    => $variant_slack
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function recalculate_product_price($taxcode_id){
        $taxcode_data = TaxcodeModel::select( 'tax_type', 'total_tax_percentage')
        ->where('id', '=', $taxcode_id)
        ->active()
        ->first();
        if (empty($taxcode_data)) {
            return;
        }

        $products = ProductModel::select('products.id', 'products.sale_amount_excluding_tax', 'products.sale_amount_including_tax')
        ->where('products.tax_code_id', '=', trim($taxcode_id))
        ->get();

        foreach($products as $product){
           
            $sale_amount_excluding_tax = 0;
            $sale_amount_including_tax = 0;

            if($taxcode_data->tax_type == 'INCLUSIVE'){
                $sale_amount_including_tax = $product->sale_amount_including_tax;
                $tax_amount = calculate_tax($taxcode_data->total_tax_percentage, $sale_amount_including_tax);
                $sale_amount_excluding_tax = $sale_amount_including_tax-$tax_amount;
            }else{
                $sale_amount_excluding_tax = $product->sale_amount_excluding_tax;
                $tax_amount = calculate_tax($taxcode_data->total_tax_percentage, $sale_amount_excluding_tax);
                $sale_amount_including_tax = $sale_amount_excluding_tax+$tax_amount;
            }

            $update_array = [
                'sale_amount_excluding_tax' => $sale_amount_excluding_tax,
                'sale_amount_including_tax' => $sale_amount_including_tax
            ];

            ProductModel::where('id', $product->id)
            ->update($update_array);
        }
    }
}
