<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;
use App\Models\MasterStatus;

class measurement_unit_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $base_controller = new Controller;
      
        $settings_mm_data = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SETTINGS'],
        ])
        ->active()
        ->first(); 

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_APP_SETTING'],
        ])
        ->update(['sort_order' => 7]);

        $uom_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_MEASUREMENT_UNIT', 
                'label' => "Measurement Units",
                'route' => "measurement_units",
                'parent' => $settings_mm_data->id,
                'sort_order' => 6,
                'is_restaurant_menu' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_MEASUREMENT_UNIT', 
                'label' => "Add Measurement Unit",
                'route' => "",
                'parent' => $uom_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_MEASUREMENT_UNIT', 
                'label' => "Edit Measurement Unit",
                'route' => "",
                'parent' => $uom_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_MEASUREMENT_UNIT', 
                'label' => "View Measurement Unit",
                'route' => "",
                'parent' => $uom_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_MEASUREMENT_UNIT', 
                'label' => "View Measurement Unit Listing",
                'route' => "",
                'parent' => $uom_sm,
                'sort_order' => 4
            ]
        )->id;

        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'MEASUREMENT_UNIT_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'MEASUREMENT_UNIT_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();
    }
}
