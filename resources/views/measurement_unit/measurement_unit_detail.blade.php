@extends('layouts.layout')

@section("content")
<measurementunitdetailcomponent :measurement_unit_data="{{ json_encode($measurement_unit_data) }}"></measurementunitdetailcomponent>
@endsection