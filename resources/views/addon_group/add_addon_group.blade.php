@extends('layouts.layout')

@section("content")
<addaddongroupcomponent :statuses="{{ json_encode($statuses) }}" :addon_group_data="{{ json_encode($addon_group_data) }}"></addaddongroupcomponent>
@endsection