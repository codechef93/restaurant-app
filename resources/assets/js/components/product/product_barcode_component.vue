<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title">{{ $t("Product Label") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i>{{ $t("Generate Barcodes") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="supplier">{{ $t("Choose Supplier") }}</label>
                        <cool-select type="text" name="supplier" :placeholder="$t('Please choose supplier')"  autocomplete="off" v-model="supplier" :items="supplier_list" item-text="label" itemValue='slack' @search='load_suppliers'></cool-select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="barcode">{{ $t("Search and Choose Products") }}</label>
                        <cool-select type="text" v-model="search_product"  autocomplete="off" inputForTextClass="form-control form-control-custom" :items="product_list" item-text="label" itemValue='label' :resetSearchOnBlur="false" disable-filtering-by-search @search='load_products' @select='add_product_to_list' :placeholder="$t('Start Typing..')"></cool-select>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-5 mb-1">
                        <label for="name">{{ $t("Name & Description") }}</label>
                    </div>
                    <div class="form-group col-md-1 mb-1">
                        <label for="quantity">{{ $t("Quantity") }}</label>  
                    </div>
                </div>

                <div class="form-row mb-2" v-for="(product, index) in products" :key="index">
                    <div class="form-group col-md-5">
                        <input type="text" v-bind:name="'product.name_'+index" v-model="product.name" v-validate="'required|max:250'" data-vv-as="Name" class="form-control form-control-custom" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('product.name_'+index) }">{{ errors.first('product.name_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-1">
                        <input type="number" v-bind:name="'product.quantity_'+index" v-model="product.quantity" v-validate="'required|numeric|min_value:1'" data-vv-as="Quantity" class="form-control form-control-custom"  autocomplete="off" step="1" min="1">
                        <span v-bind:class="{ 'error' : errors.has('product.quantity_'+index) }">{{ errors.first('product.quantity_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-1" v-if="products.length>1">
                        <button type="button" class="btn btn-outline-danger" @click="remove_product(index)"><i class="fas fa-times"></i></button>
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
    
    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : '/api/generate_barcodes',

                supplier       : '',
                product_list   : [],
                search_product : '',
                supplier_list  : [],

                products: [],
                products_template : {
                    slack: '',
                    name : '',
                    quantity : '',
                },
            }
        },
        mounted() {
            console.log('Generate barcode page loaded');
        },
        created(){
            this.products.push(this.products_template);
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
                            formData.append("products", JSON.stringify(this.products));

                            axios.post(this.api_link, formData).then((response) => {
                                
                                this.show_modal = false;
                                this.processing = false;

                                if(response.data.status_code == 200) {
                                    if(typeof response.data.link != 'undefined' && response.data.link != ""){
                                        window.open(response.data.link, '_blank');
                                    }else{
                                        location.reload();
                                    }
                                }else{
                                    try{
                                        var error_json = JSON.parse(response.data.msg);
                                        this.loop_api_errors(error_json);
                                    }catch(err){
                                        this.server_errors = response.data.msg;
                                    }
                                    this.error_class = 'error';
                                    this.show_modal = false;
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

            remove_product(index) {
                this.products.splice(index, 1);
            },

            load_suppliers (keywords) {
                if(typeof keywords != 'undefined'){
                    if (keywords.length > 0) {

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("keywords", keywords);

                        axios.post('/api/load_suppliers', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.supplier_list = response.data.data;
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                }
            },

            load_products (keywords) {
                if(typeof keywords != 'undefined'){
                    if (keywords.length > 0) {

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("keywords", keywords);
                        formData.append("supplier", this.supplier);

                        axios.post('/api/load_product_for_po', formData).then((response) => {
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

            add_product_to_list(item) {
                if( item.product_slack != '' ){
                    var current_product = {
                        slack : item.product_slack,
                        name : item.label,
                        quantity : 1,
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
            }
        }
    }
</script>