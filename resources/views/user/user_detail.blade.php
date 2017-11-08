@extends('layouts.layout')

@section("content")
<userdetailcomponent :user_data="{{ json_encode($user_data) }}" :show_init_password="{{ json_encode($show_init_password) }}"></userdetailcomponent>
@endsection