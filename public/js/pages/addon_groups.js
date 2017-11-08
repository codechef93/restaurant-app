class AddonGroups{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/addon_groups',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'addon_groups.addon_group_code' },
                { name: 'addon_groups.label' },
                { name: 'master_status.label' },
                { name: 'addon_groups.created_at' },
                { name: 'addon_groups.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 3, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [6] }
            ]
        });
    }
}