<?php

use App\Models\MasterStatus;
use App\Models\Order as OrderModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Menu as MenuModel;
use Illuminate\Database\Seeder;

class v5_2_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $payment_pending = MasterStatus::select('value')->filterByValueConstant('ORDER_STATUS', 'PAYMENT_PENDING')->first();
        $payment_failed = MasterStatus::select('value')->filterByValueConstant('ORDER_STATUS', 'PAYMENT_FAILED')->first();

        MasterStatus::where([
            ['key', '=', 'ORDER_STATUS'],
            ['value_constant', '=', 'PAYMENT_PENDING'],
        ])
        ->update(['value_constant' => 'PAYMENT_ATTEMPTED', 'label' => 'Payment Attempted']);

        MasterStatus::where([
            ['key', '=', 'ORDER_STATUS'],
            ['value_constant', '=', 'PAYMENT_FAILED'],
        ])
        ->update(['key' => 'ORDER_PAYMENT_STATUS','value_constant' => 'PAYMENT_PENDING', 'value' => 0, 'label' => 'Payment Pending', 'color' => 'label orange-label']);

        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_PAYMENT_STATUS',
                'value' => 1,
                'value_constant' => 'PAYMENT_SUCCESS',
                'label' => 'Payment Success',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_PAYMENT_STATUS',
                'value' => 2,
                'value_constant' => 'PAYMENT_FAILED',
                'label' => 'Payment Failed',
                'color' => 'label red-label'
            ]
        )->save();

        $payment_attempted = MasterStatus::select('value')->filterByValueConstant('ORDER_STATUS', 'PAYMENT_ATTEMPTED')->first();

        $payment_status_pending = MasterStatus::select('value')->filterByValueConstant('ORDER_PAYMENT_STATUS', 'PAYMENT_PENDING')->first();
        if(isset($payment_pending->value) && isset($payment_status_pending->value) && isset($payment_attempted->value)){

            OrderModel::withoutGlobalScopes()->where([
                ['status', '=', $payment_pending->value]
            ])
            ->update(['status' => $payment_attempted->value, 'payment_status' => $payment_status_pending->value]);
        }

        $payment_status_failed = MasterStatus::select('value')->filterByValueConstant('ORDER_PAYMENT_STATUS', 'PAYMENT_FAILED')->first();
        if(isset($payment_failed->value) && isset($payment_status_failed->value) && isset($payment_attempted->value)){

            OrderModel::withoutGlobalScopes()->where([
                ['status', '=', $payment_failed->value]
            ])
            ->update(['status' => $payment_attempted->value, 'payment_status' => $payment_status_failed->value]);

        }

        $pending_order_statuses = MasterStatus::select('value')->where([
            ['key', '=', 'ORDER_STATUS']
        ])->whereIn('value_constant', ['DELETED', 'HOLD', 'IN_KITCHEN', 'CUSTOMER_ORDER'])->pluck('value')->toArray();

        $pending_order_status_values = array_values($pending_order_statuses);
        
        if(isset($pending_order_status_values) && isset($payment_status_pending->value)){
            OrderModel::withoutGlobalScopes()->whereIn('status', $pending_order_status_values)
            ->update(['payment_status' => $payment_status_pending->value]);
        }

        $payment_status_success = MasterStatus::select('value')->filterByValueConstant('ORDER_PAYMENT_STATUS', 'PAYMENT_SUCCESS')->first();
        $order_closed = MasterStatus::select('value')->filterByValueConstant('ORDER_STATUS', 'CLOSED')->first();

        if(isset($order_closed->value) && isset($payment_status_success->value)){
            OrderModel::withoutGlobalScopes()->where([
                ['status', '=', $order_closed->value]
            ])
            ->update(['payment_status' => $payment_status_success->value]);
        }

        $pending_order_status_values = array_values($pending_order_statuses);

        OrderModel::withoutGlobalScopes()->where([
            ['status', '=', $order_closed->value]
        ])
        ->update(['payment_status' => $payment_status_success->value]);

        PaymentMethodModel::whereIn('payment_constant', ['STRIPE', 'PAYPAL', 'RAZORPAY'])
        ->update(['activate_on_digital_menu' => 1]);

        MenuModel::where([
            ['type', '=', 'ACTIONS'],
            ['menu_key', '=', 'A_VIEW_MEASUREMENT_UNIT'],
        ])
        ->update(['menu_key' => 'A_VIEW_MEASUREMENT_UNIT_LISTING']);

        MasterStatus::firstOrCreate(
            [
                'key' => 'ORDER_STATUS',
                'value' => '4',
                'value_constant' => 'MERGED',
                'label' => 'Merged',
                'color' => 'label grey-label'
            ]
        )->save();

        $orders_sm = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_POS_ORDERS']
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_MERGE_ORDER', 
                'label' => "Merge Order",
                'route' => "",
                'parent' => $orders_sm->id,
                'is_restaurant_menu' => 0,
                'sort_order' => 7
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UNMERGE_ORDER', 
                'label' => "Unmerge Order",
                'route' => "",
                'parent' => $orders_sm->id,
                'is_restaurant_menu' => 0,
                'sort_order' => 8
            ]
        )->id;
    }
}
