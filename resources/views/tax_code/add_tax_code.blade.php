@extends('layouts.layout')

@section("content")
<addtaxcodecomponent :statuses="{{ json_encode($statuses) }}" :tax_code_data="{{ json_encode($tax_code_data) }}"></addtaxcodecomponent>
@endsection