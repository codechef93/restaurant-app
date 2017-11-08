@extends('layouts.layout')

@section("content")
<categorydetailcomponent :category_data="{{ json_encode($category_data) }}"></categorydetailcomponent>
@endsection