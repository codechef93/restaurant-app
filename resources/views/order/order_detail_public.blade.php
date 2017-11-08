@extends('layouts.public_layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/public_order.css') }}">
@endpush

@section("content")
<orderdetailpubliccomponent :order_data="{{ json_encode($order_data) }}" :company_logo="{{ json_encode($company_logo) }}"></orderdetailpubliccomponent>
@endsection