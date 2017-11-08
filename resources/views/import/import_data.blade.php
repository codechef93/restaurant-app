@extends('layouts.layout')

@section("content")
<importcomponent :upload_options = "{{ json_encode($upload_options) }}" :templates="{{ json_encode($templates) }}"></importcomponent>
@endsection