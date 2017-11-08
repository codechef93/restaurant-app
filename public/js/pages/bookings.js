class Bookings{
    load_listing_table(){
        "use strict";
        var listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/bookings',
                type : 'POST',
                data : {
                    access_token : window.settings.access_token
                }
            },
            columns: [
                { name: 'bookings.event_type' },
                { name: 'bookings.event_code' },
                { name: 'bookings.start_date' },
                { name: 'bookings.end_date' },
                { name: 'bookings.name' },
                { name: 'bookings.email' },
                { name: 'bookings.phone' },
                { name: 'bookings.created_at' },
                { name: 'bookings.updated_at' },
                { name: 'user_created.fullname' }
            ],
            order: [[ 7, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [10] }
            ]
        });
    }
}