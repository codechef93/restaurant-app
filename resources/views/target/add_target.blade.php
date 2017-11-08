@extends('layouts.layout')

@section("content")
<addtargetcomponent :target_data="{{ json_encode($target_data) }}" :store="{{ json_encode($store)}}"></addtargetcomponent>
@endsection