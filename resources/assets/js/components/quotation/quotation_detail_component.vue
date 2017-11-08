<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> {{ $t("Quotation") }} #{{ quotation_basic.quotation_number }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="quotation_basic.status.color">{{ quotation_basic.status.label }}</span>
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4" v-if="quotation_statuses != ''">

                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="ml-auto">
                    
                    <button class="btn btn-outline-primary mr-1" v-if="printnode_enabled == true" v-on:click="printnode_print('QUOTATION')" v-bind:disabled="printing_processing == true"> <i class='fa fa-circle-notch fa-spin' v-if="printing_processing == true"></i> {{ $t("Print") }}</button>

                    <a class="btn btn-outline-primary mr-1" v-bind:href="'/print_quotation/'+slack" target="_blank">{{ $t("PDF") }}</a>

                    <button type="submit" class="btn btn-danger mr-1" v-show="!block_delete_quotation.includes(quotation_basic.status.constant)" v-if="delete_quotation_access == true" v-on:click="delete_quotation()" v-bind:disabled="quotation_delete_processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="quotation_delete_processing == true"></i> {{ $t("Delete Quotation") }}</button>

                    <div class="dropdown d-inline">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="quotation_action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $t("Change Status") }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="quotation_action">
                            <button class="dropdown-item" type="button" v-for="(quotation_status, key, index) in quotation_statuses" v-bind:value="quotation_status.value_constant" v-bind:key="index" v-on:click="change_quotation_status(quotation_status.value_constant)">Mark as {{ quotation_status.label }}</button>
                        </div>
                    </div>
                </div>

            </div>
            <hr>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="quotation_reference">{{ $t("Reference Number") }}</label>
                    <p>{{ quotation_basic.quotation_reference }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="order_date">{{ $t("Quotation Date") }}</label>
                    <p>{{ quotation_basic.quotation_date }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="order_due_date">{{ $t("Quotation Due Date") }}</label>
                    <p>{{ quotation_basic.quotation_due_date }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (quotation_basic.created_by == null)?'-':quotation_basic.created_by['fullname']+' ('+quotation_basic.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (quotation_basic.updated_by == null)?'-':quotation_basic.updated_by['fullname']+' ('+quotation_basic.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ quotation_basic.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ quotation_basic.updated_at_label }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-3">
                
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Bill To Information") }}</span>
                </div>
                <div class="form-row mb-2" v-show="quotation_basic.bill_to == 'SUPPLIER'">
                    <div class="form-group col-md-3">
                        <label for="bill_to">{{ $t("Bill To") }}</label>
                        <p>{{ quotation_basic.bill_to }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_code">{{ $t("Supplier Code") }}</label>
                        <p>{{ quotation_basic.bill_to_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_name">{{ $t("Supplier Name") }}</label>
                        <p>{{ quotation_basic.bill_to_name }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_email">{{ $t("Supplier Email") }}</label>
                        <p>{{ (quotation_basic.bill_to_email != null)?quotation_basic.bill_to_email:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_phone">{{ $t("Supplier Phone") }}</label>
                        <p>{{ (quotation_basic.bill_to_contact != null)?quotation_basic.bill_to_contact:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_address">{{ $t("Supplier Address") }}</label>
                        <p class='custom-pre'>{{ (quotation_basic.bill_to_address != null)?quotation_basic.bill_to_address:'-' }}</p>
                    </div>
                </div>
                <div class="form-row mb-2" v-show="quotation_basic.bill_to == 'CUSTOMER'">
                    <div class="form-group col-md-3">
                        <label for="bill_to">{{ $t("Bill To") }}</label>
                        <p>{{ quotation_basic.bill_to }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_name">{{ $t("Customer Name") }}</label>
                        <p>{{ (quotation_basic.bill_to_name != null)?quotation_basic.bill_to_name:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_email">{{ $t("Customer Email") }}</label>
                        <p>{{ (quotation_basic.bill_to_email != null)?quotation_basic.bill_to_email:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_phone">{{ $t("Customer Phone") }}</label>
                        <p>{{ (quotation_basic.bill_to_contact != null)?quotation_basic.bill_to_contact:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_address">{{ $t("Customer Address") }}</label>
                        <p class='custom-pre'>{{ (quotation_basic.bill_to_address != null)?quotation_basic.bill_to_address:'-' }}</p>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="currency_name">{{ $t("Currency") }}</label>
                        <p>{{ quotation_basic.currency_name }} ({{ quotation_basic.currency_code }})</p>
                    </div>
                </div>
                
            </div>
            <hr>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Product Information") }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-striped display nowrap text-nowrap w-100">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ $t("Product Code") }}</th>
                        <th scope="col">{{ $t("Product") }}</th>
                        <th scope="col" class="text-right">{{ $t("Quantity") }}</th>
                        <th scope="col" class="text-right">{{ $t("Price") }} (EXCL Tax)</th>
                        <th scope="col" class="text-right">{{ $t("Discount %") }}</th>
                        <th scope="col" class="text-right">{{ $t("Discount Amount") }}</th>
                        <th scope="col" class="text-right" v-show="tax_component_count == 0">Tax %</th>
                        <th scope="col" class="text-right" v-show="tax_component_count > 0" v-for="(tax_component_array_item, index) in tax_component_array" v-bind:key="index">{{tax_component_array_item}} %</th>
                        <th scope="col" class="text-right">{{ $t("Tax Amount") }}</th>
                        <th scope="col" class="text-right">{{ $t("Total") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(quotation_product, key, index) in products" v-bind:value="quotation_product.product_slack" v-bind:key="index">
                            <th scope="row">{{ key+1 }}</th>
                            <td>{{ (quotation_product.product_code)?quotation_product.product_code:'-' }}</td>
                            <td>{{ quotation_product.name }}</td>
                            <td class="text-right">{{ quotation_product.quantity }}</td>
                            <td class="text-right">{{ quotation_product.amount_excluding_tax }}</td>
                            <td class="text-right">{{ quotation_product.discount_percentage }}</td>
                            <td class="text-right">{{ quotation_product.discount_amount }}</td>
                            <td class="text-right" v-show="tax_component_count == 0">{{ quotation_product.tax_percentage }}</td>
                            <td class="text-right" v-show="tax_component_count > 0" v-for="(tax_component_array_item, index) in tax_component_array" v-bind:key="index">{{ quotation_product.tax_component_array[tax_component_array_item] }}</td>
                            <td class="text-right" >{{ quotation_product.tax_amount }}</td>
                            <td class="text-right">{{ quotation_product.subtotal_amount_excluding_tax }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Sub Total") }} (EXCL Tax)</td>
                            <td class="text-right">{{ quotation_basic.subtotal_excluding_tax }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total Discount") }}</td>
                            <td class="text-right">{{ quotation_basic.total_discount_amount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total After Discount") }}</td>
                            <td class="text-right">{{ quotation_basic.total_after_discount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total Tax") }}</td>
                            <td class="text-right">{{ quotation_basic.total_tax_amount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Shipping Charge") }}</td>
                            <td class="text-right">{{ quotation_basic.shipping_charge }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Packaging Charge") }}</td>
                            <td class="text-right">{{ quotation_basic.packing_charge }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total") }} (INCL Tax)</td>
                            <td class="text-right">{{ quotation_basic.total_order_amount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Notes") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-12">
                    <p class='custom-pre'>{{ (quotation_basic.notes != null)?quotation_basic.notes:'-' }}</p>
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
                quotation_delete_processing : false,
                printing_processing: false,
                
                change_quotation_link  : '/api/update_quotation_status/'+this.quotation_data.slack,
                delete_quotation_api_link : '/api/delete_quotation/'+this.quotation_data.slack,
                printnode_api_link : '/api/print_with_printnode',

                slack           : this.quotation_data.slack,
                quotation_basic : this.quotation_data,
                products        : this.quotation_data.products,
                
                tax_component_count  : (this.quotation_data.tax_option_data != null)?this.quotation_data.tax_option_data.component_count:0,
                tax_component_array  : (this.quotation_data.tax_option_data != null)?this.quotation_data.tax_option_data.component_array:[],
                table_colspan   : parseInt(8)+((this.quotation_data.tax_option_data != null)?parseInt(this.quotation_data.tax_option_data.component_count):1),

                block_delete_quotation : ['ACCEPTED']
            }
        },
        props: {
            quotation_data: [Array, Object],
            quotation_statuses: Array,
            delete_quotation_access: Boolean,
            printnode_enabled: Boolean
        },
        mounted() {
            console.log('Quotation detail page loaded');
        },
        methods: {
            change_quotation_status(quotation_status){
                this.processing = true;
                var formData = new FormData();

                formData.append("access_token", window.settings.access_token);
                formData.append("status", quotation_status);

                axios.post(this.change_quotation_link, formData).then((response) => {
                    
                    this.show_modal = false;
                    this.processing = false;

                    if(response.data.status_code == 200) {
                        location.reload();
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
            },

            delete_quotation(){

                this.$off("submit");
                this.$off("close");
                this.show_modal = true;

                this.$on("submit",function () {       
                    this.processing = true;
                    this.quotation_delete_processing = true;

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.delete_quotation_api_link, formData).then((response) => {

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
                        this.quotation_delete_processing = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                });

                this.$on("close",function () {
                    this.show_modal = false;
                });
            },

            printnode_print(type){

                this.printing_processing = true;

                var formData = new FormData();

                formData.append("access_token", window.settings.access_token);
                formData.append("print_type", type);
                formData.append("slack", this.slack);

                axios.post(this.printnode_api_link, formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.show_response_message(response.data.msg + ' (Job ID: '+response.data.data+')', 'SUCCESS');
                        this.printing_processing = false;
                    }else{
                        this.printing_processing = false;
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
            }
        }
    }
</script>