<?php

use Illuminate\Database\Seeder;

use App\Models\MasterStatus;
use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;

class targets_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
        
        $accounts_mm_data = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_ACCOUNT'],
        ])
        ->active()
        ->first();

        $targets_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_TARGET', 
                'label' => "Monthly Targets",
                'route' => "targets",
                'parent' => $accounts_mm_data->id,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_TARGET', 
                'label' => "Add Target",
                'route' => "",
                'parent' => $targets_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_TARGET', 
                'label' => "Edit Target",
                'route' => "",
                'parent' => $targets_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_TARGET', 
                'label' => "View Target Detail",
                'route' => "",
                'parent' => $targets_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DELETE_TARGET', 
                'label' => "Delete Target",
                'route' => "",
                'parent' => $targets_sm,
                'sort_order' => 4
            ]
        )->id;
    }
}
