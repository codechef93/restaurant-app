@extends('layouts.layout')

@section("content")
<addtransactioncomponent :transaction_type_data="{{ json_encode($transaction_type) }}" :accounts="{{ json_encode($accounts) }}" :transaction_data="{{ json_encode($transaction_data) }}" :payment_methods="{{ json_encode($payment_methods) }}"></addtransactioncomponent>
@endsection