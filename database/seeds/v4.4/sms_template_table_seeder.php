<?php

use Illuminate\Database\Seeder;

use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;
use App\Models\MasterStatus;
use App\Models\SmsTemplate as SmsTemplateModel;
use App\Models\Store as StoreModel;

class sms_template_table_seeder extends Seeder
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

        $setting_sms_template_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_SMS_TEMPLATE', 
                'label' => "SMS Templates",
                'route' => "sms_templates",
                'parent' => $settings_mm->id,
                'sort_order' => 5
            ]
        )->id;

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_APP_SETTING'],
        ])
        ->update(['sort_order' => 6]);

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_SMS_TEMPLATE_LISTING', 
                'label' => "View SMS Template Listing",
                'route' => "",
                'parent' => $setting_sms_template_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_SMS_TEMPLATE', 
                'label' => "Edit SMS Template",
                'route' => "",
                'parent' => $setting_sms_template_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_SMS_TEMPLATE', 
                'label' => "View SMS Template",
                'route' => "",
                'parent' => $setting_sms_template_sm,
                'sort_order' => 3
            ]
        )->id;

        $order_sm = MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_POS_ORDERS'],
        ])  
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_SHARE_INVOICE_SMS', 
                'label' => "Send Invoice SMS from Order Detail Page",
                'route' => "",
                'parent' => $order_sm->id,
                'sort_order' => 6
            ]
        )->id;

        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'SMS_TEMPLATE_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'SMS_TEMPLATE_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        SmsTemplateModel::create(
            [
                "slack" => $base_controller->generate_slack("sms_templates"),
                'template_key' => 'POS_SALE_BILL_MESSAGE',
                'message' => 'Thank you for shopping. Order {order_number}. Order amount {currency_code} {order_amount}. Link to view your ebill {public_order_link}', 
                'available_variables' => "{order_number}, {order_amount}, {currency_code}, {payment_method}, {customer_name}, {customer_email}, {customer_phone}, {order_date}, {public_order_link}",
                'description' => "This SMS will be sent to the customer while you close an order. Given the customer has a valid phone number updated.",
                'status' => 0,
                'created_by'=> 1
            ]
        )->id;
    }
}
