@extends('layouts.layout')

@section("content")
<addinvoicecomponent :currency_list="{{ json_encode($currency_list) }}" :invoice_data="{{ json_encode($invoice_data) }}" :tax_options="{{ json_encode($tax_options) }}"></addinvoicecomponent>
@endsection