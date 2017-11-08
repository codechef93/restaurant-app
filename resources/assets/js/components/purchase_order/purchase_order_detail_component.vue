<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> {{ $t("Purchase Order") }} #{{ po_basic.po_number }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="po_basic.status.color">{{ po_basic.status.label }}</span>
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4" v-if="po_statuses != ''">

                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="ml-auto">
                    
                    <button class="btn btn-outline-primary mr-1" v-if="printnode_enabled == true" v-on:click="printnode_print('PURCHASE_ORDER')" v-bind:disabled="printing_processing == true"> <i class='fa fa-circle-notch fa-spin' v-if="printing_processing == true"></i> {{ $t("Print") }}</button>

                    <a class="btn btn-outline-primary mr-1" v-bind:href="'/print_purchase_order/'+slack" target="_blank">{{ $t("PDF") }}</a>

                    <button type="button" class="btn btn-outline-primary mr-1"  v-if="create_invoice_from_po_access == true" v-on:click="generate_invoice()" v-bind:disabled="generate_invoice_processing == true"> <i class='fa fa-circle-notch fa-spin' v-if="generate_invoice_processing == true"></i> {{ $t("Generate Invoice") }}</button>

                    <button type="submit" class="btn btn-danger mr-1" v-show="!block_delete_po.includes(po_basic.status.constant)" v-if="delete_po_access == true" v-on:click="delete_po()" v-bind:disabled="po_delete_processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="po_delete_processing == true"></i> {{ $t("Delete Purchase Order") }}</button>

                    <div class="dropdown d-inline">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="po_action" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $t("Change Status") }}
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="po_action">
                            <button class="dropdown-item" type="button" v-for="(po_status, key, index) in po_statuses" v-bind:value="po_status.value_constant" v-bind:key="index" v-on:click="change_po_status(po_status.value_constant)">Mark as {{ po_status.label }}</button>
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
                    <label for="po_reference">{{ $t("Reference Number") }}</label>
                    <p>{{ po_basic.po_reference }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="order_date">{{ $t("Order Date") }}</label>
                    <p>{{ po_basic.order_date }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="order_due_date">{{ $t("Order Due Date") }}</label>
                    <p>{{ po_basic.order_due_date }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (po_basic.created_by == null)?'-':po_basic.created_by['fullname']+' ('+po_basic.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (po_basic.updated_by == null)?'-':po_basic.updated_by['fullname']+' ('+po_basic.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ po_basic.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ po_basic.updated_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Update Product Stock") }}</label>
                    <p>{{ update_stock }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-3">
                
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Supplier Information") }}</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="supplier_name">{{ $t("Supplier Name") }}</label>
                        <p>{{ po_basic.supplier_name }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier_address">{{ $t("Address") }}</label>
                        <p>{{ po_basic.supplier_address }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="currency_name">{{ $t("Currency") }}</label>
                        <p>{{ po_basic.currency_name }} ({{ po_basic.currency_code }})</p>
                    </div>
                </div>
                
            </div>

            <div v-if="po_basic.invoices != null && po_basic.invoices.length != 0">
                <hr>
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Invoices") }}</span>
                </div>
                <div class="d-flex flex-wrap mb-4">
                    <span v-for="(invoice, key, index) in po_basic.invoices" v-bind:value="invoice.slack" v-bind:key="index">
                        <span v-if="invoice.detail_link != ''"><a v-bind:href="invoice.detail_link" target="_blank">{{ invoice.invoice_number }}</a></span><span v-else>{{ invoice.invoice_number }}</span> &nbsp;&middot;&nbsp;
                    </span>
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
                        <tr v-for="(po_product, key, index) in products" v-bind:value="po_product.product_slack" v-bind:key="index">
                            <th scope="row">{{ key+1 }}</th>
                            <td>{{ (po_product.product_code)?po_product.product_code:'-' }}</td>
                            <td>{{ po_product.name }}</td>
                            <td class="text-right">
                                {{ po_product.quantity }}
                                <span v-if="po_product.stock_update == 1" class="d-block small"><i class="fas fa-check text-success"></i> Added to Stock</span>
                            </td>
                            <td class="text-right">{{ po_product.amount_excluding_tax }}</td>
                            <td class="text-right">{{ po_product.discount_percentage }}</td>
                            <td class="text-right">{{ po_product.discount_amount }}</td>
                            <td class="text-right" v-show="tax_component_count == 0">{{ po_product.tax_percentage }}</td>
                            <td class="text-right" v-show="tax_component_count > 0" v-for="(tax_component_array_item, index) in tax_component_array" v-bind:key="index">{{ po_product.tax_component_array[tax_component_array_item] }}</td>
                            <td class="text-right">{{ po_product.tax_amount }}</td>
                            <td class="text-right">{{ po_product.subtotal_amount_excluding_tax }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Sub Total") }} (EXCL Tax)</td>
                            <td class="text-right">{{ po_basic.subtotal_excluding_tax }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total Discount") }}</td>
                            <td class="text-right">{{ po_basic.total_discount_amount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total After Discount") }}</td>
                            <td class="text-right">{{ po_basic.total_after_discount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total Tax") }}</td>
                            <td class="text-right">{{ po_basic.total_tax_amount }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Shipping Charge") }}</td>
                            <td class="text-right">{{ po_basic.shipping_charge }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Packaging Charge") }}</td>
                            <td class="text-right">{{ po_basic.packing_charge }}</td>
                        </tr>
                        <tr>
                            <td v-bind:colspan="table_colspan" class="text-right">{{ $t("Total") }} (INCL Tax)</td>
                            <td class="text-right">{{ po_basic.total_order_amount }}</td>
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
                    <p class='custom-pre'>{{ (po_basic.terms != null)?po_basic.terms:'-' }}</p>
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

        <modalcomponent v-if="show_generate_invoice_modal" v-on:close="show_generate_invoice_modal = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                <p>Invoice will be created from this purchase order.</p>
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
                show_generate_invoice_modal: false,
                po_delete_processing : false,
                generate_invoice_processing: false,
                printing_processing: false,
                
                change_po_link  : '/api/update_po_status/'+this.purchase_order_data.slack,
                delete_po_api_link : '/api/delete_purchase_order/'+this.purchase_order_data.slack,
                generate_invoice_from_po_api_link : '/api/generate_invoice_from_po/'+this.purchase_order_data.slack,
                printnode_api_link : '/api/print_with_printnode',

                slack           : this.purchase_order_data.slack,
                po_basic        : this.purchase_order_data,
                products        : this.purchase_order_data.products,

                tax_component_count  : (this.purchase_order_data.tax_option_data != null)?this.purchase_order_data.tax_option_data.component_count:0,
                tax_component_array  : (this.purchase_order_data.tax_option_data != null)?this.purchase_order_data.tax_option_data.component_array:[],
                table_colspan   : parseInt(8)+((this.purchase_order_data.tax_option_data != null)?parseInt(this.purchase_order_data.tax_option_data.component_count):1),
                
                update_stock    : (this.purchase_order_data.update_stock == 1)?'Yes':'No',
                block_delete_po : ['CLOSED', 'RELEASED_TO_SUPPLIER']
            }
        },
        props: {
            purchase_order_data: [Array, Object],
            po_statuses: Array,
            delete_po_access: Boolean,
            create_invoice_from_po_access: Boolean,
            printnode_enabled: Boolean
        },
        mounted() {
            console.log('PO detail page loaded');
        },
        methods: {
            change_po_status(po_status){
                this.processing = true;
                var formData = new FormData();

                formData.append("access_token", window.settings.access_token);
                formData.append("status", po_status);

                axios.post(this.change_po_link, formData).then((response) => {
                    
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

            delete_po(){

                this.$off("submit");
                this.$off("close");
                this.show_modal = true;

                this.$on("submit",function () {       
                    this.processing = true;
                    this.po_delete_processing = true;

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.delete_po_api_link, formData).then((response) => {

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
                        this.po_delete_processing = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                });

                this.$on("close",function () {
                    this.show_modal = false;
                });
            },

            generate_invoice(){
                this.$off("submit");
                this.$off("close");
                this.show_generate_invoice_modal = true;

                this.$on("submit",function () {       
                    this.processing = true;
                    this.generate_invoice_processing = true;

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.generate_invoice_from_po_api_link, formData).then((response) => {

                        if(response.data.status_code == 200) {
                            this.show_response_message(response.data.msg, 'SUCCESS');
                            if(typeof response.data.link != 'undefined' && response.data.link != ""){

                                if(response.data.new_tab == true){
                                    window.open(response.data.link, '_blank');
                                }else{
                                    window.location.href = response.data.link;
                                }

                                setTimeout(function(){
                                    window.location.reload();
                                }, 1000);
                            }
                        }else{
                            this.show_generate_invoice_modal = false;
                            this.processing = false;
                            try{
                                var error_json = JSON.parse(response.data.msg);
                                this.loop_api_errors(error_json);
                            }catch(err){
                                this.server_errors = response.data.msg;
                            }
                            this.error_class = 'error';
                        }
                        this.generate_invoice_processing = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                });

                this.$on("close",function () {
                    this.show_generate_invoice_modal = false;
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