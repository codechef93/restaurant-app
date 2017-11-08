@extends('layouts.layout')

@section("content")
<addnotificationcomponent :roles="{{ json_encode($roles) }}" :statuses="{{ json_encode($statuses) }}" :notification_data="{{ json_encode($notification_data) }}"></addnotificationcomponent>
@endsection