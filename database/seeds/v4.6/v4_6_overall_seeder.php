<?php

use Illuminate\Database\Seeder;
use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;
use App\Models\MasterInvoicePrintType;

class v4_6_overall_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $po_sm_data = MenuModel::select('id')->where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_PURCHASE_ORDERS'],
        ])
        ->active()
        ->first();

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_CREATE_INVOICE_FROM_PO', 
                'label' => "Create Invoice from Purchase Order",
                'route' => "",
                'parent' => $po_sm_data->id,
                'sort_order' => 7
            ]
        )->id;

        $stock_mm_data = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_STOCK'],
        ])
        ->active()
        ->first();
        
        MenuModel::where([
            ['type', '=', 'ACTIONS'],
            ['menu_key', '=', 'A_GENERATE_BARCODE_PRODUCT'],
        ])
        ->update(['type' => 'SUB_MENU', 'menu_key' => 'SM_PRODUCT_LABEL', 'label' => 'Product Label', 'route' => 'generate_barcode', 'parent' => $stock_mm_data->id, 'sort_order' => 5]);

        MenuModel::where([
            ['type', '=', 'ACTIONS'],
            ['menu_key', '=', 'A_VIEW_PRODUCT_LISTING'],
        ])
        ->update(['sort_order' => 4]);

        MasterInvoicePrintType::firstOrCreate(
            [
                'print_type_value' => 'SMALL_LITE',
                'print_type_label' => 'Small Sheet - Lite',
                'status' => 1,
                'created_at' => now()
            ]
        )->save();
    }
}
