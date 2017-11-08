<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>Supplier Report</b>
        </td>
    </tr>
    <tr>
            <th>SUPPLIER CODE</th>
            <th>NAME</th>
            <th>EMAIL</th>
            <th>PHONE</th>
            <th>ADDRESS</th>
            <th>PINCODE</th>
            <th>STATUS</th>
            <th>CREATED AT</th>
            <th>CREATED BY</th>
            <th>UPDATED AT</th>
            <th>UPDATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($supplier_report_data as $supplier_report_data_item)
        <tr>
            <td>{{ $supplier_report_data_item['supplier_code'] }}</td>
            <td>{{ $supplier_report_data_item['name'] }}</td>
            <td>{{ $supplier_report_data_item['email'] }}</td>
            <td>{{ $supplier_report_data_item['phone'] }}</td>
            <td>{{ $supplier_report_data_item['address'] }}</td>
            <td>{{ $supplier_report_data_item['pincode'] }}</td>
            <td>{{ $supplier_report_data_item['status'] }}</td>
            <td>{{ $supplier_report_data_item['created_at'] }}</td>
            <td>{{ $supplier_report_data_item['created_by'] }}</td>
            <td>{{ $supplier_report_data_item['updated_at'] }}</td>
            <td>{{ $supplier_report_data_item['updated_by'] }}</td>
    @endforeach
    </tbody>
</table>