@php $data = json_decode($data); @endphp
<!DOCTYPE html>
<html>
    <head>
        <title>Order #{{ $data->order_number }}</title>
    </head>
    <body>
        <div class='pb-1rem'>
            <table class='w-100'>
                <tr>
                    <td class='w-50'>
                    @if($logo_path != '')
                    <img src="{{ $logo_path }}" class='h-50px'/>
                    @endif
                    </td>
                    <td class='right bold'>INVOICE</td>
                </tr>
            </table>
        </div>
        <div class='center border-bottom-dashed pb-1rem'>
            <div class='bold display-block'>{{ $data->store->name }}</div>
            <div>
                {{ $data->store->address }}
                @if ($data->store->pincode != '')
                    {{ $data->store->pincode }}
                @endif
            </div>
            @if ($data->store->tax_number != '')
                <div>VAT: {{ $data->store->tax_number }}</div>
            @endif
            @if ($data->store->primary_contact != '')
                <div>Contact No: {{ $data->store->primary_contact }}</div>
            @endif
        </div>
        <div class='border-bottom-dashed pt-1rem mb-1rem'>
            <table class='w-100'>
                <tr>
                    <td class='bold w-50'>Order #{{ $data->order_number }}</td>
                    <td class='right'>Date: {{ $data->created_at_label  }}</td>
                </tr>
                <tr>
                    <td class='w-50'>Billed By: {{ $data->created_by->user_code }}</td>
                    <td class='right'>Payment Method: {{ $data->payment_method }}</td>
                </tr>
                <tr>
                    <td class='w-50'>Customer : {{ $data->customer_name}}, {{ $data->customer_phone }}, {{ $data->customer_email }}</td>
                    <td class='right'>{{ $data->total_items }} Items ({{ $data->total_quantity }} Qty)</td>
                </tr>
                @if ($data->restaurant_mode == 1)
                <tr>
                    <td class='w-50'>Type: {{ $data->order_type }}</td>
                    <td class='right'>Table: {{ ($data->table != '')?$data->table:'-' }}</td>
                </tr>
                @endif
            </table>
        </div>

        <table class='border-bottom-dashed mb-1rem w-100'>
            <tr>
                <td class='bold w-45'>Description</td>
                <td class='bold right'>Qty</td>
                <td class='bold right'>Rate</td>
                <td class='bold right'>Discount</td>
                <td class='bold right'>Tax</td>
                <td class='bold right'>Amount</td>
            </tr>
            @foreach ($data->products as $order_products)
            @php 
                $spacing = '';        
                if($order_products->tax_percentage>0 || $order_products->discount_percentage >0){
                    $spacing = 'pb-0'; 
                }
                $addon_spacing = '';
                $addon_indicator = '';
                if($order_products->parent_order_product ==  false){
                    $addon_spacing = ' pl-12';
                    $addon_indicator = '+ ';
                }
            @endphp
            <tr>
                <td class='{{ $spacing.$addon_spacing }}'>{{ $addon_indicator }}{{ $order_products->product_code.'-'.$order_products->name }}</td>
                <td class='{{ $spacing }} right'>{{ $order_products->quantity }}</td>
                <td class='{{ $spacing }} right'>{{ $order_products->price }}</td>
                <td class='{{ $spacing }} right'>{{ $order_products->discount_amount }}</td>
                <td class='{{ $spacing }} right'>{{ $order_products->tax_amount }}</td>
                <td class='{{ $spacing }} right'>{{ $order_products->sub_total }}</td>
            </tr>

            @if($order_products->tax_percentage>0 || $order_products->discount_percentage >0){
                <tr>
                    <td class='{{ $addon_spacing }} small' colspan='4'>
                        @if($order_products->discount_percentage > 0)
                            Discount ({{ $order_products->discount_percentage }}%): {{ $order_products->discount_amount }}
                        @endif
                        @if($order_products->tax_percentage > 0)
                        <br>
                            @if(count($order_products->tax_components) > 0)
                                @foreach ($order_products->tax_components as $tax_component)
                                    {{ strtoupper($tax_component->tax_type) }}({{ $tax_component->tax_percentage }}%): {{ round($tax_component->tax_amount, 2) }}|
                                @endforeach
                            @endif  
                            Tax Amount: {{ $order_products->tax_amount }}
                        @endif
                    </td>
                </tr>
            @endif

            @endforeach
        </table>

        <table class='border-bottom-dashed mb-1rem w-100'>
            <tr>
                <td class='w-50'>Sub Total</td>
                <td class='right'>{{ $data->sale_amount_subtotal_excluding_tax }}</td>
            </tr>
            
            @php 
                $spacing = '';            
                if($data->order_level_discount_percentage > 0 || $data->product_level_total_discount > 0){
                    $spacing = 'pb-0';
                }
            @endphp

            <tr>
                <td class='{{ $spacing }} w-50'>Discount</td>
                <td class='{{ $spacing }} right'>{{ $data->total_discount_before_additional_discount }}</td>
            </tr>
            @if($data->order_level_discount_percentage > 0)
            <tr>
                <td class='{{ $spacing }} small' colspan='2'>
                    [Overall Discount {{ ($data->order_level_discount_percentage >0 )?'('.$data->order_level_discount_percentage.'%)':'' }}: {{ $data->order_level_discount_amount }}]
                </td>
            </tr>
            @endif
            @if($data->product_level_total_discount > 0)
            <tr>
                <td class='small' colspan='2'>
                    [Product Discount: {{ $data->product_level_total_discount }}]
                </td>
            </tr>
            @endif

            @if($data->additional_discount_percentage > 0)
            <tr>
                <td class='w-50'>Additional Discount</td>
                <td class='right'>({{ $data->additional_discount_percentage }}%) {{ $data->additional_discount_amount }}</td>
            </tr>
            @endif

            <tr>
                <td class='w-50'>Total Amount After Discount</td>
                <td class='right'>{{ $data->total_after_discount }}</td>
            </tr>

            @php 
                $spacing = '';            
                if($data->order_level_tax_percentage > 0 || $data->order_level_tax_amount > 0){
                    $spacing = 'pb-0';
                }
            @endphp
            <tr>
                <td class='{{ $spacing }} w-50'>Tax</td>
                <td class='{{ $spacing }} right'>{{ $data->total_tax_amount }}</td>
            </tr>
            @if($data->order_level_tax_percentage >0)
            <tr>
                <td class='{{ $spacing }} small' colspan='2'>
                    @if(count($data->order_level_tax_components)>0)
                        [Overall Tax: 
                        @foreach ($data->order_level_tax_components as $tax_component)
                            {{ strtoupper($tax_component->tax_type) }}({{ $tax_component->tax_percentage }}%) : {{ round($tax_component->tax_amount, 2) }}|
                        @endforeach
                        Tax Amount: {{ $data->order_level_tax_amount }}]
                    @endif
                </td>
            </tr>
            @endif

            @if($data->product_level_total_tax > 0)
            <tr>
                <td class='small' colspan='2'>
                    [Product Tax: {{ $data->product_level_total_tax }}]
                </td>
            </tr>
            @endif

            <tr>
                <td class='bold w-50'>Bill Total</td>
                <td class='bold right'>{{ $data->store->currency_code }} {{ $data->total_order_amount }}</td>
            </tr>
            <tr>
                <td>
                    <small>All prices are in {{ $data->store->currency_name }} ({{ $data->store->currency_code }})</small>
                </td>
            </tr>
        </table>

        @if(isset($data->order_type_data) && $data->order_type_data->order_type_constant == 'DELIVERY' && ($data->contact_number != '' || $data->address != ''))
        <table class='w-100'>
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