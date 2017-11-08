@extends('layouts.layout')

@section("content")
<addbusinessregistercomponent :billing_counters="{{ json_encode($billing_counters) }}"></addbusinessregistercomponent>
@endsection