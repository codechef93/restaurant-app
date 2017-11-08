<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;

class v3_5_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
        
        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_PRODUCT'],
        ])
        ->update(['label' => 'Stock' , 'menu_key' => 'MM_STOCK']);

    }
}
