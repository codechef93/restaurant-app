<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>Category Report</b>
        </td>
    </tr>
    <tr>
        <th>CATEGORY CODE</th>
        <th>LABEL</th>
        <th>DESCRIPTION</th>
        <th>STATUS</th>
        <th>CREATED AT</th>
        <th>CREATED BY</th>
        <th>UPDATED AT</th>
        <th>UPDATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($category_report_data as $category_report_data_item)
        <tr>
            <td>{{ $category_report_data_item['category_code'] }}</td>
            <td>{{ $category_report_data_item['label'] }}</td>
            <td>{{ $category_report_data_item['description'] }}</td>
            <td>{{ $category_report_data_item['status'] }}</td>
            <td>{{ $category_report_data_item['created_at_label'] }}</td>
            <td>{{ $category_report_data_item['created_by'] }}</td>
            <td>{{ $category_report_data_item['updated_at_label'] }}</td>
            <td>{{ $category_report_data_item['updated_by'] }}</td>
        </tr>
    @endforeach
    </tbody>
</table>