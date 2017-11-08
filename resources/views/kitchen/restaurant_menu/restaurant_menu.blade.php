@extends('layouts.empty_layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/restaurant_menu.css') }}">
@endpush

@section("content")
<restaurantmenucomponent :navbar_logo="{{ json_encode($navbar_logo) }}" :category_products="{{ json_encode($category_products) }}" :company_logo="{{ json_encode($company_logo) }}" :store="{{ json_encode($store) }}" :category_array="{{ json_encode($category_array) }}" :store_tax_percentage="{{ json_encode($store_tax_percentage) }}" :store_discount_percentage="{{ json_encode($store_discount_percentage) }}" :store_restaurant_mode="{{ json_encode($store_restaurant_mode) }}" :restaurant_order_types="{{ json_encode($restaurant_order_types) }}" :billing_types="{{ json_encode($billing_types) }}" :store_billing_type="{{ json_encode($store_billing_type) }}" :table="{{ json_encode($table) }}" :enable_digital_menu_otp_verification="{{ json_encode($enable_digital_menu_otp_verification) }}" :payment_methods="{{ json_encode($payment_methods) }}" :base_url="{{ json_encode($base_url)}}"></restaurantmenucomponent>
@endsection