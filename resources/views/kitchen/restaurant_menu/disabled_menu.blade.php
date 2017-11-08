@extends('layouts.empty_layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/restaurant_menu.css') }}">
@endpush

@section("content")
<disabledmenucomponent :menu_enabled="{{ json_encode($menu_enabled) }}" :inside_menu_schedule="{{ json_encode($inside_menu_schedule) }}" :menu_schedule="{{ json_encode($menu_schedule) }}"></disabledmenucomponent>
@endsection

