class MeasurementUnits{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/measurement_units',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'measurement_units.unit_code' },
                { name: 'measurement_units.label' },
                { name: 'master_status.label' },
                { name: 'measurement_units.created_at' },
                { name: 'measurement_units.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 3, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [6] }
            ]
        });
    }
}