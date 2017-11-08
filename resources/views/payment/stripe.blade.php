@extends('layouts.empty_layout')
@push('styles')
<link rel="stylesheet" href="{{ asset('plugins/payments/stripe/css/global.css') }}">
<link rel="stylesheet" href="{{ asset('plugins/payments/stripe/css/normalize.css') }}">
@endpush
@section("content")
    <div class="sr-root">

        <div class="d-flex justify-content-center">
            <label class="text-headline mb-5">
                <a href="{{ $order_detail_link }}" class="text-primary"><i class="fas fa-arrow-left"></i></a>
                &nbsp; Stripe Payment Page</label>
        </div>

        <div class="sr-main">
            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto mb-2">
                    <span class="text-subtitle">Order Number: {{ $order_number }}</span>
                </div>
                <div class="">
                    <span class="text-subtitle">Amount: {{ $order_amount }} {{ $order_currency }} + Stripe service charge may apply</span>
                </div>
                <div class="">
                    <small>{{ $order_currency_round_note }}</small>
                </div>
            </div>
            <form id="payment-form" class="sr-payment-form" action="/process_stripe_payment" method="POST">
                
                <div class="sr-combo-inputs-row">
                    <div class="sr-input sr-card-element" id="card-element"></div>
                </div>
                <div class="sr-field-error mb-3" id="card-errors" role="alert"></div>
                <button id="submit">
                    <div class="spinner hidden" id="spinner"></div>
                    <span id="button-text">Pay</span><span id="order-amount"></span>
                </button>
            </form>
            <div class="sr-result hidden">
                <p>Payment completed<br /></p>
                <pre>
                    <code></code>
                </pre>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script src="{{ asset('plugins/payments/stripe/js/stripe.js') }}"></script>
    <script>
        'use strict';

        var order_data = {
            order_slack : {!! json_encode($order_slack) !!},
            new_order_link : {!! json_encode($new_order_link) !!},
            order_print_link : {!! json_encode($order_print_link) !!},
            order_detail_link : {!! json_encode($order_detail_link) !!},
            public_order : {!! json_encode($public_order) !!}
        };

        var stripe = new Stripe_module();
        stripe.init(order_data);
    </script>
@endpush