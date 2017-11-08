class Printers{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/printers',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'printers.printer_code' },
                { name: 'printers.printer_name' },
                { name: 'master_status.label' },
                { name: 'printers.created_at' },
                { name: 'printers.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 3, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [6] }
            ]
        });
    }
}