"use strict";

export var order_mixin = {
    methods: {
        resolve_variants(product){
            if(this.enable_vairants_popup == true){
                var variant_exists = this.choose_vairants(product);
                if(variant_exists == true){
                    this.variant_parent_selected = product;
                    this.show_variant_modal = true;
                    return;
                }else{
                    this.add_to_cart(product);
                }
            }else{
                this.add_to_cart(product);
            }
        },

        skip_variant(){
            this.add_to_cart(this.variant_parent_selected);
        },

        add_to_cart(product){
            console.log(product)
            var tax_type = (product.tax_code != null)?product.tax_code.tax_type:'EXCLUSIVE';
            var product_sale_price = product.sale_amount_excluding_tax;

            var tax_percentage = (product.tax_code != null)?parseFloat(product.tax_code.total_tax_percentage):0;
            var discount_percentage = (product.discount_code != null)?parseFloat(product.discount_code.discount_percentage):0;
            var quantity = (this.cart[product.slack] != null)?parseFloat(this.cart[product.slack].quantity)+1:1;
            var total_price = parseFloat(quantity)*parseFloat(product_sale_price);
            var image = (product.images.length > 0 && product.images[0].thumbnail !='')?product.images[0]['thumbnail']:'/images/placeholder_images/menu_placeholder.png';
            

            var product_data = { 
                "product_slack" : product.slack,
                "product_code"  : product.product_code,
                "name"          : product.name,
                "price"         : product_sale_price,
                "quantity"      : quantity,
                "tax_percentage": (tax_percentage != null)?tax_percentage:0.00,
                "discount_percentage": (discount_percentage != null)?discount_percentage:0.00,
                "total_price"   : total_price.toFixed(2),
                "image"         : image,
                "customizable"  : product.customizable,
                "sale_price_including_tax" : product.sale_amount_including_tax,
                "tax_type"      : tax_type,
            };

            this.$set(this.cart, product.slack, product_data);
            this.update_prices();
            this.show_variant_modal = false;
            this.play_beep();
        },

        remove_from_cart(product_slack){
            this.$delete(this.cart, product_slack);
            this.update_prices();
        },

        validate_quantity : _.debounce(function (product_slack, event) {
            var entered_quantity = event.target.value;
            if(entered_quantity >0 || entered_quantity == ""){

                var quantity = this.cart[product_slack]['quantity'];
                var selected_addons = this.cart[product_slack]['selected_addon_products'];
                if(typeof selected_addons != 'undefined' && selected_addons != null){
                    for ( var qkey in selected_addons ) {
                        this.$set(selected_addons[qkey], 'quantity', parseFloat(quantity));
                    }
                }
                this.update_prices();
            }else{
                this.$delete(this.cart, product_slack);
                this.update_prices();
            }
        },2000),

        calculate_tax(item_total, tax_percentage){
            var tax_amount = (parseFloat(tax_percentage)/100)*parseFloat(item_total);
            return tax_amount.toFixed(2);
        },

        calculate_store_level_tax(item_total, store_tax_percentage){
            var store_level_tax_percentage = (store_tax_percentage != null)?store_tax_percentage:0; 
            var store_level_tax_amount = (parseFloat(store_level_tax_percentage)/100)*parseFloat(item_total);
            return store_level_tax_amount.toFixed(2);
        },

        calculate_discount(item_total, discount_percentage){
            var discount_amount = (parseFloat(discount_percentage)/100)*parseFloat(item_total);
            return discount_amount.toFixed(2);
        },

        calculate_store_level_discount(item_total, store_discount_percentage){
            var store_level_discount_percentage = (store_discount_percentage != null)?store_discount_percentage:0; 
            var store_level_discount_amount = (parseFloat(store_level_discount_percentage)/100)*parseFloat(item_total);
            return store_level_discount_amount.toFixed(2);
        },

        update_prices(){

            this.sub_total = 0.00;
            this.tax_total = 0.00;
            this.discount_total = 0.00;
            this.total = 0.00;
            this.quantity_count = 0;

            for ( var product_slack in this.cart) {
                const product_data = this.cart[product_slack];

                if(product_data.quantity != ""){
                    
                    var quantity = parseFloat(product_data.quantity);
                    if(!isNaN(quantity)){

                        var price_data = this.calculate_prices(quantity, product_data);

                        this.$set(this.cart[product_slack], 'total_price', price_data.total_price.toFixed(2));
                        this.$set(this.cart[product_slack], 'total_tax', price_data.total_tax);
                        this.$set(this.cart[product_slack], 'total_discount', price_data.total_discount);
                        
                        this.sub_total = this.sub_total+parseFloat(price_data.total_price);
                        this.tax_total = this.tax_total+parseFloat(price_data.total_tax);
                        this.discount_total = this.discount_total+parseFloat(price_data.total_discount);

                        this.quantity_count = this.quantity_count+quantity;
                    }
                    
                }

                if(typeof product_data.selected_addon_products != 'undefined' && product_data.selected_addon_products != null){
                    for(var addon_product in product_data.selected_addon_products){
                        const addon_product_data = product_data.selected_addon_products[addon_product]
                        var quantity = parseFloat(addon_product_data.quantity);
                        if(!isNaN(quantity)){

                            var price_data = this.calculate_prices(quantity, addon_product_data);

                            this.$set(this.cart[product_slack]['selected_addon_products'][addon_product], 'total_price', price_data.total_price.toFixed(2));
                            this.$set(this.cart[product_slack]['selected_addon_products'][addon_product], 'total_tax', price_data.total_tax);
                            this.$set(this.cart[product_slack]['selected_addon_products'][addon_product], 'total_discount', price_data.total_discount);
                            
                            this.sub_total = this.sub_total+parseFloat(price_data.total_price);
                            this.tax_total = this.tax_total+parseFloat(price_data.total_tax);
                            this.discount_total = this.discount_total+parseFloat(price_data.total_discount);
                        }
                    }
                }
            }
            this.sub_total = this.sub_total.toFixed(2);
            
            this.store_level_total_discount_amount = this.calculate_store_level_discount(this.sub_total, this.store_level_total_discount_percentage);
            this.discount_total = parseFloat(this.store_level_total_discount_amount)+parseFloat(this.discount_total);
            this.discount_total = this.discount_total.toFixed(2);

            this.total_after_discount = parseFloat(this.sub_total)-parseFloat(this.discount_total);
            
            this.additional_discount_amount =  this.calculate_discount(this.total_after_discount, this.additional_discount_percentage);
            this.additional_discount_amount = parseFloat(this.additional_discount_amount);

            this.total_after_discount = parseFloat(this.total_after_discount)-parseFloat(this.additional_discount_amount);
            this.total_after_discount = this.total_after_discount.toFixed(2);

            this.store_level_total_tax_amount = this.calculate_store_level_tax(this.total_after_discount, this.store_level_total_tax_percentage);
            this.tax_total = parseFloat(this.store_level_total_tax_amount)+parseFloat(this.tax_total);
            this.tax_total = this.tax_total.toFixed(2);

            this.total = parseFloat(this.total_after_discount)+parseFloat(this.tax_total);
            this.total = this.total.toFixed(2);

            this.item_count = Object.keys(this.cart).length;
        },

        show_addon_groups(product_slack, base_product_slack, access_type = 'pos_screen'){
            this.addon_product_group_processing = true;
            this.show_addon_group_modal = true;
            this.product_slack = product_slack;
            this.base_product_slack = base_product_slack;
            this.selected_addon_slack_array = [];

            var formData = new FormData();
            var link = '/api/get_product_addon_groups';
            switch(access_type){
                case 'pos_screen':
                    formData.append("access_token", window.settings.access_token);
                break;
                case 'restaurant_menu':
                    formData.append("store_slack", this.store_slack);
                    link = "/api/get_customer_order_product_addon_groups"; 
                break;
            }
            
            formData.append("product_slack", base_product_slack);

            axios.post(link, formData).then((response) => {
                if(response.data.status_code == 200) {
                    this.choosed_addon_product_group = response.data.data;
                    if(typeof this.cart[product_slack]['selected_addon_products'] != 'undefined'){
                        var selected_addon_products_keys_array = Object.keys(this.cart[product_slack]['selected_addon_products'])
                        this.selected_addon_slack_array = selected_addon_products_keys_array;
                    }
                    this.addon_product_group_processing = false;
                }
            })
            .catch((error) => {
                console.log(error);
            });
        },

        add_addon_to_product(product_slack){
            var selected_addon_products = {};
            var addon_product_keys_array = this.selected_addon_slack_array.sort();
            if(addon_product_keys_array.length == 0){
                
                this.$set(this.cart[product_slack], 'selected_addon_products', selected_addon_products);
                var cart_item = this.cart[product_slack];

                this.$delete(this.cart, product_slack);
                this.$set(this.cart, this.base_product_slack, cart_item);

                this.update_prices();
                this.show_addon_group_modal = false;
            }else{

                var addon_product_keys = this.base_product_slack+'_'+addon_product_keys_array.join('_');

                for ( var key in this.choosed_addon_product_group) {
                    var addon_group_product_data = this.choosed_addon_product_group[key]['products'];
                    for ( var pkey in addon_group_product_data) {
                        
                        var product = addon_group_product_data[pkey]['product'];

                        if(this.selected_addon_slack_array.includes(product.slack)){

                            var tax_type = (product.tax_code != null)?product.tax_code.tax_type:'EXCLUSIVE';
                            var product_sale_price = product.sale_amount_excluding_tax;

                            var tax_percentage = (product.tax_code != null)?parseFloat(product.tax_code.total_tax_percentage):0;
                            var discount_percentage = (product.discount_code != null)?parseFloat(product.discount_code.discount_percentage):0;
                            var quantity = 1;
                            var total_price = parseFloat(quantity)*parseFloat(product_sale_price);
                            
                            var product_data = { 
                                "product_slack" : product.slack,
                                "product_code"  : product.product_code,
                                "name"          : product.name,
                                "price"         : product_sale_price,
                                "quantity"      : quantity,
                                "tax_percentage": (tax_percentage != null)?tax_percentage:0.00,
                                "discount_percentage": (discount_percentage != null)?discount_percentage:0.00,
                                "total_price"   : total_price.toFixed(2),
                                "sale_price_including_tax" : product.sale_amount_including_tax,
                                "tax_type"      : tax_type,
                            };

                            this.$set(selected_addon_products, product.slack, product_data);
                        }

                    }
                }
                var selected_addon_products_keys_array = Object.keys(selected_addon_products);

                if(selected_addon_products_keys_array.length>0){
                    var parent_product_key = product_slack.split('_');

                    var cart_item = this.cart[product_slack];
                    
                    this.$delete(this.cart, product_slack);
                    if(this.cart[addon_product_keys] != null){
                        var current_cart_item_quantity = (cart_item.quantity != null)?cart_item.quantity:1;
                        var quantity = parseFloat(this.cart[addon_product_keys].quantity)+parseFloat(current_cart_item_quantity);
                        this.$set(this.cart[addon_product_keys], 'quantity', quantity);
                    }else{
                        this.$set(this.cart, addon_product_keys, cart_item);
                        this.$set(this.cart[addon_product_keys], 'selected_addon_products', selected_addon_products);
                    }
                }

                var quantity = this.cart[addon_product_keys]['quantity'];
                for ( var qkey in this.cart[addon_product_keys]['selected_addon_products'] ) {
                    this.$set(this.cart[addon_product_keys]['selected_addon_products'][qkey], 'quantity', parseFloat(quantity));
                }
            }

            this.update_prices();
            this.show_addon_group_modal = false;
        },

        calculate_prices(quantity, product_data){
            var total_price = 0.00;
            var total_discount = 0.00;
            var total_after_discount = 0.00;
            var total_tax = 0.00;
            
            if(product_data.tax_type == 'INCLUSIVE'){
                var inclusive_total_price = parseFloat(quantity)*parseFloat(product_data.sale_price_including_tax);
                var inclusive_total_tax = this.calculate_tax(parseFloat(product_data.sale_price_including_tax), product_data.tax_percentage);
                total_price = parseFloat(inclusive_total_price)-parseFloat(inclusive_total_tax);
                total_discount = this.calculate_discount(total_price, product_data.discount_percentage);
                total_after_discount = parseFloat(total_price)-parseFloat(total_discount);
                total_tax = inclusive_total_tax;
            }else{
                total_price = parseFloat(quantity)*parseFloat(product_data.price);
                total_discount = this.calculate_discount(total_price, product_data.discount_percentage);
                total_after_discount = parseFloat(total_price)-parseFloat(total_discount);
                total_tax = parseFloat(quantity)*this.calculate_tax(parseFloat(product_data.price), product_data.tax_percentage);
            }
            return { 
                'total_price' : total_price,
                'total_discount' : total_discount,
                'total_after_discount' : total_after_discount,
                'total_tax' : total_tax,
            };
        },

        skip_addon_to_product(){
            this.selected_addon_slack_array = [];
            this.update_prices();
            this.show_addon_group_modal = false;
        },

        choose_addon_multiple_identifier(label, multiple_selection, event){
            if(multiple_selection == 0){
                var class_name = event.target.className;
                var id_value = event.target.id;
                var currenlty_checked = document.getElementById(id_value).checked;
                var currenlty_checked_value = event.target.value;

                var addon_checkboxes = document.getElementsByClassName(class_name);
                for (var i = 0; i < addon_checkboxes.length; i++) {
                    addon_checkboxes[i].checked = false;
                    const index = this.selected_addon_slack_array.indexOf(addon_checkboxes[i].value);
                    if (index > -1) {
                        this.selected_addon_slack_array.splice(index, 1);
                    }
                }
                if(currenlty_checked == false){
                    document.getElementById(id_value).checked = false;
                }else{
                    document.getElementById(id_value).checked = true;
                    this.selected_addon_slack_array.push(currenlty_checked_value);
                }
            }
        },
        
        choose_vairants(product){
            var product_variants = product.variants_by_options_pos;
            if(typeof product_variants != 'undefined' && product_variants != null && Object.keys(product_variants).length > 0){
                this.selected_variant_list = product_variants;
                this.selected_variant_keys = Object.keys(this.selected_variant_list);
                return true;
            }
            return false;
        },
    }
}