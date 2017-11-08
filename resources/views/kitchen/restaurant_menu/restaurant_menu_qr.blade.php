@extends('layouts.layout')

@section("content")
<restaurantmenuqrcomponent :menu_link="{{ json_encode($menu_link) }}"></restaurantmenuqrcomponent>
@endsection