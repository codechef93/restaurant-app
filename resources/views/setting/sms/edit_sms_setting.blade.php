@extends('layouts.layout')

@section("content")
<editsmssettingcomponent :statuses="{{ json_encode($statuses) }}" :setting_data="{{ json_encode($setting_data) }}"></editsmssettingcomponent>
@endsection