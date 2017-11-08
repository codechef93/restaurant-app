<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> {{ $t("Invoice") }} #{{ invoice_basic.invoice_number }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="invoice_basic.status.color">{{ invoice_basic.status.label }}</span>
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4">

                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="ml-auto">

                    <button class="btn btn-outline-primary mr-1" v-if="printnode_enabled == true" v-on:click="printnode_print('INVOICE')" v-bind:disabled="printing_processing == true"> <i class='fa fa-circle-notch fa-spin' v-if="printing_processing == true"></i> {{ $t("Print") }}</button>

                    <a class="btn btn-outline-primary mr-1" v-bind:href="'/print_invoice/'+slack" target="_blank">{{ $t("PDF") }}</a>

                    <button type="submit" class="btn btn-danger mr-1" v-show="!block_delete_invoice.includes(invoice_basic.status.constant)" v-if="delete_invoice_access == true" v-on:click="delete_invoice()" v-bind:disabled="invoice_delete_processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="invoice_delete_processing == true"></i> {{ $t("Delete Invoice") }}</button>

                    <button type="submit" class="btn btn-outline-primary mr-1" v-on:click="show_payment_modal = true" v-show="!block_make_payment.includes(invoice_basic.status.constant)" v-if="make_payment_access == true"> {{ $t("Record Payment") }}</button>

                    <div class="dropdown d-inline" v-if="invoice_statuses != ''">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="invoice_action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $t("Change Status") }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="invoice_action">
                            <button class="dropdown-item" type="button" v-for="(invoice_status, key, index) in invoice_statuses" v-bind:value="invoice_status.value_constant" v-bind:key="index" v-on:click="change_invoice_status(invoice_status.value_constant)">Mark as {{ invoice_status.label }}</button>
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
                    <label for="invoice_reference">{{ $t("Reference Number") }}</label>
                    <p>{{ invoice_basic.invoice_reference }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="order_date">{{ $t("Invoice Date") }}</label>
                    <p>{{ invoice_basic.invoice_date }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="order_due_date">{{ $t("Invoice Due Date") }}</label>
                    <p>{{ invoice_basic.invoice_due_date }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (invoice_basic.created_by == null)?'-':invoice_basic.created_by['fullname']+' ('+invoice_basic.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (invoice_basic.updated_by == null)?'-':invoice_basic.updated_by['fullname']+' ('+invoice_basic.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ invoice_basic.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ invoice_basic.updated_at_label }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-3">
                
                <div class="mb-2">
                    <span class="text-subhead">Bill To Information</span>
                </div>
                <div class="form-row mb-2" v-show="invoice_basic.bill_to == 'SUPPLIER'">
                    <div class="form-group col-md-3">
                        <label for="bill_to">{{ $t("Bill To") }}</label>
                        <p>{{ invoice_basic.bill_to }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_code">{{ $t("Supplier Code") }}</label>
                        <p>{{ invoice_basic.bill_to_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_name">{{ $t("Supplier Name") }}</label>
                        <p>{{ invoice_basic.bill_to_name }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_email">{{ $t("Supplier Email") }}</label>
                        <p>{{ (invoice_basic.bill_to_email != null)?invoice_basic.bill_to_email:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_phone">{{ $t("Supplier Phone") }}</label>
                        <p>{{ (invoice_basic.bill_to_contact != null)?invoice_basic.bill_to_contact:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_address">{{ $t("Supplier Address") }}</label>
                        <p class='custom-pre'>{{ (invoice_basic.bill_to_address != null)?invoice_basic.bill_to_address:'-' }}</p>
                    </div>
                </div>
                <div class="form-row mb-2" v-show="invoice_basic.bill_to == 'CUSTOMER'">
                    <div class="form-group col-md-3">
                        <label for="bill_to">{{ $t("Bill To") }}</label>
                        <p>{{ invoice_basic.bill_to }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_name">{{ $t("Customer Name") }}</label>
                        <p>{{ (invoice_basic.bill_to_name != null)?invoice_basic.bill_to_name:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_email">{{ $t("Customer Email") }}</label>
                        <p>{{ (invoice_basic.bill_to_email != null)?invoice_basic.bill_to_email:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_phone">{{ $t("Customer Phone") }}</label>
                        <p>{{ (invoice_basic.bill_to_contact != null)?invoice_basic.bill_to_contact:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_address">{{ $t("Customer Address") }}</label>
                        <p class='custom-pre'>{{ (invoice_basic.bill_to_address != null)?invoice_basic.bill_to_address:'-' }}</p>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="currency_name">{{ $t("Currency") }}</label>
                        <p>{{ invoice_basic.currency_name }} ({{ invoice_basic.currency_code }})</p>
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
                            <td class="text-right">{{ invoice_basic.subtotal_excluding_tax }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total Discount") }}</td>
                            <td class="text-right">{{ invoice_basic.total_discount_amount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total After Discount") }}</td>
                            <td class="text-right">{{ invoice_basic.total_after_discount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total Tax") }}</td>
                            <td class="text-right">{{ invoice_basic.total_tax_amount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Shipping Charge") }}</td>
                            <td class="text-right">{{ invoice_basic.shipping_charge }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Packaging Charge") }}</td>
                            <td class="text-right">{{ invoice_basic.packing_charge }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total") }} (INCL Tax)</td>
                            <td class="text-right">{{ invoice_basic.total_order_amount }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <hr>
            
            <div class="mb-2">
                <span class="text-subhead">{{ $t("Terms") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-6">
                    <p class='custom-pre'>{{ (invoice_basic.terms != null)?invoice_basic.terms:'-' }}</p>
                </div>
            </div>

            <transactionlistcomponent :transaction_list="transactions"></transactionlistcomponent>
        </div>

        <modalcomponent v-if="show_payment_modal" v-on:close="show_payment_modal = false" :modal_width="'modal-container-md'">
            <template v-slot:modal-header>
                Record Payment
            </template>
            <template v-slot:modal-body>
                <addtransactionwidgetcomponent :transaction_type_data="transaction_type_data" :accounts="accounts" :payment_methods="payment_methods" :invoice_slack="slack" :bill_to_prop="'INVOICE'" :currency_codes="currency_codes" :default_transaction_type="default_transaction_type"></addtransactionwidgetcomponent>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="cancel_transaction">Cancel</button>
                <button type="button" class="btn btn-primary" @click="submit_transaction" v-show="show_payment_submit" v-bind:disabled="make_payment_processing == true"> <i class='fa fa-circle-notch fa-spin' v-if="make_payment_processing == true"></i> Continue</button>
            </template>
        </modalcomponent>

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
                show_payment_modal : false,
                make_payment_processing: false,
                invoice_delete_processing: false,
                show_payment_submit: true,
                printing_processing: false,
                
                change_invoice_link  : '/api/update_invoice_status/'+this.invoice_data.slack,
                delete_invoice_api_link : '/api/delete_invoice/'+this.invoice_data.slack,
                printnode_api_link : '/api/print_with_printnode',

                slack           : this.invoice_data.slack,
                invoice_basic   : this.invoice_data,
                products        : this.invoice_data.products,
                transactions    : this.invoice_data.transactions,

                tax_component_count  : (this.invoice_data.tax_option_data != null)?this.invoice_data.tax_option_data.component_count:0,
                tax_component_array  : (this.invoice_data.tax_option_data != null)?this.invoice_data.tax_option_data.component_array:[],
                table_colspan   : parseInt(8)+((this.invoice_data.tax_option_data != null)?parseInt(this.invoice_data.tax_option_data.component_count):1),

                block_make_payment : ['CANCELLED', 'PAID', 'VOID', 'WRITEOFF'],
                block_delete_invoice : ['PAID']
            }
        },
        props: {
            invoice_data: [Array, Object],
            invoice_statuses: [Array, Object],

            transaction_type_data: [Array, Object],
            accounts: [Array, Object],
            payment_methods: [Array, Object],
            currency_codes: [Array, Object],
            default_transaction_type: String,

            delete_invoice_access: Boolean,
            make_payment_access: Boolean,
            printnode_enabled: Boolean
        },
        mounted() {
            console.log('Invoice detail page loaded');
            event_bus.$on('cancel_transaction', this.hide_transaction_modal);
            event_bus.$on('start_processing', this.set_make_payment_processing_start);
            event_bus.$on('stop_processing', this.set_make_payment_processing_stop);
            event_bus.$on('invoice_paid', this.hide_payment_submit_button);
        },
        methods: {
            change_invoice_status(invoice_status){
                this.processing = true;
                var formData = new FormData();

                formData.append("access_token", window.settings.access_token);
                formData.append("status", invoice_status);

                axios.post(this.change_invoice_link, formData).then((response) => {
                    
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

            submit_transaction(){
                event_bus.$emit('submit_transaction');
            },

            cancel_transaction(){
                event_bus.$emit('cancel_transaction');
            },

            hide_transaction_modal(){
                this.$off("submit_transaction");
                this.$off("cancel_transaction");
                this.show_payment_modal = false;
            },

            set_make_payment_processing_start(){
                this.make_payment_processing = true;
            },

            set_make_payment_processing_stop(){
                this.make_payment_processing = false;
            },

            hide_payment_submit_button(){
                this.show_payment_submit = false;
            },

            delete_invoice(){

                this.$off("submit");
                this.$off("close");
                this.show_modal = true;

                this.$on("submit",function () {       
                    this.processing = true;
                    this.invoice_delete_processing = true;

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.delete_invoice_api_link, formData).then((response) => {

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
                        this.invoice_delete_processing = false;
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