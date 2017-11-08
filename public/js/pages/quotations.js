class Quotations{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/quotations',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'quotations.quotation_number' },
                { name: 'quotations.quotation_reference' },
                { name: 'quotations.bill_to' },
                { name: 'quotations.bill_to_name' },
                { name: 'quotations.quotation_date' },
                { name: 'quotations.quotation_due_date' },
                { name: 'quotations.total_order_amount' },
                { name: 'master_status.label' },
                { name: 'quotations.created_at' },
                { name: 'quotations.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 8, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [11] }
            ]
        });
    }
}