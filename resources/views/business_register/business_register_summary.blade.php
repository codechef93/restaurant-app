@extends('layouts.empty_layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/billing_summary.css') }}">
@endpush

@section("content")
<businessregistersummarycomponent :register_data="{{ json_encode($register_data) }}" :pdf_print="{{ json_encode($pdf_print) }}" :new_order_link="{{ json_encode($new_order_link) }}" :new_order_access="{{ json_encode($new_order_access) }}" :printnode_enabled="{{ json_encode($printnode_enabled) }}"></businessregistersummarycomponent>
@endsection