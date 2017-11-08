class Notifications{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/notifications',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'notifications.notification_text' },
                { name: 'user_notified.fullname' },
                { name: 'master_status.label' },
                { name: 'notifications.created_at' },
                { name: 'notifications.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 3, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [6] }
            ]
        });
    }
}