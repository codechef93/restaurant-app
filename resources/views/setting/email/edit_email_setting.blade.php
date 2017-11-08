@extends('layouts.layout')

@section("content")
<editemailsettingcomponent :statuses="{{ json_encode($statuses) }}" :setting_data="{{ json_encode($setting_data) }}"></editemailsettingcomponent>
@endsection