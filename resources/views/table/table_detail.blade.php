@extends('layouts.layout')

@section("content")
<tabledetailcomponent :table_data="{{ json_encode($table_data) }}"></tabledetailcomponent>
@endsection