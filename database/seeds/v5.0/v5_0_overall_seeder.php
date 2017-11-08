<?php

use Illuminate\Database\Seeder;

use App\Models\Menu as MenuModel;
use App\Models\MasterInvoicePrintType;

class v5_0_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MasterInvoicePrintType::firstOrCreate(
            [
                'print_type_value' => 'SMALL_V2',
                'print_type_label' => 'Small Sheet - V2',
                'status' => 1,
                'created_at' => now()
            ]
        )->save();

        $import_data_sm = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_IMPORT_DATA'],
        ])
        ->active()
        ->first(); 
        
        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPLOAD_ADDON_PRODUCT', 
                'label' => "Upload Add-on Product",
                'route' => "",
                'parent' => $import_data_sm->id,
                'sort_order' => 7
            ]
        )->id;

        $import_update_data_sm = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_UPDATE_DATA'],
        ])
        ->active()
        ->first(); 
        
        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPDATE_ADDON_PRODUCT', 
                'label' => "Update Add-on Product",
                'route' => "",
                'parent' => $import_update_data_sm->id,
                'sort_order' => 7
            ]
        )->id;

        $settings_mm = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SETTINGS'],
        ])
        ->active()
        ->first(); 

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_BILLING_COUNTERS'],
        ])
        ->update(['parent' => $settings_mm->id, 'sort_order' => 3]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_EMAIL_SETTING'],
        ])
        ->update(['sort_order' => 4]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_SMS_SETTING'],
        ])
        ->update(['sort_order' => 5]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_SMS_TEMPLATE'],
        ])
        ->update(['sort_order' => 6]);
        
        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_MEASUREMENT_UNIT'],
        ])
        ->update(['sort_order' => 7]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_APP_SETTING'],
        ])
        ->update(['sort_order' => 8]);
    }
}
