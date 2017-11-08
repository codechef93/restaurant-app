<table>
    <thead>
    <tr >
        <td colspan="4">
            <b>{{ $general['title'] }}</b>
        </td>
    </tr>
    <tr>
        <th>DATE</th>
        <th>TOTAL SALES ({{ $general['currency'] }})</th>
        <th>TAXABLE AMOUNT ({{ $general['currency'] }})</th>
        <th>TAX COLLECTED ({{ $general['currency'] }})</th>
    </tr>
    </thead>
    <tbody>
    @foreach($sales_tax_data as $sales_tax_data_item)
        <tr>
            <td>{{ (isset($sales_tax_data_item['order_date']) && $sales_tax_data_item['order_date'] != null)?$sales_tax_data_item['order_date']:'' }}</td>
            <td>{{ (isset($sales_tax_data_item['total_order_amount_sum']))?$sales_tax_data_item['total_order_amount_sum']:'' }}</td>
            <td>{{ (isset($sales_tax_data_item['total_after_discount_sum']))?$sales_tax_data_item['total_after_discount_sum']:'' }}</td>
            <td>{{ (isset($sales_tax_data_item['total_tax_amount_sum']))?$sales_tax_data_item['total_tax_amount_sum']:'' }}</td>
        </tr>
    @endforeach
    <tr>
        <td><b>GRAND TOTAL</b></td>
        <td>{{ (isset($total_sales_tax_data['grand_total_order_amount_sum']))?$total_sales_tax_data['grand_total_order_amount_sum']:'' }}</td>
        <td>{{ (isset($total_sales_tax_data['grand_total_after_discount_sum']))?$total_sales_tax_data['grand_total_after_discount_sum']:'' }}</td>
        <td>{{ (isset($total_sales_tax_data['grand_total_tax_amount_sum']))?$total_sales_tax_data['grand_total_tax_amount_sum']:'' }}</td>
    </tr>
    </tbody>
</table>