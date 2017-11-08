@extends('layouts.layout')

@section("content")
<appsettingcomponent :app_setting="{{ json_encode($setting_data) }}"></appsettingcomponent>
@endsection