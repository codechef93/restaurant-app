<?php

use Illuminate\Database\Seeder;

use App\Models\Menu as MenuModel;
use App\Models\MasterStatus;

class v5_3_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings_mm = MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SETTINGS'],
        ])  
        ->active()
        ->first();

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_ADDON_GROUPS'],
        ])
        ->update(['sort_order' => 6]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_APP_SETTING'],
        ])
        ->update(['sort_order' => 9]);

        $variant_option_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_VARIANT_OPTIONS', 
                'label' => "Variant Options",
                'route' => "variant_options",
                'parent' => $settings_mm->id,
                'is_restaurant_menu' => 0,
                'sort_order' => 8
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_VARIANT_OPTION', 
                'label' => "Add Variant Option",
                'route' => "",
                'parent' => $variant_option_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_VARIANT_OPTION', 
                'label' => "Edit Variant Option",
                'route' => "",
                'parent' => $variant_option_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_VARIANT_OPTION', 
                'label' => "View Variant Option Detail",
                'route' => "",
                'parent' => $variant_option_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_VARIANT_OPTION_LISTING', 
                'label' => "View Variant Option Listing",
                'route' => "",
                'parent' => $variant_option_sm,
                'sort_order' => 4
            ]
        )->id;

        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'VARIANT_OPTION_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'VARIANT_OPTION_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        $import_update_data_sm = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_UPDATE_DATA'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPDATE_PRODUCT_VARIANT', 
                'label' => "Update Product Variants",
                'route' => "",
                'parent' => $import_update_data_sm->id,
                'sort_order' => 8
            ]
        )->id;
    }
}
