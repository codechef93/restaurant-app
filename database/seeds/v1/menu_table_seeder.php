<?php

use Illuminate\Database\Seeder;
use App\Models\Menu as MenuModel;

class menu_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /* MAIN MENU */

        $dashboard_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_DASHBOARD', 
                'label' => "Dashboard",
                'route' => "dashboard",
                'parent' => 0,
                'sort_order' => 1
            ]
        )->id;

        $order_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_ORDERS', 
                'label' => "Order",
                'route' => "",
                'parent' => 0,
                'sort_order' => 2
            ]
        )->id;

        $user_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_USER', 
                'label' => "User",
                'route' => "",
                'parent' => 0,
                'sort_order' => 3
            ]
        )->id;

        $supplier_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_SUPPLIER', 
                'label' => "Supplier",
                'route' => "",
                'parent' => 0,
                'sort_order' => 4
            ]
        )->id;

        $tax_and_discount_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_TAX_AND_DISCOUNT', 
                'label' => "Tax & Discount Codes",
                'route' => "",
                'parent' => 0,
                'sort_order' => 5
            ]
        )->id;

        $product_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_PRODUCT', 
                'label' => "Product",
                'route' => "",
                'parent' => 0,
                'sort_order' => 6
            ]
        )->id;

        $report_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_REPORT', 
                'label' => "Reports",
                'route' => "reports",
                'parent' => 0,
                'sort_order' => 7
            ]
        )->id;

        $settings_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_SETTINGS', 
                'label' => "Settings",
                'route' => "",
                'parent' => 0,
                'sort_order' => 8
            ]
        )->id;

        /* MAIN MENU */

        /* SUB MENU*/
        
        $order_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_ORDERS', 
                'label' => "Orders",
                'route' => "orders",
                'parent' => $order_mm,
                'sort_order' => 1
            ]
        )->id;

        $purchase_order_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_PURCHASE_ORDERS', 
                'label' => "Purchase Orders",
                'route' => "purchase_orders",
                'parent' => $order_mm,
                'sort_order' => 2
            ]
        )->id;

        $user_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_USERS', 
                'label' => "Users",
                'route' => "users",
                'parent' => $user_mm,
                'sort_order' => 1
            ]
        )->id;

        $customer_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_CUSTOMERS', 
                'label' => "Customers",
                'route' => "customers",
                'parent' => $user_mm,
                'sort_order' => 2
            ]
        )->id;

        $roles_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_ROLES', 
                'label' => "Roles",
                'route' => "roles",
                'parent' => $user_mm,
                'sort_order' => 3
            ]
        )->id;

        $supplier_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_SUPPLIERS', 
                'label' => "Suppliers",
                'route' => "suppliers",
                'parent' => $supplier_mm,
                'sort_order' => 1
            ]
        )->id;

        $tax_code_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_TAXCODES', 
                'label' => "Tax Codes",
                'route' => "tax_codes",
                'parent' => $tax_and_discount_mm,
                'sort_order' => 1
            ]
        )->id;

        $discount_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_DISCOUNTCODES', 
                'label' => "Discount Codes",
                'route' => "discount_codes",
                'parent' => $tax_and_discount_mm,
                'sort_order' => 2
            ]
        )->id;

        $product_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_PRODUCTS', 
                'label' => "Products",
                'route' => "products",
                'parent' => $product_mm,
                'sort_order' => 1
            ]
        )->id;

        $category_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_CATEGORY', 
                'label' => "Categories",
                'route' => "categories",
                'parent' => $product_mm,
                'sort_order' => 2
            ]
        )->id;

        $store_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_STORE', 
                'label' => "Stores",
                'route' => "stores",
                'parent' => $settings_mm,
                'sort_order' => 1
            ]
        )->id;

        $payment_method_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_PAYMENT_METHOD', 
                'label' => "Payment Methods",
                'route' => "payment_methods",
                'parent' => $settings_mm,
                'sort_order' => 2
            ]
        )->id;

        $import_data_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_IMPORT_DATA', 
                'label' => "Import Data",
                'route' => "import_data",
                'parent' => $settings_mm,
                'sort_order' => 3
            ]
        )->id;

        $update_data_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_UPDATE_DATA', 
                'label' => "Upload & Update Data",
                'route' => "update_data",
                'parent' => $settings_mm,
                'sort_order' => 4
            ]
        )->id;

        $setting_email_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_EMAIL_SETTING', 
                'label' => "Email Settings",
                'route' => "email_setting",
                'parent' => $settings_mm,
                'sort_order' => 5
            ]
        )->id;

        $setting_app_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_APP_SETTING', 
                'label' => "App Settings",
                'route' => "app_setting",
                'parent' => $settings_mm,
                'sort_order' => 6
            ]
        )->id;

        /* SUB MENU*/

        /* ACTIONS */

        // USER

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_USER', 
                'label' => "Add User",
                'route' => "",
                'parent' => $user_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_USER', 
                'label' => "Edit User",
                'route' => "",
                'parent' => $user_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_USER', 
                'label' => "View User Detail",
                'route' => "",
                'parent' => $user_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_ROLE', 
                'label' => "Add Role",
                'route' => "",
                'parent' => $roles_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_ROLE', 
                'label' => "Edit Role",
                'route' => "",
                'parent' => $roles_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_ROLE', 
                'label' => "View Role Detail",
                'route' => "",
                'parent' => $roles_sm,
                'sort_order' => 3
            ]
        )->id;
        
        
        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_CUSTOMER', 
                'label' => "Add Customer",
                'route' => "",
                'parent' => $customer_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_CUSTOMER', 
                'label' => "Edit Customer",
                'route' => "",
                'parent' => $customer_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_CUSTOMER', 
                'label' => "View Customer Detail",
                'route' => "",
                'parent' => $customer_sm,
                'sort_order' => 3
            ]
        )->id;

        // ORDER

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_ORDER', 
                'label' => "Add Order",
                'route' => "",
                'parent' => $order_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_ORDER', 
                'label' => "Edit Order",
                'route' => "",
                'parent' => $order_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_ORDER', 
                'label' => "View Order Details",
                'route' => "",
                'parent' => $order_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DELETE_ORDER', 
                'label' => "Delete Order",
                'route' => "",
                'parent' => $order_sm,
                'sort_order' => 4
            ]
        )->id;

        
        // PRODUCT
        
        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_PRODUCT', 
                'label' => "Add Product",
                'route' => "",
                'parent' => $product_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_PRODUCT', 
                'label' => "Edit Product",
                'route' => "",
                'parent' => $product_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_PRODUCT', 
                'label' => "View Product Detail",
                'route' => "",
                'parent' => $product_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_GENERATE_BARCODE_PRODUCT', 
                'label' => "Generate Product Barcode",
                'route' => "",
                'parent' => $product_sm,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_CATEGORY', 
                'label' => "Add Category",
                'route' => "",
                'parent' => $category_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_CATEGORY', 
                'label' => "Edit Category",
                'route' => "",
                'parent' => $category_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_CATEGORY', 
                'label' => "View Category Detail",
                'route' => "",
                'parent' => $category_sm,
                'sort_order' => 3
            ]
        )->id;

        //TAX CODE AND DISCOUNT CODES

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_TAXCODE', 
                'label' => "Add Tax Code",
                'route' => "",
                'parent' => $tax_code_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_TAXCODE', 
                'label' => "Edit Tax Code",
                'route' => "",
                'parent' => $tax_code_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_TAXCODE', 
                'label' => "View Tax Code Detail",
                'route' => "",
                'parent' => $tax_code_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_DISCOUNTCODE', 
                'label' => "Add Discount Code",
                'route' => "",
                'parent' => $discount_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_DISCOUNTCODE', 
                'label' => "Edit Discount Code",
                'route' => "",
                'parent' => $discount_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_DISCOUNTCODE', 
                'label' => "View Discount Code Detail",
                'route' => "",
                'parent' => $discount_sm,
                'sort_order' => 3
            ]
        )->id;

        //SUPPLIER

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_SUPPLIER', 
                'label' => "Add Supplier",
                'route' => "",
                'parent' => $supplier_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_SUPPLIER', 
                'label' => "Edit Supplier",
                'route' => "",
                'parent' => $supplier_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_SUPPLIER', 
                'label' => "View Supplier Detail",
                'route' => "",
                'parent' => $supplier_sm,
                'sort_order' => 3
            ]
        )->id;

        //STORE

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_STORE', 
                'label' => "Add Store",
                'route' => "",
                'parent' => $store_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_STORE', 
                'label' => "Edit Store",
                'route' => "",
                'parent' => $store_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_STORE', 
                'label' => "View Store Detail",
                'route' => "",
                'parent' => $store_sm,
                'sort_order' => 3
            ]
        )->id;
        
        //PAYMENT METHOD

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_PAYMENT_METHOD', 
                'label' => "Add Payment Method",
                'route' => "",
                'parent' => $payment_method_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_PAYMENT_METHOD', 
                'label' => "Edit Payment Method",
                'route' => "",
                'parent' => $payment_method_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_PAYMENT_METHOD', 
                'label' => "View Payment Method Detail",
                'route' => "",
                'parent' => $payment_method_sm,
                'sort_order' => 3
            ]
        )->id;

        //IMPORT DATA

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPLOAD_USER', 
                'label' => "Upload Users",
                'route' => "",
                'parent' => $import_data_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPLOAD_STORE', 
                'label' => "Upload Store",
                'route' => "",
                'parent' => $import_data_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPLOAD_SUPPLIER', 
                'label' => "Upload Supplier",
                'route' => "",
                'parent' => $import_data_sm,
                'sort_order' => 3
            ]
        )->id;
    
        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPLOAD_CATEGORY', 
                'label' => "Upload Category",
                'route' => "",
                'parent' => $import_data_sm,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPLOAD_PRODUCT', 
                'label' => "Upload Product",
                'route' => "",
                'parent' => $import_data_sm,
                'sort_order' => 5
            ]
        )->id;

        //UPDATE IMPORT DATA

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPDATE_USER', 
                'label' => "Update Users",
                'route' => "",
                'parent' => $update_data_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPDATE_STORE', 
                'label' => "Update Store",
                'route' => "",
                'parent' => $update_data_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPDATE_SUPPLIER', 
                'label' => "Update Supplier",
                'route' => "",
                'parent' => $update_data_sm,
                'sort_order' => 3
            ]
        )->id;
    
        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPDATE_CATEGORY', 
                'label' => "Update Category",
                'route' => "",
                'parent' => $update_data_sm,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPDATE_PRODUCT', 
                'label' => "Update Product",
                'route' => "",
                'parent' => $update_data_sm,
                'sort_order' => 5
            ]
        )->id;

        // PURCHASE ORDER

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_PURCHASE_ORDER', 
                'label' => "Add Purchase Order",
                'route' => "",
                'parent' => $purchase_order_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_PURCHASE_ORDER', 
                'label' => "Edit Purchase Order",
                'route' => "",
                'parent' => $purchase_order_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_PURCHASE_ORDER', 
                'label' => "View Purchase Order Detail",
                'route' => "",
                'parent' => $purchase_order_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_STATUS_PURCHASE_ORDER', 
                'label' => "Change Purchase Order Status",
                'route' => "",
                'parent' => $purchase_order_sm,
                'sort_order' => 3
            ]
        )->id;
        /* ACTIONS */
        
        // setting email

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_EMAIL_SETTING', 
                'label' => "Edit Email Setting",
                'route' => "",
                'parent' => $setting_email_sm,
                'sort_order' => 1
            ]
        )->id;

        //setting app
        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_APP_SETTING', 
                'label' => "Edit App Setting",
                'route' => "",
                'parent' => $setting_app_sm,
                'sort_order' => 1
            ]
        )->id;

    }
}
