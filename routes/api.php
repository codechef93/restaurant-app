<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/user/authenticate', 'API\User@authenticate');
Route::post('/user/forgot_password', 'API\User@forgot_password');
Route::post('/user/reset_password', 'API\User@reset_password');

//customer order
Route::post('/add_customer_order', 'API\Order@store_customer_order');
Route::post('/get_customer_order_product_addon_groups', 'API\Product@get_customer_order_product_addon_groups');

//otp
Route::post('/generate_otp', 'API\Otp@store');

//payment gateway public
Route::post('/get_stripe_payment_intent_public', 'API\PaymentGateway@get_stripe_payment_intent_public');
Route::post('/record_stripe_payment_success_public', 'API\PaymentGateway@record_stripe_payment_success_public');
Route::post('/record_razorpay_payment_success_public', 'API\PaymentGateway@record_razorpay_payment_success_public');
Route::post('/get_paypal_order_data_public', 'API\PaymentGateway@get_paypal_order_data_public');

//order listing
Route::post('/get_qr_order_history', 'API\Order@get_qr_order_history');

//Pusher authorization request
//Route::post('/authorize_pusher_client', 'API\Notification@authorize_pusher_client_connection')->name('authorize_pusher_client');

Route::group(['middleware' => ['token_auth']], function () {
    //user
    Route::post('/users', 'API\User@index');
    Route::post('/add_user', 'API\User@store');
    Route::post('/update_user/{slack}', 'API\User@update')->name('update_user');
    Route::post('/reset_user_password/{slack}', 'API\User@reset_user_password');
    Route::post('/load_users', 'API\User@load_user_list');
    Route::post('/user/{slack}', 'API\User@show');
    Route::post('/user_list', 'API\User@list');

    //profile
    Route::post('/update_basic_profile', 'API\User@update_basic_profile')->name('update_basic_profile');
    Route::post('/update_password', 'API\User@update_password')->name('update_password');
    Route::post('/update_profile_image', 'API\User@update_profile_image')->name('update_profile_image');
    Route::post('/remove_profile_image', 'API\User@remove_profile_image')->name('remove_profile_image');
    Route::post('/update_profile_store', 'API\User@update_profile_store')->name('update_profile_store');
    Route::post('/update_profile_language', 'API\User@update_profile_language')->name('update_profile_language');

    //dashboard
    Route::post('/get_dashboard_stats', 'API\Dashboard@get_dashboard_stats');
    Route::post('/get_order_chart_stats', 'API\Dashboard@get_order_chart_stats');
    Route::post('/get_recent_trasactions', 'API\Dashboard@get_recent_trasactions');
    Route::post('/get_billing_counter_stats', 'API\Dashboard@get_billing_counter_stats');

    //role
    Route::post('/roles', 'API\Role@index');
    Route::post('/add_role', 'API\Role@store');
    Route::post('/update_role/{slack}', 'API\Role@update');
    Route::post('/role/{slack}', 'API\Role@show');
    Route::post('/role_list', 'API\Role@list');

    //customer
    Route::post('/customers', 'API\Customer@index');
    Route::post('/add_customer', 'API\Customer@store');
    Route::post('/update_customer/{slack}', 'API\Customer@update');
    Route::post('/load_customers', 'API\Customer@load_customer_list');
    Route::post('/customer/{slack}', 'API\Customer@show');
    Route::post('/customer_list', 'API\Customer@list');

    //category
    Route::post('/categories', 'API\Category@index');
    Route::post('/add_category', 'API\Category@store');
    Route::post('/update_category/{slack}', 'API\Category@update');
    Route::post('/category/{slack}', 'API\Category@show');
    Route::post('/category/{slack}', 'API\Category@show');
    Route::post('/category_list', 'API\Category@list');

    //supplier
    Route::post('/suppliers', 'API\Supplier@index');
    Route::post('/add_supplier', 'API\Supplier@store');
    Route::post('/update_supplier/{slack}', 'API\Supplier@update');
    Route::post('/load_suppliers', 'API\Supplier@load_supplier_list');
    Route::post('/supplier/{slack}', 'API\Supplier@show');
    Route::post('/supplier_list', 'API\Supplier@list');

    //product
    Route::post('/products', 'API\Product@index');
    Route::post('/add_product', 'API\Product@store')->name('add_product');
    Route::post('/update_product/{slack}', 'API\Product@update')->name('update_product');
    Route::post('/get_product', 'API\Product@get_product');
    Route::post('/generate_barcodes', 'API\Product@generate_barcodes');
    Route::post('/load_product_for_po', 'API\Product@load_product_for_po');
    Route::post('/load_product_for_stock_transfer', 'API\Product@load_product_for_stock_transfer');
    Route::post('/product/{slack}', 'API\Product@show');
    Route::post('/product_list', 'API\Product@list');
    Route::post('/delete_product_image', 'API\Product@delete_product_image')->name('delete_product_image');
    Route::post('/load_ingredients', 'API\Product@load_ingredients');
    Route::post('/load_addon_group_product', 'API\Product@load_addon_group_product');
    Route::post('/get_product_addon_groups', 'API\Product@get_product_addon_groups');
    Route::post('/load_variant_products', 'API\Product@load_variant_products');
    Route::post('/remove_variant_product', 'API\Product@remove_variant_product');

    //tax code
    Route::post('/tax_codes', 'API\Taxcode@index');
    Route::post('/add_tax_code', 'API\Taxcode@store');
    Route::post('/update_tax_code/{slack}', 'API\Taxcode@update');
    Route::post('/tax_code/{slack}', 'API\Taxcode@show');
    Route::post('/tax_code_list', 'API\Taxcode@list');

    //order
    Route::post('/draggable_change', 'API\Order@update_category_order');
    Route::post('/orders', 'API\Order@index');
    Route::post('/add_order', 'API\Order@store');
    Route::post('/update_order/{slack}', 'API\Order@update');
    Route::post('/delete_order/{slack}', 'API\Order@destroy');
    Route::post('/get_hold_list', 'API\Order@get_hold_list');
    Route::post('/get_in_kitchen_order_list', 'API\Order@get_in_kitchen_order_list');
    Route::post('/update_kitchen_order_status', 'API\Order@update_kitchen_order_status');
    Route::post('/get_register_order_amount', 'API\Order@get_register_order_amount');
    Route::post('/get_running_order_list', 'API\Order@get_running_order_list');
    Route::post('/get_running_order_list_waiter', 'API\Order@get_running_order_list_waiter');
    Route::post('/share_invoice_sms/{slack}', 'API\Order@share_invoice_sms');
    Route::post('/order/{slack}', 'API\Order@show');
    Route::post('/order_list', 'API\Order@list');
    Route::post('/get_waiter_order_list', 'API\Order@get_waiter_order_list');
    Route::post('/update_kitchen_item_status', 'API\Order@update_kitchen_item_status');
    Route::post('/get_digital_menu_orders_list', 'API\Order@get_digital_menu_orders_list');
    Route::post('/get_digital_menu_orders_list_waiter', 'API\Order@get_digital_menu_orders_list_waiter');
    Route::post('/get_digital_menu_orders', 'API\Order@get_digital_menu_orders');
    Route::post('/send_order_to_kitchen', 'API\Order@send_order_to_kitchen');
    Route::post('/toggle_order_dismissed_from_screen_status', 'API\Order@toggle_order_dismissed_from_screen_status');
    Route::post('/get_merge_order_list', 'API\Order@get_merge_order_list');
    Route::post('/merge_order', 'API\Order@merge_order');
    Route::post('/unmerge_order', 'API\Order@unmerge_order');
    
    //store
    Route::post('/stores', 'API\Store@index');
    Route::post('/add_store', 'API\Store@store');
    Route::post('/update_store/{slack}', 'API\Store@update');
    Route::post('/store/{slack}', 'API\Store@show');
    Route::post('/store_list', 'API\Store@list');
    Route::post('/update_menu_time', 'API\Store@update_menu_time');

    //import
    Route::post('/import_data', 'API\Import@index');
    Route::post('/update_data', 'API\Import@update_data');
    Route::post('/download_reference_sheet', 'API\Import@generate_reference_sheet');

    //discount code
    Route::post('/discount_codes', 'API\Discountcode@index');
    Route::post('/add_discount_code', 'API\Discountcode@store');
    Route::post('/update_discount_code/{slack}', 'API\Discountcode@update');
    Route::post('/discount_code/{slack}', 'API\Discountcode@show');
    Route::post('/discount_code_list', 'API\Discountcode@list');

    //payment method
    Route::post('/payment_methods', 'API\PaymentMethod@index');
    Route::post('/add_payment_method', 'API\PaymentMethod@store');
    Route::post('/update_payment_method/{slack}', 'API\PaymentMethod@update');
    Route::post('/payment_method/{slack}', 'API\PaymentMethod@show');
    Route::post('/payment_method_list', 'API\PaymentMethod@list');

    //reports
    Route::post('/user_report', 'API\Report@user_report');
    Route::post('/category_report', 'API\Report@category_report');
    Route::post('/customer_report', 'API\Report@customer_report');
    Route::post('/supplier_report', 'API\Report@supplier_report');
    Route::post('/taxcode_report', 'API\Report@taxcode_report');
    Route::post('/discountcode_report', 'API\Report@discountcode_report');
    Route::post('/product_report', 'API\Report@product_report');
    Route::post('/store_report', 'API\Report@store_report');
    Route::post('/order_report', 'API\Report@order_report');
    Route::post('/purchase_order_report', 'API\Report@purchase_order_report');
    Route::post('/invoice_report', 'API\Report@invoice_report');
    Route::post('/quotation_report', 'API\Report@quotation_report');
    Route::post('/transaction_report', 'API\Report@transaction_report');
    Route::post('/get_trending_products', 'API\Report@get_trending_products');
    Route::post('/get_category_performance', 'API\Report@get_category_performance');
    Route::post('/product_alert_report', 'API\Report@product_alert_report');
    Route::post('/store_stock_chart', 'API\Report@store_stock_chart');
    Route::post('/sale_tax_report', 'API\Report@sale_tax_report');
    Route::post('/billing_counter_report', 'API\Report@billing_counter_report');

    //purchase order
    Route::post('/purchase_orders', 'API\PurchaseOrder@index');
    Route::post('/add_purchase_order', 'API\PurchaseOrder@store');
    Route::post('/update_purchase_order/{slack}', 'API\PurchaseOrder@update');
    Route::post('/update_po_status/{slack}', 'API\PurchaseOrder@update_po_status');
    Route::post('/delete_purchase_order/{slack}', 'API\PurchaseOrder@destroy');
    Route::post('/purchase_order/{slack}', 'API\PurchaseOrder@show');
    Route::post('/purchase_order_list', 'API\PurchaseOrder@list');
    Route::post('/generate_invoice_from_po/{slack}', 'API\PurchaseOrder@generate_invoice_from_po');

    //setting
    Route::post('/add_setting_email', 'API\Setting@add_setting_email');
    Route::post('/update_setting_email/{slack}', 'API\Setting@update_setting_email');
    Route::post('/send_test_email', 'API\Setting@send_test_email');

    Route::post('/update_setting_app', 'API\Setting@update_setting_app')->name('update_setting_app');
    Route::post('/remove_company_logo', 'API\Setting@remove_company_image')->name('remove_company_logo');
    Route::post('/clear_app_cache', 'API\Setting@clear_app_cache');
    Route::post('/clear_app_storage', 'API\Setting@clear_app_storage');
    Route::post('/deactivate', 'API\Setting@deactivate_app');

    //sms setting
    Route::post('/sms_settings', 'API\SmsSetting@index');
    Route::post('/update_setting_sms/{slack}', 'API\SmsSetting@update');
    Route::post('/sms_setting/{slack}', 'API\SmsSetting@show');

    //search
    Route::post('/filter_orders', 'API\Order@filter_orders');
    Route::post('/filter_customers', 'API\Customer@filter_customers');
    Route::post('/filter_purchase_orders', 'API\PurchaseOrder@filter_purchase_orders');
    Route::post('/filter_users', 'API\User@filter_users');
    Route::post('/filter_invoices', 'API\Invoice@filter_invoices');
    Route::post('/filter_quotations', 'API\Quotation@filter_quotations');
    Route::post('/filter_transactions', 'API\Transaction@filter_transactions');

    //invoice
    Route::post('/invoices', 'API\Invoice@index');
    Route::post('/add_invoice', 'API\Invoice@store');
    Route::post('/update_invoice/{slack}', 'API\Invoice@update');
    Route::post('/update_invoice_status/{slack}', 'API\Invoice@update_invoice_status');
    Route::post('/load_bill_to_list', 'API\Invoice@load_bill_to_list');
    Route::post('/delete_invoice/{slack}', 'API\Invoice@destroy');
    Route::post('/get_invoice_pending_payment_data/{slack}', 'API\Invoice@get_invoice_pending_payment_data');
    Route::post('/invoice/{slack}', 'API\Invoice@show');
    Route::post('/invoice_list', 'API\Invoice@list');

    //quotation
    Route::post('/quotations', 'API\Quotation@index');
    Route::post('/add_quotation', 'API\Quotation@store');
    Route::post('/update_quotation/{slack}', 'API\Quotation@update');
    Route::post('/update_quotation_status/{slack}', 'API\Quotation@update_quotation_status');
    Route::post('/delete_quotation/{slack}', 'API\Quotation@destroy');
    Route::post('/quotation/{slack}', 'API\Quotation@show');
    Route::post('/quotation_list', 'API\Quotation@list');

    //payment gateway
    Route::post('/get_stripe_payment_intent', 'API\PaymentGateway@get_stripe_payment_intent');
    Route::post('/record_stripe_payment_success', 'API\PaymentGateway@record_stripe_payment_success');
    Route::post('/record_razorpay_payment_success', 'API\PaymentGateway@record_razorpay_payment_success');
    Route::post('/get_paypal_order_data', 'API\PaymentGateway@get_paypal_order_data');

    //account
    Route::post('/accounts', 'API\Account@index');
    Route::post('/add_account', 'API\Account@store');
    Route::post('/update_account/{slack}', 'API\Account@update');
    Route::post('/account/{slack}', 'API\Account@show');
    Route::post('/account_list', 'API\Account@list');

    //transactions
    Route::post('/transactions', 'API\Transaction@index');
    Route::post('/add_transaction', 'API\Transaction@store');
    Route::post('/update_transaction/{slack}', 'API\Transaction@update');
    Route::post('/delete_transaction/{slack}', 'API\Transaction@destroy');
    Route::post('/transaction/{slack}', 'API\Transaction@show');
    Route::post('/transaction_list', 'API\Transaction@list');

    //tables
    Route::post('/tables', 'API\Table@index');
    Route::post('/add_table', 'API\Table@store');
    Route::post('/update_table/{slack}', 'API\Table@update');
    Route::post('/delete_table/{slack}', 'API\Table@destroy');
    Route::post('/table/{slack}', 'API\Table@show');
    Route::post('/table_list', 'API\Table@list');

    //target
    Route::post('/targets', 'API\Target@index');
    Route::post('/add_target', 'API\Target@store');
    Route::post('/update_target/{slack}', 'API\Target@update');
    Route::post('/delete_target/{slack}', 'API\Target@destroy');
    Route::post('/target/{slack}', 'API\Target@show');
    Route::post('/target_list', 'API\Target@list');

    //stock transfer
    Route::post('/stock_transfers', 'API\StockTransfer@index');
    Route::post('/add_stock_transfer', 'API\StockTransfer@store');
    Route::post('/update_stock_transfer/{slack}', 'API\StockTransfer@update');
    Route::post('/delete_stock_transfer/{slack}', 'API\StockTransfer@destroy');
    Route::post('/reject_stock_transfer_product/{slack}', 'API\StockTransfer@reject_stock_transfer_product');
    Route::post('/merge_product_stock', 'API\StockTransfer@merge_product_stock');
    Route::post('/stock_transfer/{slack}', 'API\StockTransfer@show');
    Route::post('/stock_transfer_list', 'API\StockTransfer@list');

    //stock return
    Route::post('/stock_returns', 'API\StockReturn@index');
    Route::post('/add_stock_return', 'API\StockReturn@store');
    Route::post('/update_stock_return/{slack}', 'API\StockReturn@update');
    Route::post('/delete_stock_return/{slack}', 'API\StockReturn@destroy');
    Route::post('/stock_return/{slack}', 'API\StockReturn@show');
    Route::post('/stock_return_list', 'API\StockReturn@list');

    //notification
    Route::post('/notifications', 'API\Notification@index');
    Route::post('/add_notification', 'API\Notification@store');
    Route::post('/delete_notification/{slack}', 'API\Notification@destroy');
    Route::post('/load_notification', 'API\Notification@load_notification');
    Route::post('/notification/{slack}', 'API\Notification@show');
    Route::post('/notification_list', 'API\Notification@list');
    Route::post('/mark_as_read', 'API\Notification@mark_as_read');
    Route::post('/remove_all_notifications', 'API\Notification@remove_all_notifications');

    //business registers
    Route::post('/business_registers', 'API\BusinessRegister@index');
    Route::post('/open_register', 'API\BusinessRegister@open_register');
    Route::post('/close_register', 'API\BusinessRegister@close_register');
    Route::post('/delete_register/{slack}', 'API\BusinessRegister@destroy');
    Route::post('/business_register/{slack}', 'API\BusinessRegister@show');
    Route::post('/business_register_list', 'API\BusinessRegister@list');

    //sms templates
    Route::post('/sms_templates', 'API\SmsTemplate@index');
    Route::post('/update_sms_template/{slack}', 'API\SmsTemplate@update');

    //billing_counter
    Route::post('/billing_counters', 'API\BillingCounter@index');
    Route::post('/add_billing_counter', 'API\BillingCounter@store');
    Route::post('/update_billing_counter/{slack}', 'API\BillingCounter@update');
    Route::post('/billing_counter/{slack}', 'API\BillingCounter@show');
    Route::post('/billing_counter_list', 'API\BillingCounter@list');

    //masters
    Route::post('/get_billing_master_account_type', 'API\Master@get_billing_master_account_type');
    Route::post('/get_billing_type', 'API\Master@get_billing_type');
    Route::post('/get_master_invoice_print_type', 'API\Master@get_master_invoice_print_type');
    Route::post('/get_master_order_type', 'API\Master@get_master_order_type');
    Route::post('/get_master_status', 'API\Master@get_master_status');
    Route::post('/get_master_transaction_type', 'API\Master@get_master_transaction_type');

    //measurement unit
    Route::post('/measurement_units', 'API\MeasurementUnit@index');
    Route::post('/add_measurement_unit', 'API\MeasurementUnit@store');
    Route::post('/update_measurement_unit/{slack}', 'API\MeasurementUnit@update');
    Route::post('/measurement_unit/{slack}', 'API\MeasurementUnit@show');
    Route::post('/measurement_unit_list', 'API\MeasurementUnit@list');

    //booking
    Route::post('/bookings', 'API\Booking@index');
    Route::post('/add_booking', 'API\Booking@store');
    Route::post('/update_booking/{slack}', 'API\Booking@update');
    Route::post('/booking/{slack}', 'API\Booking@show');
    Route::post('/booking_list', 'API\Booking@list');
    Route::post('/delete_booking/{slack}', 'API\Booking@destroy');
    Route::post('/load_events', 'API\Booking@load_events');
    Route::post('/get_running_booking_list', 'API\Booking@running_list');

    //add on group
    Route::post('/addon_groups', 'API\AddonGroup@index');
    Route::post('/add_addon_group', 'API\AddonGroup@store');
    Route::post('/update_addon_group/{slack}', 'API\AddonGroup@update');
    Route::post('/addon_group/{slack}', 'API\AddonGroup@show');
    Route::post('/addon_group_list', 'API\AddonGroup@list');

    //variant option
    Route::post('/variant_options', 'API\VariantOption@index');
    Route::post('/add_variant_option', 'API\VariantOption@store');
    Route::post('/update_variant_option/{slack}', 'API\VariantOption@update');
    Route::post('/variant_option/{slack}', 'API\VariantOption@show');
    Route::post('/variant_option_list', 'API\VariantOption@list');

    //printer
    Route::post('/printers', 'API\Printer@index');
    Route::post('/add_printer', 'API\Printer@store');
    Route::post('/update_printer/{slack}', 'API\Printer@update');
    Route::post('/printer/{slack}', 'API\Printer@show');
    Route::post('/printer_list', 'API\Printer@list');
    Route::post('/print_with_printnode', 'API\Printer@print_with_printnode');

    //testing
    Route::post('/posting', 'API\Order@testing');
});