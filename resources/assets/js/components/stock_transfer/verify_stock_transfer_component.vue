<template>
    <div class="row">
        <div class="col-md-12">
            
            <form class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title"><span class='text-muted'>{{ $t("Verify Stock Transfer") }}</span> #{{ stock_transfer.stock_transfer_reference }} </span>
                    </div>
                    <div class="">
                        <span v-bind:class="stock_transfer.status.color">{{ stock_transfer.status.label }}</span>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="from_store">{{ $t("Source Store") }}</label>
                        <span class="d-block">{{ stock_transfer.from_store_code }} - {{ stock_transfer.from_store_name }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="to_store">{{ $t("Destination Store") }}</label>
                        <span class="d-block">{{ stock_transfer.to_store_code }} - {{ stock_transfer.to_store_name }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="created_by">{{ $t("Created By") }}</label>
                        <p>{{ (stock_transfer.created_by == null)?'-':stock_transfer.created_by['fullname']+' ('+stock_transfer.created_by['user_code']+')' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="updated_by">{{ $t("Updated By") }}</label>
                        <p>{{ (stock_transfer.updated_by == null)?'-':stock_transfer.updated_by['fullname']+' ('+stock_transfer.updated_by['user_code']+')' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="created_on">{{ $t("Created On") }}</label>
                        <p>{{ stock_transfer.created_at_label }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="updated_on">{{ $t("Updated On") }}</label>
                        <p>{{ stock_transfer.updated_at_label }}</p>
                    </div>
                </div>

                <div class="mb-2">
                    <span class="text-subhead">Product Information</span>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-2 mb-1">
                        <label for="name">{{ $t("Source Store Product Code") }}</label>
                    </div>
                    <div class="form-group col-md-4 mb-1">
                        <label for="name">{{ $t("Name & Description") }}</label>
                    </div>
                    <div class="form-group col-md-2 mb-1">
                        <label for="quantity">{{ $t("Transferred Quantity") }}</label>  
                    </div>
                </div>

                <div class="form-row mb-2" v-for="(product, index) in products" :key="index">
                    <div class="form-group col-md-2">
                        <input type="text" v-model="product.product_code" class="form-control form-control-custom" autocomplete="off" readonly>
                    </div>
                    <div class="form-group col-md-4">
                        <input type="text" v-model="product.name" class="form-control form-control-custom" autocomplete="off" readonly>
                    </div>
                    <div class="form-group col-md-2">
                        <input type="number" v-model="product.quantity" class="form-control form-control-custom"  autocomplete="off" step="0.01" min="0" readonly>
                    </div>
                    <div class="form-group col-sm-4 col-12" v-if="product.status.constant == 'PENDING'">
                        <button type="button" class="btn btn-outline-primary" @click="merge_product_quantity(product.stock_transfer_slack, product.quantity, index)" v-bind:disabled="product.verify_processing == true">  <i class='fa fa-circle-notch fa-spin'  v-if="product.verify_processing == true"></i> {{ $t("Verify & Accept") }}</button>
                        <button type="button" class="btn btn-outline-danger" @click="rejected_stock_transfer_product(product.stock_transfer_slack, index)" v-bind:disabled="product.reject_processing == true">  <i class='fa fa-circle-notch fa-spin'  v-if="product.reject_processing == true"></i> {{ $t("Reject") }}</button>
                    </div>
                    <div class="form-group col-sm-4 col-12" v-else>
                        <span class="align-text-top" v-bind:class="product.status.color">{{ product.status.label }}</span>
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

        <modalcomponent v-if="show_verify_modal" v-on:close="show_verify_modal = false">
            <template v-slot:modal-header>
                Verify product
            </template>
            <template v-slot:modal-body>
                <p v-html="verify_server_errors" v-bind:class="[verify_error_class]"></p>
                <form data-vv-scope="verify_form">
                    <div class="form-group">
                        <label for="store_product_slack">{{ $t("Merge the stock with existing product in store inventory") }}</label>
                        <cool-select type="text" name="store_product_slack" v-validate="'required'" :placeholder="$t('Please choose the product')" autocomplete="off" v-model="store_product_slack" :items="product_list" item-text="label" itemValue='product_slack' @search='load_products' ref="store_product_slack">
                            <template #item="{ item }">
                                <div class='d-flex justify-content-start'>
                                <div>
                                    {{ item.product_code }} - {{ item.label }}
                                </div>
                                </div>
                            </template>
                        </cool-select>
                        <span v-bind:class="{ 'error' : errors.has('verify_form.store_product_slack') }">{{ errors.first('verify_form.store_product_slack') }}</span>
                    </div>
                    <div class="form-group">
                        <label for="accepted_quantity">{{ $t("Accepted Quantity") }}</label>
                        <input type="number" name='accepted_quantity' v-model="accepted_quantity" v-validate="{ required: true, decimal: true, min_value:0.01, max_value: validate_max_quantity }" class="form-control form-control-custom" :placeholder="$t('Please enter accepted quantity')"  autocomplete="off" step="1" min="0">
                        <span v-bind:class="{ 'error' : errors.has('verify_form.accepted_quantity') }">{{ errors.first('verify_form.accepted_quantity') }}</span>
                    </div>
                    <small class="form-text text-muted">The accepted quantity will be added to the selected product and the same quantity will be reduced from the source store inventory.</small>
                    <div class="form-group text-center">
                        <span class="d-block">Or</span>
                        <a v-bind:href="new_product_web_link" class="btn-label">Add as a New Product in Store</a>
                    </div>
                </form>
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
                verify_server_errors   : '',
                verify_error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                show_verify_modal : false,
                reject_api_link : '/api/reject_stock_transfer_product/',
                merge_stock_api_link : '/api/merge_product_stock',

                new_product_web_link_prefix : '/add_new_stock_transfer_product/',
                new_product_web_link : '',

                search_product   : '',
                product_list     : [],
                products         : [],
                products_template : {
                    slack: '',
                    product_code: '',
                    name : '',
                    quantity : '',
                    reject_processing : false,
                    verify_processing : false
                },

                validate_max_quantity :'',
                store_product_slack : '',
                accepted_quantity : '',

                stock_transfer_product_list : (this.stock_transfer_data != null)?this.stock_transfer_data.products:[],

                stock_transfer : this.stock_transfer_data
                
            }
        },
        props: {
            stock_transfer_data: [Array, Object],
            current_store: [Array, Object],
            to_stores: [Array, Object]
        },
        mounted() {
            console.log('Add stock transfer page loaded');
        },
        created() {
            this.update_product_list(this.stock_transfer_product_list);
        },
        methods: {

            update_product_list(stock_transfer_products) {
                if(stock_transfer_products != null && stock_transfer_products.length > 0){
                    this.products = [];
                    for (let i = 0; i < stock_transfer_products.length; i++) {
                        var individual_product = {
                            stock_transfer_slack: stock_transfer_products[i].slack,
                            slack: stock_transfer_products[i].product_slack,
                            name : stock_transfer_products[i].product_name,
                            quantity : stock_transfer_products[i].quantity,
                            product_code: stock_transfer_products[i].product_code,
                            reject_processing : false,
                            verify_processing : false,
                            status: stock_transfer_products[i].status,
                        };
                        this.products.push(individual_product);
                    }
                }else{
                    this.products = [];
                    this.products.push(this.products_template);
                }
            },

            rejected_stock_transfer_product(stock_transfer_slack, index){

                this.$off("submit");
                this.$off("close");
                this.show_modal = true;

                this.$on("submit",function () {       
                    
                    this.$validator.validateAll('confirmation_form').then((isValid) => {
                        if (isValid) {
                            this.processing = true;
                            this.$set(this.products[index], 'reject_processing', true);

                            var formData = new FormData();
                            formData.append("access_token", window.settings.access_token);

                            axios.post(this.reject_api_link + stock_transfer_slack, formData).then((response) => {

                                if(response.data.status_code == 200) {
                                    location.reload();
                                }else{
                                    this.show_modal = false;
                                    this.processing = false;
                                    this.$set(this.products[index], 'reject_processing', false);
                                    try{
                                        var error_json = JSON.parse(response.data.msg);
                                        this.loop_api_errors(error_json);
                                    }catch(err){
                                        this.server_errors = response.data.msg;
                                    }
                                    this.error_class = 'error';
                                }
                                this.$set(this.products[index], 'reject_processing', false);
                            })
                            .catch((error) => {
                                console.log(error);
                            });
                        }
                    });
                });

                this.$on("close",function () {
                    this.show_modal = false;
                });

            },

            merge_product_quantity(stock_transfer_slack, allowed_quantity, index){

                this.new_product_web_link = this.new_product_web_link_prefix+stock_transfer_slack;

                this.validate_max_quantity = allowed_quantity;   

                this.$off("submit");
                this.$off("close");
                this.show_verify_modal = true;

                this.$on("submit",function () {       
                    
                    this.$validator.validateAll('verify_form').then((isValid) => {
                        if (isValid) {
                            this.processing = true;
                            this.verify_processing = true;

                            var formData = new FormData();
                            formData.append("access_token", window.settings.access_token);
                            formData.append("stock_transfer_slack", stock_transfer_slack);
                            formData.append("store_product_slack", this.store_product_slack);
                            formData.append("accepted_quantity", this.accepted_quantity);

                            axios.post(this.merge_stock_api_link, formData).then((response) => {

                                if(response.data.status_code == 200) {
                                    location.reload();
                                }else{
                                    this.processing = false;
                                    try{
                                        var error_json = JSON.parse(response.data.msg);
                                        this.loop_api_errors(error_json);
                                    }catch(err){
                                        this.verify_server_errors = response.data.msg;
                                    }
                                    this.verify_error_class = 'error';
                                }
                                this.verify_processing = false;
                            })
                            .catch((error) => {
                                console.log(error);
                            });
                        }
                    });
                });

                this.$on("close",function () {
                    this.show_verify_modal = false;
                });
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