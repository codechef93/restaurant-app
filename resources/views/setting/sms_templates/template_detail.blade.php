@extends('layouts.layout')

@section("content")
<smstemplatedetailcomponent :sms_template_data="{{ json_encode($sms_template_data) }}"></smstemplatedetailcomponent>
@endsection