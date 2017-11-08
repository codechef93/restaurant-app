@extends('layouts.layout')

@section("content")
<bookingdetailcomponent :booking_data="{{ json_encode($booking_data) }}" :delete_booking_access="{{ json_encode($delete_booking_access) }}"></bookingdetailcomponent>
@endsection