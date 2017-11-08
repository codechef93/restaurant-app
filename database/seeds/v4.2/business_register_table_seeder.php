<?php

use Illuminate\Database\Seeder;

use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;

class business_register_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
        
        $account_mm_data = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_ACCOUNT'],
        ])
        ->active()
        ->first();

        $business_register_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_BUSINESS_REGISTERS', 
                'label' => "Business Registers",
                'route' => "business_registers",
                'parent' => $account_mm_data->id,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_BUSINESS_REGISTER_LISTING', 
                'label' => "View Business Register Listing",
                'route' => "",
                'parent' => $business_register_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_BUSINESS_REGISTER', 
                'label' => "View Business Register Detail",
                'route' => "",
                'parent' => $business_register_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DELETE_BUSINESS_REGISTER', 
                'label' => "Delete Business Register",
                'route' => "",
                'parent' => $business_register_sm,
                'sort_order' => 3
            ]
        )->id;
    }
}
