@extends('layouts.layout')

@section("content")
<variantoptiondetailcomponent :variant_option_data="{{ json_encode($variant_option_data) }}"></variantoptiondetailcomponent>
@endsection