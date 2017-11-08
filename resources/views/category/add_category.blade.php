@extends('layouts.layout')

@section("content")
<addcategorycomponent :statuses="{{ json_encode($statuses) }}" :category_data="{{ json_encode($category_data) }}"></addcategorycomponent>
@endsection