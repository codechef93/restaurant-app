@extends('layouts.layout')

@section("content")
<addsuppliercomponent :statuses="{{ json_encode($statuses) }}" :supplier_data="{{ json_encode($supplier_data) }}"></addsuppliercomponent>
@endsection