<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>Purchase Order Report</b>
        </td>
    </tr>
    <tr>
            <th>'PO NUMBER</th>
            <th>'PO REFERENCE</th>
            <th>'ORDER DATE</th>
            <th>'ORDER DUE DATE</th>
            <th>'SUPPLIER CODE</th>
            <th>'SUPPLIER NAME</th>
            <th>'SUPPLIER ADDRESS</th>
            <th>'CURRENCY NAME</th>
            <th>'CURRENCY CODE</th>
            <th>'SUBTOTAL EXCLUDING TAX</th>
            <th>'TOTAL DISCOUNT AMOUNT</th>
            <th>'TOTAL AFTER DISCOUNT</th>
            <th>'TOTAL TAX AMOUNT</th>
            <th>'SHIPPING CHARGE</th>
            <th>'PACKING CHARGE</th>
            <th>'TOTAL ORDER AMOUNT</th>
            <th>'STATUS</th>
            <th>'CREATED AT</th>
            <th>'CREATED BY</th>
            <th>'UPDATED AT</th>
            <th>'UPDATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($purchase_order_report_data as $purchase_order_report_data_item)
        <tr>
            <td>{{ $purchase_order_report_data_item['po_number'] }}</td>
            <td>{{ $purchase_order_report_data_item['po_reference'] }}</td>
            <td>{{ $purchase_order_report_data_item['order_date'] }}</td>
            <td>{{ $purchase_order_report_data_item['order_due_date'] }}</td>
            <td>{{ $purchase_order_report_data_item['supplier_code'] }}</td>
            <td>{{ $purchase_order_report_data_item['supplier_name'] }}</td>
            <td>{{ $purchase_order_report_data_item['product_level_total_discount'] }}</td>
            <td>{{ $purchase_order_report_data_item['supplier_address'] }}</td>
            <td>{{ $purchase_order_report_data_item['currency_name'] }}</td>
            <td>{{ $purchase_order_report_data_item['currency_code'] }}</td>
            <td>{{ $purchase_order_report_data_item['subtotal_excluding_tax'] }}</td>
            <td>{{ $purchase_order_report_data_item['total_discount_amount'] }}</td>
            <td>{{ $purchase_order_report_data_item['total_after_discount'] }}</td>
            <td>{{ $purchase_order_report_data_item['total_tax_amount'] }}</td>
            <td>{{ $purchase_order_report_data_item['shipping_charge'] }}</td>
            <td>{{ $purchase_order_report_data_item['packing_charge'] }}</td>
            <td>{{ $purchase_order_report_data_item['total_order_amount'] }}</td>
            <td>{{ $purchase_order_report_data_item['status'] }}</td>
            <td>{{ $purchase_order_report_data_item['created_at'] }}</td>
            <td>{{ $purchase_order_report_data_item['created_by'] }}</td>
            <td>{{ $purchase_order_report_data_item['updated_at'] }}</td>
            <td>{{ $purchase_order_report_data_item['updated_by'] }}</td>
    @endforeach
    </tbody>
</table>