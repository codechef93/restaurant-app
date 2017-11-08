@extends('layouts.layout')

@section("content")
<printerdetailcomponent :printer_data="{{ json_encode($printer_data) }}"></printerdetailcomponent>
@endsection