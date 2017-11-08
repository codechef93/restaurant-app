@extends('layouts.layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/digital_menu_orders.css') }}">
@endpush

@section("content")
<digitalmenuorderscomponent :edit_order_link="{{ json_encode($edit_order_link) }}"  :edit_order_access="{{ json_encode($edit_order_access) }}"></digitalmenuorderscomponent>
@endsection