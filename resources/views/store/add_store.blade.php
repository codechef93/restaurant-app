@extends('layouts.layout')

@section("content")
<addstorecomponent :statuses="{{ json_encode($statuses) }}" :tax_codes="{{ json_encode($tax_codes) }}" :discount_codes="{{ json_encode($discount_codes) }}" :store_data="{{ json_encode($store_data) }}" :invoice_print_types="{{ json_encode($invoice_print_types) }}" :currency_list="{{ json_encode($currency_list) }}" :country_list="{{ json_encode($country_list) }}" :accounts="{{ json_encode($accounts) }}" :billing_type_list="{{ json_encode($billing_type_list) }}" :waiter_role="{{ json_encode($waiter_role) }}" :languages="{{ json_encode($languages) }}" :chef_role="{{ json_encode($chef_role) }}" :printers="{{ json_encode($printers) }}" :tax_code_slack="{{ json_encode($tax_code_slack) }}"></addstorecomponent>
@endsection