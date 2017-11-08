@extends('layouts.layout')

@section("content")
<addprintercomponent :statuses="{{ json_encode($statuses) }}" :printer_data="{{ json_encode($printer_data) }}"></addprintercomponent>
@endsection