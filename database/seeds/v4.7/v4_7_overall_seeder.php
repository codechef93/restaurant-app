<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;

class v4_7_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $import_data_sm = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_IMPORT_DATA'],
        ])
        ->active()
        ->first(); 
        
        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPLOAD_INGREDIENT', 
                'label' => "Upload Ingredient",
                'route' => "",
                'parent' => $import_data_sm->id,
                'sort_order' => 6
            ]
        )->id;

        $import_update_data_sm = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_UPDATE_DATA'],
        ])
        ->active()
        ->first(); 
        
        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_UPDATE_INGREDIENT', 
                'label' => "Update Ingredient",
                'route' => "",
                'parent' => $import_update_data_sm->id,
                'sort_order' => 6
            ]
        )->id;

        $restaurant_mm = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_RESTAURANT'],
        ])
        ->active()
        ->first();

        $waiter_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_RESTAURANT_WAITER', 
                'label' => "Waiter View",
                'route' => "waiter",
                'parent' => $restaurant_mm->id,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_RESTAURANT_TABLES'],
        ])
        ->update(['sort_order' => 3]);

        MenuModel::whereIn('menu_key', ['MM_RESTAURANT'])
        ->update(['is_restaurant_menu' => 1]);
    }
}
