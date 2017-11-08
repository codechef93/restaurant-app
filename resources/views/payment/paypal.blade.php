@extends('layouts.empty_layout')
@push('styles')
@endpush
@section("content")
    
    <div class="d-flex justify-content-center mt-4">
        <label class="text-headline mb-5">
            <a href="{{ $order_detail_link }}" class="text-primary"><i class="fas fa-arrow-left"></i></a>
            &nbsp; Paypal Payment Page</label>
    </div>
    <div id="paypal-button-container" class="text-center"></div>
    
@endsection

@push('scripts')
    <script src="https://www.paypal.com/sdk/js?client-id={{ $client_id }}&currency={{ $order_currency }}"></script>
    <script src="{{ asset('plugins/payments/paypal/paypal.js') }}"></script>
    <script>
        'use strict';

        var order_data = {
            order_slack : {!! json_encode($order_slack) !!},
            new_order_link : {!! json_encode($new_order_link) !!},
            order_print_link : {!! json_encode($order_print_link) !!},
            order_detail_link : {!! json_encode($order_detail_link) !!},
            order_amount : {!! json_encode($order_amount) !!},
            public_order : {!! json_encode($public_order) !!}
        };

        var paypal_module = new Paypal_module();
        paypal_module.init(paypal, order_data);
    </script>
@endpush