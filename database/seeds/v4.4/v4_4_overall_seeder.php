<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;

class v4_4_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
      
        $dashboard_mm = MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_DASHBOARD'],
        ])  
        ->active()
        ->first();

        MenuModel::where([
            ['id', '=', $dashboard_mm->id],
        ])
        ->update(['route' => '']);
        
        $master_dashboard_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_MASTER_DASHBOARD', 
                'label' => "Master Dashboard",
                'route' => "dashboard",
                'parent' => $dashboard_mm->id,
                'sort_order' => 1
            ]
        )->id;

        $billing_counter_dashboard_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_BILLING_COUNTER_DASHBOARD', 
                'label' => "Billing Counter Dashboard",
                'route' => "billing_counter_dashboard",
                'parent' => $dashboard_mm->id,
                'sort_order' => 2
            ]
        )->id;

    }
}
