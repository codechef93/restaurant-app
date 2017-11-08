<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="stock_transfer_slack == ''">{{ $t("Add Stock Transfer") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Stock Transfer") }}</span>
                    </div>
                    <div class="" v-if="edit_stock_transfer_access == true">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="from_store">{{ $t("From Store") }}</label>
                        <span class="d-block">{{ current_store.store_code }} - {{ current_store.name }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_store">{{ $t("To Store") }}</label>
                        <select name="to_store" v-model="to_store" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose To Store..</option>
                            <option v-for="(to_store, index) in to_stores" v-bind:value="to_store.slack" v-bind:key="index">
                                {{ to_store.store_code }} - {{ to_store.name }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('to_store') }">{{ errors.first('to_store') }}</span>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="barcode">{{ $t("Search and Add Products") }}</label>
                        <cool-select type="text" v-model="search_product"  autocomplete="off" inputForTextClass="form-control form-control-custom" :items="product_list" item-text="label" itemValue='label' :resetSearchOnBlur="false" disable-filtering-by-search @search='load_products' @select='add_product_to_list'></cool-select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-5 mb-1">
                        <label for="name">{{ $t("Name & Description") }}</label>
                    </div>
                    <div class="form-group col-md-1 mb-1">
                        <label for="quantity">{{ $t("Quantity") }}</label>  
                    </div>
                </div>

                <div class="form-row mb-2" v-for="(product, index) in products" :key="index">
                    <div class="form-group col-md-5">
                        <input type="text" v-bind:name="'product.name_'+index" v-model="product.name" v-validate="'required|max:250'" data-vv-as="Name" class="form-control form-control-custom" autocomplete="off" readonly>
                        <span v-bind:class="{ 'error' : errors.has('product.name_'+index) }">{{ errors.first('product.name_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-1">
                        <input type="number" v-bind:name="'product.quantity_'+index" v-model="product.quantity" v-validate="'required|decimal|min_value:1'" data-vv-as="Quantity" class="form-control form-control-custom"  autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('product.quantity_'+index) }">{{ errors.first('product.quantity_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-1" v-if="products.length>1">
                        <button type="button" class="btn btn-outline-danger" @click="remove_product(index)"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="form-group col-md-4">
                        <span class="d-block mt-1" v-if="product.quantity_left != null &&product.quantity_left != ''">{{ $t("Total Stock Left") }}: {{ product.quantity_left }}</span>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="notes">{{ $t("Notes") }}</label>
                        <textarea name="notes" v-model="notes" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Enter notes')"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('notes') }">{{ errors.first('notes') }}</span>
                    </div>
                </div>

            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                {{ $t("Are you sure you want to proceed?") }}
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="$emit('close')">Cancel</button>
                <button type="button" class="btn btn-primary" @click="$emit('submit')" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Continue</button>
            </template>
        </modalcomponent>
        
    </div>
</template>

<script>
    'use strict';
    
    import { CoolSelect } from "vue-cool-select";
    import 'vue-cool-select/dist/themes/bootstrap.css';
    
    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.stock_transfer_data == null)?'/api/add_stock_transfer':'/api/update_stock_transfer/'+this.stock_transfer_data.slack,

                stock_transfer_slack    : (this.stock_transfer_data == null)?'':this.stock_transfer_data.slack,
                to_store         : (this.stock_transfer_data == null)?'':this.stock_transfer_data.to_store_data.slack,
                notes            : (this.stock_transfer_data == null)?'':this.stock_transfer_data.notes,

                search_product   : '',
                product_list     : [],
                products         : [],
                products_template : {
                    slack: '',
                    name : '',
                    quantity : '',
                    quantity_left : ''
                },

                stock_transfer_product_list : (this.stock_transfer_data != null)?this.stock_transfer_data.products:[],
                
            }
        },
        props: {
            stock_transfer_data: [Array, Object],
            current_store: [Array, Object],
            to_stores: [Array, Object],
            edit_stock_transfer_access: Boolean
        },
        mounted() {
            console.log('Add stock transfer page loaded');
        },
        created() {
            this.update_product_list(this.stock_transfer_product_list);
        },
        methods: {
            
            submit_form(){

                this.$off("submit");
                this.$off("close");

                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();

                            formData.append("access_token", window.settings.access_token);
                            formData.append("to_store", (this.to_store == null)?'':this.to_store);
                            formData.append("notes", (this.to_store == null)?'':this.notes);
                            formData.append("products", JSON.stringify(this.products));

                            axios.post(this.api_link, formData).then((response) => {
                                if(response.data.status_code == 200) {
                                    this.show_response_message(response.data.msg, 'SUCCESS');
                                
                                    setTimeout(function(){
                                        location.reload();
                                    }, 1000);
                                }else{
                                    this.show_modal = false;
                                    this.processing = false;
                                    try{
                                        var error_json = JSON.parse(response.data.msg);
                                        this.loop_api_errors(error_json);
                                    }catch(err){
                                        this.server_errors = response.data.msg;
                                    }
                                    this.error_class = 'error';
                                }
                            })
                            .catch((error) => {
                                console.log(error);
                            });
                        });
                        
                        this.$on("close",function () {
                            this.show_modal = false;
                        });
                    }
                });
            },

            update_product_list(stock_transfer_products) {
                if(stock_transfer_products != null && stock_transfer_products.length > 0){
                    this.products = [];
                    for (let i = 0; i < stock_transfer_products.length; i++) {
                        var individual_product = {
                            slack: stock_transfer_products[i].product_slack,
                            name : stock_transfer_products[i].product_name,
                            quantity : stock_transfer_products[i].quantity
                        };
                        this.products.push(individual_product);
                    }
                }else{
                    this.products = [];
                    this.products.push(this.products_template);
                }
            },

            remove_product(index) {
                this.products.splice(index, 1);
            },

            add_product_to_list(item) {
                if( item.product_slack != '' ){
                    var current_product = {
                        slack : item.product_slack,
                        name : item.label,
                        quantity : 1,
                        quantity_left : item.quantity
                    };
                }

                var item_found = false;
                for(var i = 0; i < this.products.length; i++){   
                    if(this.products[i].slack == item.product_slack){
                        this.products[i].quantity++;
                        item_found = true;
                    }
                }

                if(this.products[0].name == '' && this.products[0].quantity == ''){
                    this.$set(this.products, 0, current_product);
                }else{
                    if(item_found == false){
                        this.products.push(current_product);
                    }
                }
                this.product_list = [];
            },

            load_products (keywords) {
                if(typeof keywords != 'undefined'){
                    if (keywords.length > 0) {

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("keywords", keywords);

                        axios.post('/api/load_product_for_stock_transfer', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.product_list = response.data.data;
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                }
            },
        }
    }
</script>