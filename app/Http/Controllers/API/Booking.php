<?php

namespace App\Http\Controllers\API;

use Exception;
use Validator;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

use App\Http\Resources\BookingResource;

use App\Models\Booking as BookingModel;

use App\Http\Resources\Collections\BookingCollection;

class Booking extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            $data['action_key'] = 'A_VIEW_BOOKING_LISTING';
            if(check_access(array($data['action_key']), true) == false){
                $response = $this->no_access_response_for_listing_table();
                return $response;
            }

            $item_array = array();

            $draw = $request->draw;
            $limit = $request->length;
            $offset = $request->start;
            
            $order_by = $request->order[0]["column"];
            $order_direction = $request->order[0]["dir"];
            $order_by_column =  $request->columns[$order_by]['name'];

            $filter_string = $request->search['value'];
            $filter_columns = array_filter(data_get($request->columns, '*.name'));
            
            $query = BookingModel::select('bookings.*', 'user_created.fullname')
            ->take($limit)
            ->skip($offset)
            ->createdUser()

            ->when($order_by_column, function ($query, $order_by_column) use ($order_direction) {
                $query->orderBy($order_by_column, $order_direction);
            }, function ($query) {
                $query->orderBy('created_at', 'desc');
            })

            ->when($filter_string, function ($query, $filter_string) use ($filter_columns) {
                $query->where(function ($query) use ($filter_string, $filter_columns){
                    foreach($filter_columns as $filter_column){
                        $query->orWhere($filter_column, 'like', '%'.$filter_string.'%');
                    }
                });
            })

            ->get();

            $bookings = BookingResource::collection($query);
           
            $total_count = BookingModel::select("id")->get()->count();

            $item_array = [];
            foreach($bookings as $key => $booking){
                
                $booking = $booking->toArray($request);

                $item_array[$key][] = Str::title($booking['event_type']);
                $item_array[$key][] = $booking['event_code'];
                $item_array[$key][] = $booking['start_date_raw'];
                $item_array[$key][] = $booking['end_date_raw'];
                $item_array[$key][] = (isset($booking['name']))?$booking['name']:'-';
                $item_array[$key][] = (isset($booking['email']))?$booking['email']:'-';
                $item_array[$key][] = (isset($booking['phone']))?$booking['phone']:'-';
                $item_array[$key][] = $booking['created_at_label'];
                $item_array[$key][] = $booking['updated_at_label'];
                $item_array[$key][] = (isset($booking['created_by']['fullname']))?$booking['created_by']['fullname']:'-';
                $item_array[$key][] = view('booking.layouts.booking_actions', ['booking' => $booking])->render();
            }

            $response = [
                'draw' => $draw,
                'recordsTotal' => $total_count,
                'recordsFiltered' => $total_count,
                'data' => $item_array
            ];
            
            return response()->json($response);
        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {

            if(!check_access(['A_ADD_BOOKING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);
            
            DB::beginTransaction();

            $start_date = $request->start_date.(($request->start_time !='')?' '.$request->start_time:' 12:00 AM');
            $end_date = $request->end_date.(($request->end_time !='')?' '.$request->end_time:' 11:59 PM');
            
            $start_date = date(config('app.sql_date_time_format'), strtotime($start_date));
            $end_date = date(config('app.sql_date_time_format'), strtotime($end_date));
            if(strtotime($start_date) > strtotime($end_date)){
                throw new Exception("End date should be greater or equal to start date", 400);
            }

            $booking = [
                "slack" => $this->generate_slack("bookings"),
                "event_type" => $request->event_type,
                "store_id" => $request->logged_user_store_id,
                "start_date" => $start_date,
                "end_date" => $end_date,
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
                "no_of_persons" => $request->no_of_persons,
                "description" => $request->event_description,
                "created_by" => $request->logged_user_id
            ];
            
            $booking_id = BookingModel::create($booking)->id;

            $code_start_config = Config::get('constants.unique_code_start.booking');
            $code_start = (isset($code_start_config))?$code_start_config:100;
            
            $booking_code = [
                "event_code" => ($code_start+$booking_id)
            ];
            BookingModel::where('id', $booking_id)
            ->update($booking_code);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Booking or event added successfully", 
                    "data" => $booking['slack']
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function show($slack)
    { 
        //
    }

    /**
     * list all the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list(Request $request)
    {
        //
    }

    public function running_list(Request $request)
    {
        $now = Carbon::now();
        $booking_list = BookingModel::where('store_id', $request->logged_user_store_id)
        ->where('end_date', '>=', Carbon::now()->subDays(1))
        ->get();
        $res = array(
            "booking_list" => $booking_list
        );
        return response()->json($res);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $slack
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slack)
    {
        try {

            if(!check_access(['A_EDIT_BOOKING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $this->validate_request($request);

            $booking_exists = BookingModel::select('id')
            ->where([
                ['slack', '=', $slack],
            ])
            ->first();
            if (empty($booking_exists)) {
                throw new Exception("Invalid booking", 400);
            }

            DB::beginTransaction();

            $start_date = $request->start_date.(($request->start_time !='')?' '.$request->start_time:' 12:00 AM');
            $end_date = $request->end_date.(($request->end_time !='')?' '.$request->end_time:' 11:59 PM');

            $start_date = date(config('app.sql_date_time_format'), strtotime($start_date));
            $end_date = date(config('app.sql_date_time_format'), strtotime($end_date));
            if(strtotime($start_date) > strtotime($end_date)){
                throw new Exception("End date should be greater or equal to start date", 400);
            }
            
            $booking = [
                "event_type" => $request->event_type,
                "start_date" => $start_date,
                "end_date" => $end_date,
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
                "no_of_persons" => $request->no_of_persons,
                "description" => $request->description,
                "updated_by" => $request->logged_user_id
            ];
            $action_response = BookingModel::where('slack', $slack)
            ->update($booking);

            DB::commit();

            return response()->json($this->generate_response(
                array(
                    "message" => "Booking or event updated successfully", 
                    "data" => $slack
                ), 'SUCCESS'
            ));
        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $slack)
    {
        try{

            if(!check_access(['A_DELETE_BOOKING'], true)){
                throw new Exception("Invalid request", 400);
            }

            $booking_detail = BookingModel::select('id')->where('slack', $slack)->first();
            if (empty($booking_detail)) {
                throw new Exception("Invalid booking or event provided", 400);
            }
            $booking_id = $booking_detail->id;

            DB::beginTransaction();
            BookingModel::where('id', $booking_id)->delete();
            DB::commit();

            $forward_link = route('bookings');

            return response()->json($this->generate_response(
                array(
                    "message" => "Booking or event deleted successfully", 
                    "data" => $slack,
                    "link" => $forward_link
                ), 'SUCCESS'
            ));

        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function load_events(Request $request)
    {
        try {

            if(!check_access(['SM_CALENDAR'], true)){
                throw new Exception("Invalid request", 400);
            }

            $start_date = date(config('app.sql_date_time_format'), strtotime($request->start_date));
            $end_date = date(config('app.sql_date_time_format'), strtotime($request->end_date));

            $query = BookingModel::query()
            ->select('bookings.*');

            if($start_date != ''){
                $query = $query->where('bookings.start_date', '>=', $start_date);
            }
            if($end_date != ''){
                $query = $query->where('bookings.end_date', '<=', $end_date);
            }
            $bookings = $query->get();

            $list = $bookings->map(function ($item, $key) {
                return [
                    "id" => $item['slack'],
                    "title" => Str::title($item['event_type']).' : '.$item['event_code'],
                    "start" => $item['start_date'],
                    "end" => $item['end_date'],
                    "extendedProps" => [
                        'name' => $item['name'],
                        'email' => $item['email'],
                        'phone' => $item['phone'],
                        'description' => $item['description'],
                        'no_of_persons' => $item['no_of_persons'],
                    ],
                    "url" => (check_access(['A_DETAIL_BOOKING'], true))?route('booking', ['slack' => $item['slack']]):'',
                    "color" => ($item['event_type'] == 'BOOKING')?'#62C163':'#2489F3',
                ];
            });

            return response()->json($this->generate_response(
                array(
                    "message" => "Bookings list loaded successfully", 
                    "data" => $list
                ), 'SUCCESS'
            ));
        }catch(Exception $e){
            return response()->json($this->generate_response(
                array(
                    "message" => $e->getMessage(),
                    "status_code" => $e->getCode()
                )
            ));
        }
    }

    public function validate_request($request)
    {
        $validator = Validator::make($request->all(), [
            'event_type' => 'required',
            'start_date' => 'date|required',
            'end_date' => 'date|required|after_or_equal:start_date',
            'email'  => $this->get_validation_rules("email", false),
            'phone'  => $this->get_validation_rules("phone", false),
            'name'   => $this->get_validation_rules("fullname", false),
            'description' => $this->get_validation_rules("text", false),
            'no_of_persons' => $this->get_validation_rules("numeric", false),
        ]);
        $validation_status = $validator->fails();
        if($validation_status){
            throw new Exception($validator->errors());
        }
    }
}
