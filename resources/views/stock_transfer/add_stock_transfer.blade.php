@extends('layouts.layout')

@section("content")
<addstocktransfercomponent :stock_transfer_data="{{ json_encode($stock_transfer_data) }}" :current_store="{{ json_encode($current_store) }}" :to_stores="{{ json_encode($to_stores) }}" :edit_stock_transfer_access="{{ json_encode($edit_stock_transfer_access) }}"></addstocktransfercomponent>
@endsection