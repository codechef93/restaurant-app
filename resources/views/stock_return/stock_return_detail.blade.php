@extends('layouts.layout')

@section("content")
<stockreturndetailcomponent :stock_return_data="{{ json_encode($stock_return_data) }}" :delete_stock_return_access="{{ json_encode($delete_stock_return_access) }}"></stockreturndetailcomponent>
@endsection