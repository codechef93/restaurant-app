@extends('layouts.layout')

@section("content")
<transactiondetailcomponent :transaction_data="{{ json_encode($transaction_data) }}" :delete_transaction_access="{{ json_encode($delete_transaction_access) }}"></transactiondetailcomponent>
@endsection