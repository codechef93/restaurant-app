@php $data = json_decode($data); @endphp
<!DOCTYPE html>
<html>
    <head>
        <title>Order KOT #{{ $data->order_number }}</title>
    </head>
    <body >
        <div class='center border-bottom-dashed pb-1rem'>
            <div class='bold display-block'>KOT</div>
        </div>
        <div class='border-bottom-dashed pt-1rem mb-1rem'>
            <table class='w-100'>
                <tr>
                    <td class='bold w-50'>Order #{{ $data->order_number }}</td>
                    <td class='right' >Date: {{ $data->created_at_label  }}</td>
                </tr>
                @if ($data->restaurant_mode == 1)
                <tr>
                    <td class='w-50'>Type: {{ $data->order_type }}</td>
                    <td class='right'>Table: {{ ($data->table != '')?$data->table:'-'  }}</td>
                </tr>
                @if ($data->waiter_data !='')
                <tr>
                    <td class='' colspan="2">Waiter: {{ ($data->waiter_data !='')?$data->waiter_data->user_code .'-'.$data->waiter_data->fullname:'-' }}</td>
                </tr>
                @endif
                @endif
            </table>
        </div>

        <table class='border-bottom-dashed mb-1rem w-100 page-break-avoid'>
            <tr>
                <td class='bold w-90'>Description</td>
                <td class='bold right'>Qty</td>
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
            </tr>
            @endforeach
        </table>

    </body>
</html>