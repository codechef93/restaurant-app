<?php

use Illuminate\Database\Seeder;

use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;
use App\Models\MasterStatus;

class setting_sms_gateways_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
        
        $settings_mm = MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SETTINGS'],
        ])  
        ->active()
        ->first();

        $setting_sms_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_SMS_SETTING', 
                'label' => "SMS Settings",
                'route' => "sms_setting",
                'parent' => $settings_mm->id,
                'sort_order' => 4
            ]
        )->id;
        
        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_EMAIL_SETTING'],
        ])
        ->update(['sort_order' => 3]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_APP_SETTING'],
        ])
        ->update(['sort_order' => 5]);

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_SMS_SETTING', 
                'label' => "Edit SMS Setting",
                'route' => "",
                'parent' => $setting_sms_sm,
                'sort_order' => 1
            ]
        )->id;

        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'SMS_SETTING_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'SMS_SETTING_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();
    }
}
