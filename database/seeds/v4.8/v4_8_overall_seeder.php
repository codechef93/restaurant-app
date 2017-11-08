<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;

use App\Models\SettingApp as SettingAppModel;
use App\Models\Menu as MenuModel;
use App\Models\SettingSms as SettingSmsModel;

class v4_8_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;

        SettingAppModel::where([
            ['app_title', '=', NULL],
        ])
        ->update(['app_title' => 'Appsthing POS']);

        $restaurant_mm = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_RESTAURANT'],
        ])
        ->active()
        ->first();

        $restaurant_menu_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_RESTAURANT_MENU', 
                'label' => "Restaurant Menu",
                'route' => "restaurant_menu",
                'parent' => $restaurant_mm->id,
                'is_restaurant_menu' => 1,
                'sort_order' => 4
            ]
        )->id;

        $sms_setting_sm = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_SMS_SETTING'],
        ])
        ->active()
        ->first();

        MenuModel::where([
            ['id', '=', $sms_setting_sm->id],
        ])
        ->update(['route' => 'sms_settings']);

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_SMS_SETTING', 
                'label' => "View SMS Setting Detail",
                'route' => "",
                'parent' => $sms_setting_sm->id,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_SMS_SETTING_LISTING', 
                'label' => "View SMS Setting Listing",
                'route' => "",
                'parent' => $sms_setting_sm->id,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::where([
            ['menu_key', '=', 'A_EDIT_SMS_SETTING'],
            ['type', '=', 'ACTIONS'],
        ])
        ->update(['sort_order' => 2]);
        
        $sms_setting_data = SettingSmsModel::select('*')
        ->active()
        ->first();

        if(!empty($sms_setting_data)){
            SettingSmsModel::where([
                ['id', '=', $sms_setting_data->id]
            ])
            ->update(['gateway_type' => 'TWILIO']);
        }else{
            
            SettingSmsModel::create(
                [
                    "slack" => $base_controller->generate_slack("setting_sms_gateways"),
                    'gateway_type' => 'TWILIO',
                    'status' => 0
                ]
            )->id;

            SettingSmsModel::create(
                [
                    "slack" => $base_controller->generate_slack("setting_sms_gateways"),
                    'gateway_type' => 'TEXTLOCAL',
                    'status' => 0
                ]
            )->id;

            SettingSmsModel::create(
                [
                    "slack" => $base_controller->generate_slack("setting_sms_gateways"),
                    'gateway_type' => 'MSG91',
                    'status' => 0
                ]
            )->id;
        }
    }
}
