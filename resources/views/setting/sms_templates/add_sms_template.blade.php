@extends('layouts.layout')

@section("content")
<addsmstemplatecomponent :statuses="{{ json_encode($statuses) }}" :sms_template_data="{{ json_encode($sms_template_data) }}"></addsmstemplatecomponent>
@endsection