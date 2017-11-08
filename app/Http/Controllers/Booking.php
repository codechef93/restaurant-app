<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Booking as BookingModel;

use App\Http\Resources\BookingResource;

class Booking extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_BOOKINGS';
        $data['sub_menu_key'] = 'SM_BOOKINGS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('booking.bookings', $data);
    }

    //This is the function that loads the add/edit page
    public function add_booking($slack = null){
        //check access
        $data['menu_key'] = 'MM_BOOKINGS';
        $data['sub_menu_key'] = 'SM_BOOKINGS';
        $data['action_key'] = ($slack == null)?'A_ADD_BOOKING':'A_EDIT_BOOKING';
        check_access(array($data['action_key']));

        $data['booking_data'] = null;
        if(isset($slack)){
            $booking = BookingModel::where('slack', '=', $slack)->first();
            if (empty($booking)) {
                abort(404);
            }
            
            $booking_data = new BookingResource($booking);
            $data['booking_data'] = $booking_data;
        }

        return view('booking.add_booking', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_BOOKINGS';
        $data['sub_menu_key'] = 'SM_BOOKINGS';
        $data['action_key'] = 'A_DETAIL_BOOKING';
        check_access([$data['action_key']]);

        $booking = BookingModel::where('slack', '=', $slack)->first();
        
        if (empty($booking)) {
            abort(404);
        }

        $booking_data = new BookingResource($booking);
        
        $data['booking_data'] = $booking_data;

        $data['delete_booking_access'] = check_access(['A_DELETE_BOOKING'] ,true);

        return view('booking.booking_detail', $data);
    }

    public function calendar(){
        $data['menu_key'] = 'MM_BOOKINGS';
        $data['sub_menu_key'] = 'SM_CALENDAR';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $data['timezone'] = config('app.timezone');
        
        return view('calendar.calendar', $data);
    }
}
