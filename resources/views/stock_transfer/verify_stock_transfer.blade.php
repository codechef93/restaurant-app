@extends('layouts.layout')

@section("content")
<verifystocktransfercomponent :stock_transfer_data="{{ json_encode($stock_transfer_data) }}"></verifystocktransfercomponent>
@endsection