class Products{

    constructor() {
        var self = this;
        this.data = {};
        this.data.access_token = window.settings.access_token;
    }
    
    load_listing_table(){
        "use strict";
        self.listing_table = $('#listing-table').DataTable({
            ajax: {
                url  : '/api/products',
                type : 'POST',
                data : this.data
            },
            columns: [
                { name: 'products.product_code' },
                { name: 'products.name' },
                { name: 'suppliers.name' },
                { name: 'category.label' },
                { name: 'tax_codes.label' },
                { name: 'discount_codes.label' },
                { name: 'products.quantity' },
                { name: 'products.sale_amount_excluding_tax' },
                { name: 'master_status.label' },
                { name: 'products.created_at' },
                { name: 'products.updated_at' },
                { name: 'user_created.fullname' },
            ],
            order: [[ 9, "desc" ]],
            columnDefs: [
                { "orderable": false, "targets": [12] }
            ]
        });

        document.addEventListener("product_type_filter", function(e) {
            var product_type_filter = e.detail;

            console.log(e);
        
            var product_list = new Products();
            product_list.data.product_filter = product_type_filter;
            
            self.listing_table.settings()[0].ajax.data = product_list.data;
            self.listing_table.ajax.reload();
        });

    }
}