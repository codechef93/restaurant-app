class BusinessRegisters{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/business_registers',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'user.fullname' },
                { name: 'business_registers.opening_date' },
                { name: 'business_registers.closing_date' },
                { name: 'business_registers.created_at' },
                { name: 'business_registers.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 3, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [6] }
            ]
        });
    }
}