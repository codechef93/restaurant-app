@extends('layouts.layout')

@section("content")
<notificationdetailcomponent :notification_data="{{ json_encode($notification_data) }}" :delete_notification_access="{{ json_encode($delete_notification_access) }}"></notificationdetailcomponent>
@endsection