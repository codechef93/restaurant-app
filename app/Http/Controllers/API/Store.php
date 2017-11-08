<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Config;

use App\Http\Resources\StoreResource;
use App\Models\Store as StoreModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\Country as CountryModel;
use App\Models\Account as AccountModel;
use App\Models\MasterAccountType as MasterAccountTypeModel;
use App\Models\MasterBillingType as MasterBillingTypeModel;
use App\Models\Role as RoleModel;
use App\Models\Language as LanguageModel;
use App\Models\Printer as PrinterModel;

use App\Http\Resources\Collections\StoreCollection;
use Intervention\Image\ImageManagerStatic as Image;

class Store extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_STORE_LISTING';
            if(check_access(array($data['action_key']), true) == false){
                $response = $this->no_access_response_for_listing_table();
                return $response;
            }

            $item_array = array();

            $draw = $request->draw;
            $limit = $request->length;
            $offset = $request->start;
            
            $order_by = $request->order[0]["column"];
            $order_direction = $request->order[0]["dir"];
            $order_by_column =  $request->columns[$order_by]['name'];

            $filter_string = $request->search['value'];
            $filter_columns = array_filter(data_get($request->columns, '*.name'));
            
            $query = StoreModel::select('stores.*', 'master_status.label as status_label', 'master_status.color as status_color', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
            ->statusJoin()
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

            ->get();

            $stores = StoreResource::collection($query);
           
            $total_count = StoreModel::select("id")->get()->count();

            $item_array = [];
            foreach($stores as $key => $store){

                $store = $store->toArray($request);

                $item_array[$key][] = $store['store_code'];
                $item_array[$key][] = $store['name'];
                $item_array[$key][] = (isset($store['status']['label']))?view('common.status', ['status_data' => ['label' => $store['status']['label'], "color" => $store['status']['color']]])->render():'-';
                $item_array[$key][] = $store['created_at_label'];
                $item_array[$key][] = $store['updated_at_label'];
                $item_array[$key][] = (isset($store['created_by']) && isset($store['created_by']['fullname']))?$store['created_by']['fullname']:'-';
                $item_array[$key][] = view('store.layouts.store_actions', array('store' => $store))->render();
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

            if(!check_access(['A_ADD_STORE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $store_data_exists = StoreModel::select('id')
            ->where('store_code', '=', trim($request->store_code))
            ->first();
            if (!empty($store_data_exists)) {
                throw new Exception("Store code already assigned to a store", 400);
            }

            $country_data = CountryModel::select('id')
            ->where('id', '=', trim($request->country))
            ->active()
            ->first();
            if (empty($country_data)) {
                throw new Exception("Invalid country selected", 400);
            }

            $currency_data = CountryModel::select('currency_code', 'currency_name')
            ->where('currency_code', '=', trim($request->currency_code))
            ->active()
            ->first();
            if (empty($currency_data)) {
                throw new Exception("Invalid currency selected", 400);
            }

            $store_data_exists = StoreModel::select('id')
            ->where('store_code', '=', trim($request->store_code))
            ->first();
            if (!empty($store_data_exists)) {
                throw new Exception("Store code already assigned to a store", 400);
            }

            $billing_type = MasterBillingTypeModel::select('id', 'label')
            ->active()
            ->where('billing_type_constant', '=', trim($request->restaurant_billing_type))
            ->first();

            if($request->restaurant_waiter_role != '' && $request->restaurant_chef_role != ''){
                if($request->restaurant_waiter_role == $request->restaurant_chef_role){
                    throw new Exception("Chef and Waiter roles cannot be the same", 400);
                }
            }

            $waiter_role = RoleModel::select('id', 'slack', 'role_code', 'label')->resolveSuperAdminRole()->active()->where('slack' , '=', trim($request->restaurant_waiter_role))->first();

            $chef_role = RoleModel::select('id', 'slack', 'role_code', 'label')->resolveSuperAdminRole()->active()->where('slack' , '=', trim($request->restaurant_chef_role))->first();

            $language = LanguageModel::select("id")->where('language_constant', '=', trim($request->menu_language))->active()->first();

            $pos_invoice_printer = PrinterModel::select('id', 'slack')->active()->where('slack' , '=', trim($request->pos_invoice_printer))->first();

            $kot_printer = PrinterModel::select('id', 'slack')->active()->where('slack' , '=', trim($request->kot_printer))->first();
            
            $other_printer = PrinterModel::select('id', 'slack')->active()->where('slack' , '=', trim($request->other_printer))->first();

            $slack = $this->generate_slack("stores");

            $store_logo = "";
            $store_logo_base64 = "";

            if($request->hasFile('store_image')){
    
                $upload_dir = Config::get('constants.upload.store.upload_path');
                $store_image = $request->store_image;

                $extension = $store_image->getClientOriginalExtension();
                $file_name = $slack.'_'.uniqid().'.'.$extension;
                $path = Storage::disk('store')->putFileAs('/', $store_image, $file_name);
                $file_name = basename($path);

                // $image = Image::make($store_image);
                // $file_path = $upload_dir.'thumb_'.$file_name;
                // $image->fit(150);
                // $image->fit(150, 150, function ($constraint) {
                //     $constraint->upsize();
                // });
                // $image->save($file_path);
                // $image->destroy();
                
                $store_logo = $file_name;
                $store_logo_base64 = 'data:image/'.$extension.';base64,'.base64_encode($store_image);
            }

            DB::beginTransaction();
            
            $store = [
                "slack" => $slack,
                "store_code" => strtoupper(trim($request->store_code)),
                "name" => $request->name,
                "tax_number" => $request->tax_number,
                "final_letter" => $request->final_letter,
                "delivery_tax" => $request->delivery_tax === NULL ? 0 : $request->delivery_tax,
                "address" => $request->address,
                "country_id" => $request->country,
                "pincode" => $request->pincode,
                "primary_contact" => $request->primary_contact,
                "secondary_contact" => $request->secondary_contact,
                "primary_email" => $request->primary_email,
                "secondary_email" => $request->secondary_email,
                "invoice_type" => $request->invoice_type,
                "currency_code" => $currency_data->currency_code,
                "currency_name" => $currency_data->currency_name,
                "restaurant_mode" => $request->restaurant_mode,
                "restaurant_billing_type_id" => (!empty($billing_type))?$billing_type->id:'',
                "restaurant_waiter_role_id" => (!empty($waiter_role))?$waiter_role->id:'',
                "restaurant_chef_role_id" => (!empty($chef_role))?$chef_role->id:'',
                "enable_customer_popup" => $request->enable_customer_popup,
                "enable_variants_popup" => $request->enable_variants_popup,
                "digital_menu_enabled" => ($request->digital_menu_enabled == true)?1:0,
                "enable_digital_menu_otp_verification" => $request->enable_digital_menu_otp_verification,
                "digital_menu_send_order_to_kitchen" => $request->digital_menu_send_order_to_kitchen,
                "menu_language_id" => (isset($language) && !empty($language))?$language->id:'',
                "printnode_enabled" => ($request->printnode_enabled == true)?1:0,
                "printnode_api_key" => (!empty($request->printnode_api_key))?$request->printnode_api_key:'',
                "pos_printer_id" => (!empty($pos_invoice_printer))?$pos_invoice_printer->id:'',
                "kot_printer_id" => (!empty($kot_printer))?$kot_printer->id:'',
                "other_printer_id" => (!empty($other_printer))?$other_printer->id:'',
                "status" => $request->status,
                "created_by" => $request->logged_user_id,
                "store_logo" => $store_logo
            ];
            
            $store_id = StoreModel::create($store)->id;

            $this->create_default_business_account($request, $store_id);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Store created successfully", 
                    "data"    => $store['slack']
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

            if(!check_access(['A_DETAIL_STORE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $item = StoreModel::select('*')
            ->where('slack', $slack)
            ->first();

            $item_data = new StoreResource($item);

            return response()->json($this->generate_response(
                array(
                    "message" => "Store loaded successfully", 
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

            if(!check_access(['A_VIEW_STORE_LISTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $list = new StoreCollection(StoreModel::select('*')
            ->orderBy('created_at', 'desc')->paginate());

            return response()->json($this->generate_response(
                array(
                    "message" => "Stores loaded successfully", 
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

            if(!check_access(['A_EDIT_STORE'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $store_data_exists = StoreModel::select('id')
            ->where([
                ['slack', '!=', $slack],
                ['store_code', '=', trim($request->store_code)]
            ])
            ->first();
           
            if (!empty($store_data_exists)) {
                throw new Exception("Store code already assigned to a store", 400);
            }

            $tax_code_id = NULL;
            if(isset($request->tax_code)){
                $taxcode_data = TaxcodeModel::select('id', 'tax_type')
                ->where('slack', '=', trim($request->tax_code))
                ->active()
                ->first();
                if (empty($taxcode_data)) {
                    throw new Exception("Tax code not found or inactive in the system", 400);
                }
                if($taxcode_data->tax_type == 'INCLUSIVE'){
                    throw new Exception("Only exclusive tax code can be assigned to a store", 400);
                }
                $tax_code_id = $taxcode_data->id;
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

            $country_data = CountryModel::select('id')
            ->where('id', '=', trim($request->country))
            ->active()
            ->first();
            if (empty($country_data)) {
                throw new Exception("Invalid country selected", 400);
            }

            $currency_data = CountryModel::select('currency_code', 'currency_name')
            ->where('currency_code', '=', trim($request->currency_code))
            ->active()
            ->first();
            if (empty($currency_data)) {
                throw new Exception("Invalid currency selected", 400);
            }

            if($request->status == 0){
                $active_store_exists = StoreModel::select('id')
                ->where([
                    ['slack', '!=', $slack],
                    ['status', '=', 1]
                ])
                ->count();
                if ($active_store_exists == 0) {
                    throw new Exception("Atleast one store needs to be active in the system", 400);
                }
            }

            if($request->restaurant_waiter_role != '' && $request->restaurant_chef_role != ''){
                if($request->restaurant_waiter_role == $request->restaurant_chef_role){
                    throw new Exception("Chef and Waiter roles cannot be the same", 400);
                }
            }

            $billing_type = MasterBillingTypeModel::select('id', 'label')
            ->active()
            ->where('billing_type_constant', '=', trim($request->restaurant_billing_type))
            ->first();

            $waiter_role = RoleModel::select('id', 'slack', 'role_code', 'label')->resolveSuperAdminRole()->active()->where('slack' , '=', trim($request->restaurant_waiter_role))->first();

            $chef_role = RoleModel::select('id', 'slack', 'role_code', 'label')->resolveSuperAdminRole()->active()->where('slack' , '=', trim($request->restaurant_chef_role))->first();

            $language = LanguageModel::select("id")->where('language_constant', '=', trim($request->menu_language))->active()->first();

            $pos_invoice_printer = PrinterModel::select('id', 'slack')->active()->where('slack' , '=', trim($request->pos_invoice_printer))->first();

            $kot_printer = PrinterModel::select('id', 'slack')->active()->where('slack' , '=', trim($request->kot_printer))->first();
            
            $other_printer = PrinterModel::select('id', 'slack')->active()->where('slack' , '=', trim($request->other_printer))->first();

            $menu_open_time = ($request->menu_open_time != null)?Carbon::parse($request->menu_open_time)->format(config("app.time_format")):null;
            $menu_close_time = ($request->menu_close_time != null)?Carbon::parse($request->menu_close_time)->format(config("app.time_format")):null;
            if(($menu_open_time != '' && $menu_close_time == '') || ($menu_open_time == '' && $menu_close_time != '')){
                throw new Exception("Please provide both Menu Open Time and Menu Close Time", 400);
            }else if(($menu_open_time != '' && $menu_close_time != '') && $menu_close_time < $menu_open_time){
                //throw new Exception("Menu Open Time expected to be before Menu Close Time", 400);  
            }

            DB::beginTransaction();

            $store_data = StoreModel::where('slack', $slack)->first();

            $store_logo = $store_data->store_logo;
            $store_logo_base64 = $store_data->store_logo_base64;

            if($request->hasFile('store_image')){
    
                $upload_dir = Config::get('constants.upload.store.upload_path');
                $store_image = $request->store_image;

                $extension = $store_image->getClientOriginalExtension();
                $file_name = $slack.'_'.uniqid().'.'.$extension;
                $path = Storage::disk('store')->putFileAs('/', $store_image, $file_name);
                $file_name = basename($path);
                
                $store_logo = "/storage/store/".$file_name;
                $store_logo_base64 = 'data:image/'.$extension.';base64,'.base64_encode(file_get_contents($store_image->path()));
                // dd();
            }

            $store = [
                "store_code" => strtoupper(trim($request->store_code)),
                "name" => $request->name,
                "tax_number" => $request->tax_number,
                "final_letter" => $request->final_letter,
                "delivery_tax" => $request->delivery_tax === NULL ? 0 : $request->delivery_tax,
                "tax_code_id" => $tax_code_id,
                "discount_code_id" => $discount_code_id,
                "address" => $request->address,
                "country_id" => $request->country,
                "pincode" => $request->pincode,
                "primary_contact" => $request->primary_contact,
                "secondary_contact" => $request->secondary_contact,
                "primary_email" => $request->primary_email,
                "secondary_email" => $request->secondary_email,
                "invoice_type" => $request->invoice_type,
                "currency_code" => $currency_data->currency_code,
                "currency_name" => $currency_data->currency_name,
                "restaurant_mode" => $request->restaurant_mode,
                "restaurant_billing_type_id" => (!empty($billing_type))?$billing_type->id:'',
                "restaurant_waiter_role_id" => (!empty($waiter_role))?$waiter_role->id:'',
                "restaurant_chef_role_id" => (!empty($chef_role))?$chef_role->id:'',
                "enable_customer_popup" => $request->enable_customer_popup,
                "enable_variants_popup" => $request->enable_variants_popup,
                "digital_menu_enabled" => ($request->digital_menu_enabled == true)?1:0,
                "enable_digital_menu_otp_verification" => $request->enable_digital_menu_otp_verification,
                "digital_menu_send_order_to_kitchen" => $request->digital_menu_send_order_to_kitchen,
                "menu_language_id" => (isset($language) && !empty($language))?$language->id:'',
                "menu_open_time" => $menu_open_time,
                "menu_close_time" => $menu_close_time,
                "printnode_enabled" => ($request->printnode_enabled == true)?1:0,
                "printnode_api_key" => (!empty($request->printnode_api_key))?$request->printnode_api_key:'',
                "pos_printer_id" => (!empty($pos_invoice_printer))?$pos_invoice_printer->id:'',
                "kot_printer_id" => (!empty($kot_printer))?$kot_printer->id:'',
                "other_printer_id" => (!empty($other_printer))?$other_printer->id:'',
                "status" => $request->status,
                "updated_by" => $request->logged_user_id,
                "store_logo" => $store_logo,
                "store_logo_base64" => $store_logo_base64
            ];

            $action_response = StoreModel::where('slack', $slack)
            ->update($store);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Store updated successfully", 
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function create_default_business_account($request, $store_id){
        
        $account_exists = AccountModel::select('id')
        ->where('store_id', '=', trim($store_id))
        ->first();
        
        if (!empty($account_exists)) {
            return;   
        }

        $account_type_data = MasterAccountTypeModel::select('id')
        ->where('account_type_constant', '=', 'BASIC')
        ->first();

        $account = [
            "slack" => $this->generate_slack("accounts"),
            "store_id" => $store_id,
            "account_code" => Str::random(6),
            "account_type" => $account_type_data->id,
            "label" => 'Default Sales Account',
            "description" => 'Default Sales Account',
            "initial_balance" => 0,
            "pos_default" => 1,
            "status" => 1,
            "created_by" => $request->logged_user_id
        ];
        
        $account_id = AccountModel::create($account)->id;
        
        $code_start_config = Config::get('constants.unique_code_start.account');
        $code_start = (isset($code_start_config))?$code_start_config:100;
        
        $account_code = [
            "account_code" => ($code_start+$account_id)
        ];

        AccountModel::withoutGlobalScopes()->where('id', $account_id)
        ->update($account_code);
    }

    public function validate_request($request)
    {
        $validator = Validator::make($request->all(), [
            'name' => $this->get_validation_rules("name_label", true),
            'address' => $this->get_validation_rules("text", true),
            'pincode' => $this->get_validation_rules("pincode", false),
            'store_code' => $this->get_validation_rules("codes", true),
            'tax_number' => $this->get_validation_rules("name_label", false),
            'primary_contact' => $this->get_validation_rules("phone", false),
            'secondary_contact' => $this->get_validation_rules("phone", false),
            'primary_email' => $this->get_validation_rules("email", false),
            'secondary_email' => $this->get_validation_rules("email", false),
            'invoice_type' => 'max:50|required',
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
