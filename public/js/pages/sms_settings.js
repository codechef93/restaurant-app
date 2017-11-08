class SmsSettings{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/sms_settings',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'setting_sms_gateways.gateway_type' },
                { name: 'master_status.label' },
                { name: 'setting_sms_gateways.created_at' },
                { name: 'setting_sms_gateways.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 2, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [5] }
            ]
        });
    }
}