@extends('layouts.empty_layout')

@section("content")
    <forgotpasswordcomponent :company_logo = {{ json_encode($company_logo)}}></forgotpasswordcomponent>
@endsection