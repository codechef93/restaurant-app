<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>Discountcode Report</b>
        </td>
    </tr>
    <tr>
            <th>DISCOUNT CODE</th>
            <th>NAME</th>
            <th>DISCOUNT PERCENTAGE</th>
            <th>DESCRIPTION</th>
            <th>STATUS</th>
            <th>CREATED AT</th>
            <th>CREATED BY</th>
            <th>UPDATED AT</th>
            <th>UPDATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($discountcode_report_data as $discountcode_report_data_item)
        <tr>
            <td>{{ $discountcode_report_data_item['discount_code'] }}</td>
            <td>{{ $discountcode_report_data_item['name'] }}</td>
            <td>{{ $discountcode_report_data_item['discount_percentage'] }}</td>
            <td>{{ $discountcode_report_data_item['description'] }}</td>
            <td>{{ $discountcode_report_data_item['status'] }}</td>
            <td>{{ $discountcode_report_data_item['created_at'] }}</td>
            <td>{{ $discountcode_report_data_item['created_by'] }}</td>
            <td>{{ $discountcode_report_data_item['updated_at'] }}</td>
            <td>{{ $discountcode_report_data_item['updated_by'] }}</td>
    @endforeach
    </tbody>
</table>