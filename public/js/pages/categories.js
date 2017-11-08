class Categories{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/categories',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'category.label' },
                { name: 'category.category_code' },
                { name: 'category.display_on_pos_screen' },
                { name: 'category.display_on_qr_menu' },
                { name: 'master_status.label' },
                { name: 'category.created_at' },
                { name: 'category.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 5, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [8] }
            ]
        });
    }
}