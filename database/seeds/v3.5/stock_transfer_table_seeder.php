<?php

use Illuminate\Database\Seeder;

use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;
use App\Models\MasterStatus;

class stock_transfer_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
        
        $product_mm_data = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_PRODUCT'],
        ])
        ->active()
        ->first();

        $stock_transfer_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_STOCK_TRANSFER', 
                'label' => "Stock Transfer",
                'route' => "stock_transfers",
                'parent' => $product_mm_data->id,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_STOCK_TRANSFER', 
                'label' => "Add New Stock Transfer",
                'route' => "",
                'parent' => $stock_transfer_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_STOCK_TRANSFER', 
                'label' => "Edit Stock Transfer",
                'route' => "",
                'parent' => $stock_transfer_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_STOCK_TRANSFER', 
                'label' => "View Stock Transfer Detail",
                'route' => "",
                'parent' => $stock_transfer_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DELETE_STOCK_TRANSFER', 
                'label' => "Delete Stock Transfer",
                'route' => "",
                'parent' => $stock_transfer_sm,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VERIFY_STOCK_TRANSFER', 
                'label' => "Verify Stock Transfer Request",
                'route' => "",
                'parent' => $stock_transfer_sm,
                'sort_order' => 5
            ]
        )->id;

        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'STOCK_TRANSFER_STATUS',
                'value' => '0',
                'value_constant' => 'PENDING',
                'label' => 'Pending',
                'color' => 'label yellow-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'STOCK_TRANSFER_STATUS',
                'value' => '1',
                'value_constant' => 'INITIATED',
                'label' => 'Initiated',
                'color' => 'label blue-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'STOCK_TRANSFER_STATUS',
                'value' => '2',
                'value_constant' => 'VERIFIED',
                'label' => 'Verified',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'STOCK_TRANSFER_PRODUCT_STATUS',
                'value' => '0',
                'value_constant' => 'PENDING',
                'label' => 'Pending',
                'color' => 'label yellow-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'STOCK_TRANSFER_PRODUCT_STATUS',
                'value' => '1',
                'value_constant' => 'ACCEPTED',
                'label' => 'Accepted',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'STOCK_TRANSFER_PRODUCT_STATUS',
                'value' => '2',
                'value_constant' => 'REJECTED',
                'label' => 'Rejected',
                'color' => 'label red-label'
            ]
        )->save();

    }
}
