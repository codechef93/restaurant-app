<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> {{ $t("Stock Return") }} #{{ stock_return_basic.return_number }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="stock_return_basic.status.color">{{ stock_return_basic.status.label }}</span>
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4">

                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="ml-auto">

                    <a class="btn btn-outline-primary mr-1" v-bind:href="'/print_stock_return/'+slack" target="_blank">{{ $t("Print") }}</a>

                    <button type="submit" class="btn btn-danger mr-1" v-if="delete_stock_return_access == true" v-on:click="delete_stock_return()" v-bind:disabled="stock_return_delete_processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="stock_return_delete_processing == true"></i> {{ $t("Delete Stock Return") }}</button>
                </div>

            </div>
            <hr>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="return_number">{{ $t("Return Number") }}</label>
                    <p>{{ stock_return_basic.return_number }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="return_date">{{ $t("Return Date") }}</label>
                    <p>{{ stock_return_basic.return_date }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (stock_return_basic.created_by == null)?'-':stock_return_basic.created_by['fullname']+' ('+stock_return_basic.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (stock_return_basic.updated_by == null)?'-':stock_return_basic.updated_by['fullname']+' ('+stock_return_basic.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ stock_return_basic.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ stock_return_basic.updated_at_label }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-3">
                
                <div class="mb-2">
                    <span class="text-subhead">Bill To Information</span>
                </div>
                <div class="form-row mb-2" v-show="stock_return_basic.bill_to == 'SUPPLIER'">
                    <div class="form-group col-md-3">
                        <label for="bill_to">{{ $t("Bill To") }}</label>
                        <p>{{ stock_return_basic.bill_to }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_code">{{ $t("Supplier Code") }}</label>
                        <p>{{ stock_return_basic.bill_to_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_name">{{ $t("Supplier Name") }}</label>
                        <p>{{ stock_return_basic.bill_to_name }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_email">{{ $t("Supplier Email") }}</label>
                        <p>{{ (stock_return_basic.bill_to_email != null)?stock_return_basic.bill_to_email:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_phone">{{ $t("Supplier Phone") }}</label>
                        <p>{{ (stock_return_basic.bill_to_contact != null)?stock_return_basic.bill_to_contact:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_address">{{ $t("Supplier Address") }}</label>
                        <p class='custom-pre'>{{ (stock_return_basic.bill_to_address != null)?stock_return_basic.bill_to_address:'-' }}</p>
                    </div>
                </div>
                <div class="form-row mb-2" v-show="stock_return_basic.bill_to == 'CUSTOMER'">
                    <div class="form-group col-md-3">
                        <label for="bill_to">{{ $t("Bill To") }}</label>
                        <p>{{ stock_return_basic.bill_to }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_name">{{ $t("Customer Name") }}</label>
                        <p>{{ (stock_return_basic.bill_to_name != null)?stock_return_basic.bill_to_name:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_email">{{ $t("Customer Email") }}</label>
                        <p>{{ (stock_return_basic.bill_to_email != null)?stock_return_basic.bill_to_email:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_phone">{{ $t("Customer Phone") }}</label>
                        <p>{{ (stock_return_basic.bill_to_contact != null)?stock_return_basic.bill_to_contact:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_address">{{ $t("Customer Address") }}</label>
                        <p class='custom-pre'>{{ (stock_return_basic.bill_to_address != null)?stock_return_basic.bill_to_address:'-' }}</p>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="currency_name">{{ $t("Currency") }}</label>
                        <p>{{ stock_return_basic.currency_name }} ({{ stock_return_basic.currency_code }})</p>
                    </div>
                </div>
                
            </div>
            <hr>

            <div class="mb-2">
                <span class="text-subhead">Product Information</span>
            </div>
            <div class="table-responsive mb-2">
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
                        <th scope="col" class="text-right" v-show="tax_component_count == 0">{{ $t("Tax %") }}</th>
                        <th scope="col" class="text-right" v-show="tax_component_count > 0" v-for="(tax_component_array_item, index) in tax_component_array" v-bind:key="index">{{tax_component_array_item}} %</th>
                        <th scope="col" class="text-right">{{ $t("Tax Amount") }}</th>
                        <th scope="col" class="text-right">{{ $t("Total") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(invoice_product, key, index) in products" v-bind:value="invoice_product.product_slack" v-bind:key="index">
                            <th scope="row">{{ key+1 }}</th>
                            <td>{{ (invoice_product.product_code)?invoice_product.product_code:'-' }}</td>
                            <td>{{ invoice_product.name }}</td>
                            <td class="text-right">{{ invoice_product.quantity }}</td>
                            <td class="text-right">{{ invoice_product.amount_excluding_tax }}</td>
                            <td class="text-right">{{ invoice_product.discount_percentage }}</td>
                            <td class="text-right">{{ invoice_product.discount_amount }}</td>
                            <td class="text-right" v-show="tax_component_count == 0">{{ invoice_product.tax_percentage }}</td>
                            <td class="text-right" v-show="tax_component_count > 0" v-for="(tax_component_array_item, index) in tax_component_array" v-bind:key="index">{{ invoice_product.tax_component_array[tax_component_array_item] }}</td>
                            <td class="text-right">{{ invoice_product.tax_amount }}</td>
                            <td class="text-right">{{ invoice_product.subtotal_amount_excluding_tax }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Sub Total") }} (EXCL Tax)</td>
                            <td class="text-right">{{ stock_return_basic.subtotal_excluding_tax }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total Discount") }}</td>
                            <td class="text-right">{{ stock_return_basic.total_discount_amount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total After Discount") }}</td>
                            <td class="text-right">{{ stock_return_basic.total_after_discount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total Tax") }}</td>
                            <td class="text-right">{{ stock_return_basic.total_tax_amount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Shipping Charge") }}</td>
                            <td class="text-right">{{ stock_return_basic.shipping_charge }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Packaging Charge") }}</td>
                            <td class="text-right">{{ stock_return_basic.packing_charge }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total") }} (INCL Tax)</td>
                            <td class="text-right">{{ stock_return_basic.total_order_amount }}</td>
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
                    <p class='custom-pre'>{{ (stock_return_basic.notes != null)?stock_return_basic.notes:'-' }}</p>
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
    import {event_bus} from '../../event_bus.js';

    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                show_modal      : false,
                stock_return_delete_processing: false,
                
                delete_stock_return_api_link : '/api/delete_stock_return/'+this.stock_return_data.slack,

                slack           : this.stock_return_data.slack,
                stock_return_basic   : this.stock_return_data,
                products        : this.stock_return_data.products,

                tax_component_count  : (this.stock_return_data.tax_option_data != null)?this.stock_return_data.tax_option_data.component_count:0,
                tax_component_array  : (this.stock_return_data.tax_option_data != null)?this.stock_return_data.tax_option_data.component_array:[],
                table_colspan   : parseInt(8)+((this.stock_return_data.tax_option_data != null)?parseInt(this.stock_return_data.tax_option_data.component_count):1),
            }
        },
        props: {
            stock_return_data: [Array, Object],
            delete_stock_return_access: Boolean,
        },
        mounted() {
            console.log('Stock retail detail page loaded');
        },
        methods: {
            delete_stock_return(){

                this.$off("submit");
                this.$off("close");
                this.show_modal = true;

                this.$on("submit",function () {       
                    this.processing = true;
                    this.stock_return_delete_processing = true;

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.delete_stock_return_api_link, formData).then((response) => {

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
                        this.stock_return_delete_processing = false;
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