<!DOCTYPE html>
<html>
    <head>
        <title>Register Report | {{ $store }} | {{ $date }}</title>
    </head>
    <body>

        <div class='mb-2rem'>
            <table>
                <tr>
                    <td><h2>REGISTER REPORT</h2></td>
                </tr>
            </table>
        </div>

        <div class='mb-2rem'>
            <table class='w-100 bordered-table'>
                <tr>
                    <th class='w-25 left headers-bg'>
                        Store
                    </th>
                    <td class='w-25'>
                        {{ $store }}
                    </td>
                    <th class='w-25 left headers-bg'>
                        Billing Counter
                    </th>
                    <td class='w-25'>
                        {{ $data['billing_counter']['billing_counter_code'] }} - {{ $data['billing_counter']['counter_name'] }}
                    </td>
                </tr>
            </table>
        </div>

        <div class='mb-2rem'>
            <table class='w-100 bordered-table'>
                <tr>
                    <th class='w-25 left headers-bg'>
                        Total Closing Amount ({{ $currency }})
                    </th>
                    <td class='w-25 right'>
                        {{ $data['total_closing_amount'] }}
                    </td>
                    <th class='w-25 left headers-bg'>
                        Total Order Count
                    </th>
                    <td class='w-25 right'>
                        {{ $data['total_order_count'] }}
                    </td>
                </tr>
            </table>
        </div>

        <div class=''>
            <table class='w-100 bordered-table'>
                <tr>
                    <td class='w-50 left bold' colspan="2">
                        Opened By
                    </td>
                    <td class='w-50' colspan="2">
                        {{ $data['user']['fullname'] }}
                    </td>
                </tr>
                <tr>
                    <td class='left bold'>
                        Opened On
                    </td>
                    <td class=''>
                        {{ $data['opening_date_label'] }}
                    </td>
                    <td class='left bold'>
                        Closed On
                    </td>
                    <td class=''>
                        {{ ($data['closing_date_label'])?$data['closing_date_label']:'-' }}
                    </td>
                </tr>
            </table>
        </div>

        <div class='mb-3rem'>
            <table class='w-100 bordered-table'>
                <tr>
                    <th class=''>
                        
                    </th>
                    <th class=''>
                        Amount ({{ $currency }})
                    </th>
                    <th class=''>
                        Count
                    </th>
                </tr>
                <tr>
                    <td class=''>
                        Opening Amount
                    </td>
                    <td class='right'>
                        {{ $data['opening_amount'] }}
                    </td>
                    <td class=''>
                        -
                    </td>
                </tr>
                <tr>
                    <td class=''>
                        Closing Amount
                    </td>
                    <td class='right'>
                        {{ $data['closing_amount'] }}
                    </td>
                    <td class=''>
                        -
                    </td>
                </tr>
                <tr>
                    <td class=''>
                        Credit Card Slips
                    </td>
                    <td class=''>
                        -
                    </td>
                    <td class='right'>
                        {{ $data['credit_card_slips'] }}
                    </td>
                </tr>
                <tr>
                    <td class=''>
                        Cheques
                    </td>
                    <td class=''>
                        -
                    </td>
                    <td class='right'>
                        {{ $data['cheques'] }}
                    </td>
                </tr>
                @foreach ($data['payment_method_data'] as $payment_method)
                <tr>
                    <td class=''>
                        {{ $payment_method['payment_method'] }}
                    </td>
                    <td class='right'>
                        {{ $payment_method['value'] }}
                    </td>
                    <td class='right'>
                        {{ $payment_method['order_count'] }}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td class=''>
                        Order Grand Total
                    </td>
                    <td class='right'>
                        {{ (isset($data['order_data']['order_value']))?$data['order_data']['order_value']:0 }}
                    </td>
                    <td class='right'>
                        {{ $data['order_data']['order_count'] }}
                    </td>
                </tr>
            </table>
        </div>

        @if (isset($data['sub_registers']) && count($data['sub_registers']) > 0)
            <div class="mb-1rem"> 
                <table>
                    <tr>
                        <td><h4>Users joined the Counter</h4></td>
                    </tr>
                </table>
            </div>

            @foreach ($data['sub_registers'] as $sub_register)
            <div class=''>
                <table class='w-100 bordered-table'>
                    <tr>
                        <td class='w-50 left bold' colspan="2">
                            Joined By
                        </td>
                        <td class='w-50' colspan="2">
                            {{ $sub_register['user']['fullname'] }}
                        </td>
                    </tr>
                    <tr>
                        <td class='left bold'>
                            Joined On
                        </td>
                        <td class=''>
                            {{ $sub_register['opening_date_label'] }}
                        </td>
                        <td class='left bold'>
                            Exited On
                        </td>
                        <td class=''>
                            {{ ($sub_register['closing_date_label'])?$sub_register['closing_date_label']:'-' }}
                        </td>
                    </tr>
                </table>
            </div>
                        
            <div class='mb-3rem'>
                <table class='w-100 bordered-table'>
                    <tr>
                        <th class=''>
                            
                        </th>
                        <th class=''>
                            Amount ({{ $currency }})
                        </th>
                        <th class=''>
                            Count
                        </th>
                    </tr>
                    <tr>
                        <td class=''>
                            Opening Amount
                        </td>
                        <td class='right'>
                            {{ $sub_register['opening_amount'] }}
                        </td>
                        <td class=''>
                            -
                        </td>
                    </tr>
                    <tr>
                        <td class=''>
                            Closing Amount
                        </td>
                        <td class='right'>
                            {{ $sub_register['closing_amount'] }}
                        </td>
                        <td class=''>
                            -
                        </td>
                    </tr>
                    <tr>
                        <td class=''>
                            Credit Card Slips
                        </td>
                        <td class=''>
                            -
                        </td>
                        <td class='right'>
                            {{ $sub_register['credit_card_slips'] }}
                        </td>
                    </tr>
                    <tr>
                        <td class=''>
                            Cheques
                        </td>
                        <td class=''>
                            -
                        </td>
                        <td class='right'>
                            {{ $sub_register['cheques'] }}
                        </td>
                    </tr>
                    @foreach ($sub_register['payment_method_data'] as $payment_method)
                    <tr>
                        <td class=''>
                            {{ $payment_method['payment_method'] }}
                        </td>
                        <td class='right'>
                            {{ $payment_method['value'] }}
                        </td>
                        <td class='right'>
                            {{ $payment_method['order_count'] }}
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td class=''>
                            Order Grand Total
                        </td>
                        <td class='right'>
                            {{ (isset($sub_register['order_data']['order_value']))?$sub_register['order_data']['order_value']:0 }}
                        </td>
                        <td class='right'>
                            {{ $sub_register['order_data']['order_count'] }}
                        </td>
                    </tr>
                </table>
            </div>
            @endforeach
        @endif
        <div class='center'>
            <div class='display-block'>Thank You!</div>
        </div>

    </body>
</html>