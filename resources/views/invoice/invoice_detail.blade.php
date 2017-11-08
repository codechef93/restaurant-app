@extends('layouts.layout')

@section("content")
<invoicedetailcomponent :invoice_statuses="{{ json_encode($invoice_statuses) }}" :invoice_data="{{ json_encode($invoice_data) }}" :transaction_type_data="{{ json_encode($transaction_type) }}" :accounts="{{ json_encode($accounts) }}" :payment_methods="{{ json_encode($payment_methods) }}" :currency_codes="{{ json_encode($currency_codes) }}" :delete_invoice_access="{{ json_encode($delete_invoice_access) }}" :make_payment_access="{{ json_encode($make_payment_access) }}" :default_transaction_type="{{ json_encode($default_transaction_type) }}" :printnode_enabled="{{ json_encode($printnode_enabled) }}"></invoicedetailcomponent>
@endsection