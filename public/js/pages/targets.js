class Targets{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/targets',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'targets.month' },
                { name: 'targets.income' },
                { name: 'targets.expense' },
                { name: 'targets.sales' },
                { name: 'targets.net_profit' },
                { name: 'targets.created_at' },
                { name: 'targets.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 0, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [8] }
            ]
        });
    }
}