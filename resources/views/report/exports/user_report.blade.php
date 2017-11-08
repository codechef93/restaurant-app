<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>User Report</b>
        </td>
    </tr>
    <tr>
        <th>USER CODE</th>
        <th>FULL NAME</th>
        <th>EMAIL</th>
        <th>PHONE</th>
        <th>ROLE</th>
        <th>STATUS</th>
        <th>CREATED AT</th>
        <th>CREATED BY</th>
        <th>UPDATED AT</th>
        <th>UPDATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($user_report_data as $user_report_data_item)
        <tr>
            <td>{{ $user_report_data_item['user_code'] }}</td>
            <td>{{ $user_report_data_item['fullname'] }}</td>
            <td>{{ $user_report_data_item['email'] }}</td>
            <td>{{ $user_report_data_item['phone'] }}</td>
            <td>{{ $user_report_data_item['role'] }}</td>
            <td>{{ $user_report_data_item['status'] }}</td>
            <td>{{ $user_report_data_item['created_at'] }}</td>
            <td>{{ $user_report_data_item['created_by'] }}</td>
            <td>{{ $user_report_data_item['updated_at'] }}</td>
            <td>{{ $user_report_data_item['updated_by'] }}</td>
    @endforeach
    </tbody>
</table>