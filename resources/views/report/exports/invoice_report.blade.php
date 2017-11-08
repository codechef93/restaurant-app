<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>Invoice Report</b>
        </td>
    </tr>
    <tr>
            <th>BILL TO</th>
            <th>INVOICE NUMBER</th>
            <th>INVOICE REFERENCE</th>
            <th>INVOICE DATE</th>
            <th>INVOICE DUE DATE</th>
            <th>BILL TO CODE</th>
            <th>BILL TO NAME</th>
            <th>BILL TO CONTACT</th>
            <th>BILL TO EMAIL</th>
            <th>BILL TO ADDRESS</th>
            <th>CURRENCY NAME</th>
            <th>CURRENCY CODE</th>
            <th>SUBTOTAL EXCLUDING TAX</th>
            <th>TOTAL DISCOUNT AMOUNT</th>
            <th>TOTAL AFTER DISCOUNT</th>
            <th>TOTAL TAX AMOUNT</th>
            <th>SHIPPING CHARGE</th>
            <th>PACKING CHARGE</th>
            <th>TOTAL AMOUNT</th>
            <th>STATUS</th>
            <th>CREATED AT</th>
            <th>CREATED BY</th>
            <th>UPDATED AT</th>
            <th>UPDATED BY</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoice_report_data as $invoice_report_data_item)
        <tr>
            <td>{{ $invoice_report_data_item['bill_to'] }}</td>
            <td>{{ $invoice_report_data_item['invoice_number'] }}</td>
            <td>{{ $invoice_report_data_item['invoice_reference'] }}</td>
            <td>{{ $invoice_report_data_item['invoice_date'] }}</td>
            <td>{{ $invoice_report_data_item['invoice_due_date'] }}</td>
            <td>{{ $invoice_report_data_item['bill_to_code'] }}</td>
            <td>{{ $invoice_report_data_item['bill_to_name'] }}</td>
            <td>{{ $invoice_report_data_item['bill_to_contact'] }}</td>
            <td>{{ $invoice_report_data_item['bill_to_email'] }}</td>
            <td>{{ $invoice_report_data_item['bill_to_address'] }}</td>
            <td>{{ $invoice_report_data_item['currency_name'] }}</td>
            <td>{{ $invoice_report_data_item['currency_code'] }}</td>
            <td>{{ $invoice_report_data_item['subtotal_excluding_tax'] }}</td>
            <td>{{ $invoice_report_data_item['total_discount_amount'] }}</td>
            <td>{{ $invoice_report_data_item['total_after_discount'] }}</td>
            <td>{{ $invoice_report_data_item['total_tax_amount'] }}</td>
            <td>{{ $invoice_report_data_item['shipping_charge'] }}</td>
            <td>{{ $invoice_report_data_item['packing_charge'] }}</td>
            <td>{{ $invoice_report_data_item['total_amount'] }}</td>
            <td>{{ $invoice_report_data_item['status'] }}</td>
            <td>{{ $invoice_report_data_item['created_at'] }}</td>
            <td>{{ $invoice_report_data_item['created_by'] }}</td>
            <td>{{ $invoice_report_data_item['updated_at'] }}</td>
            <td>{{ $invoice_report_data_item['updated_by'] }}</td>
    @endforeach
    </tbody>
</table>