class Accounts{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/accounts',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'accounts.account_code' },
                { name: 'accounts.label' },
                { name: 'master_account_type.label' },
                { name: 'accounts.pos_default' },
                { name: 'master_status.label' },
                { name: 'accounts.created_at' },
                { name: 'accounts.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 5, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [8] }
            ]
        });
    }
}