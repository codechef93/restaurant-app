@extends('layouts.layout')

@section("content")
<addstockreturncomponent :currency_list="{{ json_encode($currency_list) }}" :stock_return_data="{{ json_encode($stock_return_data) }}" :tax_options="{{ json_encode($tax_options) }}"></addstockreturncomponent>
@endsection