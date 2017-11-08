<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;

class v3_8_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
      
        $pos_orders_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_POS_ORDERS'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_ORDER_LISTING', 
                'label' => "View Order Listing",
                'route' => "",
                'parent' => $pos_orders_data->id,
                'sort_order' => 5
            ]
        )->id;

        $invoices_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_INVOICES'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_INVOICE_LISTING', 
                'label' => "View Invoice Listing",
                'route' => "",
                'parent' => $invoices_data->id,
                'sort_order' => 7
            ]
        )->id;

        $po_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_PURCHASE_ORDERS'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_PURCHASE_ORDER_LISTING', 
                'label' => "View Purchase Order Listing",
                'route' => "",
                'parent' => $po_data->id,
                'sort_order' => 6
            ]
        )->id;

        $quotation_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_QUOTATIONS'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_QUOTATION_LISTING', 
                'label' => "View Quotation Listing",
                'route' => "",
                'parent' => $quotation_data->id,
                'sort_order' => 6
            ]
        )->id;

        $accounts_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_ACCOUNTS'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_ACCOUNT_LISTING', 
                'label' => "View Account Listing",
                'route' => "",
                'parent' => $accounts_data->id,
                'sort_order' => 4
            ]
        )->id;

        $transactions_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_TRANSACTIONS'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_TRANSACTION_LISTING', 
                'label' => "View Transaction Listing",
                'route' => "",
                'parent' => $transactions_data->id,
                'sort_order' => 5
            ]
        )->id;

        $targets_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_TARGET'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_TARGET_LISTING', 
                'label' => "View Target Listing",
                'route' => "",
                'parent' => $targets_data->id,
                'sort_order' => 5
            ]
        )->id;

        $users_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_USERS'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_USER_LISTING', 
                'label' => "View User Listing",
                'route' => "",
                'parent' => $users_data->id,
                'sort_order' => 4
            ]
        )->id;

        $customers_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_CUSTOMERS'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_CUSTOMER_LISTING', 
                'label' => "View Customer Listing",
                'route' => "",
                'parent' => $customers_data->id,
                'sort_order' => 4
            ]
        )->id;

        $roles_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_ROLES'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_ROLE_LISTING', 
                'label' => "View Role Listing",
                'route' => "",
                'parent' => $roles_data->id,
                'sort_order' => 4
            ]
        )->id;

        $suppliers_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_SUPPLIERS'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_SUPPLIER_LISTING', 
                'label' => "View Supplier Listing",
                'route' => "",
                'parent' => $suppliers_data->id,
                'sort_order' => 4
            ]
        )->id;

        $taxcode_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_TAXCODES'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_TAXCODE_LISTING', 
                'label' => "View Tax Code Listing",
                'route' => "",
                'parent' => $taxcode_data->id,
                'sort_order' => 4
            ]
        )->id;

        $discountcode_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_DISCOUNTCODES'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_DISCOUNTCODE_LISTING', 
                'label' => "View Discount Code Listing",
                'route' => "",
                'parent' => $discountcode_data->id,
                'sort_order' => 4
            ]
        )->id;

        $products_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_PRODUCTS'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_PRODUCT_LISTING', 
                'label' => "View Product Listing",
                'route' => "",
                'parent' => $products_data->id,
                'sort_order' => 5
            ]
        )->id;

        $category_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_CATEGORY'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_CATEGORY_LISTING', 
                'label' => "View Category Listing",
                'route' => "",
                'parent' => $category_data->id,
                'sort_order' => 4
            ]
        )->id;

        $stock_transfer_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_STOCK_TRANSFER'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_STOCK_TRANSFER_LISTING', 
                'label' => "View Stock Transfer Listing",
                'route' => "",
                'parent' => $stock_transfer_data->id,
                'sort_order' => 6
            ]
        )->id;

        $kitchen_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_RESTAURANT_KITCHEN'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_KITCHEN_ORDER_LISTING', 
                'label' => "View Kitchen Order Listing",
                'route' => "",
                'parent' => $kitchen_data->id,
                'sort_order' => 1
            ]
        )->id;

        $restaurant_table_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_RESTAURANT_TABLES'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_RESTAURANT_TABLE_LISTING', 
                'label' => "View Table Listing",
                'route' => "",
                'parent' => $restaurant_table_data->id,
                'sort_order' => 4
            ]
        )->id;

        $store_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_STORE'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_STORE_LISTING', 
                'label' => "View Store Listing",
                'route' => "",
                'parent' => $store_data->id,
                'sort_order' => 4
            ]
        )->id;

        $payment_method_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_PAYMENT_METHOD'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_PAYMENT_METHOD_LISTING', 
                'label' => "View Payment Method Listing",
                'route' => "",
                'parent' => $payment_method_data->id,
                'sort_order' => 4
            ]
        )->id;

    }
}
