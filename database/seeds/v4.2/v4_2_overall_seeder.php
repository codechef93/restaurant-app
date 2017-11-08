<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;

class v4_2_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $base_controller = new Controller;
      
        $reports_mm_data = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_REPORT'],
        ])
        ->active()
        ->first(); 

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_REPORT'],
        ])
        ->update(['route' => '']);
        
        $download_report_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_DOWNLOAD_REPORT', 
                'label' => "Download Reports",
                'route' => "download_reports",
                'parent' => $reports_mm_data->id,
                'sort_order' => 1
            ]
        )->id;

        $best_seller_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_BEST_SELLER', 
                'label' => "Best Seller Report",
                'route' => "best_seller_report",
                'parent' => $reports_mm_data->id,
                'sort_order' => 2
            ]
        )->id;

        $day_wise_sale_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_DAY_WISE_SALE', 
                'label' => "Day Wise Sale Report",
                'route' => "day_wise_sale_report",
                'parent' => $reports_mm_data->id,
                'sort_order' => 3
            ]
        )->id;

        $category_report_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_CATEGORY_REPORT', 
                'label' => "Category Report",
                'route' => "catgeory_report",
                'parent' => $reports_mm_data->id,
                'sort_order' => 4
            ]
        )->id;

        $product_quantity_alert_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_PRODUCT_QUANTITY_ALERT', 
                'label' => "Stock Quantity Alert",
                'route' => "product_quantity_alert",
                'parent' => $reports_mm_data->id,
                'sort_order' => 5
            ]
        )->id;

        $store_stock_chart_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_STORE_STOCK_CHART', 
                'label' => "Store Stock Chart",
                'route' => "store_stock_chart",
                'parent' => $reports_mm_data->id,
                'sort_order' => 6
            ]
        )->id;
        
        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SETTINGS'],
        ])
        ->update(['sort_order' => 12]);

        $import_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_IMPORT', 
                'label' => "Import Data",
                'route' => "",
                'parent' => 0,
                'sort_order' => 11,
                'icon' => 'fas fa-cloud-download-alt'
            ]
        )->id;

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_IMPORT_DATA'],
        ])
        ->update(['parent' => $import_mm, 'sort_order' => 1]);

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_UPDATE_DATA'],
        ])
        ->update(['parent' => $import_mm, 'sort_order' => 2]);

    }
}
