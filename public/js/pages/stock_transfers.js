class Stocktransfers{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/stock_transfers',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'stock_transfer.stock_transfer_reference' },
                { name: 'stock_transfer.from_store_code' },
                { name: 'stock_transfer.from_store_name' },
                { name: 'stock_transfer.to_store_code' },
                { name: 'stock_transfer.to_store_name' },
                { name: 'master_status.label' },
                { name: 'stock_transfer.created_at' },
                { name: 'stock_transfer.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 6, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [9] }
            ]
        });
    }
}