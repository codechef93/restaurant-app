@extends('layouts.layout')

@section("content")
<profilecomponent :user="{{ json_encode($user) }}"></profilecomponent>
@endsection