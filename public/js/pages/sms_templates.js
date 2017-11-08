class SmsTemplates{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/sms_templates',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'sms_templates.template_key' },
                { name: 'sms_templates.message' },
                { name: 'master_status.label' },
                { name: 'sms_templates.created_at' },
                { name: 'sms_templates.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 3, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [6] }
            ]
        });
    }
}