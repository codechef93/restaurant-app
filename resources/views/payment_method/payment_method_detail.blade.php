@extends('layouts.layout')

@section("content")
<paymentmethoddetailcomponent :payment_method_data="{{ json_encode($payment_method_data) }}"></paymentmethoddetailcomponent>
@endsection