<?php

use Illuminate\Database\Seeder;

use App\Http\Controllers\Controller;
use App\Models\Menu as MenuModel;
use App\Models\MasterStatus;

class notification_table_seeder extends Seeder
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
            ['menu_key', '=', 'MM_SETTINGS'],
        ])
        ->update(['sort_order' => 11]);

        $notification_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_NOTIFICATION', 
                'label' => "Notification",
                'route' => "",
                'parent' => 0,
                'sort_order' => 10,
                'icon' => 'fas fa-bell',
            ]
        )->id;

        $notification_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_NOTIFICATIONS', 
                'label' => "Notifications",
                'route' => "notifications",
                'parent' => $notification_mm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_NOTIFICATION', 
                'label' => "Add New Notification",
                'route' => "",
                'parent' => $notification_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_NOTIFICATION', 
                'label' => "View Notification",
                'route' => "",
                'parent' => $notification_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DELETE_NOTIFICATION', 
                'label' => "Delete Notification",
                'route' => "",
                'parent' => $notification_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_NOTIFICATION_LISTING', 
                'label' => "View Notification Listing",
                'route' => "",
                'parent' => $notification_sm,
                'sort_order' => 4
            ]
        )->id;
                
        //Status seeder
        MasterStatus::firstOrCreate(
            [
                'key' => 'NOTIFICATION_STATUS',
                'value' => '1',
                'value_constant' => 'ACTIVE',
                'label' => 'Active',
                'color' => 'label green-label'
            ]
        )->save();

        MasterStatus::firstOrCreate(
            [
                'key' => 'NOTIFICATION_STATUS',
                'value' => '0',
                'value_constant' => 'INACTIVE',
                'label' => 'Inactive',
                'color' => 'label red-label'
            ]
        )->save();
    }
}
