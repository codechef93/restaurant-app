@extends('layouts.layout')

@section("content")
<smssettingcomponent :sms_setting_data="{{ json_encode($sms_setting_data) }}"></smssettingcomponent>
@endsection