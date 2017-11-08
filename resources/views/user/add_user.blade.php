@extends('layouts.layout')

@section("content")
<addusercomponent :roles="{{ json_encode($roles) }}" :statuses="{{ json_encode($statuses) }}" :stores="{{ json_encode($stores) }}"  :is_super_admin="{{ json_encode($is_super_admin) }}" :user_data="{{ json_encode($user_data) }}"></addusercomponent>
@endsection