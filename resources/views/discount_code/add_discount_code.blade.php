@extends('layouts.layout')

@section("content")
<adddiscountcodecomponent :statuses="{{ json_encode($statuses) }}" :discount_code_data="{{ json_encode($discount_code_data) }}"></adddiscountcodecomponent>
@endsection