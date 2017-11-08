@extends('layouts.layout')

@section("content")
<accountdetailcomponent :account_data="{{ json_encode($account_data) }}"></accountdetailcomponent>
@endsection