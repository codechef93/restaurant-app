<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>Transaction Report</b>
        </td>
    </tr>
    <tr>
            <th>TRANSACTION DATE</th>
            <th>TRANSACTION CODE</th>
            <th>ACCOUNT CODE</th>
            <th>ACCOUNT NAME</th>
            <th>TRANSACTION TYPE</th>
            <th>PAYMENT METHOD</th>
            <th>PAYMENT FOR</th>
            <th>BILL TO NAME</th>
            <th>BILL TO CONTACT</th>
            <th>BILL TO ADDRESS</th>
            <th>CURRENCY CODE</th>
            <th>TOTAL AMOUNT</th>
            <th>PAYMENT GATEWAY TRANSACTION ID</th>
            <th>PAYMENT GATEWAY TRANSACTION STATUS</th>
            <th>CREATED AT</th>
            <th>CREATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($transaction_report_data as $transaction_report_data_item)
        <tr>
            <td>{{ $transaction_report_data_item['transaction_date'] }}</td>
            <td>{{ $transaction_report_data_item['transaction_code'] }}</td>
            <td>{{ $transaction_report_data_item['account_code'] }}</td>
            <td>{{ $transaction_report_data_item['account_name'] }}</td>
            <td>{{ $transaction_report_data_item['transaction_type'] }}</td>
            <td>{{ $transaction_report_data_item['payment_method'] }}</td>
            <td>{{ $transaction_report_data_item['payment_for'] }}</td>
            <td>{{ $transaction_report_data_item['bill_to_name'] }}</td>
            <td>{{ $transaction_report_data_item['bill_to_contact'] }}</td>
            <td>{{ $transaction_report_data_item['bill_to_address'] }}</td>
            <td>{{ $transaction_report_data_item['currency_code'] }}</td>
            <td>{{ $transaction_report_data_item['total_amount'] }}</td>
            <td>{{ $transaction_report_data_item['payment_gateway_transaction_id'] }}</td>
            <td>{{ $transaction_report_data_item['payment_gateway_transaction_status'] }}</td>
            <td>{{ $transaction_report_data_item['created_at'] }}</td>
            <td>{{ $transaction_report_data_item['created_by'] }}</td>
            
    @endforeach
    </tbody>
</table>