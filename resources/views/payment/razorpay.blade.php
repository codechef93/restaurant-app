@extends('layouts.empty_layout')
@push('styles')
@endpush
@section("content")
    
    <div class="d-flex justify-content-center mt-4">
        <label class="text-headline mb-5">
            <a href="{{ $order_detail_link }}" class="text-primary"><i class="fas fa-arrow-left"></i></a>
            &nbsp; Razorpay Payment Page</label>
    </div>
    <input type="hidden" name="shopping_order_id" value="{{ $order_number }}">
    <div class="d-flex justify-content-center">
        <button id="rzp-button1" class="btn btn-lg btn-primary">Proceed to Pay</button>
    </div>
    <div class="d-flex justify-content-center mt-5">
        <div id="razorpay-response-container"></div>
    </div>
@endsection

@push('scripts')

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script src="{{ asset('plugins/payments/razorpay/razorpay.js') }}"></script>
    <script>
        'use strict';

        var order_data = {
            order_slack : {!! json_encode($order_slack) !!},
            new_order_link : {!! json_encode($new_order_link) !!},
            order_print_link : {!! json_encode($order_print_link) !!},
            razorpay_data : {!! json_encode($razorpay_array) !!},
            public_order : {!! json_encode($public_order) !!}
        };

        var razorpay_module = new Razorpay_module();
        razorpay_module.init(order_data);

    </script>
@endpush