@extends('layouts.layout')

@section("content")
<addbookingcomponent :booking_data="{{ json_encode($booking_data) }}"></addbookingcomponent>
@endsection