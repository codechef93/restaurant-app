class PurchaseOrders{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/purchase_orders',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'purchase_orders.po_number' },
                { name: 'purchase_orders.po_reference' },
                { name: 'purchase_orders.supplier_name' },
                { name: 'purchase_orders.order_date' },
                { name: 'purchase_orders.order_due_date' },
                { name: 'purchase_orders.total_order_amount' },
                { name: 'master_status.label' },
                { name: 'purchase_orders.created_at' },
                { name: 'purchase_orders.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 7, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [10] }
            ]
        });
    }
}