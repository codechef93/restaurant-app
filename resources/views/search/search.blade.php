@extends('layouts.layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endpush

@section("content")
    <searchcomponent :search_items= "{{ json_encode($search_items) }}"></searchcomponent>
@endsection