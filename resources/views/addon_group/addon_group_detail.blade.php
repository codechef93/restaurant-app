@extends('layouts.layout')

@section("content")
<addongroupdetailcomponent :addon_group_data="{{ json_encode($addon_group_data) }}"></addongroupdetailcomponent>
@endsection