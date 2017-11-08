<?php

use Illuminate\Database\Seeder;

use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;
use App\Models\MasterStatus;

class stock_return_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
        
        $stock_mm_data = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_STOCK'],
        ])
        ->active()
        ->first();

        $return_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_RETURNS', 
                'label' => "Stock Return",
                'route' => "stock_returns",
                'parent' => $stock_mm_data->id,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_STOCK_RETURN', 
                'label' => "Add New Stock Return",
                'route' => "",
                'parent' => $return_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_STOCK_RETURN', 
                'label' => "Edit Stock Return",
                'route' => "",
                'parent' => $return_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_STOCK_RETURN', 
                'label' => "View Stock Return Detail",
                'route' => "",
                'parent' => $return_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DELETE_STOCK_RETURN', 
                'label' => "Delete Stock Return",
                'route' => "",
                'parent' => $return_sm,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_STOCK_RETURN_LISTING', 
                'label' => "View Stock Return Listing",
                'route' => "",
                'parent' => $return_sm,
                'sort_order' => 5
            ]
        )->id;
                
        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'STOCK_RETURN_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'STOCK_RETURN_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'STOCK_RETURN_PRODUCT_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'STOCK_RETURN_PRODUCT_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();
    }
}
