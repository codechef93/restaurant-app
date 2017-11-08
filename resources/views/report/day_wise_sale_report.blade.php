@extends('layouts.layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/report.css') }}">
@endpush

@section("content")
<daywisesalereportcomponent></daywisesalereportcomponent>
@endsection