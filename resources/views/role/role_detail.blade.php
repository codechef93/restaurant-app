@extends('layouts.layout')

@section("content")
<roledetailcomponent :role_data="{{ json_encode($role_data) }}" :access_menus="{{ json_encode($access_menus) }}"></roledetailcomponent>
@endsection