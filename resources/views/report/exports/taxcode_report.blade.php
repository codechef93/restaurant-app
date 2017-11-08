<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>Taxcode Report</b>
        </td>
    </tr>
    <tr>
            <th>TAX CODE</th>
            <th>NAME</th>
            <th>TAX PERCENTAGE</th>
            <th>DESCRIPTION</th>
            <th>STATUS</th>
            <th>CREATED AT</th>
            <th>CREATED BY</th>
            <th>UPDATED AT</th>
            <th>UPDATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($taxcode_report_data as $taxcode_report_data_item)
        <tr>
            <td>{{ $taxcode_report_data_item['tax_code'] }}</td>
            <td>{{ $taxcode_report_data_item['name'] }}</td>
            <td>{{ $taxcode_report_data_item['tax_percentage'] }}</td>
            <td>{{ $taxcode_report_data_item['description'] }}</td>
            <td>{{ $taxcode_report_data_item['status'] }}</td>
            <td>{{ $taxcode_report_data_item['created_at'] }}</td>
            <td>{{ $taxcode_report_data_item['created_by'] }}</td>
            <td>{{ $taxcode_report_data_item['updated_at'] }}</td>
            <td>{{ $taxcode_report_data_item['updated_by'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>