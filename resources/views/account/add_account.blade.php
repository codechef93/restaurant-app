@extends('layouts.layout')

@section("content")
<addaccountcomponent :account_types="{{ json_encode($account_types) }}" :statuses="{{ json_encode($statuses) }}" :account_data="{{ json_encode($account_data) }}"></addaccountcomponent>
@endsection