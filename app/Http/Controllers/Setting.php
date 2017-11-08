<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;

use App\Models\SettingEmail as SettingEmailModel;
use App\Models\SettingApp as SettingAppModel;
use App\Models\SettingSms as SettingSmsModel;
use App\Models\MasterStatus;
use App\Models\MasterDateFormat;
use App\Models\AppActivation;

use App\Http\Resources\SettingEmailResource;
use App\Http\Resources\SettingSmsResource;
use App\Http\Resources\SettingAppResource;

class Setting extends Controller
{
    public function email_setting(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_EMAIL_SETTING';
        check_access([$data['sub_menu_key']]);
    
        $email_setting = SettingEmailModel::select('*')->first();
        $email_setting_data = collect();
        if(!empty($email_setting)){
            $email_setting_data = new SettingEmailResource($email_setting);
        }
        $data['email_setting'] = $email_setting_data;

        return view('setting.email.email_setting', $data);
    }

    public function edit_email_setting($slack = null){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_EMAIL_SETTING';
        $data['action_key'] = 'A_EDIT_EMAIL_SETTING';
        check_access([$data['action_key']]);

        $email_setting = SettingEmailModel::select('*')
        
        ->when($slack, function ($query, $slack) {
            $query->where('slack', $slack);
        })

        ->first();
        
        $email_setting_data = collect();
        if(!empty($email_setting)){
            $email_setting_data = new SettingEmailResource($email_setting);
        }
        $data['setting_data'] = $email_setting_data;

        $data['statuses'] = MasterStatus::select('value', 'label')->filterByKey('MAIL_SETTING_STATUS')->active()->sortValueAsc()->get();

        return view('setting.email.edit_email_setting', $data);
    }

    public function app_setting(Request $request){
        //check access
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_APP_SETTING';
        check_access([$data['sub_menu_key']]);
    
        $app_setting = SettingAppModel::select('*')->first();
        $app_setting_data = collect();
        if(!empty($app_setting)){
            $app_setting_data = new SettingAppResource($app_setting);
        }
        $data['setting_data'] = $app_setting_data;

        return view('setting.app.app_setting', $data);
    }

    public function edit_app_setting(Request $request, $slack = null){
        $data['menu_key'] = 'MM_SETTINGS';
        $data['sub_menu_key'] = 'SM_APP_SETTING';
        $data['action_key'] = 'A_EDIT_APP_SETTING';
        check_access([$data['action_key']]);

        $data['date_time_formats'] = MasterDateFormat::select('date_format_value', 'date_format_label')->where([
            ['key', '=', 'DATE_TIME_FORMAT'],
            ['status', '=', 1],
        ])->get();

        $data['date_formats'] = MasterDateFormat::select('date_format_value', 'date_format_label')->where([
            ['key', '=', 'DATE_FORMAT'],
            ['status', '=', 1],
        ])->get();

        $app_setting = SettingAppModel::select('*')
        ->first();
        
        $app_setting_data = collect();
        if(!empty($app_setting)){
            $app_setting_data = new SettingAppResource($app_setting);
        }
        $data['setting_data'] = $app_setting_data;

        $data['timezones'] = timezone_identifiers_list();

        $activation_data = AppActivation::select('activation_code')->first();

        $data['deactivation_eligible'] = ($request->logged_user_role_id == 1 && isset($activation_data->activation_code))?true:false;
        
        $data['chost'] = trim($_SERVER['HTTP_HOST']);
        $data['cip'] = trim($_SERVER['REMOTE_ADDR']);
        
        return view('setting.app.edit_app_setting', $data);
    }

    public function cpanel_migrate(){

        if(App::environment('production')){
            try {

                DB::connection()->getPdo();
                if(DB::connection()->getDatabaseName()){
                    echo "Yes! Successfully connected to the DB: " . DB::connection()->getDatabaseName() .'<br>';
                }else{
                    die("Could not find the database. Please check your configuration.");
                }

                Artisan::call('migrate', [
                    '--force' => true,
                ]);

                echo 'Migration done!';

            } catch (Exception $e) {
                Response::make($e->getMessage(), 500);
            }
        }else{
            App::abort(404);
        } 
    }

    public function cpanel_storage_link(){

        if(App::environment('production')){
            try {

                Artisan::call('storage:link');

                echo 'Storage linking done!';

            } catch (Exception $e) {
                Response::make($e->getMessage(), 500);
            }
        }else{
            App::abort(404);
        } 
    }

    public function cpanel_intial_config(){

        if(App::environment('production')){
            try {
                Artisan::call('config:clear');
                Artisan::call('cache:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
                echo 'Cleared!';

            } catch (Exception $e) {
                Response::make($e->getMessage(), 500);
            }
        }else{
            App::abort(404);
        } 
    }
}
