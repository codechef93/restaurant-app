class Transactions{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/transactions',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'transactions.transaction_code' },
                { name: 'transactions.transaction_date' },
                { name: 'master_transaction_type.label' },
                { name: 'accounts.label' },
                { name: 'transactions.payment_method' },
                { name: 'transactions.bill_to' },
                { name: 'transactions.bill_to_name' },
                { name: 'transactions.amount' },
                { name: 'transactions.created_at' },
                { name: 'transactions.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 8, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [11] }
            ]
        });
    }
}