<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Stock Transfer") }}</span> #{{ stock_transfer.stock_transfer_reference }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="stock_transfer.status.color">{{ stock_transfer.status.label }}</span>
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4">

                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="ml-auto">
                    <button type="button" class="btn btn-danger mr-1" v-if="delete_stock_transfer_access == true" v-on:click="delete_stock_transfer()" v-bind:disabled="stock_transfer_delete_processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="stock_transfer_delete_processing == true"></i> {{ $t("Delete Stock Transfer") }}</button>
                </div>
            </div>
            <hr>

            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="stock_transfer_reference">{{ $t("Stock Transfer Reference") }}</label>
                    <p>{{ stock_transfer.stock_transfer_reference }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="from_store_code">{{ $t("From Store Code") }}</label>
                    <p>{{ stock_transfer.from_store_code }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="from_store">{{ $t("From Store") }}</label>
                    <p>{{ stock_transfer.from_store_name }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="to_store_code">{{ $t("To Store Code") }}</label>
                    <p>{{ stock_transfer.to_store_code }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="to_store">{{ $t("To Store") }}</label>
                    <p>{{ stock_transfer.to_store_name }}</p>
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
            <hr>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Product Information") }}</span>
            </div>
            <div class="table-responsive mb-2">
                <table class="table table-striped table-bordered display nowrap text-nowrap w-100">
                    <thead>
                        <tr>
                            <th scope="col" colspan="2"></th>
                            <th scope="col" colspan="3">{{ $t("Transferred Records") }}</th>
                            <th scope="col" colspan="4">{{ $t("Accept & Inward Records") }}</th>
                            <th scope="col" colspan="4"></th>
                        </tr>
                    </thead>
                    <thead>
                        <tr>
                        
                            <th scope="col">#</th>
                            <th scope="col">{{ $t("Status") }}</th>

                            <th scope="col">{{ $t("Product Code") }}</th>
                            <th scope="col">{{ $t("Name & Description") }}</th>
                            <th scope="col" class="text-right">{{ $t("Quantity") }}</th>

                            <th scope="col" class="text-right">{{ $t("Accepted Quantity") }}</th>
                            <th scope="col">{{ $t("Inward Type") }}</th>
                            <th scope="col">{{ $t("Product Code") }}</th>
                            <th scope="col">{{ $t("Name & Description") }}</th>
                            
                            <th scope="col">{{ $t("Created By") }}</th>
                            <th scope="col">{{ $t("Updated By") }}</th>
                            <th scope="col">{{ $t("Created On") }}</th>
                            <th scope="col">{{ $t("Updated On") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(stock_transfer_product, key, index) in products" v-bind:value="stock_transfer_product.product_slack" v-bind:key="index">
                            <th scope="row">{{ key+1 }}</th>
                            <td><span v-bind:class="stock_transfer_product.status.color">{{ stock_transfer_product.status.label }}</span></td>
                            <td>{{ (stock_transfer_product.product_code)?stock_transfer_product.product_code:'-' }}</td>
                            <td>{{ stock_transfer_product.product_name }}</td>
                            <td class="text-right">{{ stock_transfer_product.quantity }}</td>

                            <td class="text-right">{{ (stock_transfer_product.accepted_quantity)?stock_transfer_product.accepted_quantity:'-' }}</td>
                            <td>{{ (stock_transfer_product.inward_type)?((stock_transfer_product.inward_type == 'MERGE')?'Merged with Existing Product':'Created a New Product'):'-' }}</td>
                            <td>{{ (stock_transfer_product.destination_product_code)?stock_transfer_product.destination_product_code:'-' }}</td>
                            <td>{{ (stock_transfer_product.destination_product_name)?stock_transfer_product.destination_product_name:'-' }}</td>
                            
                            <td>{{ (stock_transfer_product.created_by == null)?'-':stock_transfer_product.created_by['fullname']+' ('+stock_transfer_product.created_by['user_code']+')' }}</td>
                            <td>{{ (stock_transfer_product.updated_by == null)?'-':stock_transfer_product.updated_by['fullname']+' ('+stock_transfer_product.updated_by['user_code']+')' }}</td>
                            <td>{{ (stock_transfer_product.created_at_label)?stock_transfer_product.created_at_label:'-' }}</td>
                            <td>{{ (stock_transfer_product.updated_at_label)?stock_transfer_product.updated_at_label:'-' }}</td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
            <hr>
            
            <div class="mb-2">
                <span class="text-subhead">{{ $t("Notes") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-6">
                    <p class=''>{{ (stock_transfer.notes != null)?stock_transfer.notes:'-' }}</p>
                </div>
            </div>

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
    
    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                show_modal      : false,
                show_change_status_modal: false,

                stock_transfer_delete_processing: false, 
                delete_stock_transfer_api_link: '/api/delete_stock_transfer/'+this.stock_transfer_data.slack,

                stock_transfer_status_processing: false, 
                change_stock_transfer_status_api_link: '/api/change_stock_transfer_status/'+this.stock_transfer_data.slack,

                stock_transfer: this.stock_transfer_data,
                products      : this.stock_transfer_data.products,

                stock_transfer_status : '',
                reason: ''
            }
        },
        props: {
            stock_transfer_data: [Array, Object],
            store: [Array, Object],
            stock_transfer_statuses:[Array, Object],
            delete_stock_transfer_access: Boolean,
        },
        mounted() {
            console.log('Stock transfer detail page loaded');
        },
        methods: {
           delete_stock_transfer(){

                this.$off("submit");
                this.$off("close");
                this.show_modal = true;

                this.$on("submit",function () {       
                    this.processing = true;
                    this.stock_transfer_delete_processing = true;

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.delete_stock_transfer_api_link, formData).then((response) => {

                        if(response.data.status_code == 200) {
                            if(typeof response.data.link != 'undefined' && response.data.link != ""){
                                window.location.href = response.data.link;
                            }else{
                                location.reload();
                            }
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
                        this.stock_transfer_delete_processing = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                });

                this.$on("close",function () {
                    this.show_modal = false;
                });
            },
        }
    }
</script>