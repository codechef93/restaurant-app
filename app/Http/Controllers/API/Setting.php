<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Artisan;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use GuzzleHttp\Client;

use App\Models\SettingEmail as SettingEmailModel;
use App\Models\SettingApp as SettingAppModel;
use App\Models\SettingSms as SettingSmsModel;
use App\Models\AppActivation;

use App\Providers\MailServiceProvider;

use App\Mail\TestEmail;

class Setting extends Controller
{
    
    public function add_setting_email(Request $request)
    {
        try {

            if(!check_access(['A_EDIT_EMAIL_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_email_setting_request($request);

            $request->type = 'SIMPLE';

            Artisan::call('config:clear');

            DB::beginTransaction();
            
            $email_setting = [
                "slack" => $this->generate_slack("setting_mail"),
                "type" => $request->type,
                "driver" => $request->driver,
                "host" => $request->host,
                "port" => $request->port,
                "username" => $request->username,
                "password" => $request->password,
                "encryption" => $request->encryption,
                "from_email" => $request->from_email,
                "from_email_name" => $request->from_email_name,
                "status" => $request->status,
                "created_by" => $request->logged_user_id
            ];

            $setting_id = SettingEmailModel::create($email_setting)->id;

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Email settings added successfully", 
                    "data"    => $email_setting['slack']
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

    public function update_setting_email(Request $request, $slack)
    {
        try {

            if(!check_access(['A_EDIT_EMAIL_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }
            
            $this->validate_email_setting_request($request);

            $email_setting_data_exists = SettingEmailModel::select('id')
            ->where([
                ['slack', '=', $slack]
            ])
            ->first();
            if (empty($email_setting_data_exists)) {
                throw new Exception("Trying to update invalid email setting", 400);
            }

            $request->type = 'SIMPLE';

            Artisan::call('config:clear');

            DB::beginTransaction();
            
            $email_setting = [
                "type" => $request->type,
                "driver" => $request->driver,
                "host" => $request->host,
                "port" => $request->port,
                "username" => $request->username,
                "password" => $request->password,
                "encryption" => $request->encryption,
                "from_email" => $request->from_email,
                "from_email_name" => $request->from_email_name,
                "status" => $request->status,
                "updated_by" => $request->logged_user_id
            ];

            $action_response = SettingEmailModel::where('slack', $slack)
            ->update($email_setting);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Email settings updated successfully", 
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

    public function update_setting_app(Request $request)
    {
        try {

            if(!check_access(['A_EDIT_APP_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }
            
            $this->validate_app_setting_request($request);

            Artisan::call('config:clear');

            DB::beginTransaction();

            $app_setting = SettingAppModel::select('*')->first();
            $company_logo_file = (isset($app_setting->company_logo))?$app_setting->company_logo:'';
            $invoice_print_logo_file = (isset($app_setting->invoice_print_logo))?$app_setting->invoice_print_logo:'';
            $navbar_logo_file = (isset($app_setting->navbar_logo))?$app_setting->navbar_logo:'';
            $favicon_file = (isset($app_setting->favicon))?$app_setting->favicon:'';

            if($request->hasFile('company_logo')){

                $remove_company_logo_file = $company_logo_file;

                Storage::disk('company')->delete(
                    [
                        $remove_company_logo_file
                    ]
                );

                $upload_dir = Config::get('constants.upload.company.upload_path');
                $company_logo = $request->company_logo;

                $extension = $company_logo->getClientOriginalExtension();
                $company_logo_file_name = 'logo_company'.'.'.$extension;
                $path = Storage::disk('company')->putFileAs('/', $company_logo, $company_logo_file_name);
                $company_logo_file_name = basename($path);

                $image = Image::make($company_logo);
                $file_path = $upload_dir.$company_logo_file_name;
                $image->save($file_path);
                $image->destroy();

                $company_logo_file = (isset($company_logo_file_name))?$company_logo_file_name:'';
            }

            if($request->hasFile('invoice_print_logo')){
                $remove_invoice_print_logo_file = $invoice_print_logo_file;

                Storage::disk('company')->delete(
                    [
                        $remove_invoice_print_logo_file
                    ]
                );

                $upload_dir = Config::get('constants.upload.company.upload_path');
                $invoice_print_logo = $request->invoice_print_logo;

                $extension = $invoice_print_logo->getClientOriginalExtension();
                $invoice_print_logo_file_name = 'logo_invoice_print'.'.'.$extension;
                $path = Storage::disk('company')->putFileAs('/', $invoice_print_logo, $invoice_print_logo_file_name);
                $invoice_print_logo_file_name = basename($path);

                $image = Image::make($invoice_print_logo);
                $file_path = $upload_dir.$invoice_print_logo_file_name;
                //$image->resize(160, 80);
                $image->save($file_path);
                $image->destroy();

                $invoice_print_logo_file = (isset($invoice_print_logo_file_name))?$invoice_print_logo_file_name:'';
            }

            if($request->hasFile('navbar_logo')){

                $remove_navbar_logo_file = $navbar_logo_file;

                Storage::disk('company')->delete(
                    [
                        $remove_navbar_logo_file
                    ]
                );

                $upload_dir = Config::get('constants.upload.company.upload_path');
                $navbar_logo = $request->navbar_logo;

                $extension = $navbar_logo->getClientOriginalExtension();
                $navbar_logo_file_name = 'logo_navbar'.'.'.$extension;
                $path = Storage::disk('company')->putFileAs('/', $navbar_logo, $navbar_logo_file_name);
                $navbar_logo_file_name = basename($path);

                $image = Image::make($navbar_logo);
                $file_path = $upload_dir.$navbar_logo_file_name;
                $image->save($file_path);
                $image->destroy();

                $navbar_logo_file = (isset($navbar_logo_file_name))?$navbar_logo_file_name:'';
            }

            if($request->hasFile('favicon')){

                $remove_favicon_file = $favicon_file;

                Storage::disk('company')->delete(
                    [
                        $remove_favicon_file
                    ]
                );

                $upload_dir = Config::get('constants.upload.company.upload_path');
                $favicon = $request->favicon;

                $extension = $favicon->getClientOriginalExtension();
                $favicon_file_name = 'favicon'.'.'.$extension;
                $path = Storage::disk('company')->putFileAs('/', $favicon, $favicon_file_name);
                $favicon_file_name = basename($path);

                $image = Image::make($favicon);
                $file_path = $upload_dir.$favicon_file_name;
                $image->save($file_path);
                $image->destroy();

                $favicon_file = (isset($favicon_file_name))?$favicon_file_name:'';
            }

            SettingAppModel::truncate();

            $app_setting = [
                "company_name" => $request->company_name,
                "timezone" => $request->timezone,
                "app_title" => $request->app_title,
                "app_date_time_format" => $request->date_time_format,
                "app_date_format" => $request->date_format,
                "company_logo" => $company_logo_file,
                "invoice_print_logo" => $invoice_print_logo_file,
                "navbar_logo" => $navbar_logo_file,
                "favicon" => $favicon_file,
                "updated_by" => $request->logged_user_id
            ];

            $action_response = SettingAppModel::create($app_setting)->id;

            DB::commit();

            file_put_contents("timezone_config.txt", $request->timezone);

            return response()->json($this->generate_response(
                array(
                    "message" => "App settings updated successfully",
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

    public function remove_company_image(Request $request)
    {
        try {

            if(!check_access(['A_EDIT_APP_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $type = $request->type;
            $app_setting = SettingAppModel::select('company_name', 'company_logo', 'invoice_print_logo', 'navbar_logo', 'favicon')->first();

            switch($type){
                case 'company_logo':
                    if($app_setting->company_logo != ''){
                        Storage::disk('company')->delete(
                            [
                                $app_setting->company_logo
                            ]
                        );
                    }
        
                    $app_setting_array = [        
                        'company_logo' => '',
                    ];
                break;
                case 'invoice_print_logo':
                    if($app_setting->invoice_print_logo != ''){
                        Storage::disk('company')->delete(
                            [
                                $app_setting->invoice_print_logo
                            ]
                        );
                    }
        
                    $app_setting_array = [        
                        'invoice_print_logo' => '',
                    ];
                break;
                case 'navbar_logo':
                    if($app_setting->navbar_logo != ''){
                        Storage::disk('company')->delete(
                            [
                                $app_setting->navbar_logo
                            ]
                        );
                    }
        
                    $app_setting_array = [        
                        'navbar_logo' => '',
                    ];
                break;
                case 'favicon':
                    if($app_setting->favicon != ''){
                        Storage::disk('company')->delete(
                            [
                                $app_setting->favicon
                            ]
                        );
                    }
        
                    $app_setting_array = [        
                        'favicon' => '',
                    ];
                break;
            }
            

            $data = SettingAppModel::where('company_name', $app_setting->company_name)->update($app_setting_array);
        
            return response()->json($this->generate_response(
                array(
                    "message" => "Company Logo removed successfully", 
                    "data"    => $data
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

    public function clear_app_cache(Request $request)
    {
        try {
            if(!check_access(['A_EDIT_APP_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            Artisan::call('cache:clear');

            return response()->json($this->generate_response(
                array(
                    "message" => "Cache has been cleared successfully"
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

    public function clear_app_storage(Request $request)
    {
        try {
            if(!check_access(['A_EDIT_APP_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $days = 259200; // 3 days
            $folder_list = ['storage/order', 'storage/reports'];

            foreach($folder_list as $folder_list_item){

                $files_list = [];
    
                $limit = time() - $days;
                $dir = realpath($folder_list_item);
                if (!is_dir($dir)) {
                    return;
                }
                
                $folder_directory = opendir($dir);
                if ($folder_directory === false) {
                    return;
                }
                
                while (($file = readdir($folder_directory)) !== false) {
                    $file = $dir . '/' . $file;
                    if (!is_file($file)) {
                        continue;
                    }
                    
                    if (filemtime($file) < $limit) {
                        $list[] = $file;
                        unlink($file);
                    }
                }
                closedir($folder_directory);
            }

            return response()->json($this->generate_response(
                array(
                    "message" => "Old Storage has been cleared successfully"
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

    public function validate_email_setting_request($request)
    {
        $validator = Validator::make($request->all(), [
            'host' => $this->get_validation_rules("name_label", true),
            'port' => $this->get_validation_rules("name_label", true),
            'username' => $this->get_validation_rules("name_label", true),
            'password' => $this->get_validation_rules("name_label", true),
            'encryption' => $this->get_validation_rules("name_label", true),
            'from_email' => $this->get_validation_rules("email", true),
            'from_email_name' => $this->get_validation_rules("name_label", true),
            'status' => $this->get_validation_rules("status", true),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }

    public function validate_app_setting_request($request)
    {
        $validator = Validator::make($request->all(), [
            'company_name' => $this->get_validation_rules("name_label", true),
            'app_title' => $this->get_validation_rules("name_label", true),
            'date_time_format' => 'max:50|required',
            'date_format' => 'max:50|required',
            'company_logo' => $this->get_validation_rules("company_logo", false),
            'invoice_print_logo' => $this->get_validation_rules("invoice_print_logo", false),
            'navbar_logo' => $this->get_validation_rules("navbar_logo", false),
            'favicon' => $this->get_validation_rules("favicon", false),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }

    public function send_test_email(Request $request)
    {
        try {
            if(!check_access(['A_EDIT_EMAIL_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $validator = Validator::make($request->all(), [
                'email' => $this->get_validation_rules("email", true),
            ]);
            $validation_status = $validator->fails();
            if($validation_status){
                throw new Exception($validator->errors());
            }

            $slack = $request->setting_slack;
            $email = $request->email;
            $server = $request->getHttpHost();

            $email_setting_data_exists = SettingEmailModel::select('id')
            ->where([
                ['slack', '=', $slack]
            ])
            ->first();
            if (empty($email_setting_data_exists)) {
                throw new Exception("Email setting is not configured", 400);
            }

            Mail::to(trim($email))->send(new TestEmail(['datetime' => now(), 'server' => $server]));

            return response()->json($this->generate_response(
                array(
                    "message" => "Test Email sent successfully"
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

    public function deactivate_app(Request $request)
    {
        try {
            if(!check_access(['A_EDIT_APP_SETTING'], true)){
                throw new Exception("Invalid request", 400);
            }

            if($request->logged_user_role_id != 1){
                throw new Exception("Invalid request", 400);
            }

            $validator = Validator::make($request->all(), [
                'pcode' => 'required',
            ]);
            $validation_status = $validator->fails();
            if($validation_status){
                throw new Exception($validator->errors());
            }

            $purchase_code = trim($request->pcode);
            $chost = trim($request->chost);
            $cip = trim($request->cip);

            $activation_data = AppActivation::select('activation_code')->first();
            if(isset($activation_data->activation_code) && $activation_data->activation_code != ''){
                $client = new Client();
                $response = $client->post(config('app.deactivate_link'), [
                    'form_params' => [
                        'purchase_code' => $purchase_code,
                        'activation_code' => $activation_data->activation_code,
                        'chost' => $chost,
                        'ip' => $cip,
                    ]
                ]);
                $response_body = $response->getBody();
                $response_body_array = json_decode($response_body, true);
                if($response_body_array['status_code'] == 200){
                    DB::beginTransaction();
                    
                    AppActivation::where('activation_code', $activation_data->activation_code)->delete();
                    
                    DB::commit();

                    return response()->json($this->generate_response(
                        array(
                            "message" => $response_body_array['msg']
                        ), 'SUCCESS'
                    ));
                }else{
                    throw new Exception($response_body_array['msg'], 400);
                }
            }else{
                throw new Exception("Invalid request", 400);
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
}
