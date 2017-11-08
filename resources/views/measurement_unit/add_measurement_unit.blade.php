@extends('layouts.layout')

@section("content")
<addmeasurementunitcomponent :statuses="{{ json_encode($statuses) }}" :measurement_unit_data="{{ json_encode($measurement_unit_data) }}"></addmeasurementunitcomponent>
@endsection