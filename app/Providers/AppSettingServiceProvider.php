<?php

namespace App\Providers;

use Config;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use App\Models\SettingApp as SettingAppModel;

class AppSettingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (\Schema::hasTable('setting_app')) {
            $app_setting = SettingAppModel::select('*')->first();
            if ($app_setting) //checking if table is not empty
            {
                config([
                    'app.company' => $app_setting->company_name,
                    'app.date_time_format' => $app_setting->app_date_time_format,
                    'app.date_format' => $app_setting->app_date_format,
                    'app.company_logo' => ($app_setting->company_logo != "")?config('constants.upload.company.view_path').$app_setting->company_logo:config('constants.upload.company.company_logo_default'),
                    'app.invoice_print_logo' => ($app_setting->invoice_print_logo != "")?public_path(config('constants.upload.company.view_path').$app_setting->invoice_print_logo):public_path(config('constants.upload.company.invoice_logo_default')),
                    'app.navbar_logo' => ($app_setting->navbar_logo != "")?config('constants.upload.company.view_path').$app_setting->navbar_logo:config('constants.upload.company.navbar_logo_default'),
                    'app.favicon' => ($app_setting->favicon != "")?config('constants.upload.company.view_path').$app_setting->favicon:config('constants.upload.company.favicon_default'),
                    'app.app_title' => (isset($app_setting->app_title) && $app_setting->app_title != "")?$app_setting->app_title:config('constants.upload.company.app_title'),
                    'app.timezone' => $app_setting->timezone,
                ]);
            }
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
