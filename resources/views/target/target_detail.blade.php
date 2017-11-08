@extends('layouts.layout')

@section("content")
<targetdetailcomponent :target_data="{{ json_encode($target_data) }}" :store="{{ json_encode($store)}}" :delete_target_access="{{ json_encode($delete_target_access)}}"></targetdetailcomponent>
@endsection