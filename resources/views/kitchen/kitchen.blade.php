@extends('layouts.layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kitchen_view.css') }}">
    <link rel="stylesheet" href="{{ asset('css/labels.css') }}">
@endpush

@section("content")
<kitchenviewcomponent :kitchen_statuses="{{ json_encode($kitchen_statuses) }}" :change_kitchen_order_status="{{ json_encode($change_kitchen_order_status) }}" :pos_order_edit="{{ json_encode($pos_order_edit) }}" :store_slack="{{ json_encode($store_slack) }}" :category="{{ json_encode($category) }}"></kitchenviewcomponent>
@endsection