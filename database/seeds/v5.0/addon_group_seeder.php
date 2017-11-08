<?php

use Illuminate\Database\Seeder;

use App\Models\Menu as MenuModel;
use App\Models\MasterStatus;

class addon_group_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stock_mm = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_STOCK'],
        ])
        ->active()
        ->first();

        $add_on_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_ADDON_GROUPS', 
                'label' => "Add-on Groups",
                'route' => "addon_groups",
                'parent' => $stock_mm->id,
                'is_restaurant_menu' => 0,
                'sort_order' => 7
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_ADDON_GROUP', 
                'label' => "Add Add-on Group",
                'route' => "",
                'parent' => $add_on_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_ADDON_GROUP', 
                'label' => "Edit Add-on Group",
                'route' => "",
                'parent' => $add_on_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_ADDON_GROUP', 
                'label' => "View Add-on Group Detail",
                'route' => "",
                'parent' => $add_on_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_ADDON_GROUP_LISTING', 
                'label' => "View Add-on Group Listing",
                'route' => "",
                'parent' => $add_on_sm,
                'sort_order' => 4
            ]
        )->id;

        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'ADDON_GROUP_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'ADDON_GROUP_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();
    }
}
