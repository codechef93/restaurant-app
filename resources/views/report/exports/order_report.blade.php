<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>Order Report</b>
        </td>
    </tr>
    <tr>
            <th>ORDER NUMBER</th>
            <th>CUSTOMER PHONE</th>
            <th>CUSTOMER EMAIL</th>
            <th>ORDER LEVEL DISCOUNT CODE</th>
            <th>ORDER LEVEL DISCOUNT PERCENTAGE</th>
            <th>ORDER LEVEL DISCOUNT AMOUNT</th>
            <th>PRODUCT LEVEL TOTAL DISCOUNT</th>
            <th>ORDER LEVEL TAX CODE</th>
            <th>ORDER LEVEL TAX PERCENTAGE</th>
            <th>ORDER LEVEL TAX AMOUNT</th>
            <th>ORDER LEVEL TAX COMPONENTS</th>
            <th>PRODUCT LEVEL TOTAL TAX</th>
            <th>PURCHASE AMOUNT SUBTOTAL EXCLUDING TAX</th>
            <th>SALE AMOUNT SUBTOTAL EXCLUDING TAX</th>
            <th>TOTAL DISCOUNT AMOUNT</th>
            <th>TOTAL AFTER DISCOUNT</th>
            <th>TOTAL TAX AMOUNT</th>
            <th>TOTAL ORDER AMOUNT</th>
            <th>PAYMENT METHOD</th>
            <th>STATUS</th>
            <th>CREATED AT</th>
            <th>CREATED BY</th>
            <th>UPDATED AT</th>
            <th>UPDATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order_report_data as $order_report_data_item)
        <tr>
            <td>{{ $order_report_data_item['order_number'] }}</td>
            <td>{{ $order_report_data_item['customer_phone'] }}</td>
            <td>{{ $order_report_data_item['customer_email'] }}</td>
            <td>{{ $order_report_data_item['order_level_discount_code'] }}</td>
            <td>{{ $order_report_data_item['order_level_discount_percentage'] }}</td>
            <td>{{ $order_report_data_item['order_level_discount_amount'] }}</td>
            <td>{{ $order_report_data_item['product_level_total_discount'] }}</td>
            <td>{{ $order_report_data_item['order_level_tax_code'] }}</td>
            <td>{{ $order_report_data_item['order_level_tax_percentage'] }}</td>
            <td>{{ $order_report_data_item['order_level_tax_amount'] }}</td>
            <td>{{ $order_report_data_item['order_level_tax_components'] }}</td>
            <td>{{ $order_report_data_item['product_level_total_tax'] }}</td>
            <td>{{ $order_report_data_item['purchase_amount_subtotal_excluding_tax'] }}</td>
            <td>{{ $order_report_data_item['sale_amount_subtotal_excluding_tax'] }}</td>
            <td>{{ $order_report_data_item['total_discount_amount'] }}</td>
            <td>{{ $order_report_data_item['total_after_discount'] }}</td>
            <td>{{ $order_report_data_item['total_tax_amount'] }}</td>
            <td>{{ $order_report_data_item['total_order_amount'] }}</td>
            <td>{{ $order_report_data_item['payment_method'] }}</td>
            <td>{{ $order_report_data_item['status'] }}</td>
            <td>{{ $order_report_data_item['created_at'] }}</td>
            <td>{{ $order_report_data_item['created_by'] }}</td>
            <td>{{ $order_report_data_item['updated_at'] }}</td>
            <td>{{ $order_report_data_item['updated_by'] }}</td>
    @endforeach
    </tbody>
</table>