@extends('layouts.layout')

@section("content")
<updatedatacomponent :upload_options = "{{ json_encode($upload_options) }}" :templates="{{ json_encode($templates) }}"></updatedatacomponent>
@endsection