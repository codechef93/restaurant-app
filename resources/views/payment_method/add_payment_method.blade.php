@extends('layouts.layout')

@section("content")
<addpaymentmethodcomponent :statuses="{{ json_encode($statuses) }}" :payment_method_data="{{ json_encode($payment_method_data) }}"></addpaymentmethodcomponent>
@endsection