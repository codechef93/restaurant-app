@extends('layouts.layout')

@section("content")
<orderdetailcomponent :logged_user_id="{{ json_encode($logged_user_id) }}" :order_data="{{ json_encode($order_data) }}" :delete_order_access="{{ json_encode($delete_order_access) }}" :share_invoice_sms_access="{{ json_encode($share_invoice_sms_access) }}" :merge_order_access="{{ json_encode($merge_order_access) }}" :unmerge_order_access="{{ json_encode($unmerge_order_access) }}" :print_order_link="{{ json_encode($print_order_link) }}" :print_kot_link="{{ json_encode($print_kot_link) }}" :printnode_enabled="{{ json_encode($printnode_enabled) }}"></orderdetailcomponents>
@endsection