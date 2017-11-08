@extends('layouts.empty_layout')

@section("content")
    <signincomponent :is_demo="{{ json_encode($is_demo) }}" :preview_mode = {{ json_encode($preview_mode) }} :company_logo = {{ json_encode($company_logo)}}></signincomponent>
@endsection