@extends('layouts.layout')

@section("content")
<addbillingcountercomponent :statuses="{{ json_encode($statuses) }}" :billing_counter_data="{{ json_encode($billing_counter_data) }}"></addbillingcountercomponent>
@endsection