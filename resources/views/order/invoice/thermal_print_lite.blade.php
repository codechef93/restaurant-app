@php $data = json_decode($data); @endphp
<!DOCTYPE html>
<html>
    <head>
        <title>Order #{{ $data->order_number }}</title>
    </head>
    <body >
        <div class='center border-bottom-dashed pb-1rem'>
            <div class='bold display-block'>{{ $data->store->name }}</div>
        </div>
        <div class='border-bottom-dashed pt-1rem mb-1rem'>
            <table class='w-100'>
                <tr>
                    <td class='bold w-50'>Order #{{ $data->order_number }}</td>
                    <td class='right' >Date: {{ $data->created_at_label  }}</td>
                </tr>
            </table>
        </div>

        <table class='border-bottom-dashed mb-1rem w-100 page-break-avoid'>
            <tr>
                <td class='bold w-45'>Description</td>
                <td class='bold right'>Qty</td>
                <td class='bold right'>Rate</td>
                <td class='bold right'>Amount</td>
            </tr>
            @foreach ($data->products as $order_products)
            @php 
                $addon_spacing = '';
                $addon_indicator = '';
                if($order_products->parent_order_product ==  false){
                    $addon_spacing = ' pl-12';
                    $addon_indicator = '+ ';
                }
            @endphp
            <tr class="">
                <td class='{{ $addon_spacing }} pb-1rem'>{{ $addon_indicator }}{{ $order_products->product_code.'-'.$order_products->name }}</td>
                <td class='pb-1rem right'>{{ $order_products->quantity }}</td>
                <td class='pb-1rem right'>{{ $order_products->price }}</td>
                <td class='pb-1rem right'>{{ $order_products->sub_total }}</td>
            </tr>
            @endforeach
        </table>

        <table class='border-bottom-dashed mb-1rem w-100 page-break-avoid'>
            <tr>
                <td class='w-50'>Sub Total</td>
                <td class='right'>{{ $data->sale_amount_subtotal_excluding_tax }}</td>
            </tr>
            <tr>
                <td class='w-50'>Discount</td>
                <td class='right'>{{ $data->total_discount_before_additional_discount }}</td>
            </tr>

            @if($data->additional_discount_percentage > 0)
            <tr>
                <td class='w-50'>Additional Discount</td>
                <td class='right'>({{ $data->additional_discount_percentage }}%) {{ $data->additional_discount_amount }}</td>
            </tr>
            @endif
            <tr>
                <td class='w-50'>Tax</td>
                <td class='right'>{{ $data->total_tax_amount }}</td>
            </tr>
            <tr>
                <td class='bold w-50'>Bill Total</td>
                <td class='bold right'>{{ $data->store->currency_code }} {{ $data->total_order_amount }}</td>
            </tr>
            <tr>
                <td colspan="2">
                    <small>All prices are in {{ $data->store->currency_name }} ({{ $data->store->currency_code }})</small>
                </td>
            </tr>
        </table>

        @if(isset($data->order_type_data) && $data->order_type_data->order_type_constant == 'DELIVERY' && ($data->contact_number != '' || $data->address != ''))
        <table class='w-100 page-break-avoid'>
            <tr>
                <td class=''>Delivery Information</td>
            </tr>
            <tr>
                <td class=''>
                    Contact Number: {{ ($data->contact_number)?$data->contact_number:'-' }}
                    @if($data->address != '')
                    <br>
                    Address: {{ $data->address }}
                    @endif
                </td>
            </tr>
        </table>
        @endif

        <div class='center'>
            <div class='display-block'>Thank You!</div>
        </div>

    </body>
</html>