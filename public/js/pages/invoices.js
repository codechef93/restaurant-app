class Invoices{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/invoices',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'invoices.invoice_number' },
                { name: 'invoices.invoice_reference' },
                { name: 'invoices.bill_to' },
                { name: 'invoices.bill_to_name' },
                { name: 'invoices.invoice_date' },
                { name: 'invoices.invoice_due_date' },
                { name: 'invoices.total_order_amount' },
                { name: 'master_status.label' },
                { name: 'invoices.created_at' },
                { name: 'invoices.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 8, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [11] }
            ]
        });
    }
}