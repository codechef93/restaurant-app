class VariantOption{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/variant_options',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'variant_options.variant_option_code' },
                { name: 'variant_options.label' },
                { name: 'master_status.label' },
                { name: 'variant_options.created_at' },
                { name: 'variant_options.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 3, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [6] }
            ]
        });
    }
}