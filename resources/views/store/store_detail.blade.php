@extends('layouts.layout')

@section("content")
<storedetailcomponent :store_data="{{ json_encode($store_data) }}"></storedetailcomponent>
@endsection