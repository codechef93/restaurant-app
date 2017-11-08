@extends('layouts.layout')

@section("content")
<discountcodedetailcomponent :discount_code_data="{{ json_encode($discount_code_data) }}"></discountcodedetailcomponent>
@endsection