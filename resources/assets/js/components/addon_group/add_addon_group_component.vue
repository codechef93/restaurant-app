<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="addon_group_slack == ''">{{ $t("Add Add-on Group") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Add-on Group") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="addon_group_name">{{ $t("Add-on Group Name") }}</label>
                        <input type="text" name="addon_group_name" v-model="addon_group_name" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter add-on group name')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('addon_group_name') }">{{ errors.first('addon_group_name') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">{{ $t("Status") }}</label>
                        <select name="status" v-model="status" v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('status') }">{{ errors.first('status') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-6">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="multiple_selection" v-model="multiple_selection">
                            <label class="custom-control-label" for="multiple_selection">{{ $t("Choose Multiple Products") }}</label>
                            <small class="form-text text-muted">If this option is enabled, multiple add-on products can be selected.</small>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Choose Add-on Products") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-4">
                        <label for="addon_product">{{ $t("Search and Add Products") }}</label>
                        <cool-select type="text" v-model="search_addon_products"  autocomplete="off" inputForTextClass="form-control form-control-custom" :items="addon_products_list" item-text="name" itemValue='name' :resetSearchOnBlur="false" disable-filtering-by-search @search='load_addon_products' @select='add_addon_product_to_list' :placeholder="$t('Start Typing..')">
                                <template #item="{ item }">
                                <div class='d-flex justify-content-start'>
                                <div>
                                    {{ item.product_code }} - {{ item.label }}
                                </div>
                                </div>
                            </template>
                        </cool-select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6 mb-1">
                        <label for="name">{{ $t("Name & Description") }}</label>
                    </div>
                    <div class="form-group col-md-2 mb-1">
                        <label for="sale_price">{{ $t("Sale Price") }}</label>  
                    </div>
                </div>

                <div class="form-row mb-2" v-for="(addon_product, index) in addon_products" :key="index">
                    <div class="form-group col-md-6">
                        <input type="text" v-bind:name="'addon_product.name_'+index" v-model="addon_product.name" v-validate="'max:250'" data-vv-as="Name" class="form-control form-control-custom" autocomplete="off" readonly="true">
                        <span v-bind:class="{ 'error' : errors.has('addon_product.name_'+index) }">{{ errors.first('addon_product.name_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-2">
                        <input type="number" v-bind:name="'addon_product.sale_price_'+index" v-model="addon_product.sale_price" v-validate="addon_product.name != ''?'required|decimal|min_value:0.01':''" data-vv-as="Sale Price" class="form-control form-control-custom" autocomplete="off" step="0.01" min="0" readonly="true">
                        <span v-bind:class="{ 'error' : errors.has('addon_product.sale_price_'+index) }">{{ errors.first('addon_product.sale_price_'+index) }}</span> 
                    </div>
                    <div class="form-group col-md-1" v-if="addon_products.length>1">
                        <button type="button" class="btn btn-outline-danger" @click="remove_addon_product(index)"><i class="fas fa-times"></i></button>
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
                api_link        : (this.addon_group_data == null)?'/api/add_addon_group':'/api/update_addon_group/'+this.addon_group_data.slack,

                multiple_selection : (this.addon_group_data == null)?false:(this.addon_group_data.multiple_selection != null)?((this.addon_group_data.multiple_selection == 1)?true:false):false,

                addon_group_slack  : (this.addon_group_data == null)?'':this.addon_group_data.slack,
                addon_group_name   : (this.addon_group_data == null)?'':this.addon_group_data.label,
                status          : (this.addon_group_data == null)?'':(this.addon_group_data.status == null)?'':this.addon_group_data.status.value,

                addon_products_list   : [],
                search_addon_products : '',
                addon_product_template : {
                    addon_product_slack: '',
                    name : '',
                    sale_price : ''
                },

                addon_products : (this.addon_group_data != null)?this.addon_group_data.products:[],
            }
        },
        props: {
            statuses: Array,
            addon_group_data: [Array, Object]
        },
        mounted() {
            console.log('Add add-on group page loaded');
        },
        created(){
            this.update_addon_product_list(this.addon_products);
        },
        methods: {
            load_addon_products (keywords) {
                if(typeof keywords != 'undefined'){
                    if (keywords.length > 0) {

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("keywords", keywords);

                        axios.post('/api/load_addon_group_product', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.addon_products_list = response.data.data;
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                }
            },

            add_addon_product_to_list(item) {
                if( item.product_slack != '' ){
                    var current_addon_product = {
                        addon_product_slack : item.product_slack,
                        name : item.label,
                        sale_price : item.sale_amount_excluding_tax
                    };
                }
                
                var item_found = false;
                for(var i = 0; i < this.addon_products.length; i++){   
                    if(this.addon_products[i].addon_product_slack == item.product_slack){
                        item_found = true;
                    }
                }

                if(this.addon_products[0].name == '' && this.addon_products[0].addon_product_slack == ''){
                    this.$set(this.addon_products, 0, current_addon_product);
                }else{
                    if(item_found == false){
                        this.addon_products.push(current_addon_product);
                    }
                }
                this.addon_products_list = [];
            },

            remove_addon_product(index) {
                this.addon_products.splice(index, 1);
            },

            update_addon_product_list(addon_products_list) {
                if(addon_products_list != null && addon_products_list.length > 0){
                    this.addon_products = [];
                    for (let i = 0; i < addon_products_list.length; i++) {
                        var individual_product = {
                            addon_product_slack: addon_products_list[i].product.slack,
                            name : addon_products_list[i].product.name,
                            sale_price: addon_products_list[i].product.sale_amount_excluding_tax,
                        };
                        this.addon_products.push(individual_product);
                    }
                }else{
                    this.addon_products = [];
                    this.addon_products.push(this.addon_product_template);
                }
            },

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
                            formData.append("addon_group_name", (this.addon_group_name == null)?'':this.addon_group_name);
                            formData.append("multiple_selection", (this.multiple_selection == true)?1:0);
                            formData.append("status", (this.status == null)?'':this.status);
                            formData.append("addon_products", (this.addon_products.length>0)?JSON.stringify(this.addon_products):[]);

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
            }
        }
    }
</script>