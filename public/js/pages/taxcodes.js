class Taxcodes{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/tax_codes',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'tax_codes.label' },
                { name: 'tax_codes.tax_code' },
                { name: 'tax_codes.total_tax_percentage' },
                { name: 'master_status.label' },
                { name: 'tax_codes.created_at' },
                { name: 'tax_codes.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 4, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [7] }
            ]
        });
    }
}