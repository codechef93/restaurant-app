@extends('layouts.empty_layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/billing_summary.css') }}">
@endpush

@section("content")
<ordersummarycomponent :order_data="{{ json_encode($order_data) }}" :pdf_print="{{ json_encode($pdf_print) }}" :new_order_link="{{ json_encode($new_order_link) }}" :order_detail_link="{{ json_encode($order_detail_link) }}" :order_detail_access="{{ json_encode($order_detail_access) }}" :new_order_access="{{ json_encode($new_order_access) }}" :print_order_link="{{ json_encode($print_order_link) }}" :print_kot_link="{{ json_encode($print_kot_link) }}" :edit_order_link="{{ json_encode($edit_order_link) }}" :edit_order_access="{{ json_encode($edit_order_access) }}" :printnode_enabled="{{ json_encode($printnode_enabled) }}"></ordersummarycomponent>
@endsection