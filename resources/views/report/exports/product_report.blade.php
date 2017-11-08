<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>Product Report</b>
        </td>
    </tr>
    <tr>
            <th>PRODUCT CODE</th>
            <th>NAME</th>
            <th>DESCRIPTION</th>
            <th>SUPPLIER CODE</th>
            <th>SUPPLIER NAME</th>
            <th>CATEGORY CODE</th>
            <th>CATEGORY NAME</th>
            <th>TAX CODE</th>
            <th>TAX PERCENTAGE</th>
            <th>DISCOUNT CODE</th>
            <th>DISCOUNT PERCENTAGE</th>
            <th>QUANTITY</th>
            <th>PURCHASE PRICE WITHOUT TAX</th>
            <th>SALE PRICE WITHOUT TAX</th>
            <th>STATUS</th>
            <th>CREATED AT</th>
            <th>CREATED BY</th>
            <th>UPDATED AT</th>
            <th>UPDATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($product_report_data as $product_report_data_item)
        <tr>
            <td>{{ $product_report_data_item['product_code'] }}</td>
            <td>{{ $product_report_data_item['name'] }}</td>
            <td>{{ $product_report_data_item['description'] }}</td>
            <td>{{ $product_report_data_item['supplier_code'] }}</td>
            <td>{{ $product_report_data_item['supplier_name'] }}</td>
            <td>{{ $product_report_data_item['category_code'] }}</td>
            <td>{{ $product_report_data_item['category_name'] }}</td>
            <td>{{ $product_report_data_item['tax_code'] }}</td>
            <td>{{ $product_report_data_item['tax_percentage'] }}</td>
            <td>{{ $product_report_data_item['discount_code'] }}</td>
            <td>{{ $product_report_data_item['discount_percentage'] }}</td>
            <td>{{ $product_report_data_item['quantity'] }}</td>
            <td>{{ $product_report_data_item['purchase_price_without_tax'] }}</td>
            <td>{{ $product_report_data_item['sale_price_without_tax'] }}</td>
            <td>{{ $product_report_data_item['status'] }}</td>
            <td>{{ $product_report_data_item['created_at'] }}</td>
            <td>{{ $product_report_data_item['created_by'] }}</td>
            <td>{{ $product_report_data_item['updated_at'] }}</td>
            <td>{{ $product_report_data_item['updated_by'] }}</td>
    @endforeach
    </tbody>
</table>