@extends('layouts.layout')

@section("content")
<businessregisterdetailcomponent :business_register_data="{{ json_encode($business_register_data) }}" :delete_register_access="{{ json_encode($delete_register_access) }}" :print_register_report_link="{{ json_encode($print_register_report_link) }}"></businessregisterdetailcomponent>
@endsection