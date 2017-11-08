<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use App\Models\MasterStatus;
use App\Models\PaymentMethod;
use App\Models\Menu as MenuModel;
use App\Models\Customer as CustomerModel;
use App\Models\SettingApp as SettingAppModel;
use App\Models\MasterTaxOption as MasterTaxOptionModel;

class v3_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;

        //order status
        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_STATUS',
                'value' => '5',
                'value_constant' => 'IN_KITCHEN',
                'label' => 'In Kitchen',
                'color' => 'label blue-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_KITCHEN_STATUS',
                'value' => '0',
                'value_constant' => 'CONFIRMED',
                'label' => 'Order Confirmed',
                'color' => 'label yellow-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_KITCHEN_STATUS',
                'value' => '1',
                'value_constant' => 'STARTED_PREPARING',
                'label' => 'Started Preparing',
                'color' => 'label blue-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_KITCHEN_STATUS',
                'value' => '2',
                'value_constant' => 'ORDER_READY',
                'label' => 'Ready to Serve',
                'color' => 'label green-label'
            ]
        )->save();
        
        // Restaurant module

        $restaurant_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_RESTAURANT', 
                'label' => "Restaurant",
                'route' => "",
                'parent' => 0,
                'sort_order' => 9
            ]
        )->id;

        $kitchen_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_RESTAURANT_KITCHEN', 
                'label' => "Kitchen View",
                'route' => "kitchen",
                'parent' => $restaurant_mm,
                'sort_order' => 1
            ]
        )->id;

        $table_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_RESTAURANT_TABLES', 
                'label' => "Tables",
                'route' => "tables",
                'parent' => $restaurant_mm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_RESTAURANT_TABLE', 
                'label' => "Add Table",
                'route' => "",
                'parent' => $table_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_RESTAURANT_TABLE', 
                'label' => "Edit Table",
                'route' => "",
                'parent' => $table_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_RESTAURANT_TABLE', 
                'label' => "View Table Detail",
                'route' => "",
                'parent' => $table_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_CHANGE_KITCHEN_ORDER_STATUS', 
                'label' => "Change Kitchen Order Status",
                'route' => "",
                'parent' => $kitchen_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_ORDERS'],
        ])
        ->update(['label' => 'Sales & Orders']);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_USER'],
        ])
        ->update(['label' => 'User & Customer']);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SETTINGS'],
        ])
        ->update(['sort_order' => 10]);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_ACCOUNT'],
        ])
        ->update(['route' => '']);

        //icons update

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_DASHBOARD'],
        ])
        ->update(['icon' => 'fas fa-chart-line']);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_ORDERS'],
        ])
        ->update(['icon' => 'fas fa-shopping-cart']);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_ACCOUNT'],
        ])
        ->update(['icon' => 'fas fa-money-check-alt']);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_USER'],
        ])
        ->update(['icon' => 'fas fa-user-astronaut']);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SUPPLIER'],
        ])
        ->update(['icon' => 'fas fa-truck']);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_TAX_AND_DISCOUNT'],
        ])
        ->update(['icon' => 'fas fa-percentage']);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_PRODUCT'],
        ])
        ->update(['icon' => 'fas fa-cubes']);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_REPORT'],
        ])
        ->update(['icon' => 'fas fa-chart-pie']);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_RESTAURANT'],
        ])
        ->update(['icon' => 'fas fa-utensils']);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SETTINGS'],
        ])
        ->update(['icon' => 'fas fa-cog']);

        SettingAppModel::where([
            ['company_name', '=', 'Appsthing POS']
        ])
        ->update(['company_name' => 'Appsthing']);

        MasterTaxOptionModel::firstOrCreate(
            [
                "tax_option_constant" => 'VAT',
                "label" => 'VAT',
                "component_count" => '1',
                "component_1" => 'VAT',
                "component_2" => '',
                "component_3" => '',
                "description" => '',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();
    }
}
