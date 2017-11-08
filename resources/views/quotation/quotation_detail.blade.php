@extends('layouts.layout')

@section("content")
<quotationdetailcomponent :quotation_statuses="{{ json_encode($quotation_statuses) }}" :quotation_data="{{ json_encode($quotation_data) }}" :delete_quotation_access="{{ json_encode($delete_quotation_access) }}" :printnode_enabled="{{ json_encode($printnode_enabled) }}"></quotationdetailcomponent>
@endsection