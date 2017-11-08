@extends('layouts.layout')

@section("content")
<addvariantoptioncomponent :statuses="{{ json_encode($statuses) }}" :variant_option_data="{{ json_encode($variant_option_data) }}"></addvariantoptioncomponent>
@endsection