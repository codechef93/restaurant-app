<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>Customer Report</b>
        </td>
    </tr>
    <tr>
            <th>NAME</th>
            <th>EMAIL</th>
            <th>PHONE</th>
            <th>ADDRESS</th>
            <th>STATUS</th>
            <th>CREATED AT</th>
            <th>CREATED BY</th>
            <th>UPDATED AT</th>
            <th>UPDATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($customer_report_data as $customer_report_data_item)
        <tr>
            <td>{{ $customer_report_data_item['name'] }}</td>
            <td>{{ $customer_report_data_item['email'] }}</td>
            <td>{{ $customer_report_data_item['phone'] }}</td>
            <td>{{ $customer_report_data_item['address'] }}</td>
            <td>{{ $customer_report_data_item['status'] }}</td>
            <td>{{ $customer_report_data_item['created_at'] }}</td>
            <td>{{ $customer_report_data_item['created_by'] }}</td>
            <td>{{ $customer_report_data_item['updated_at'] }}</td>
            <td>{{ $customer_report_data_item['updated_by'] }}</td>
    @endforeach
    </tbody>
</table>