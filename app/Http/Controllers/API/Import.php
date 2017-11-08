<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;

use App\Imports\DataImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Config;
use Maatwebsite\Excel\HeadingRowImport;
use Illuminate\Support\Facades\Storage;

use App\Models\Role as RoleModel;
use App\Models\MasterStatus as MasterStatusModel;
use App\Models\User as UserModel;
use App\Models\Store as StoreModel;
use App\Models\Supplier as SupplierModel;
use App\Models\Category as CategoryModel;
use App\Models\Product as ProductModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\AddonGroup as AddonGroupModel;
use App\Models\ProductAddonGroup as ProductAddonGroupModel;
use App\Models\ProductIngredient as ProductIngredientModel;
use App\Models\VariantOption as VariantOptionModel;

use App\Http\Controllers\API\User as UserAPI;
use App\Http\Controllers\API\Store as StoreAPI;
use App\Http\Controllers\API\Supplier as SupplierAPI;
use App\Http\Controllers\API\Category as CategoryAPI;
use App\Http\Controllers\API\Product as ProductAPI;

use Carbon\Carbon;
use Mpdf\Mpdf;

class Import extends Controller
{
    public function index(Request $request){
        try {
            
            $import_type = $request->upload_type;
            $import_file = $request->upload_file;

            if(empty($import_type)){
                throw new Exception("Invalid request", 400);
            }
            if(empty($import_file)){
                throw new Exception("File is required", 400);
            }

            $filename = '';
            switch($import_type){
                case "USER":
                if(!check_access(['A_UPLOAD_USER'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "STORE":
                if(!check_access(['A_UPLOAD_STORE'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "SUPPLIER":
                if(!check_access(['A_UPLOAD_SUPPLIER'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "CATEGORY":
                if(!check_access(['A_UPLOAD_CATEGORY'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "PRODUCT":
                if(!check_access(['A_UPLOAD_PRODUCT'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "INGREDIENT":
                if(!check_access(['A_UPLOAD_INGREDIENT'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "ADDON_PRODUCT":
                if(!check_access(['A_UPLOAD_ADDON_PRODUCT'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
            }

            $custom_filename = strtolower($import_type).'_'.date('Y_m_d_H_i_s').'_'.uniqid();
            $extension = $import_file->getClientOriginalExtension();
            $custom_file = $custom_filename.".".$extension;

            Storage::disk('imports')->delete(
                [
                    $custom_file
                ]
            );

            $path = Storage::disk('imports')->putFileAs('/', $import_file, $custom_file);

            $import_response = $this->forward_import_request($import_type, $custom_file);

            if($import_response['import_status'] == false){
                Storage::disk('imports')->delete(
                    [
                        $custom_file
                    ]
                );
            }
            
            return response()->json($this->generate_response(
                array(
                    "message" => "Import file read successfully",
                    "data" => $import_response,
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

    public function forward_import_request($import_type, $import_file){

        switch($import_type){
            case "USER":
                $response = $this->import_file($import_type, $import_file);
                if($response['import_status'] == true){
                    $user_api = new UserAPI();
                    $request = request();
                    foreach ($response['data'] as $user_array_item) {
                        $request->merge($user_array_item);
                        $user_api->store($request);
                    }
                    unset($response['data']);
                }
            break;
            case "STORE":
                $response = $this->import_file($import_type, $import_file);
                if($response['import_status'] == true){
                    $store_api = new StoreAPI();
                    $request = request();
                    foreach ($response['data'] as $store_array_item) {
                        $request->merge($store_array_item);
                        $store_api->store($request);
                    }
                    unset($response['data']);
                }
            break;
            case "SUPPLIER":
                $response = $this->import_file($import_type, $import_file);
                if($response['import_status'] == true){
                    $supplier_api = new SupplierAPI();
                    $request = request();
                    foreach ($response['data'] as $supplier_array_item) {
                        $request->merge($supplier_array_item);
                        $supplier_api->store($request);
                    }
                    unset($response['data']);
                }
            break;
            case "CATEGORY":
                $response = $this->import_file($import_type, $import_file);
                if($response['import_status'] == true){
                    $category_api = new CategoryAPI();
                    $request = request();
                    foreach ($response['data'] as $category_array_item) {
                        $request->merge($category_array_item);
                        $category_api->store($request);
                    }
                    unset($response['data']);
                }
            break;
            case "PRODUCT":
                $response = $this->import_file($import_type, $import_file);
                if($response['import_status'] == true){
                    $product_api = new ProductAPI();
                    $request = request();
                    foreach ($response['data'] as $product_array_item) {
                        $request->merge($product_array_item);
                        $product_api->store($request);
                    }
                    unset($response['data']);
                }
            break;
            case "INGREDIENT":
                $response = $this->import_file($import_type, $import_file);
                if($response['import_status'] == true){
                    $product_api = new ProductAPI();
                    $request = request();
                    foreach ($response['data'] as $product_array_item) {
                        $request->merge($product_array_item);
                        $product_api->store($request);
                    }
                    unset($response['data']);
                }
            break;
            case "ADDON_PRODUCT":
                $response = $this->import_file($import_type, $import_file);
                if($response['import_status'] == true){
                    $product_api = new ProductAPI();
                    $request = request();
                    foreach ($response['data'] as $product_array_item) {
                        $request->merge($product_array_item);
                        $product_api->store($request);
                    }
                    unset($response['data']);
                }
            break;
        }

        return $response;
    }

    public function import_file($import_type, $import_file){
        
        $data_array   = [];
        $error_array  = [];

        switch($import_type){
            case "USER":
                $valid_template = $this->validate_template("USER", $import_file);
            break;
            case "STORE":
                $valid_template = $this->validate_template("STORE", $import_file);
            break;
            case "SUPPLIER":
                $valid_template = $this->validate_template("SUPPLIER", $import_file);
            break;
            case "CATEGORY":
                $valid_template = $this->validate_template("CATEGORY", $import_file);
            break;
            case "PRODUCT":
                $valid_template = $this->validate_template("PRODUCT", $import_file);
            break;
            case "INGREDIENT":
                $valid_template = $this->validate_template("INGREDIENT", $import_file);
            break;
            case "ADDON_PRODUCT":
                $valid_template = $this->validate_template("ADDON_PRODUCT", $import_file);
            break;
        }
        
        if($valid_template){
            
            $upload_folder = Config::get('constants.upload.imports.upload_path');
            $excel_data = (new DataImport)->toArray( $upload_folder.$import_file);
            
            $excel_data = $excel_data[0];
            if(count($excel_data) == 0){
                throw new Exception("Please provide some data in the excel sheet.", 400);
            }

            foreach($excel_data as $key => $excel_data_item){
                switch($import_type){
                    case "USER":
                        $validate_response = $this->validate_user_data($excel_data_item);
                    break;
                    case "STORE":
                        $validate_response = $this->validate_store_data($excel_data_item);
                    break;
                    case "SUPPLIER":
                        $validate_response = $this->validate_supplier_data($excel_data_item);
                    break;
                    case "CATEGORY":
                        $validate_response = $this->validate_category_data($excel_data_item);
                    break;
                    case "PRODUCT":
                        $validate_response = $this->validate_product_data($excel_data_item);
                    break;
                    case "INGREDIENT":
                        $validate_response = $this->validate_ingredient_data($excel_data_item);
                    break;
                    case "ADDON_PRODUCT":
                        $validate_response = $this->validate_addon_product_data($excel_data_item);
                    break;
                }
                if(count($validate_response['error_list'])>0){
                    $error_array[$key+2] = $validate_response['error_list'];
                }
                $data_array[] = $validate_response['data'];
            }

            $response = [
                'import_status' => (count($error_array)>0)?false:true,
                'errors' => $error_array
            ];
            if(count($error_array) == 0){
                $response['data'] = $data_array;
            }
            
            return $response;

        }else{
            throw new Exception("Invalid file uploaded", 400);
        }
    }

    public function validate_template($template_type, $import_file){
        $valid_template = false;
        
        //check template if valid
        switch($template_type){
            case "USER":
                $template_format = Config::get('constants.upload.imports.user_format');
            break;
            case "STORE":
                $template_format = Config::get('constants.upload.imports.store_format');
            break;
            case "SUPPLIER":
                $template_format = Config::get('constants.upload.imports.supplier_format');
            break;
            case "CATEGORY":
                $template_format = Config::get('constants.upload.imports.category_format');
            break;
            case "PRODUCT":
                $template_format = Config::get('constants.upload.imports.product_format');
            break;
            case "INGREDIENT":
                $template_format = Config::get('constants.upload.imports.ingredient_format');
            break;
            case "ADDON_PRODUCT":
                $template_format = Config::get('constants.upload.imports.addon_product_format');
            break;
        }
        
        $default_foramt_file_path = public_path($template_format);
        $format_headings = (new HeadingRowImport)->toArray($default_foramt_file_path);
        $format_headings = array_filter(array_map('trim', $format_headings[0][0]));
        
        $upload_folder = Config::get('constants.upload.imports.upload_path');
        $uploaded_file_headings = (new HeadingRowImport)->toArray($upload_folder.$import_file);
        $uploaded_file_headings = array_filter(array_map('trim', $uploaded_file_headings[0][0])); 

        $valid_template = ($format_headings == $uploaded_file_headings)?true:false;

        return $valid_template;
    }

    public function validate_user_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];
        $stores = '';

        $fullname       = $excel_data_item['fullname'];
        $email          = $excel_data_item['email'];
        $contact_number = $excel_data_item['contact_number'];
        $role_code      = $excel_data_item['role_code'];
        $status         = $excel_data_item['status'];
        $store_codes    = $excel_data_item['store_codes_csv'];

        $validator = Validator::make($excel_data_item, [
            'fullname' => $this->get_validation_rules("fullname", true),
            'email' => $this->get_validation_rules("email", true),
            'contact_number' => $this->get_validation_rules("phone", true),
            'role_code' => $this->get_validation_rules("codes", true),
            'status' => $this->get_validation_rules("filled",true),
            //'store_codes_csv' => $this->get_validation_rules("filled",true)
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {
                $error_array[] = $message;
            }
        }

        $role_data = RoleModel::select('slack')->where('role_code', '=', $role_code)->resolveSuperAdminRole()->active()->first();
        if (!$role_data) {
            $error_array[] = 'Invalid role code provided';
        }

        if ($email != "") {
            $email_data = UserModel::where('email', '=', $email)->first();
            if ($email_data) {
                $error_array[] = 'Email already assigned to another user';
            }
        }

        if ($contact_number != "") {
            $contact_number_data = UserModel::where('phone', '=', $contact_number)->first();
            if ($contact_number_data) {
                $error_array[] = 'Contact number already assigned to another user';
            }
        }

        if ($status != "") {
            $status_data = MasterStatusModel::select('value')->where([
                ['value_constant', '=', strtoupper($status)],
                ['key', '=', 'USER_STATUS']
            ])->active()->first();
            if (!$status_data) {
                $error_array[] = 'Invalid status provided';
            }
        }

        if($store_codes != ''){
            $store_codes_array = explode(",",$store_codes);
            $store_codes_array = array_map('trim',$store_codes_array);
            if (count($store_codes_array) > 0) {
                
                $store_data = StoreModel::whereIn('store_code', $store_codes_array)->active()->get();
                $valid_store_slack_array = $store_data->pluck('slack')->toArray();
                $valid_store_code_array = $store_data->pluck('store_code')->toArray();
                
                $invalid_store_codes = array_diff($store_codes_array, $valid_store_code_array);

                if(count($invalid_store_codes) > 0){
                    $error_array[] = implode(',', $invalid_store_codes).' : Invalid stores provided';
                }
                if ($store_data->isEmpty()) {
                    $error_array[] = 'Invalid stores provided';
                }else{
                    $stores = implode(',', $valid_store_slack_array);
                }
            }
        }

        if(count($error_array) == 0){
            $data = [
                "fullname"  => $fullname,
                "email"     => $email,
                "phone"     => $contact_number,
                "role"      => $role_data->slack,
                "status"    => $status_data->value,
                "user_stores" => $stores
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }

    public function validate_store_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];

        $store_code = $excel_data_item['store_code'];
        $name = $excel_data_item['name'];
        $address = $excel_data_item['address'];
        $pincode = $excel_data_item['pincode'];
        $tax_number = $excel_data_item['tax_number'];
        $primary_contact_no = $excel_data_item['primary_contact_no'];
        $secondary_contact_no = $excel_data_item['secondary_contact_no'];
        $primary_email = $excel_data_item['primary_email'];
        $secondary_email = $excel_data_item['secondary_email'];
        $status = $excel_data_item['status'];

        $validator = Validator::make($excel_data_item, [
            'name' => $this->get_validation_rules("name_label", true),
            'address' => $this->get_validation_rules("text", true),
            'pincode' => $this->get_validation_rules("pincode", false),
            'store_code' => $this->get_validation_rules("codes", true),
            'tax_number' => $this->get_validation_rules("name_label", false),
            'primary_contact' => $this->get_validation_rules("phone", false),
            'secondary_contact' => $this->get_validation_rules("phone", false),
            'primary_email' => $this->get_validation_rules("email", false),
            'secondary_email' => $this->get_validation_rules("email", false),
            'status' => $this->get_validation_rules("status", true),
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {
                $error_array[] = $message;
            }
        }

        $store_data_exists = StoreModel::select('id')
        ->where('store_code', '=', trim($store_code))
        ->first();
        if (!empty($store_data_exists)) {
            throw new Exception("Store code already assigned to a store", 400);
        }

        if ($status != "") {
            $status_data = MasterStatusModel::select('value')->where([
                ['value_constant', '=', strtoupper($status)],
                ['key', '=', 'STORE_STATUS']
            ])->active()->first();
            if (!$status_data) {
                $error_array[] = 'Invalid status provided';
            }
        }

        if(count($error_array) == 0){
            $data = [
                "store_code" => $store_code,
                "name" => $name,
                "tax_number" => $tax_number,
                "address" => $address,
                "pincode" => $pincode,
                "primary_contact" => $primary_contact_no,
                "secondary_contact" => $secondary_contact_no,
                "primary_email" => $primary_email,
                "secondary_email" => $secondary_email,
                "status" => $status_data->value,
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }

    public function validate_supplier_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];

        $supplier_name = $excel_data_item['supplier_name'];
        $contact_email = $excel_data_item['contact_email'];
        $contact_number = $excel_data_item['contact_number'];
        $address = $excel_data_item['address'];
        $pincode = $excel_data_item['pincode'];
        $status = $excel_data_item['status'];

        $validator = Validator::make($excel_data_item, [
            'supplier_name' => $this->get_validation_rules("name_label", true),
            'contact_email' => $this->get_validation_rules("email", false),
            'contact_number' => $this->get_validation_rules("phone", false),
            'address' => $this->get_validation_rules("text", false),
            'pincode' => $this->get_validation_rules("pincode", false),
            'status' => $this->get_validation_rules("filled",true)
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {
                $error_array[] = $message;
            }
        }

        $supplier_data_exists = SupplierModel::select('id')
        ->where('name', '=', trim($supplier_name))
        ->first();
        if (!empty($supplier_data_exists)) {
            throw new Exception("Supplier already available in the system", 400);
        }

        if ($status != "") {
            $status_data = MasterStatusModel::select('value')->where([
                ['value_constant', '=', strtoupper($status)],
                ['key', '=', 'SUPPLIER_STATUS']
            ])->active()->first();
            if (!$status_data) {
                $error_array[] = 'Invalid status provided';
            }
        }

        if(count($error_array) == 0){
            $data = [
                "supplier_name" => $supplier_name,
                "address" => $address,
                "phone" => $contact_number,
                "email" => $contact_email,
                "pincode" => $pincode,
                "status" => $status_data->value,
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }

    public function validate_category_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];

        $category_name = $excel_data_item['category_name'];
        $description = $excel_data_item['description'];
        $status = $excel_data_item['status'];
        
        $validator = Validator::make($excel_data_item, [
            'category_name' => $this->get_validation_rules("name_label", true),
            'description' => $this->get_validation_rules("text"),
            'status' => $this->get_validation_rules("filled",true)
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {
                $error_array[] = $message;
            }
        }
        
        $category_data_exists = CategoryModel::select('id')
        ->where('label', '=', trim($category_name))
        ->first();
        if (!empty($category_data_exists)) {
            throw new Exception("Category already available in the system", 400);
        }

        if ($status != "") {
            $status_data = MasterStatusModel::select('value')->where([
                ['value_constant', '=', strtoupper($status)],
                ['key', '=', 'CATEGORY_STATUS']
            ])->active()->first();
            if (!$status_data) {
                $error_array[] = 'Invalid status provided';
            }
        }

        if(count($error_array) == 0){
            $data = [
                "category_name" => $category_name,
                "description" => $description,
                "status" => $status_data->value,
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }

    public function validate_product_data($excel_data_item)
    {   
        $response = [];
        $data = [];
        $error_array  = [];

        $product_code = $excel_data_item['product_code'];
        $product_name = $excel_data_item['product_name'];
        $supplier_code = $excel_data_item['supplier_code'];
        $category_code = $excel_data_item['category_code'];
        $tax_code = $excel_data_item['tax_code'];
        $purchase_price_excluding_tax = $excel_data_item['purchase_price_excluding_tax'];
        $sale_price_excluding_tax = $excel_data_item['sale_price_excluding_tax'];
        $sale_price_including_tax = $excel_data_item['sale_price_including_tax'];
        $quantity = $excel_data_item['quantity'];
        $stock_alert_quantity = $excel_data_item['stock_alert_quantity'];
        $description = $excel_data_item['description'];
        $discount_code = $excel_data_item['discount_code'];
        $add_on_group_codes = $excel_data_item['add_on_group_code_csv'];

        $status = $excel_data_item['status'];

        $validator = Validator::make($excel_data_item, [
            'product_name' => $this->get_validation_rules("name_label", true),
            'product_code' => $this->get_validation_rules("codes", true),
            'supplier_code' => $this->get_validation_rules("codes", true),
            'category_code' => $this->get_validation_rules("codes", true),
            'tax_code' => $this->get_validation_rules("codes", true),
            'discount_code' => $this->get_validation_rules("codes", false),
            'purchase_price_excluding_tax' => $this->get_validation_rules("numeric", true),
            'sale_price_excluding_tax' => $this->get_validation_rules("numeric", false),
            'sale_price_including_tax' => $this->get_validation_rules("numeric", false),
            'quantity' => $this->get_validation_rules("numeric", true),
            'stock_alert_quantity' => $this->get_validation_rules("numeric", false),
            'description' => $this->get_validation_rules("text", false),
            'status' => $this->get_validation_rules("filled",true)
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {
                $error_array[] = $message;
            }
        }

        $product_data_exists = ProductModel::select('id')
        ->where('product_code', '=', trim($product_code))
        ->first();
        if (!empty($product_data_exists)) {
            throw new Exception("Product code already assigned to a product", 400);
        }

        $supplier_data = SupplierModel::select('slack')
        ->where('supplier_code', '=', trim($supplier_code))
        ->active()
        ->first();
        if (empty($supplier_data)) {
            throw new Exception("Supplier not found or inactive in the system", 400);
        }

        $category_data = CategoryModel::select('slack')
        ->where('category_code', '=', trim($category_code))
        ->active()
        ->first();
        if (empty($category_data)) {
            throw new Exception("Category not found or inactive in the system", 400);
        }

        $taxcode_data = TaxcodeModel::select('slack', 'tax_type', 'total_tax_percentage')
        ->where('tax_code', '=', trim($tax_code))
        ->active()
        ->first();
        if (empty($taxcode_data)) {
            throw new Exception("Taxcode not found or inactive in the system", 400);
        }

        if (!empty($taxcode_data)) {
            if($taxcode_data->tax_type == 'INCLUSIVE'){
                if($sale_price_including_tax == ''){
                    throw new Exception("Sale price including tax is required", 400);
                }

                $tax_amount = calculate_tax($sale_price_including_tax, $taxcode_data->total_tax_percentage);

                $sale_price_excluding_tax = $sale_price_including_tax-$tax_amount;

            }else{
                if($sale_price_excluding_tax == ''){
                    throw new Exception("Sale price excluding tax is required", 400);
                }

                $tax_amount = calculate_tax($sale_price_excluding_tax, $taxcode_data->total_tax_percentage);

                $sale_price_including_tax = $sale_price_excluding_tax+$tax_amount;
            }
        }

        if($discount_code != ""){
            $discount_code_data = DiscountcodeModel::select('id')
            ->where('discount_code', '=', trim($discount_code))
            ->active()
            ->first();
            if (empty($discount_code_data)) {
                throw new Exception("Discount code not found or inactive in the system", 400);
            }
        }

        if ($add_on_group_codes != "") {
            $add_on_group_codes_array = explode(",",$add_on_group_codes);
            $add_on_group_codes_array = array_map('trim',$add_on_group_codes_array);
            if (count($add_on_group_codes_array) > 0) {

                $addon_group_data = AddonGroupModel::select('slack', 'addon_group_code')->whereIn('addon_group_code', $add_on_group_codes_array)->active()->get();
                
                $valid_addon_group_slack_array = $addon_group_data->pluck('slack')->toArray();
                $valid_addon_group_code_array = $addon_group_data->pluck('addon_group_code')->toArray();
                
                $invalid_addon_group_codes = array_diff($add_on_group_codes_array, $valid_addon_group_code_array);

                if(count($invalid_addon_group_codes) > 0){
                    $error_array[] = implode(',', $invalid_addon_group_codes).' : Invalid Add-on group code provided';
                }
                if ($addon_group_data->isEmpty()) {
                    $error_array[] = 'Invalid Add-on group provided';
                }else{
                    $addon_group_slack_array = [];
                    foreach($valid_addon_group_slack_array as $valid_addon_group_slack_array_item){
                        $addon_group_slack_array[]['slack'] = $valid_addon_group_slack_array_item;
                    }
                    $addon_group_slack_array_encoded = json_encode($addon_group_slack_array);
                }
            }
        }

        if ($status != "") {
            $status_data = MasterStatusModel::select('value')->where([
                ['value_constant', '=', strtoupper($status)],
                ['key', '=', 'PRODUCT_STATUS']
            ])->active()->first();
            if (!$status_data) {
                $error_array[] = 'Invalid status provided';
            }
        }

        if(count($error_array) == 0){
            $data = [
                "product_name" => $product_name,
                "product_code" => $product_code,
                "description" => $description,
                "category" => $category_data->slack,
                "supplier" => $supplier_data->slack,
                "tax_code" => $taxcode_data->slack,
                "quantity" => $quantity,
                "alert_quantity" => $stock_alert_quantity,
                "purchase_price" => $purchase_price_excluding_tax,
                "sale_price" => $sale_price_excluding_tax,
                "sale_amount_including_tax" => $sale_price_including_tax,
                "discount_code" => (isset($discount_code_data))?$discount_code_data->slack:NULL,
                "addon_group_values" => (isset($addon_group_slack_array_encoded))?$addon_group_slack_array_encoded:'',
                "status" => $status_data->value,
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }

    public function validate_ingredient_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];

        $product_code = $excel_data_item['product_code'];
        $product_name = $excel_data_item['product_name'];
        $supplier_code = $excel_data_item['supplier_code'];
        $category_code = $excel_data_item['category_code'];
        $tax_code = $excel_data_item['tax_code'];
        $purchase_price_excluding_tax = $excel_data_item['purchase_price_excluding_tax'];
        $sale_price_excluding_tax = $excel_data_item['sale_price_excluding_tax'];
        $quantity = $excel_data_item['quantity'];
        $stock_alert_quantity = $excel_data_item['stock_alert_quantity'];
        $description = $excel_data_item['description'];
        $discount_code = $excel_data_item['discount_code'];
        
        $status = $excel_data_item['status'];

        $validator = Validator::make($excel_data_item, [
            'product_name' => $this->get_validation_rules("name_label", true),
            'product_code' => $this->get_validation_rules("codes", true),
            'supplier_code' => $this->get_validation_rules("codes", true),
            'category_code' => $this->get_validation_rules("codes", true),
            'tax_code' => $this->get_validation_rules("codes", true),
            'discount_code' => $this->get_validation_rules("codes", false),
            'purchase_price_excluding_tax' => $this->get_validation_rules("numeric", true),
            'sale_price_excluding_tax' => $this->get_validation_rules("numeric", true),
            'quantity' => $this->get_validation_rules("numeric", true),
            'stock_alert_quantity' => $this->get_validation_rules("numeric", false),
            'description' => $this->get_validation_rules("text", false),
            'status' => $this->get_validation_rules("filled",true)
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {
                $error_array[] = $message;
            }
        }

        $product_data_exists = ProductModel::select('id')
        ->where('product_code', '=', trim($product_code))
        ->first();
        if (!empty($product_data_exists)) {
            throw new Exception("Product code already assigned to a product", 400);
        }

        $supplier_data = SupplierModel::select('slack')
        ->where('supplier_code', '=', trim($supplier_code))
        ->active()
        ->first();
        if (empty($supplier_data)) {
            throw new Exception("Supplier not found or inactive in the system", 400);
        }

        $category_data = CategoryModel::select('slack')
        ->where('category_code', '=', trim($category_code))
        ->active()
        ->first();
        if (empty($category_data)) {
            throw new Exception("Category not found or inactive in the system", 400);
        }

        $taxcode_data = TaxcodeModel::select('slack')
        ->where('tax_code', '=', trim($tax_code))
        ->active()
        ->first();
        if (empty($taxcode_data)) {
            throw new Exception("Taxcode not found or inactive in the system", 400);
        }

        if($discount_code != ""){
            $discount_code_data = DiscountcodeModel::select('id')
            ->where('discount_code', '=', trim($discount_code))
            ->active()
            ->first();
            if (empty($discount_code_data)) {
                throw new Exception("Discount code not found or inactive in the system", 400);
            }
        }

        if ($status != "") {
            $status_data = MasterStatusModel::select('value')->where([
                ['value_constant', '=', strtoupper($status)],
                ['key', '=', 'PRODUCT_STATUS']
            ])->active()->first();
            if (!$status_data) {
                $error_array[] = 'Invalid status provided';
            }
        }

        if(count($error_array) == 0){
            $data = [
                "product_name" => $product_name,
                "product_code" => $product_code,
                "description" => $description,
                "category" => $category_data->slack,
                "supplier" => $supplier_data->slack,
                "tax_code" => $taxcode_data->slack,
                "quantity" => $quantity,
                "alert_quantity" => $stock_alert_quantity,
                "purchase_price" => $purchase_price_excluding_tax,
                "sale_price" => $sale_price_excluding_tax,
                "discount_code" => (isset($discount_code_data))?$discount_code_data->slack:NULL,
                "is_ingredient" => 1,
                "is_addon_product" => 0,
                "status" => $status_data->value,
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }

    public function validate_addon_product_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];

        $product_code = $excel_data_item['product_code'];
        $product_name = $excel_data_item['product_name'];
        $supplier_code = $excel_data_item['supplier_code'];
        $category_code = $excel_data_item['category_code'];
        $tax_code = $excel_data_item['tax_code'];
        $purchase_price_excluding_tax = $excel_data_item['purchase_price_excluding_tax'];
        $sale_price_excluding_tax = $excel_data_item['sale_price_excluding_tax'];
        $quantity = $excel_data_item['quantity'];
        $stock_alert_quantity = $excel_data_item['stock_alert_quantity'];
        $description = $excel_data_item['description'];
        $discount_code = $excel_data_item['discount_code'];
        
        $status = $excel_data_item['status'];

        $validator = Validator::make($excel_data_item, [
            'product_name' => $this->get_validation_rules("name_label", true),
            'product_code' => $this->get_validation_rules("codes", true),
            'supplier_code' => $this->get_validation_rules("codes", true),
            'category_code' => $this->get_validation_rules("codes", true),
            'tax_code' => $this->get_validation_rules("codes", true),
            'discount_code' => $this->get_validation_rules("codes", false),
            'purchase_price_excluding_tax' => $this->get_validation_rules("numeric", true),
            'sale_price_excluding_tax' => $this->get_validation_rules("numeric", true),
            'quantity' => $this->get_validation_rules("numeric", true),
            'stock_alert_quantity' => $this->get_validation_rules("numeric", false),
            'description' => $this->get_validation_rules("text", false),
            'status' => $this->get_validation_rules("filled",true)
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {
                $error_array[] = $message;
            }
        }

        $product_data_exists = ProductModel::select('id')
        ->where('product_code', '=', trim($product_code))
        ->first();
        if (!empty($product_data_exists)) {
            throw new Exception("Product code already assigned to a product", 400);
        }

        $supplier_data = SupplierModel::select('slack')
        ->where('supplier_code', '=', trim($supplier_code))
        ->active()
        ->first();
        if (empty($supplier_data)) {
            throw new Exception("Supplier not found or inactive in the system", 400);
        }

        $category_data = CategoryModel::select('slack')
        ->where('category_code', '=', trim($category_code))
        ->active()
        ->first();
        if (empty($category_data)) {
            throw new Exception("Category not found or inactive in the system", 400);
        }

        $taxcode_data = TaxcodeModel::select('slack')
        ->where('tax_code', '=', trim($tax_code))
        ->active()
        ->first();
        if (empty($taxcode_data)) {
            throw new Exception("Taxcode not found or inactive in the system", 400);
        }

        if($discount_code != ""){
            $discount_code_data = DiscountcodeModel::select('id')
            ->where('discount_code', '=', trim($discount_code))
            ->active()
            ->first();
            if (empty($discount_code_data)) {
                throw new Exception("Discount code not found or inactive in the system", 400);
            }
        }

        if ($status != "") {
            $status_data = MasterStatusModel::select('value')->where([
                ['value_constant', '=', strtoupper($status)],
                ['key', '=', 'PRODUCT_STATUS']
            ])->active()->first();
            if (!$status_data) {
                $error_array[] = 'Invalid status provided';
            }
        }

        if(count($error_array) == 0){
            $data = [
                "product_name" => $product_name,
                "product_code" => $product_code,
                "description" => $description,
                "category" => $category_data->slack,
                "supplier" => $supplier_data->slack,
                "tax_code" => $taxcode_data->slack,
                "quantity" => $quantity,
                "alert_quantity" => $stock_alert_quantity,
                "purchase_price" => $purchase_price_excluding_tax,
                "sale_price" => $sale_price_excluding_tax,
                "discount_code" => (isset($discount_code_data))?$discount_code_data->slack:NULL,
                "is_ingredient" => 0,
                "is_addon_product" => 1,
                "status" => $status_data->value,
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }

    /* 
        upload and update section begins
    */

    public function update_data(Request $request){
        try {
            
            $update_type = $request->upload_type;
            $update_file = $request->upload_file;

            if(empty($update_type)){
                throw new Exception("Invalid request", 400);
            }
            if(empty($update_file)){
                throw new Exception("File is required", 400);
            }

            $filename = '';
            switch($update_type){
                case "USER":
                if(!check_access(['A_UPDATE_USER'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "STORE":
                if(!check_access(['A_UPDATE_STORE'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "SUPPLIER":
                if(!check_access(['A_UPDATE_SUPPLIER'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "CATEGORY":
                if(!check_access(['A_UPDATE_CATEGORY'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "PRODUCT":
                if(!check_access(['A_UPDATE_PRODUCT'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "INGREDIENT":
                if(!check_access(['A_UPDATE_INGREDIENT'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "ADDON_PRODUCT":
                if(!check_access(['A_UPDATE_ADDON_PRODUCT'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
                case "PRODUCT_VARIANT":
                if(!check_access(['A_UPDATE_PRODUCT_VARIANT'], true)){
                    throw new Exception("Invalid request", 400);
                }
                break;
            }

            $custom_filename = strtolower($update_type).'_'.date('Y_m_d_H_i_s').'_'.uniqid();

            $extension = $update_file->getClientOriginalExtension();
            $custom_file = $custom_filename.".".$extension;

            Storage::disk('updates')->delete(
                [
                    $custom_file
                ]
            );

            $path = Storage::disk('updates')->putFileAs('/', $update_file, $custom_file);

            $update_response = $this->forward_update_request($update_type, $custom_file);

            if($update_response['update_status'] == false){
                Storage::disk('updates')->delete(
                    [
                        $custom_file
                    ]
                );
            }
            
            return response()->json($this->generate_response(
                array(
                    "message" => "update file read successfully",
                    "data" => $update_response,
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

    public function forward_update_request($update_type, $update_file){

        switch($update_type){
            case "USER":
                $response = $this->update_file($update_type, $update_file);
                if($response['update_status'] == true){
                    foreach ($response['data'] as $user_array_item) {
                            
                        $update_data = $user_array_item['update_data'];
                        $update_key = $user_array_item['update_key'];
                        $user_stores = (isset($update_data['user_stores']))?$update_data['user_stores']:'';
                        
                        unset($update_data['user_stores']);

                        if(!empty($update_data) && !empty($update_key)){
                            $data = UserModel::where('slack', $update_key)
                            ->update($update_data);
                        }

                        if($user_stores != '' && !empty($update_key)){
                            $user_api = new UserAPI();
                            $request = request();
                            $request->merge(['user_stores' => $user_stores]);
                            $user_api->update_user_stores($request, $update_key);
                        }
                            
                    }

                    unset($response['data']);
                }
            break;
            case "STORE":
                $response = $this->update_file($update_type, $update_file);
                if($response['update_status'] == true){
                    foreach ($response['data'] as $store_array_item) {
                        
                        $update_data = $store_array_item['update_data'];
                        $update_key = $store_array_item['update_key'];

                        if(!empty($update_data) && !empty($update_key)){
                            $data = StoreModel::where('slack', $update_key)
                            ->update($update_data);
                        }
                    }
                    unset($response['data']);
                }
            break;
            case "SUPPLIER":
                $response = $this->update_file($update_type, $update_file);
                if($response['update_status'] == true){
                    foreach ($response['data'] as $supplier_array_item) {
                        $update_data = $supplier_array_item['update_data'];
                        $update_key = $supplier_array_item['update_key'];

                        if(!empty($update_data) && !empty($update_key)){
                            $data = SupplierModel::where('slack', $update_key)
                            ->update($update_data);
                        }
                    }
                    unset($response['data']);
                }
            break;
            case "CATEGORY":
                $response = $this->update_file($update_type, $update_file);
                if($response['update_status'] == true){
                    foreach ($response['data'] as $category_array_item) {
                        $update_data = $category_array_item['update_data'];
                        $update_key = $category_array_item['update_key'];

                        if(!empty($update_data) && !empty($update_key)){
                            $data = CategoryModel::where('slack', $update_key)
                            ->update($update_data);
                        }
                    }
                    unset($response['data']);
                }
            break;
            case "PRODUCT":
                $response = $this->update_file($update_type, $update_file);
                if($response['update_status'] == true){
                    foreach ($response['data'] as $product_array_item) {
                        $update_data = $product_array_item['update_data'];
                        $update_key = $product_array_item['update_key'];

                        if(!empty($update_data) && !empty($update_key)){

                            $addon_group_data_array = $update_data['addon_group_slack_array'];
                            unset($update_data['addon_group_slack_array']);

                            $data = ProductModel::where('slack', $update_key)
                            ->update($update_data);

                            if(!empty($addon_group_data_array)){

                                $product_data = ProductModel::select('id')
                                ->where('slack', '=', trim($update_key))
                                ->first();

                                ProductAddonGroupModel::where('product_id', $product_data->id)->delete();
                
                                foreach($addon_group_data_array as $key => $addon_group_data_array_item){
                
                                    
                                    if(!empty($addon_group_data_array_item['slack'])){
                
                                        $addon_group_data = AddonGroupModel::select('id')
                                        ->where('slack', '=', trim($addon_group_data_array_item['slack']))
                                        ->active()
                                        ->first();
                
                                        if (!empty($addon_group_data)) {
                                            $product_addon_group_array[] = [
                                                "product_id" => $product_data->id,
                                                "addon_group_id" => $addon_group_data->id,
                                                "created_by" => request()->logged_user_id
                                            ];
                                        }
                                    }
                                }
                
                                if(!empty($product_addon_group_array) && count($product_addon_group_array)>0){
                                    foreach($product_addon_group_array as $product_addon_group_array_item){
                                        ProductAddonGroupModel::create($product_addon_group_array_item);
                                    }
                                }
                            }
                        }
                    }
                    unset($response['data']);
                }
            break;
            case "INGREDIENT":
                $response = $this->update_file($update_type, $update_file);
                if($response['update_status'] == true){
                    foreach ($response['data'] as $product_array_item) {
                        $update_data = $product_array_item['update_data'];
                        $update_key = $product_array_item['update_key'];

                        if(!empty($update_data) && !empty($update_key)){
                            $data = ProductModel::where('slack', $update_key)
                            ->update($update_data);
                        }
                    }
                    unset($response['data']);
                }
            break;
            case "ADDON_PRODUCT":
                $response = $this->update_file($update_type, $update_file);
                if($response['update_status'] == true){
                    foreach ($response['data'] as $product_array_item) {
                        $update_data = $product_array_item['update_data'];
                        $update_key = $product_array_item['update_key'];

                        if(!empty($update_data) && !empty($update_key)){
                            $data = ProductModel::where('slack', $update_key)
                            ->update($update_data);
                        }
                    }
                    unset($response['data']);
                }
            break;
            case "PRODUCT_VARIANT":
                $response = $this->update_file($update_type, $update_file);
                if($response['update_status'] == true){
                    $product_api = new ProductAPI();
                    foreach ($response['data'] as $variant_array_item) {
                        if(!empty($variant_array_item)){
                            $request = request();
                            $request->merge(['variants' => $variant_array_item]);
                            $product_api->add_variants_from_import($request);
                        }
                    }
                    unset($response['data']);
                }
            break;
        }

        return $response;
    }

    public function update_file($update_type, $update_file){
        
        $data_array   = [];
        $error_array  = [];

        switch($update_type){
            case "USER":
                $valid_template = $this->validate_upload_update_template("USER", $update_file);
            break;
            case "STORE":
                $valid_template = $this->validate_upload_update_template("STORE", $update_file);
            break;
            case "SUPPLIER":
                $valid_template = $this->validate_upload_update_template("SUPPLIER", $update_file);
            break;
            case "CATEGORY":
                $valid_template = $this->validate_upload_update_template("CATEGORY", $update_file);
            break;
            case "PRODUCT":
                $valid_template = $this->validate_upload_update_template("PRODUCT", $update_file);
            break;
            case "INGREDIENT":
                $valid_template = $this->validate_upload_update_template("INGREDIENT", $update_file);
            break;
            case "ADDON_PRODUCT":
                $valid_template = $this->validate_upload_update_template("ADDON_PRODUCT", $update_file);
            break;
            case "PRODUCT_VARIANT":
                $valid_template = $this->validate_upload_update_template("PRODUCT_VARIANT", $update_file);
            break;
        }
        
        if($valid_template){
            
            $upload_folder = Config::get('constants.upload.updates.upload_path');
            $excel_data = (new DataImport)->toArray( $upload_folder.$update_file);
            
            $excel_data = $excel_data[0];
            if(count($excel_data) == 0){
                throw new Exception("Please provide some data in the excel sheet.", 400);
            }

            foreach($excel_data as $key => $excel_data_item){
                switch($update_type){
                    case "USER":
                        $validate_response = $this->validate_update_user_data($excel_data_item);
                    break;
                    case "STORE":
                        $validate_response = $this->validate_update_store_data($excel_data_item);
                    break;
                    case "SUPPLIER":
                        $validate_response = $this->validate_update_supplier_data($excel_data_item);
                    break;
                    case "CATEGORY":
                        $validate_response = $this->validate_update_category_data($excel_data_item);
                    break;
                    case "PRODUCT":
                        $validate_response = $this->validate_update_product_data($excel_data_item);
                    break;
                    case "INGREDIENT":
                        $validate_response = $this->validate_update_ingredient_data($excel_data_item);
                    break;
                    case "ADDON_PRODUCT":
                        $validate_response = $this->validate_update_addon_product_data($excel_data_item);
                    break;
                    case "PRODUCT_VARIANT":
                        $validate_response = $this->validate_update_product_variant_data($excel_data_item);
                    break;
                }
                if(count($validate_response['error_list'])>0){
                    $error_array[$key+2] = $validate_response['error_list'];
                }
                $data_array[] = $validate_response['data'];
            }

            $response = [
                'update_status' => (count($error_array)>0)?false:true,
                'errors' => $error_array
            ];
            if(count($error_array) == 0){
                $response['data'] = $data_array;
            }

            return $response;

        }else{
            throw new Exception("Invalid file uploaded", 400);
        }
    }

    public function validate_upload_update_template($template_type, $update_file){
        $valid_template = false;
        
        //check template if valid
        switch($template_type){
            case "USER":
                $template_format = Config::get('constants.upload.updates.user_format');
            break;
            case "STORE":
                $template_format = Config::get('constants.upload.updates.store_format');
            break;
            case "SUPPLIER":
                $template_format = Config::get('constants.upload.updates.supplier_format');
            break;
            case "CATEGORY":
                $template_format = Config::get('constants.upload.updates.category_format');
            break;
            case "PRODUCT":
                $template_format = Config::get('constants.upload.updates.product_format');
            break;
            case "INGREDIENT":
                $template_format = Config::get('constants.upload.updates.ingredient_format');
            break;
            case "ADDON_PRODUCT":
                $template_format = Config::get('constants.upload.updates.addon_product_format');
            break;
            case "PRODUCT_VARIANT":
                $template_format = Config::get('constants.upload.updates.product_variant_format');
            break;
        }
        $default_format_file_path = public_path($template_format);
        $format_headings = (new HeadingRowImport)->toArray($default_format_file_path);
        $format_headings = array_filter(array_map('trim', $format_headings[0][0]));
        
        $upload_folder = Config::get('constants.upload.updates.upload_path');
        $uploaded_file_headings = (new HeadingRowImport)->toArray($upload_folder.$update_file);
        $uploaded_file_headings = array_filter(array_map('trim', $uploaded_file_headings[0][0])); 

        $valid_template = ($format_headings == $uploaded_file_headings)?true:false;

        return $valid_template;
    }
    
    public function validate_update_user_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];
        $stores = '';

        $user_code       = $excel_data_item['user_code'];
        $fullname       = $excel_data_item['fullname'];
        $email          = $excel_data_item['email'];
        $contact_number = $excel_data_item['contact_number'];
        $role_code      = $excel_data_item['role_code'];
        $status         = $excel_data_item['status'];
        $store_codes    = $excel_data_item['store_codes_csv'];

        $validator_config = [];
        $validator_config['user_code'] = $this->get_validation_rules("codes", true);

        if($user_code != ""){
            $user_data = UserModel::where('user_code', '=', $user_code)->first();
            if (!$user_data) {
                $error_array[] = 'Invalid user code provided';
            }else{
                $slack = $user_data->slack;
            }
        }

        if(isset($slack)){
            if ($fullname != "") {
                $validator_config['fullname'] = $this->get_validation_rules("fullname", false);
            }

            if ($email != "") {
                $validator_config['email']  = $this->get_validation_rules("email", false);
                $email_data = UserModel::where('email', '=', $email)->where('slack', '!=', $slack)->first();
                if ($email_data) {
                    $error_array[] = 'Email already assigned to another user';
                }
            }

            if ($contact_number != "") {
                $validator_config['contact_number'] = $this->get_validation_rules("phone", false);
                $contact_number_data = UserModel::where('phone', '=', $contact_number)->where('slack', '!=', $slack)->first();
                if ($contact_number_data) {
                    $error_array[] = 'Contact number already assigned to another user';
                }
            }

            if ($role_code != "") {
                $validator_config['role_code'] = $this->get_validation_rules("codes", false);
                $role_data = RoleModel::select('slack')->where('role_code', '=', $role_code)->resolveSuperAdminRole()->active()->first();
                if (!$role_data) {
                    $error_array[] = 'Invalid role code provided';
                }
            }

            if($status != "") {
                $status_data = MasterStatusModel::select('value')->where([
                    ['value_constant', '=', strtoupper($status)],
                    ['key', '=', 'USER_STATUS']
                ])->active()->first();
                if (!$status_data) {
                    $error_array[] = 'Invalid status provided';
                }
            }

            if($store_codes != '') {
                $store_codes_array = explode(",",$store_codes);
                $store_codes_array = array_map('trim',$store_codes_array);
                if (count($store_codes_array) > 0) {
                    
                    $store_data = StoreModel::whereIn('store_code', $store_codes_array)->active()->get();
                    $valid_store_slack_array = $store_data->pluck('slack')->toArray();
                    $valid_store_code_array = $store_data->pluck('store_code')->toArray();
                    
                    $invalid_store_codes = array_diff($store_codes_array, $valid_store_code_array);

                    if(count($invalid_store_codes) > 0){
                        $error_array[] = implode(',', $invalid_store_codes).' : Invalid stores provided';
                    }
                    if ($store_data->isEmpty()) {
                        $error_array[] = 'Invalid stores provided';
                    }else{
                        $stores = implode(',', $valid_store_slack_array);
                    }
                }
            }

            if(!empty($validator_config)) {
                $validator = Validator::make($excel_data_item, $validator_config);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    foreach ($errors->all() as $message) {
                        $error_array[] = $message;
                    }
                }
            }
        }
        
        if(count($error_array) == 0) {
            $update_data = [
                "fullname"  => $fullname,
                "email"     => $email,
                "phone"     => $contact_number,
                "role"      => (!empty($role_data))?$role_data->id:'',
                "status"    => (!empty($status_data))?$status_data->value:'',
                "user_stores" => $stores
            ];
            $update_data = array_filter($update_data, 'skip_zero_array_filter');

            $data = [
                "update_data" => $update_data,
                "update_key" => (isset($slack))?$slack:''
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }

    public function validate_update_store_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];

        $store_code = $excel_data_item['store_code'];
        $name = $excel_data_item['name'];
        $address = $excel_data_item['address'];
        $pincode = $excel_data_item['pincode'];
        $tax_number = $excel_data_item['tax_number'];
        $primary_contact_no = $excel_data_item['primary_contact_no'];
        $secondary_contact_no = $excel_data_item['secondary_contact_no'];
        $primary_email = $excel_data_item['primary_email'];
        $secondary_email = $excel_data_item['secondary_email'];
        $tax_code = $excel_data_item['tax_code'];
        $discount_code = $excel_data_item['discount_code'];
        $status = $excel_data_item['status'];

        $validator_config = [];
        $validator_config['store_code'] = $this->get_validation_rules("codes", true);

        if($store_code != ""){
            $store_data = StoreModel::where('store_code', '=', $store_code)->first();
            if (!$store_data) {
                $error_array[] = 'Invalid store code provided';
            }else{
                $slack = $store_data->slack;
            }
        }

        if(isset($slack)){

            if($name != ""){
                $validator_config['name'] = $this->get_validation_rules("name_label", false);
            }

            if($address != ""){
                $validator_config['address'] = $this->get_validation_rules("text", false);
            }

            if($pincode != ""){
                $validator_config['pincode'] = $this->get_validation_rules("pincode", false);
            }

            if($tax_number != ""){
                $validator_config['tax_number'] = $this->get_validation_rules("name_label", false);
            }

            if($primary_contact_no != ""){
                $validator_config['primary_contact'] = $this->get_validation_rules("phone", false);
            }

            if($secondary_contact_no != ""){
                $validator_config['secondary_contact'] = $this->get_validation_rules("phone", false);
            }

            if($primary_email != ""){
                $validator_config['primary_email'] = $this->get_validation_rules("email", false);
            }

            if($secondary_email != ""){
                $validator_config['secondary_email'] = $this->get_validation_rules("email", false);
            }

            if($tax_code != ""){
                $validator_config['tax_code'] = $this->get_validation_rules("codes", false);
                $taxcode_data = TaxcodeModel::select('id')
                ->where('tax_code', '=', trim($tax_code))
                ->active()
                ->first();
                if (empty($taxcode_data)) {
                    throw new Exception("Taxcode not found or inactive in the system", 400);
                }
            }

            if($discount_code != ""){
                $validator_config['discount_code'] = $this->get_validation_rules("codes", false);
                $discount_code_data = DiscountcodeModel::select('id')
                ->where('discount_code', '=', trim($discount_code))
                ->active()
                ->first();
                if (empty($discount_code_data)) {
                    throw new Exception("Discount code not found or inactive in the system", 400);
                }
            }

            if($status != "") {
                $status_data = MasterStatusModel::select('value')->where([
                    ['value_constant', '=', strtoupper($status)],
                    ['key', '=', 'STORE_STATUS']
                ])->active()->first();
                if (!$status_data) {
                    $error_array[] = 'Invalid status provided';
                }
            }

            if(!empty($validator_config)) {
                $validator = Validator::make($excel_data_item, $validator_config);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    foreach ($errors->all() as $message) {
                        $error_array[] = $message;
                    }
                }
            }

        }

        if(count($error_array) == 0) {
            $update_data = [
                "name" => $name,
                "tax_number" => $tax_number,
                "address" => $address,
                "pincode" => $pincode,
                "primary_contact" => $primary_contact_no,
                "secondary_contact" => $secondary_contact_no,
                "primary_email" => $primary_email,
                "secondary_email" => $secondary_email,
                "tax_code_id" => (isset($taxcode_data))?$taxcode_data->id:NULL,
                "discount_code_id" => (isset($discount_code_data))?$discount_code_data->id:NULL,
                "status" => (isset($status_data))?$status_data->value:'',
            ];
            $update_data = array_filter($update_data, 'skip_zero_array_filter');

            $data = [
                "update_data" => $update_data,
                "update_key" => (isset($slack))?$slack:''
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }

    public function validate_update_supplier_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];

        $supplier_code = $excel_data_item['supplier_code'];
        $supplier_name = $excel_data_item['supplier_name'];
        $contact_email = $excel_data_item['contact_email'];
        $contact_number = $excel_data_item['contact_number'];
        $address = $excel_data_item['address'];
        $pincode = $excel_data_item['pincode'];
        $status = $excel_data_item['status'];

        $validator_config = [];
        $validator_config['supplier_code'] = $this->get_validation_rules("codes", true);

        $supplier_data = SupplierModel::where('supplier_code', '=', trim($supplier_code))->first();
        if (!$supplier_data) {
            $error_array[] = 'Invalid supplier code provided';
        }else{
            $slack = $supplier_data->slack;
        }

        if(isset($slack)){

            if($supplier_name != ""){
                $validator_config['supplier_name'] = $this->get_validation_rules("name_label", false);
            }

            if($contact_email != ""){
                $validator_config['contact_email'] = $this->get_validation_rules("email", false);
            }

            if($contact_number != ""){
                $validator_config['contact_number'] = $this->get_validation_rules("phone", false);
            }

            if($address != ""){
                $validator_config['address'] = $this->get_validation_rules("text", false);
            }

            if($pincode != ""){
                $validator_config['pincode'] = $this->get_validation_rules("pincode", false);
            }

            if($status != "") {
                $status_data = MasterStatusModel::select('value')->where([
                    ['value_constant', '=', strtoupper($status)],
                    ['key', '=', 'SUPPLIER_STATUS']
                ])->active()->first();
                if (!$status_data) {
                    $error_array[] = 'Invalid status provided';
                }
            }

            if(!empty($validator_config)) {
                $validator = Validator::make($excel_data_item, $validator_config);
                if ($validator->fails()) {
                    $errors = $validator->errors();
                    foreach ($errors->all() as $message) {
                        $error_array[] = $message;
                    }
                }
            }
        }

        if(count($error_array) == 0) {
            $update_data = [
                "supplier_name" => $supplier_name,
                "address" => $address,
                "phone" => $contact_number,
                "email" => $contact_email,
                "pincode" => $pincode,
                "status" => (isset($status_data))?$status_data->value:'',
            ];
            $update_data = array_filter($update_data, 'skip_zero_array_filter');

            $data = [
                "update_data" => $update_data,
                "update_key" => (isset($slack))?$slack:''
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];

        return $response;
    }

    public function validate_update_category_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];

        $excel_data_item = array_map('trim', $excel_data_item);
        $category_code = $excel_data_item['category_code'];
        $category_name = $excel_data_item['category_name'];
        $description = $excel_data_item['description'];
        $status = $excel_data_item['status'];
        
        $validator_config = [];
        $validator_config['category_code'] = $this->get_validation_rules("codes", true);

        $category_data = CategoryModel::where('category_code', '=', trim($category_code))->first();
        if (!$category_data) {
            $error_array[] = 'Invalid category code provided';
        }else{
            $slack = $category_data->slack;
        }

        if(isset($slack)){

            if($category_name != ""){
                $validator_config['category_name'] = $this->get_validation_rules("name_label", false);
            }

            if($description != ""){
                $validator_config['description'] = $this->get_validation_rules("text", false);
            }

            if($status != "") {
                $status_data = MasterStatusModel::select('value')->where([
                    ['value_constant', '=', strtoupper($status)],
                    ['key', '=', 'CATEGORY_STATUS']
                ])->active()->first();
                if (!$status_data) {
                    $error_array[] = 'Invalid status provided';
                }
            }

        }
        
        if(count($error_array) == 0) {
            $update_data = [
                "label" => $category_name,
                "description" => $description,
                "status" => (isset($status_data))?$status_data->value:''
            ];
            $update_data = array_filter($update_data, 'skip_zero_array_filter');

            $data = [
                "update_data" => $update_data,
                "update_key" => (isset($slack))?$slack:''
            ];
        }

        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];

        return $response;
    }

    public function validate_update_product_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];

        $product_code = $excel_data_item['product_code'];
        $product_name = $excel_data_item['product_name'];
        $supplier_code = $excel_data_item['supplier_code'];
        $category_code = $excel_data_item['category_code'];
        $tax_code = $excel_data_item['tax_code'];
        $purchase_price_excluding_tax = $excel_data_item['purchase_price_excluding_tax'];
        $sale_price_excluding_tax = $excel_data_item['sale_price_excluding_tax'];
        $sale_price_including_tax = $excel_data_item['sale_price_including_tax'];
        $quantity = $excel_data_item['quantity'];
        $stock_alert_quantity = $excel_data_item['stock_alert_quantity'];
        $description = $excel_data_item['description'];
        $discount_code = $excel_data_item['discount_code'];
        $add_on_group_codes = $excel_data_item['add_on_group_code_csv'];
        
        $status = $excel_data_item['status'];

        $validator_config = [];
        $validator_config['product_code'] = $this->get_validation_rules("codes", true);

        $product_data = ProductModel::where('product_code', '=', trim($product_code))->first();
        if (!$product_data) {
            $error_array[] = 'Invalid product code provided';
        }else{
            $slack = $product_data->slack;
            $tax_code_id = $product_data->tax_code_id;

            $product_taxcode_data = TaxcodeModel::select('tax_type', 'total_tax_percentage')
            ->where('id', '=', trim($tax_code_id))
            ->active()
            ->first();
        }

        if(isset($slack)){

            if($product_name != ""){
                $validator_config['product_name'] = $this->get_validation_rules("name_label", false);
            }

            if($supplier_code != ""){
                $validator_config['supplier_code'] = $this->get_validation_rules("codes", false);
                
                $supplier_data = SupplierModel::select('id')
                ->where('supplier_code', '=', trim($supplier_code))
                ->active()
                ->first();
                if (empty($supplier_data)) {
                    throw new Exception("Supplier not found or inactive in the system", 400);
                }
            }

            if($category_code != ""){
                $validator_config['category_code'] = $this->get_validation_rules("codes", false);

                $category_data = CategoryModel::select('id')
                ->where('category_code', '=', trim($category_code))
                ->active()
                ->first();
                if (empty($category_data)) {
                    throw new Exception("Category not found or inactive in the system", 400);
                }
            }

            if($tax_code != ""){
                $validator_config['tax_code'] = $this->get_validation_rules("codes", false);

                $taxcode_data = TaxcodeModel::select('id', 'tax_type', 'total_tax_percentage')
                ->where('tax_code', '=', trim($tax_code))
                ->active()
                ->first();
                if (empty($taxcode_data)) {
                    throw new Exception("Taxcode not found or inactive in the system", 400);
                }

                if (!empty($taxcode_data) && $sale_price_including_tax == "" && $sale_price_excluding_tax == "") {
                    if($taxcode_data->tax_type == 'INCLUSIVE'){
                        if($product_data->sale_amount_including_tax == ''){
                            throw new Exception("Sale price including tax is required", 400);
                        }

                        $sale_price_including_tax = $product_data->sale_amount_including_tax;
                        $tax_amount = calculate_tax($sale_price_including_tax, $taxcode_data->total_tax_percentage);
                        $sale_price_excluding_tax = $sale_price_including_tax-$tax_amount;
        
                    }else{
                        if($product_data->sale_amount_excluding_tax == ''){
                            throw new Exception("Sale price excluding tax is required", 400);
                        }
        
                        $sale_price_excluding_tax = $product_data->sale_amount_excluding_tax;
                        $tax_amount = calculate_tax($sale_price_excluding_tax, $taxcode_data->total_tax_percentage);
                        $sale_price_including_tax = $sale_price_excluding_tax+$tax_amount;
                    }
                }
            }

            if($discount_code != ""){
                $validator_config['discount_code'] = $this->get_validation_rules("codes", false);

                $discount_code_data = DiscountcodeModel::select('id')
                ->where('discount_code', '=', trim($discount_code))
                ->active()
                ->first();
                if (empty($discount_code_data)) {
                    throw new Exception("Discount code not found or inactive in the system", 400);
                }
            }

            if($sale_price_including_tax != "" || $sale_price_excluding_tax != ""){
                if (!empty($taxcode_data)) {
                    $tax_type = $taxcode_data->tax_type; 
                    if($tax_type == 'INCLUSIVE'){
                        if($sale_price_excluding_tax == ''){
                            throw new Exception("Sale price including tax is required", 400);
                        }

                        $sale_price_including_tax = $sale_price_including_tax;
                        $tax_amount = calculate_tax($sale_price_including_tax, $taxcode_data->total_tax_percentage);
                        $sale_price_excluding_tax = $sale_price_including_tax-$tax_amount;
                    }else{
                        if($sale_price_excluding_tax == ''){
                            throw new Exception("Sale price excluding tax is required", 400);
                        }
        
                        $sale_price_excluding_tax = $sale_price_excluding_tax;
                        $tax_amount = calculate_tax($sale_price_excluding_tax, $taxcode_data->total_tax_percentage);
                        $sale_price_including_tax = $sale_price_excluding_tax+$tax_amount;
                    }
                }else{
                    $tax_type = $product_taxcode_data->tax_type;
                    if($tax_type == 'INCLUSIVE'){
                        if($sale_price_including_tax == ''){
                            throw new Exception("Sale price including tax is required", 400);
                        }

                        $sale_price_including_tax = $sale_price_including_tax;
                        $tax_amount = calculate_tax($sale_price_including_tax, $product_taxcode_data->total_tax_percentage);
                        $sale_price_excluding_tax = $sale_price_including_tax-$tax_amount;
                    }else{
                        if($sale_price_excluding_tax == ''){
                            throw new Exception("Sale price excluding tax is required", 400);
                        }
        
                        $sale_price_excluding_tax = $sale_price_excluding_tax;
                        $tax_amount = calculate_tax($sale_price_excluding_tax, $product_taxcode_data->total_tax_percentage);
                        $sale_price_including_tax = $sale_price_excluding_tax+$tax_amount;
                    }
                }
            }

            if($purchase_price_excluding_tax != ""){
                $validator_config['purchase_price_excluding_tax'] = $this->get_validation_rules("numeric", false);
            }

            if($quantity != ""){
                $validator_config['quantity'] = $this->get_validation_rules("numeric", false);
            }

            if($stock_alert_quantity != ""){
                $validator_config['stock_alert_quantity'] = $this->get_validation_rules("numeric", false);
            }

            if($description != ""){
                $validator_config['description'] = $this->get_validation_rules("text", false);
            }

            if ($add_on_group_codes != "") {
                $add_on_group_codes_array = explode(",",$add_on_group_codes);
                $add_on_group_codes_array = array_map('trim',$add_on_group_codes_array);
                if (count($add_on_group_codes_array) > 0) {
    
                    $addon_group_data = AddonGroupModel::select('slack', 'addon_group_code')->whereIn('addon_group_code', $add_on_group_codes_array)->active()->get();
                    
                    $valid_addon_group_slack_array = $addon_group_data->pluck('slack')->toArray();
                    $valid_addon_group_code_array = $addon_group_data->pluck('addon_group_code')->toArray();
                    
                    $invalid_addon_group_codes = array_diff($add_on_group_codes_array, $valid_addon_group_code_array);
    
                    if(count($invalid_addon_group_codes) > 0){
                        $error_array[] = implode(',', $invalid_addon_group_codes).' : Invalid Add-on group code provided';
                    }
                    if ($addon_group_data->isEmpty()) {
                        $error_array[] = 'Invalid Add-on group provided';
                    }else{
                        $addon_group_slack_array = [];
                        foreach($valid_addon_group_slack_array as $valid_addon_group_slack_array_item){
                            $addon_group_slack_array[]['slack'] = $valid_addon_group_slack_array_item;
                        }
                    }
                }
            }

            if ($status != "") {
                $status_data = MasterStatusModel::select('value')->where([
                    ['value_constant', '=', strtoupper($status)],
                    ['key', '=', 'PRODUCT_STATUS']
                ])->active()->first();
                if (!$status_data) {
                    $error_array[] = 'Invalid status provided';
                }
            }

        }

        if(count($error_array) == 0) {
            $update_data = [
                "name" => $product_name,
                "description" => $description,
                "category_id" => (isset($category_data))?$category_data->id:'',
                "supplier_id" => (isset($supplier_data))?$supplier_data->id:'',
                "tax_code_id" => (isset($taxcode_data))?$taxcode_data->id:'',
                "discount_code_id" => (isset($discount_code_data))?$discount_code_data->id:NULL,
                "quantity" => $quantity,
                "alert_quantity" => $stock_alert_quantity,
                "purchase_amount_excluding_tax" => $purchase_price_excluding_tax,
                "sale_amount_excluding_tax" => $sale_price_excluding_tax,
                "sale_amount_including_tax" => $sale_price_including_tax,
                "status" => (isset($status_data))?$status_data->value:'',
                "addon_group_slack_array" => (isset($addon_group_slack_array))?$addon_group_slack_array:[]
            ];
            $update_data = array_filter($update_data, 'skip_zero_array_filter');

            $data = [
                "update_data" => $update_data,
                "update_key" => (isset($slack))?$slack:''
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }
    
    public function validate_update_ingredient_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];

        $product_code = $excel_data_item['product_code'];
        $product_name = $excel_data_item['product_name'];
        $supplier_code = $excel_data_item['supplier_code'];
        $category_code = $excel_data_item['category_code'];
        $tax_code = $excel_data_item['tax_code'];
        $purchase_price_excluding_tax = $excel_data_item['purchase_price_excluding_tax'];
        $sale_price_excluding_tax = $excel_data_item['sale_price_excluding_tax'];
        $quantity = $excel_data_item['quantity'];
        $stock_alert_quantity = $excel_data_item['stock_alert_quantity'];
        $description = $excel_data_item['description'];
        $discount_code = $excel_data_item['discount_code'];
        $status = $excel_data_item['status'];

        $validator_config = [];
        $validator_config['product_code'] = $this->get_validation_rules("codes", true);

        $product_data = ProductModel::where('product_code', '=', trim($product_code))->isIngredient()->first();
        if (!$product_data) {
            $error_array[] = 'Invalid product code provided (The product might not be an ingredient or the product might not exist in the system)';
        }else{
            $slack = $product_data->slack;
        }

        if(isset($slack)){

            if($product_name != ""){
                $validator_config['product_name'] = $this->get_validation_rules("name_label", false);
            }

            if($supplier_code != ""){
                $validator_config['supplier_code'] = $this->get_validation_rules("codes", false);
                
                $supplier_data = SupplierModel::select('id')
                ->where('supplier_code', '=', trim($supplier_code))
                ->active()
                ->first();
                if (empty($supplier_data)) {
                    throw new Exception("Supplier not found or inactive in the system", 400);
                }
            }

            if($category_code != ""){
                $validator_config['category_code'] = $this->get_validation_rules("codes", false);

                $category_data = CategoryModel::select('id')
                ->where('category_code', '=', trim($category_code))
                ->active()
                ->first();
                if (empty($category_data)) {
                    throw new Exception("Category not found or inactive in the system", 400);
                }
            }

            if($tax_code != ""){
                $validator_config['tax_code'] = $this->get_validation_rules("codes", false);

                $taxcode_data = TaxcodeModel::select('id')
                ->where('tax_code', '=', trim($tax_code))
                ->active()
                ->first();
                if (empty($taxcode_data)) {
                    throw new Exception("Taxcode not found or inactive in the system", 400);
                }
            }

            if($discount_code != ""){
                $validator_config['discount_code'] = $this->get_validation_rules("codes", false);

                $discount_code_data = DiscountcodeModel::select('id')
                ->where('discount_code', '=', trim($discount_code))
                ->active()
                ->first();
                if (empty($discount_code_data)) {
                    throw new Exception("Discount code not found or inactive in the system", 400);
                }
            }

            if($purchase_price_excluding_tax != ""){
                $validator_config['purchase_price_excluding_tax'] = $this->get_validation_rules("numeric", false);
            }

            if($sale_price_excluding_tax != ""){
                $validator_config['sale_price_excluding_tax'] = $this->get_validation_rules("numeric", false);
            }

            if($quantity != ""){
                $validator_config['quantity'] = $this->get_validation_rules("numeric", false);
            }

            if($stock_alert_quantity != ""){
                $validator_config['stock_alert_quantity'] = $this->get_validation_rules("numeric", false);
            }

            if($description != ""){
                $validator_config['description'] = $this->get_validation_rules("text", false);
            }

            if ($status != "") {
                $status_data = MasterStatusModel::select('value')->where([
                    ['value_constant', '=', strtoupper($status)],
                    ['key', '=', 'PRODUCT_STATUS']
                ])->active()->first();
                if (!$status_data) {
                    $error_array[] = 'Invalid status provided';
                }
            }

        }

        if(count($error_array) == 0) {
            $update_data = [
                "name" => $product_name,
                "description" => $description,
                "category_id" => (isset($category_data))?$category_data->id:'',
                "supplier_id" => (isset($supplier_data))?$supplier_data->id:'',
                "tax_code_id" => (isset($taxcode_data))?$taxcode_data->id:'',
                "discount_code_id" => (isset($discount_code_data))?$discount_code_data->id:NULL,
                "quantity" => $quantity,
                "alert_quantity" => $stock_alert_quantity,
                "purchase_amount_excluding_tax" => $purchase_price_excluding_tax,
                "sale_amount_excluding_tax" => $sale_price_excluding_tax,
                "is_ingredient" => 1,
                "is_addon_product" => 0,
                "status" => (isset($status_data))?$status_data->value:''
            ];
            $update_data = array_filter($update_data, 'skip_zero_array_filter');

            $data = [
                "update_data" => $update_data,
                "update_key" => (isset($slack))?$slack:''
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }

    public function validate_update_addon_product_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];

        $product_code = $excel_data_item['product_code'];
        $product_name = $excel_data_item['product_name'];
        $supplier_code = $excel_data_item['supplier_code'];
        $category_code = $excel_data_item['category_code'];
        $tax_code = $excel_data_item['tax_code'];
        $purchase_price_excluding_tax = $excel_data_item['purchase_price_excluding_tax'];
        $sale_price_excluding_tax = $excel_data_item['sale_price_excluding_tax'];
        $quantity = $excel_data_item['quantity'];
        $stock_alert_quantity = $excel_data_item['stock_alert_quantity'];
        $description = $excel_data_item['description'];
        $discount_code = $excel_data_item['discount_code'];
        $status = $excel_data_item['status'];

        $validator_config = [];
        $validator_config['product_code'] = $this->get_validation_rules("codes", true);

        $product_data = ProductModel::where('product_code', '=', trim($product_code))->addonProduct()->first();
        if (!$product_data) {
            $error_array[] = 'Invalid product code provided (The product might not be an add-on product or the product might not exist in the system)';
        }else{
            $slack = $product_data->slack;
        }

        if(isset($slack)){

            if($product_name != ""){
                $validator_config['product_name'] = $this->get_validation_rules("name_label", false);
            }

            if($supplier_code != ""){
                $validator_config['supplier_code'] = $this->get_validation_rules("codes", false);
                
                $supplier_data = SupplierModel::select('id')
                ->where('supplier_code', '=', trim($supplier_code))
                ->active()
                ->first();
                if (empty($supplier_data)) {
                    throw new Exception("Supplier not found or inactive in the system", 400);
                }
            }

            if($category_code != ""){
                $validator_config['category_code'] = $this->get_validation_rules("codes", false);

                $category_data = CategoryModel::select('id')
                ->where('category_code', '=', trim($category_code))
                ->active()
                ->first();
                if (empty($category_data)) {
                    throw new Exception("Category not found or inactive in the system", 400);
                }
            }

            if($tax_code != ""){
                $validator_config['tax_code'] = $this->get_validation_rules("codes", false);

                $taxcode_data = TaxcodeModel::select('id')
                ->where('tax_code', '=', trim($tax_code))
                ->active()
                ->first();
                if (empty($taxcode_data)) {
                    throw new Exception("Taxcode not found or inactive in the system", 400);
                }
            }

            if($discount_code != ""){
                $validator_config['discount_code'] = $this->get_validation_rules("codes", false);

                $discount_code_data = DiscountcodeModel::select('id')
                ->where('discount_code', '=', trim($discount_code))
                ->active()
                ->first();
                if (empty($discount_code_data)) {
                    throw new Exception("Discount code not found or inactive in the system", 400);
                }
            }

            if($purchase_price_excluding_tax != ""){
                $validator_config['purchase_price_excluding_tax'] = $this->get_validation_rules("numeric", false);
            }

            if($sale_price_excluding_tax != ""){
                $validator_config['sale_price_excluding_tax'] = $this->get_validation_rules("numeric", false);
            }

            if($quantity != ""){
                $validator_config['quantity'] = $this->get_validation_rules("numeric", false);
            }

            if($stock_alert_quantity != ""){
                $validator_config['stock_alert_quantity'] = $this->get_validation_rules("numeric", false);
            }

            if($description != ""){
                $validator_config['description'] = $this->get_validation_rules("text", false);
            }

            if ($status != "") {
                $status_data = MasterStatusModel::select('value')->where([
                    ['value_constant', '=', strtoupper($status)],
                    ['key', '=', 'PRODUCT_STATUS']
                ])->active()->first();
                if (!$status_data) {
                    $error_array[] = 'Invalid status provided';
                }
            }

        }

        if(count($error_array) == 0) {
            $update_data = [
                "name" => $product_name,
                "description" => $description,
                "category_id" => (isset($category_data))?$category_data->id:'',
                "supplier_id" => (isset($supplier_data))?$supplier_data->id:'',
                "tax_code_id" => (isset($taxcode_data))?$taxcode_data->id:'',
                "discount_code_id" => (isset($discount_code_data))?$discount_code_data->id:NULL,
                "quantity" => $quantity,
                "alert_quantity" => $stock_alert_quantity,
                "purchase_amount_excluding_tax" => $purchase_price_excluding_tax,
                "sale_amount_excluding_tax" => $sale_price_excluding_tax,
                "is_ingredient" => 0,
                "is_addon_product" => 1,
                "status" => (isset($status_data))?$status_data->value:''
            ];
            $update_data = array_filter($update_data, 'skip_zero_array_filter');

            $data = [
                "update_data" => $update_data,
                "update_key" => (isset($slack))?$slack:''
            ];
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;
    }

    public function validate_update_product_variant_data($excel_data_item)
    {
        $response = [];
        $data = [];
        $error_array  = [];
        
        $product_code_variant_option_code_csv = $excel_data_item['product_code_variant_option_code_csv'];

        $validator_config = [];
        $validator_config['product_code_variant_option_code_csv'] = $this->get_validation_rules("string", true);

        if(!empty($validator_config)) {
            $validator = Validator::make($excel_data_item, $validator_config);
            
            if ($validator->fails()) {
                $errors = $validator->errors();
                foreach ($errors->all() as $message) {
                    $error_array[] = $message;
                }
            }
        }

        if($validator->fails() == false){
            $exploded_variants = explode(',', $product_code_variant_option_code_csv);

            if(count($exploded_variants)>0){
                foreach($exploded_variants as $position => $exploded_variant){
                    if($exploded_variant != ''){
                        $error_exists = 0;

                        $product_data = explode("-", $exploded_variant);

                        $product_code = (isset($product_data[0]) && $product_data[0] != '')?$product_data[0]:'';
                        $variant_option_code = (isset($product_data[1]) && $product_data[1] != '')?$product_data[1]:'';
                        
                        if ($product_code == '') {
                            $error_exists++;
                            $error_array[] = 'Product code is missing at position '. ($position+1) .' in csv';
                        }

                        if ($variant_option_code == '') {
                            $error_exists++;
                            $error_array[] = 'Variant option code is missing at position '. ($position+1) .' in csv';
                        }

                        if($product_code != ''){
                            $variant_product_data = ProductModel::select('id', 'slack')
                            ->where('product_code', '=', trim($product_code))
                            ->mainProduct()
                            ->active()
                            ->first();
                            if (!$variant_product_data) {
                                $error_exists++;
                                $error_array[] = $product_code .': Invalid product code provided (The product might not be an billing product or the product might not exist in the system)';
                            }
                        }
                        
                        if($variant_option_code != ''){
                            $variant_option_data = VariantOptionModel::select('slack')
                            ->where('variant_option_code', '=', trim($variant_option_code))
                            ->active()
                            ->first();
                            if (!$variant_option_data) {
                                $error_exists++;
                                $error_array[] = $variant_option_code .': Invalid variant option code provided';
                            }
                        }

                        if($error_exists == 0){
                            $update_data[] = [
                                "variant_slack" => $variant_product_data->slack,
                                "variant_option_slack" => $variant_option_data->slack,
                            ];
                        }
                    }
                }
            }
        }

        if(count($error_array) == 0) {
            $update_data = array_filter($update_data, 'skip_zero_array_filter');
            $data = $update_data;
        }
        
        $response = [
            "error_list" => $error_array,
            "data" => $data
        ];
        return $response;

    }

    public function generate_reference_sheet(Request $request){
        try {
            $view_path = Config::get('constants.upload.imports.view_path');

            $download_link = '';
                
            $date = Carbon::now();
            $current_date = $date->format('d-m-Y H:i');
            $store = $request->logged_user_store_code.'-'.$request->logged_user_store_name;

            $data = [];

            $data['role_codes'] = RoleModel::select('role_code', 'label')->resolveSuperAdminRole()->active()->get()->toArray();

            $data['store_codes'] = StoreModel::select('store_code', 'name')->active()->get()->toArray();

            $data['supplier_codes'] = SupplierModel::select('supplier_code', 'name')->active()->get()->toArray();

            $data['category_codes'] = CategoryModel::select('category_code', 'label')->active()->get()->toArray();

            $data['tax_codes'] = TaxcodeModel::select('tax_code', 'label', 'total_tax_percentage')->active()->get()->toArray();

            $data['discount_codes'] = DiscountcodeModel::select('discount_code', 'label', 'discount_percentage')->active()->get()->toArray();

            $data['product_codes'] = ProductModel::select('product_code', 'products.name')
            ->categoryJoin()
            ->supplierJoin()
            ->taxcodeJoin()
            ->categoryActive()
            ->supplierActive()
            ->taxcodeActive()
            ->active()->get()->toArray();

            $data['user_codes'] = UserModel::select('user_code', 'fullname')
            ->hideSuperAdminRole()
            ->active()->get()->toArray();

            $data['statuses'] = MasterStatusModel::select('key', DB::raw('GROUP_CONCAT(value_constant) AS status_values'))
            ->whereIn('key', ['USER_STATUS', 'STORE_STATUS', 'SUPPLIER_STATUS', 'CATEGORY_STATUS', 'PRODUCT_STATUS'])
            ->active()->groupBy('key')->get()->toArray();

            $print_ref_page = view('import.reference_sheet', ['data' => $data, 'store' => $store, 'date' => $current_date])->render();

            $pdf_filename = "reference_sheet_".date('Y_m_d_h_i_s')."_".uniqid().".pdf";
            
            ini_set("pcre.backtrack_limit", "5000000");
            set_time_limit(180);

            $mpdf_config = [
                'mode'          => 'utf-8',
                'format'        => 'A4',
                'orientation'   => 'P',
                'margin_left'   => 1,
                'margin_right'  => 1,
                'margin_top'    => 1,
                'margin_bottom' => 1,
                'margin_footer' => 1,
                'tempDir' => storage_path()."/pdf_temp" 
            ];

            $mpdf = new Mpdf($mpdf_config);
            $mpdf->SetDisplayMode('real');
            $mpdf->SetHTMLFooter('<div class="footer">store: '.$store.' | generated on: '.$current_date.' | page: {PAGENO}/{nb}</div>');
            $mpdf->WriteHTML($print_ref_page);

            Storage::disk('imports')->delete(
                [
                    $pdf_filename
                ]
            );

            $view_path = Config::get('constants.upload.imports.view_path');
            $upload_dir = Storage::disk('imports')->getAdapter()->getPathPrefix();

            $mpdf->Output($upload_dir.$pdf_filename, \Mpdf\Output\Destination::FILE);

            $download_link = $view_path.$pdf_filename;

            return response()->json($this->generate_response(
                array(
                    "message" => "Import and update by upload reference sheet generated successfully",
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
}