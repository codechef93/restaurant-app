<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['app_indicator']], function () {
    Route::get('/', "Entry@sign_in")->name('home');
    Route::get('/logout', "Entry@logout")->name('logout');
    Route::get('/forgot_password', "Entry@forgot_password")->name('forgot_password');
    Route::get('/reset_password/{user_slack}/{forgot_password_token}', "Entry@reset_password")->name('reset_password');
    Route::get('/generate_lockout_password/{password_string?}', "Entry@generate_lockout_password")->name('generate_lockout_password');
});

Route::group(['middleware' => ['app_indicator', 'token_auth', 'user_menu']], function () {

    //search 
    Route::get('/search', "Search@index")->name('search');

    //dashboard
    Route::get('/dashboard', "Dashboard@index")->name('dashboard');
    Route::get('/billing_counter_dashboard', "Dashboard@billing_counter_dashboard")->name('billing_counter_dashboard');

    //user
    Route::get('/users', "User@index")->name('users');
    Route::get('/user/{slack}', "User@detail")->name('user');
    Route::get('/add_user', "User@add_user")->name('add_user');
    Route::get('/edit_user/{slack?}', "User@add_user")->name('edit_user');
    Route::get('/profile/{slack}', "User@profile")->name('profile');
    Route::get('/edit_profile', "User@edit_profile")->name('edit_profile');

    //role
    Route::get('/roles', "Role@index")->name('roles');
    Route::get('/role/{slack}', "Role@detail")->name('role');
    Route::get('/add_role', "Role@add_role")->name('add_role');
    Route::get('/edit_role/{slack?}', "Role@add_role")->name('edit_role');

    //customer
    Route::get('/customers', "Customer@index")->name('customers');
    Route::get('/customer/{slack}', "Customer@detail")->name('customer');
    Route::get('/add_customer', "Customer@add_customer")->name('add_customer');
    Route::get('/edit_customer/{slack?}', "Customer@add_customer")->name('edit_customer');

    //product
    Route::get('/products', "Product@index")->name('products');
    Route::get('/product/{slack}', "Product@detail")->name('product');
    Route::get('/add_product', "Product@add_product")->name('add_product');
    Route::get('/edit_product/{slack?}', "Product@add_product")->name('edit_product');
    Route::get('/generate_barcode', "Product@generate_barcode")->name('generate_barcode');
    Route::get('/add_new_stock_transfer_product/{slack?}', "Product@add_product")->name('add_new_stock_transfer_product');

    //category
    Route::get('/categories', "Category@index")->name('categories');
    Route::get('/category/{slack}', "Category@detail")->name('category');
    Route::get('/add_category', "Category@add_category")->name('add_category');
    Route::get('/edit_category/{slack?}', "Category@add_category")->name('edit_category');

    //supplier
    Route::get('/suppliers', "Supplier@index")->name('suppliers');
    Route::get('/supplier/{slack}', "Supplier@detail")->name('supplier');
    Route::get('/add_supplier', "Supplier@add_supplier")->name('add_supplier');
    Route::get('/edit_supplier/{slack?}', "Supplier@add_supplier")->name('edit_supplier');

    //tax code
    Route::get('/tax_codes', "Taxcode@index")->name('tax_codes');
    Route::get('/tax_code/{slack}', "Taxcode@detail")->name('tax_code');
    Route::get('/add_tax_code', "Taxcode@add_tax_code")->name('add_tax_code');
    Route::get('/edit_tax_code/{slack?}', "Taxcode@add_tax_code")->name('edit_tax_code');

    //order
    Route::get('/orders', "Order@index")->name('orders');
    Route::get('/order/{slack}', "Order@detail")->name('order_detail');
    Route::get('/add_order/{table_id?}', "Order@add_order")->name('add_order');
    Route::get('/edit_order/{slack?}', "Order@add_order")->name('edit_order');
    Route::get('/print_order/{slack}', "Order@print_order")->name('print_order');
    Route::get('/print_kot/{slack}', "Order@print_kot")->name('print_kot');
    Route::get('/order_summary/{slack}', "Order@order_summary")->name('order_summary');

    //store
    Route::get('/stores', "Store@index")->name('stores');
    Route::get('/store/{slack}', "Store@detail")->name('store');
    Route::get('/add_store', "Store@add_store")->name('add_store');
    Route::get('/edit_store/{slack?}', "Store@add_store")->name('edit_store');
    Route::get('/select_store', "Store@select_store")->name('select_store');

    //uploads
    Route::get('/import_data', "Import@index")->name('import_data');
    Route::get('/update_data', "Import@update_data")->name('update_data');

    //discount code
    Route::get('/discount_codes', "Discountcode@index")->name('discount_codes');
    Route::get('/discount_code/{slack}', "Discountcode@detail")->name('discount_code');
    Route::get('/add_discount_code', "Discountcode@add_discount_code")->name('add_discount_code');
    Route::get('/edit_discount_code/{slack?}', "Discountcode@add_discount_code")->name('edit_discount_code');

    //payment methods
    Route::get('/payment_methods', "PaymentMethod@index")->name('payment_methods');
    Route::get('/payment_method/{slack}', "PaymentMethod@detail")->name('payment_method');
    Route::get('/add_payment_method', "PaymentMethod@add_payment_method")->name('add_payment_method');
    Route::get('/edit_payment_method/{slack?}', "PaymentMethod@add_payment_method")->name('edit_payment_method');

    //reports
    Route::get('/download_reports', "Report@index")->name('download_reports');
    Route::get('/best_seller_report', "Report@best_seller_report")->name('best_seller_report');
    Route::get('/day_wise_sale_report', "Report@day_wise_sale_report")->name('day_wise_sale_report');
    Route::get('/catgeory_report', "Report@catgeory_report")->name('catgeory_report');
    Route::get('/product_quantity_alert', "Report@product_quantity_alert")->name('product_quantity_alert');
    Route::get('/store_stock_chart', "Report@store_stock_chart")->name('store_stock_chart');

    //setting email
    Route::get('/email_setting', "Setting@email_setting")->name('email_setting');
    Route::get('/edit_email_setting/{slack?}', "Setting@edit_email_setting")->name('edit_email_setting');

    //setting app
    Route::get('/app_setting', "Setting@app_setting")->name('app_setting');
    Route::get('/edit_app_setting', "Setting@edit_app_setting")->name('edit_app_setting');

    //purchase order
    Route::get('/purchase_orders', "PurchaseOrder@index")->name('purchase_orders');
    Route::get('/purchase_order/{slack}', "PurchaseOrder@detail")->name('purchase_order_detail');
    Route::get('/add_purchase_order', "PurchaseOrder@add_purchase_order")->name('add_purchase_order');
    Route::get('/edit_purchase_order/{slack?}', "PurchaseOrder@add_purchase_order")->name('edit_purchase_order');
    Route::get('/print_purchase_order/{slack}', "PurchaseOrder@print_purchase_order")->name('print_purchase_order');

    //invoice
    Route::get('/invoices', "Invoice@index")->name('invoices');
    Route::get('/invoice/{slack}', "Invoice@detail")->name('invoice_detail');
    Route::get('/add_invoice', "Invoice@add_invoice")->name('add_invoice');
    Route::get('/edit_invoice/{slack?}', "Invoice@add_invoice")->name('edit_invoice');
    Route::get('/print_invoice/{slack}', "Invoice@print_invoice")->name('print_invoice');

    //quotation
    Route::get('/quotations', "Quotation@index")->name('quotations');
    Route::get('/quotation/{slack}', "Quotation@detail")->name('quotation_detail');
    Route::get('/add_quotation', "Quotation@add_quotation")->name('add_quotation');
    Route::get('/edit_quotation/{slack?}', "Quotation@add_quotation")->name('edit_quotation');
    Route::get('/print_quotation/{slack}', "Quotation@print_quotation")->name('print_quotation');

    //payment gateway
    Route::get('/payment_gateway/{type}/{slack}', "Order@payment_gateway")->name('payment_gateway');

    //accounts
    Route::get('/accounts', "Account@index")->name('accounts');
    Route::get('/account/{slack}', "Account@detail")->name('account');
    Route::get('/add_account', "Account@add_account")->name('add_account');
    Route::get('/edit_account/{slack?}', "Account@add_account")->name('edit_account');

    //transaction
    Route::get('/transactions', "Transaction@index")->name('transactions');
    Route::get('/transaction/{slack}', "Transaction@detail")->name('transaction');
    Route::get('/add_transaction', "Transaction@add_transaction")->name('add_transaction');
    Route::get('/edit_transaction/{slack?}', "Transaction@add_transaction")->name('edit_transaction');

    //restaurant table
    Route::get('/tables', "Table@index")->name('tables');
    Route::get('/table/{slack}', "Table@detail")->name('table');
    Route::get('/add_table', "Table@add_table")->name('add_table');
    Route::get('/edit_table/{slack?}', "Table@add_table")->name('edit_table');
    
    //restaurant table floorplan
    Route::get('/tables/create', "WaiterController@create")->name('tables_create');
    Route::post('/tables/store', "WaiterController@store")->name('tables_store');
    Route::get('/tables/{id}/edit', "WaiterController@edit")->name('tables_edit');
    Route::post('/tables/{id}/update', "WaiterController@update")->name('tables_update');
    Route::get('/tables/{id}/delete', "WaiterController@delete")->name('tables_delete');

    //kitchen
    Route::get('/kitchen', "Kitchen@index")->name('kitchen');
    //bar
    Route::get('/bar', "Kitchen@bar")->name('bar');
    Route::get('/kitchen1', "Kitchen@kitchen1")->name('kitchen1');
    Route::get('/kitchen2', "Kitchen@kitchen2")->name('kitchen2');
    Route::get('/kitchen3', "Kitchen@kitchen3")->name('kitchen3');

    //target
    Route::get('/targets', "Target@index")->name('targets');
    Route::get('/target/{slack}', "Target@detail")->name('target');
    Route::get('/add_target', "Target@add_target")->name('add_target');
    Route::get('/edit_target/{slack?}', "Target@add_target")->name('edit_target');

    //stock transfers
    Route::get('/stock_transfers', "StockTransfer@index")->name('stock_transfers');
    Route::get('/stock_transfer/{slack}', "StockTransfer@detail")->name('stock_transfer');
    Route::get('/add_stock_transfer', "StockTransfer@add_stock_transfer")->name('add_stock_transfer');
    Route::get('/edit_stock_transfer/{slack?}', "StockTransfer@add_stock_transfer")->name('edit_stock_transfer');
    Route::get('/verify_stock_transfer/{slack?}', "StockTransfer@verify_stock_transfer")->name('verify_stock_transfer');

    //stock return
    Route::get('/stock_returns', "StockReturn@index")->name('stock_returns');
    Route::get('/stock_return/{slack}', "StockReturn@detail")->name('stock_return_detail');
    Route::get('/add_stock_return', "StockReturn@add_stock_return")->name('add_stock_return');
    Route::get('/edit_stock_return/{slack?}', "StockReturn@add_stock_return")->name('edit_stock_return');
    Route::get('/print_stock_return/{slack}', "StockReturn@print_stock_return")->name('print_stock_return');
    
    //notifications
    Route::get('/notifications', "Notification@index")->name('notifications');
    Route::get('/notification/{slack}', "Notification@detail")->name('notification');
    Route::get('/add_notification', "Notification@add_notification")->name('add_notification');
    Route::get('/edit_notification/{slack?}', "Notification@add_notification")->name('edit_notification');

    //business registers
    Route::get('/business_registers', "BusinessRegister@index")->name('business_registers');
    Route::get('/business_register/{slack}', "BusinessRegister@detail")->name('business_register');
    Route::get('/add_business_register', "BusinessRegister@add_business_register")->name('add_business_register');
    Route::get('/print_register_report/{slack?}', "BusinessRegister@print_register_report")->name('print_register_report');
    Route::get('/register_summary/{slack}', "BusinessRegister@register_summary")->name('register_summary');

    //setting sms
    
    Route::get('/sms_settings', "SmsSetting@index")->name('sms_settings');
    Route::get('/sms_setting/{slack}', "SmsSetting@detail")->name('sms_setting');
    Route::get('/edit_sms_setting/{slack}', "SmsSetting@add_sms_setting")->name('edit_sms_setting');

    //sms templates
    Route::get('/sms_templates', "SmsTemplates@index")->name('sms_templates');
    Route::get('/sms_template/{slack}', "SmsTemplates@detail")->name('sms_template');
    Route::get('/edit_sms_template/{slack}', "SmsTemplates@add_sms_template")->name('add_sms_template');

    //billing counter
    Route::get('/billing_counters', "BillingCounter@index")->name('billing_counters');
    Route::get('/billing_counter/{slack}', "BillingCounter@detail")->name('billing_counter');
    Route::get('/add_billing_counter', "BillingCounter@add_billing_counter")->name('add_billing_counter');
    Route::get('/edit_billing_counter/{slack?}', "BillingCounter@add_billing_counter")->name('edit_billing_counter');
    
    //measurement unit
    Route::get('/measurement_units', "MeasurementUnit@index")->name('measurement_units');
    Route::get('/measurement_unit/{slack}', "MeasurementUnit@detail")->name('measurement_unit');
    Route::get('/add_measurement_unit', "MeasurementUnit@add_measurement_unit")->name('add_measurement_unit');
    Route::get('/edit_measurement_unit/{slack?}', "MeasurementUnit@add_measurement_unit")->name('edit_measurement_unit');

    //waiter view
    Route::get('/waiter', "Kitchen@waiter")->name('waiter');

    //Restaurant menu
    Route::get('/restaurant_menu', "RestaurantMenu@index")->name('restaurant_menu');

    //Bookings
    Route::get('/bookings', "Booking@index")->name('bookings');
    Route::get('/booking/{slack}', "Booking@detail")->name('booking');
    Route::get('/add_booking', "Booking@add_booking")->name('add_booking');
    Route::get('/edit_booking/{slack?}', "Booking@add_booking")->name('edit_booking');

    //Calendar
    Route::get('/calendar', "Booking@calendar")->name('calendar');

    //Digital menu orders
    Route::get('/digital_menu_orders', "Order@digital_menu_orders")->name('digital_menu_orders');

    //Add-on Product Group
    Route::get('/addon_groups', "AddonGroup@index")->name('addon_groups');
    Route::get('/addon_group/{slack}', "AddonGroup@detail")->name('addon_group');
    Route::get('/add_addon_group', "AddonGroup@add_addon_group")->name('add_addon_group');
    Route::get('/edit_addon_group/{slack?}', "AddonGroup@add_addon_group")->name('edit_addon_group');

    //Variant Options
    Route::get('/variant_options', "VariantOption@index")->name('variant_options');
    Route::get('/variant_option/{slack}', "VariantOption@detail")->name('variant_option');
    Route::get('/add_variant_option', "VariantOption@add_variant_option")->name('add_variant_option');
    Route::get('/edit_variant_option/{slack?}', "VariantOption@add_variant_option")->name('edit_variant_option');

    //Printers
    Route::get('/printers', "Printer@index")->name('printers');
    Route::get('/printer/{slack}', "Printer@detail")->name('printer');
    Route::get('/add_printer', "Printer@add_printer")->name('add_printer');
    Route::get('/edit_printer/{slack?}', "Printer@add_printer")->name('edit_printer');
});

Route::get('/order_public/{slack}', "Order@detail_public_view")->name('order_public');
Route::get('/our_menu/{store_slack}/{table_slack?}', "RestaurantMenu@our_menu")->name('our_menu');
Route::get('/qrmenu_payment/{type}/{slack}', "Order@payment_gateway_public")->name('payment_gateway_public');
Route::get('/print_order_public/{slack}', "Order@print_order_public")->name('print_order_public');

//Routes for C Panel
Route::get('/execute_database_migrations', "Setting@cpanel_migrate");
Route::get('/execute_create_storage_link', "Setting@cpanel_storage_link");
Route::get('/execute_initial_configs', "Setting@cpanel_intial_config");



//Clear cache
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});