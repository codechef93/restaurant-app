<?php

use Illuminate\Database\Seeder;
use App\Models\MasterStatus;
use App\Models\Menu as MenuModel;

class invoice_requirement_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //update Order submenu as POS Orders

        MenuModel::where([
            ['type', '=', 'SUB_MENU'],
            ['menu_key', '=', 'SM_ORDERS'],
        ])
        ->update(['sort_order' => 4]);

        //add menu for invoice
        
        $order_mm_data = MenuModel::select('id')->where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_ORDERS'],
        ])
        ->active()
        ->first();

        $invoice_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_INVOICES', 
                'label' => "Invoices",
                'route' => "invoices",
                'parent' => $order_mm_data->id,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_INVOICE', 
                'label' => "Add Invoice",
                'route' => "",
                'parent' => $invoice_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_INVOICE', 
                'label' => "Edit Invoice",
                'route' => "",
                'parent' => $invoice_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_INVOICE', 
                'label' => "View Invoice Details",
                'route' => "",
                'parent' => $invoice_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DELETE_INVOICE', 
                'label' => "Delete Invoice",
                'route' => "",
                'parent' => $invoice_sm,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_STATUS_INVOICE', 
                'label' => "Change Invoice Status",
                'route' => "",
                'parent' => $invoice_sm,
                'sort_order' => 5
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_MAKE_PAYMENT_INVOICE', 
                'label' => "Add Invoice Payment",
                'route' => "",
                'parent' => $invoice_sm,
                'sort_order' => 6
            ]
        )->id;

        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'INVOICE_STATUS',
                'value' => '0',
                'value_constant' => 'CANCELLED',
                'label' => 'Cancelled',
                'color' => 'label red-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'INVOICE_STATUS',
                'value' => '1',
                'value_constant' => 'NEW',
                'label' => 'New',
                'color' => 'label blue-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'INVOICE_STATUS',
                'value' => '2',
                'value_constant' => 'SENT',
                'label' => 'Sent',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'INVOICE_STATUS',
                'value' => '3',
                'value_constant' => 'PAID',
                'label' => 'Paid',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'INVOICE_STATUS',
                'value' => '4',
                'value_constant' => 'OVERDUE',
                'label' => 'Overdue',
                'color' => 'label orange-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'INVOICE_STATUS',
                'value' => '5',
                'value_constant' => 'VOID',
                'label' => 'Void',
                'color' => 'label grey-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'INVOICE_STATUS',
                'value' => '6',
                'value_constant' => 'WRITEOFF',
                'label' => 'Write Off',
                'color' => 'label grey-label'
            ]
        )->save(); 

        MasterStatus::firstOrCreate(
            [
                'key' => 'INVOICE_PRODUCT_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'INVOICE_PRODUCT_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();

    }
}