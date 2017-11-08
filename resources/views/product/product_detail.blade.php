@extends('layouts.layout')

@section("content")
<productdetailcomponent :product_data="{{ json_encode($product_data) }}"></productdetailcomponent>
@endsection