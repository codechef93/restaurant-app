<!DOCTYPE html>
<html>
    <head>
        <title>Reference Sheet Print | {{ $store }} | {{ $date }}</title>
        <style>
            .container{
                display: table;
                border-collapse:collapse;
                font-family: 'Courier New';
                font-size: 10px;
            }
            .column {
                display: table-cell;
                float: left;
                width: 100%;
                padding: 11px;
                border-bottom: 0.5px solid #ccc;
            }
            .footer{
                text-align: center;
                font-family: 'Courier New';
                font-size: 8px;
            }
        </style>

    </head>

    <body>
        <div class='container'>
            <div class="column">
                <h4>REFERENCE SHEET</h4>
                <small>Store : {{ $store }} | Generated On : {{ $date }} |</small>
                <small><i>These are the active codes available in the application for the selected store.</i></small>
            </div>
            @foreach ($data as $key => $data_item)
            <div class="column">
                @if($key == "role_codes")   
                    <div><h5>Role Codes</h5></div>
                @endif
                @if($key == "store_codes")    
                    <div><h5>Store Codes</h5></div>
                @endif
                @if($key == "supplier_codes")    
                    <div><h5>Supplier Codes</h5></div>
                @endif
                @if($key == "category_codes")    
                    <div><h5>Category Codes</h5></div>
                @endif
                @if($key == "tax_codes")    
                    <div><h5>Tax Codes</h5></div>
                @endif
                @if($key == "discount_codes")    
                    <div><h5>Discount Codes</h5></div>
                @endif
                @if($key == "product_codes")    
                    <div><h5>Product Codes</h5></div>
                @endif
                @if($key == "user_codes")    
                    <div><h5>User Codes</h5></div>
                @endif
                @if($key == "statuses")    
                    <div><h5>Statuses</h5></div>
                @endif
                @if(count($data_item)>0)
                    @foreach ($data_item as $value)
                        @if($key == "role_codes")   
                            <div><b>{{ $value["role_code"] }}</b> - {{ $value["label"] }}</div>
                        @endif
                        @if($key == "store_codes")    
                            <div><b>{{ $value["store_code"] }}</b> - {{ $value["name"] }}</div>
                        @endif
                        @if($key == "supplier_codes")    
                            <div><b>{{ $value["supplier_code"] }}</b> - {{ $value["name"] }}</div>
                        @endif
                        @if($key == "category_codes")    
                            <div><b>{{ $value["category_code"] }}</b> - {{ $value["label"] }}</div>
                        @endif
                        @if($key == "tax_codes")    
                            <div><b>{{ $value["tax_code"] }}</b> - {{ $value["label"] }}, Tax percentage: {{ $value["total_tax_percentage"] }}</div>
                        @endif
                        @if($key == "discount_codes")    
                            <div><b>{{ $value["discount_code"] }}</b> - {{ $value["label"] }}, Discount percentage: {{ $value["discount_percentage"] }}</div>
                        @endif
                        @if($key == "product_codes")    
                            <div><b>{{ $value["product_code"] }}</b> - {{ $value["name"] }}</div>
                        @endif
                        @if($key == "user_codes")    
                            <div><b>{{ $value["user_code"] }}</b> - {{ $value["fullname"] }}</div>
                        @endif
                        @if($key == "statuses")    
                            <div><b>{{ str_replace('_STATUS','',$value["key"]) }}</b> - {{ $value["status_values"] }}</div>
                        @endif
                    @endforeach
                @else
                    <div>Data not found</div>
                @endif
            </div>
            @endforeach
        </div>
    </body>
</html>