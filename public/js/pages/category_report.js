class Category_report{

    constructor() {
        var self = this;
        this.data = {};
        this.data.access_token = window.settings.access_token;
    }

    load_listing_table(){
        "use strict";
        self.listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/get_category_performance',
                type : 'POST',
                data : this.data
            },
            columns: [
                { name: 'category.label' },
                { name: 'category.category_code' },
                { name: 'sold_quantity' },
                { name: 'purchased_amount' },
                { name: 'sold_amount' },
                { name: 'profit_loss' },
            ],
            order: [[ 0, "asc" ]]
        });
    }
}

document.addEventListener("month", function(e) {
    var month = e.detail;

    var category_object = new Category_report();
    category_object.data.month = month;
    
    self.listing_table.settings()[0].ajax.data = category_object.data;
    self.listing_table.ajax.reload();
});