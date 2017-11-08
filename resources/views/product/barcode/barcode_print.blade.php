<!DOCTYPE html>
<html>
    <head>
        <title>Barcode Print | {{ $store }} | {{ $date }}</title>
    </head>

    <body>
        <div class='container'>
            @foreach ($data as $data_item)
                @for ($i = 0; $i < $data_item['count']; $i++)
                    @if($i%64 == 0 && $i != 0)
                    <pagebreak>
                    @endif
                    <div class="column">
                        <div class='mb-2px'>{{ $data_item['product_name'] }}</div>
                        <img src="{!! $data_item['product_barcode'] !!}">
                        <div class='mt-2px'>{{ $data_item['product_code'] }}</div>
                        <div class='mt-2px'>{{ $data_item['price'] }}</div>
                    </div>
                @endfor
            @endforeach
        </div>
    </body>
</html>