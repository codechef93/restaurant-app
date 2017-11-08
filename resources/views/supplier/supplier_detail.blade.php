@extends('layouts.layout')

@section("content")
<supplierdetailcomponent :supplier_data="{{ json_encode($supplier_data) }}"></supplierdetailcomponent>
@endsection