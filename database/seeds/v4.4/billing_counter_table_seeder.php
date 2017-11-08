<?php

use Illuminate\Database\Seeder;

use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;
use App\Models\MasterStatus;

class billing_counter_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
        
        $business_account_mm = MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_ACCOUNT'],
        ])  
        ->active()
        ->first();

        $billing_counter_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_BILLING_COUNTERS', 
                'label' => "Billing Counters",
                'route' => "billing_counters",
                'parent' => $business_account_mm->id,
                'sort_order' => 5
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_BILLING_COUNTER', 
                'label' => "Add Billing Counter",
                'route' => "",
                'parent' => $billing_counter_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_BILLING_COUNTER', 
                'label' => "Edit Billing Counter",
                'route' => "",
                'parent' => $billing_counter_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_BILLING_COUNTER', 
                'label' => "View Billing Counter Detail",
                'route' => "",
                'parent' => $billing_counter_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_BILLING_COUNTER_LISTING', 
                'label' => "View Billing Counter Listing",
                'route' => "",
                'parent' => $billing_counter_sm,
                'sort_order' => 4
            ]
        )->id;

        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'BILLING_COUNTER_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'BILLING_COUNTER_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();
    }
}
