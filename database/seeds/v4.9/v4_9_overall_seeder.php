<?php

use Illuminate\Database\Seeder;

use App\Http\Controllers\Controller;
use App\Models\MasterStatus;
use App\Models\User as UserModel;
use App\Models\Menu as MenuModel;
use App\Models\Language as LanguageModel;

class v4_9_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
        
        UserModel::updateOrCreate(
            ['email' => 'customer@appsthing.com'],
            [
                'slack' => $base_controller->generate_slack("users"),
                'user_code' => 'CUSTOMER_USER',
                'fullname' => "Customer",
                'email' => 'customer@appsthing.com',
                'password' => '',
                'phone' => '0000000000',
                'role_id' => 1, 
                'status' => 1
            ]
        )->save();

        //order status
        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_STATUS',
                'value' => '6',
                'value_constant' => 'CUSTOMER_ORDER',
                'label' => 'Digital Menu Order',
                'color' => 'label yellow-label'
            ]
        )->save();

        $orders_mm = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_ORDERS'],
        ])
        ->active()
        ->first();

        $digital_menu_orders_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_DIGITAL_MENU_ORDERS', 
                'label' => "Digital Menu Orders",
                'route' => "digital_menu_orders",
                'parent' => $orders_mm->id,
                'is_restaurant_menu' => 1,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_PURCHASE_ORDERS'],
        ])
        ->update(['sort_order' => 1]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_INVOICES'],
        ])
        ->update(['sort_order' => 2]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_POS_ORDERS'],
        ])
        ->update(['sort_order' => 3]);
        
        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_QUOTATIONS'],
        ])
        ->update(['sort_order' => 5]);

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_DIGITAL_MENU_ORDER_LISTING', 
                'label' => "View Digital Menu Order Listing",
                'route' => "",
                'parent' => $digital_menu_orders_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'EDIT_DIGITAL_MENU_ORDER', 
                'label' => "Edit Digital Menu Order",
                'route' => "",
                'parent' => $digital_menu_orders_sm,
                'sort_order' => 2
            ]
        )->id;

        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'NL',
                "language_code" => 'nl',
                "language" => 'Dutch',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        LanguageModel::firstOrCreate(
            [
                "language_constant" => 'PT',
                "language_code" => 'pt',
                "language" => 'Portuguese',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();
    }
}
