@extends('layouts.layout')

@section("content")
<calendarcomponent :timezone="{{ json_encode($timezone) }}"></calendarcomponent>
@endsection