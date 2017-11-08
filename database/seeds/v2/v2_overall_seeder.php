<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use App\Models\MasterStatus;
use App\Models\PaymentMethod;
use App\Models\Menu as MenuModel;
use App\Models\Customer as CustomerModel;

class v2_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;

        CustomerModel::where([
            ['id', '=', 1],
            ['customer_type', '=', 'DEFAULT'],
        ])
        ->update(['name' => 'Walkin Customer', 'email' => 'walkincustomer@appsthing.com']);

        //order payment status
        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_STATUS',
                'value' => '3',
                'value_constant' => 'PAYMENT_PENDING',
                'label' => 'Payment Pending',
                'color' => 'label orange-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_STATUS',
                'value' => '4',
                'value_constant' => 'PAYMENT_FAILED',
                'label' => 'Payment Failed',
                'color' => 'label red-label'
            ]
        )->save();

        PaymentMethod::firstOrCreate(
            [
                "slack" => $base_controller->generate_slack("payment_methods"),
                "payment_constant" => 'STRIPE',
                "label" => 'Stripe',
                "description" => 'Stripe Payment',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        PaymentMethod::firstOrCreate(
            [
                "slack" => $base_controller->generate_slack("payment_methods"),
                "payment_constant" => 'PAYPAL',
                "label" => 'Paypal',
                "description" => 'Paypal Payment',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        /* Main menu */

        $account_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_ACCOUNT', 
                'label' => "Business Account",
                'route' => "account",
                'parent' => 0,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_USER'],
        ])
        ->update(['sort_order' => 4]);


        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SUPPLIER'],
        ])
        ->update(['sort_order' => 5]);


        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_TAX_AND_DISCOUNT'],
        ])
        ->update(['sort_order' => 6]);


        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_PRODUCT'],
        ])
        ->update(['sort_order' => 7]);


        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_REPORT'],
        ])
        ->update(['sort_order' => 8]);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SETTINGS'],
        ])
        ->update(['sort_order' => 9]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_ORDERS'],
        ])
        ->update(['menu_key' => 'SM_POS_ORDERS']);

        /* Sub menu */

        $account_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_ACCOUNTS', 
                'label' => "Accounts",
                'route' => "accounts",
                'parent' => $account_mm,
                'sort_order' => 1
            ]
        )->id;

        $transaction_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_TRANSACTIONS', 
                'label' => "Transactions",
                'route' => "transactions",
                'parent' => $account_mm,
                'sort_order' => 2
            ]
        )->id;
        
        // Account

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_ACCOUNT', 
                'label' => "Add Account",
                'route' => "",
                'parent' => $account_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_ACCOUNT', 
                'label' => "Edit Account",
                'route' => "",
                'parent' => $account_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_ACCOUNT', 
                'label' => "View Account Detail",
                'route' => "",
                'parent' => $account_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_TRANSACTION', 
                'label' => "Add Transaction",
                'route' => "",
                'parent' => $transaction_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_TRANSACTION', 
                'label' => "Edit Transaction",
                'route' => "",
                'parent' => $transaction_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_TRANSACTION', 
                'label' => "View Transaction Detail",
                'route' => "",
                'parent' => $transaction_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DELETE_TRANSACTION', 
                'label' => "Delete Transaction",
                'route' => "",
                'parent' => $transaction_sm,
                'sort_order' => 4
            ]
        )->id;

        $po_sm_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_PURCHASE_ORDERS'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DELETE_PURCHASE_ORDER', 
                'label' => "Delete Purchase Order",
                'route' => "",
                'parent' => $po_sm_data->id,
                'sort_order' => 5
            ]
        )->id;

        MenuModel::where([
            ['type', '=', 'ACTIONS'],
            ['menu_key', '=', 'A_EDIT_STATUS_PURCHASE_ORDER'],
        ])
        ->update(['sort_order' => 4]);
    }
}
