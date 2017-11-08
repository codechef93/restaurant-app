@extends('layouts.layout')

@section("content")
<emailsettingcomponent :email_setting="{{ json_encode($email_setting) }}"></emailsettingcomponent>
@endsection