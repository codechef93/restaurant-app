<?php

use Illuminate\Database\Seeder;
use App\Models\MasterStatus;
use App\Models\Menu as MenuModel;

class quotation_requirement_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //add menu for invoice
        $order_mm_data = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_ORDERS'],
        ])
        ->active()
        ->first();

        $quotation_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_QUOTATIONS', 
                'label' => "Quotations",
                'route' => "quotations",
                'parent' => $order_mm_data->id,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_QUOTATION', 
                'label' => "Add Quotation",
                'route' => "",
                'parent' => $quotation_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_QUOTATION', 
                'label' => "Edit Quotation",
                'route' => "",
                'parent' => $quotation_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_QUOTATION', 
                'label' => "View Quotation Details",
                'route' => "",
                'parent' => $quotation_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DELETE_QUOTATION', 
                'label' => "Delete Quotation",
                'route' => "",
                'parent' => $quotation_sm,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_STATUS_QUOTATION', 
                'label' => "Change Quotation Status",
                'route' => "",
                'parent' => $quotation_sm,
                'sort_order' => 5
            ]
        )->id;
        
        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'QUOTATION_STATUS',
                'value' => '0',
                'value_constant' => 'CANCELLED',
                'label' => 'Cancelled',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'QUOTATION_STATUS',
                'value' => '1',
                'value_constant' => 'PENDING',
                'label' => 'Pending',
                'color' => 'label blue-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'QUOTATION_STATUS',
                'value' => '2',
                'value_constant' => 'ACCEPTED',
                'label' => 'Accepted',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'QUOTATION_STATUS',
                'value' => '3',
                'value_constant' => 'DECLINED',
                'label' => 'Declined',
                'color' => 'label red-label'
            ]
        )->save();


        MasterStatus::firstOrCreate(
            [
                'key' => 'QUOTATION_STATUS',
                'value' => '4',
                'value_constant' => 'EXPIRED',
                'label' => 'Expired',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'QUOTATION_PRODUCT_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'QUOTATION_PRODUCT_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

    }
}
