@extends('layouts.layout')

@section("content")
<customerdetailcomponent :customer_data="{{ json_encode($customer_data) }}"></customerdetailcomponent>
@endsection