<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>{{ $general['title'] }}</b>
        </td>
    </tr>
    <tr>
        <th>DATE</th>
        <th>BILLING COUNTER</th>
        <th>TOTAL SALES COUNT</th>
        <th>TOTAL SALES AMOUNT ({{ $general['currency'] }})</th>
        @foreach($payment_methods as $payment_method)
        <th>{{ $payment_method->label }} Sale Count</th>
        <th>{{ $payment_method->label }} Sale Amount ({{ $general['currency'] }})</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach($business_register_report_data as $date => $business_register_data_array)
        @foreach($business_register_data_array as $key => $business_register_data_item)
            <tr>
                <td>{{ $date }}</td>
                <td>{{ (isset($business_register_data_item->billing_counter_data) && $business_register_data_item->billing_counter_data != null)?$business_register_data_item->billing_counter_data:'' }}</td>
                <td>{{ (isset($business_register_data_item['order_count']))?$business_register_data_item['order_count']:'' }}</td>
                <td>{{ (isset($business_register_data_item['total_order_amount_sum']))?$business_register_data_item['total_order_amount_sum']:'' }}</td>
                @foreach($payment_methods as $payment_method)
                <td>{{ $business_register_data_item[strtolower($payment_method->label).'_sale_count'] }}</td>
                <td>{{ $business_register_data_item[strtolower($payment_method->label).'_sale_amount'] }}</td>
                @endforeach
            </tr>
        @endforeach
    @endforeach
    <tr>
        <td colspan="2"><b>GRAND TOTAL</b></td>
        <td>{{ (isset($total_count_array['total_order_count']))?$total_count_array['total_order_count']:'' }}</td>
        <td>{{ (isset($total_count_array['total_order_value']))?$total_count_array['total_order_value']:'' }}</td>
    </tr>
    </tbody>
</table>