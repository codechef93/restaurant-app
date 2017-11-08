@extends('layouts.layout')

@section("content")
<editprofilecomponent :user="{{ json_encode($user) }}"></editprofilecomponent>
@endsection