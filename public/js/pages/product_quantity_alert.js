class Product_quantity_alert{

    constructor() {
        var self = this;
        this.data = {};
        this.data.access_token = window.settings.access_token;
    }

    load_listing_table(){
        "use strict";
        self.listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/product_alert_report',
                type : 'POST',
                data : this.data
            },
            columns: [
                { name: 'products.product_code' },
                { name: 'products.name' },
                { name: 'products.alert_quantity' },
                { name: 'products.quantity' },
            ],
            order: [[ 3, "desc" ]]
        });
    }
}

document.addEventListener("product_type", function(e) {
    var product_type = e.detail;

    var product_quantity_alert = new Product_quantity_alert();
    product_quantity_alert.data.product_type = product_type;
    
    self.listing_table.settings()[0].ajax.data = product_quantity_alert.data;
    self.listing_table.ajax.reload();
});