@extends('layouts.layout')

@section("content")
<addtablecomponent :statuses="{{ json_encode($statuses) }}" :table_data="{{ json_encode($table_data) }}" :menu_link="{{ json_encode($menu_link) }}" :waiter_list="{{ json_encode($waiter_list) }}" :restoareas="{{ json_encode($restoareas) }}"></addtablecomponent>
@endsection