@extends('layouts.layout')

@section("content")
<billingcounterdetailcomponent :billing_counter_data="{{ json_encode($billing_counter_data) }}"></billingcounterdetailcomponent>
@endsection