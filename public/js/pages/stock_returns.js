class StockReturns{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/stock_returns',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'stock_returns.return_number' },
                { name: 'stock_returns.bill_to' },
                { name: 'stock_returns.bill_to_name' },
                { name: 'stock_returns.return_date' },
                { name: 'stock_returns.total_order_amount' },
                { name: 'master_status.label' },
                { name: 'stock_returns.created_at' },
                { name: 'stock_returns.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 6, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [9] }
            ]
        });
    }
}