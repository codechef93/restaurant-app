<?php

use Illuminate\Database\Seeder;

use App\Models\Menu as MenuModel;
use App\Models\MasterStatus;

class v5_4_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $settings_mm = MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SETTINGS'],
        ])  
        ->active()
        ->first();

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_APP_SETTING'],
        ])
        ->update(['sort_order' => 10]);

        $printer_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_PRINTERS', 
                'label' => "Printers",
                'route' => "printers",
                'parent' => $settings_mm->id,
                'is_restaurant_menu' => 0,
                'sort_order' => 9
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_PRINTER', 
                'label' => "Add Printer",
                'route' => "",
                'parent' => $printer_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_PRINTER', 
                'label' => "Edit Printer",
                'route' => "",
                'parent' => $printer_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_PRINTER', 
                'label' => "View Printer Detail",
                'route' => "",
                'parent' => $printer_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_PRINTER_LISTING', 
                'label' => "View Printer Listing",
                'route' => "",
                'parent' => $printer_sm,
                'sort_order' => 4
            ]
        )->id;

        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'PRINTER_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'PRINTER_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();
    }
}
