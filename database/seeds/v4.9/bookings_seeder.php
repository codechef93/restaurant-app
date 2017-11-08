<?php

use Illuminate\Database\Seeder;
use App\Models\Menu as MenuModel;

class bookings_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_REPORT'],
        ])
        ->update(['sort_order' => 9]);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_RESTAURANT'],
        ])
        ->update(['sort_order' => 10]);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_NOTIFICATION'],
        ])
        ->update(['sort_order' => 11]);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_IMPORT'],
        ])
        ->update(['sort_order' => 12]);

        MenuModel::where([
            ['type', '=', 'MAIN_MENU'],
            ['menu_key', '=', 'MM_SETTINGS'],
        ])
        ->update(['sort_order' =>13]);

        $booking_calendar_mm = MenuModel::create(
            [
                'type' => 'MAIN_MENU',
                'menu_key' => 'MM_BOOKINGS', 
                'label' => "Bookings & Calendar",
                'route' => "",
                'parent' => 0,
                'sort_order' => 8,
                'icon' => 'fas fa-calendar-alt'
            ]
        )->id;

        $booking_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_BOOKINGS', 
                'label' => "Bookings & Events",
                'route' => "bookings",
                'parent' => $booking_calendar_mm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_ADD_BOOKING', 
                'label' => "Add Booking",
                'route' => "",
                'parent' => $booking_sm,
                'sort_order' => 1
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_EDIT_BOOKING', 
                'label' => "Edit Booking",
                'route' => "",
                'parent' => $booking_sm,
                'sort_order' => 2
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DETAIL_BOOKING', 
                'label' => "View Booking & Event Detail",
                'route' => "",
                'parent' => $booking_sm,
                'sort_order' => 3
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_DELETE_BOOKING', 
                'label' => "Delete Booking",
                'route' => "",
                'parent' => $booking_sm,
                'sort_order' => 4
            ]
        )->id;

        MenuModel::create(
            [
                'type' => 'ACTIONS',
                'menu_key' => 'A_VIEW_BOOKING_LISTING', 
                'label' => "View Booking & Event Listing",
                'route' => "",
                'parent' => $booking_sm,
                'sort_order' => 5
            ]
        )->id;
        
        $calendar_sm = MenuModel::create(
            [
                'type' => 'SUB_MENU',
                'menu_key' => 'SM_CALENDAR', 
                'label' => "Calendar",
                'route' => "calendar",
                'parent' => $booking_calendar_mm,
                'sort_order' => 1
            ]
        )->id;
    }
}
