<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;

use App\Models\PaymentMethod;
use App\Models\Menu as MenuModel;

class v5_1_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;

        PaymentMethod::firstOrCreate(
            [
                "slack" => $base_controller->generate_slack("payment_methods"),
                "payment_constant" => 'RAZORPAY',
                "label" => 'Razorpay',
                "description" => 'Razorpay Payment',
                "status" => 1,
                "created_at" => NOW(),
                "updated_at" => NOW(),
                "created_by" => 1
            ]
        )->save();

        $orders_mm = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_ORDERS'],
        ])
        ->active()
        ->first(); 

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_TRANSACTIONS'],
        ])
        ->update(['parent' => $orders_mm->id, 'sort_order' => 6]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_TARGET'],
        ])
        ->update(['sort_order' => 2]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_BUSINESS_REGISTERS'],
        ])
        ->update(['sort_order' => 3]);
        
    }
}
