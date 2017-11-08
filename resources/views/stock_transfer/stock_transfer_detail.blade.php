@extends('layouts.layout')

@section("content")
<stocktransferdetailcomponent :stock_transfer_data="{{ json_encode($stock_transfer_data) }}" :delete_stock_transfer_access="{{ json_encode($delete_stock_transfer_access)}}" :stock_transfer_statuses="{{ json_encode($stock_transfer_statuses) }}"></stocktransferdetailcomponent>
@endsection