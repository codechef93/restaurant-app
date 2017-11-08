<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="filter_requests" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title">{{ $t("Search") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>
                
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <select name="search_item" v-model="search_item" v-validate="'required'" class="form-control form-control-lg custom-select">
                            <option v-for="(search_item, index) in search_items" v-bind:value="search_item" v-bind:key="index">
                               {{ search_item }}
                            </option>
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <input type="text" v-model="search_query" class="form-control" :placeholder="$t('Search by keyword')" autocomplete="off">
                    </div>
                    <div class="form-group col-md-3">
                        <button type="submit" class="btn btn-primary" :disabled="search_query.length == 0"> {{ $t("Search") }}</button>
                    </div>
                </div>

                
            </form>
                
            <div v-if="orders.length>0">
                <div class="card filter-card mb-2" v-for="(order, index) in orders" v-bind:value="order.slack" v-bind:key="index">
                    <div class="card-body p-3">
                        <div class="form-row">
                            <div class="form-group col-md-3">
                                <label for="order_number">{{ $t("Order Number") }}</label>
                                <p class="m-0">{{ order.order_number }}&nbsp;&nbsp;<a v-if="order.detail_link != ''" v-bind:href="order.detail_link" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>
                            </div> 
                            <div class="form-group col-md-3">
                                <label for="email">{{ $t("Email") }}</label>
                                <p class="m-0">{{ order.customer_email }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="email">{{ $t("Phone") }}</label>
                                <p class="m-0">{{ order.customer_phone }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="email">{{ $t("Amount") }}</label>
                                <p class="m-0">{{ order.total_order_amount }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="created_on">{{ $t("Created On") }}</label>
                                <p class="m-0">{{ order.created_at_label }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="status">{{ $t("Status") }}</label>
                                <p class="m-0"><span v-bind:class="order.status.color">{{ order.status.label }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="customers.length>0">
                <div class="card filter-card mb-2" v-for="(customer, index) in customers" v-bind:value="customer.slack" v-bind:key="index">
                    <div class="card-body p-3">
                        <div class="form-row mb-2">
                            <div class="form-group col-md-3">
                                <label for="fullname">{{ $t("Fullname") }}</label>
                                <p class="m-0">{{ customer.name }}&nbsp;&nbsp;<a v-if="customer.detail_link != ''" v-bind:href="customer.detail_link" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="email">{{ $t("Email") }}</label>
                                <p class="m-0">{{ customer.email }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="phone">{{ $t("Phone") }}</label>
                                <p class="m-0">{{ customer.phone }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="created_on">{{ $t("Created On") }}</label>
                                <p class="m-0">{{ customer.created_at_label }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="status">{{ $t("Status") }}</label>
                                <p class="m-0"><span v-bind:class="customer.status.color">{{ customer.status.label }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="transactions.length>0">
                <div class="card filter-card mb-2" v-for="(transaction_item, index) in transactions" v-bind:value="transaction_item.slack" v-bind:key="index">
                    <div class="card-body p-3">
                        <div class="form-row mb-2">
                            <div class="form-group col-md-3">
                                <label for="po_reference">{{ $t("Transaction Number") }}</label>
                                <p class="m-0">{{ transaction_item.transaction_code }}&nbsp;&nbsp;<a v-if="transaction_item.detail_link != ''" v-bind:href="transaction_item.detail_link" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="po_reference">{{ $t("Payment Method") }}</label>
                                <p class="m-0">{{ transaction_item.payment_method }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="order_date">{{ $t("Transaction Date") }}</label>
                                <p class="m-0">{{ transaction_item.transaction_date }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="created_by">{{ $t("Created By") }}</label>
                                <p class="m-0">{{ (transaction_item.created_by == null)?'-':transaction_item.created_by['fullname']+' ('+transaction_item.created_by['user_code']+')' }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="created_on">{{ $t("Created On") }}</label>
                                <p class="m-0">{{ transaction_item.created_at_label }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="updated_on">{{ $t("Updated On") }}</label>
                                <p class="m-0">{{ transaction_item.updated_at_label }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="invoices.length>0">
                <div class="card filter-card mb-2" v-for="(invoice, index) in invoices" v-bind:value="invoice.slack" v-bind:key="index">
                    <div class="card-body p-3">
                        <div class="form-row mb-2">
                            <div class="form-group col-md-3">
                                <label for="po_reference">{{ $t("Invoice Number") }}</label>
                                <p class="m-0">{{ invoice.invoice_number }}&nbsp;&nbsp;<a v-if="invoice.detail_link != ''" v-bind:href="invoice.detail_link" target="_blank"><i class="fas fa-external-link-alt"></i></a></p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="po_reference">{{ $t("Reference Number") }}</label>
                                <p class="m-0">{{ invoice.invoice_reference }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="order_date">{{ $t("Invoice Date") }}</label>
                                <p class="m-0">{{ invoice.invoice_date }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="order_due_date">{{ $t("Invoice Due Date") }}</label>
                                <p class="m-0">{{ invoice.invoice_due_date }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="created_by">{{ $t("Created By") }}</label>
                                <p class="m-0">{{ (invoice.created_by == null)?'-':invoice.created_by['fullname']+' ('+invoice.created_by['user_code']+')' }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="created_on">{{ $t("Created On") }}</label>
                                <p class="m-0">{{ invoice.created_at_label }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="updated_on">{{ $t("Updated On") }}</label>
                                <p class="m-0">{{ invoice.updated_at_label }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="updated_on">{{ $t("Status") }}</label>
                                <p class="m-0"><span v-bind:class="invoice.status.color">{{ invoice.status.label }}</span></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="no_result == true">
                No records
            </div>

        </div>
        
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

                search_query    : '',
                search_item     : 'ORDERS',

                order_link      : '/api/filter_orders',
                customer_link   : '/api/filter_customers',
                transaction_link   : '/api/filter_transactions',
                invoice_link    : '/api/filter_invoices',

                orders          : [],
                customers       : [],
                transactions    : [],
                invoices        : [],

                no_result       : false,
            }
        },
        props: {
            search_items: [Array, Object]
        },
        mounted() {
            console.log('Search page loaded');
        },
        methods: {

            reset_responses(){
                this.orders = [];
                this.customers =[];
                this.transactions = [];
                this.invoices = [];
            },

            empty_result_checker(){
                this.no_result = (this.orders.length == 0 && this.customers.length == 0 && this.transactions.length == 0 && this.invoices.length == 0);
            },

            set_form_data(){
                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("search_item", this.search_item);
                formData.append("keyword", this.search_query);
                return formData;
            },

            filter_requests(){
                this.reset_responses();

                switch(this.search_item){
                    case 'ORDERS':
                        this.load_orders();
                    break;

                    case 'CUSTOMERS':
                        this.load_customers();
                    break;

                    case 'TRANSACTIONS':
                        this.load_transactions();
                    break;

                    case 'INVOICES':
                        this.load_invoices();
                    break;
                } 
            },

            load_orders(){
                var formData = this.set_form_data();

                axios.post(this.order_link, formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.orders = response.data.data;
                    }else{
                        this.orders = [];
                    }
                    this.empty_result_checker();
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            load_customers(){
                var formData = this.set_form_data();

                axios.post(this.customer_link, formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.customers = response.data.data;
                    }else{
                        this.customers = [];
                    }
                    this.empty_result_checker();
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            load_transactions(){
                var formData = this.set_form_data();

                axios.post(this.transaction_link, formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.transactions = response.data.data;
                    }else{
                        this.transactions = [];
                    }
                    this.empty_result_checker();
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            load_invoices(){
                var formData = this.set_form_data();

                axios.post(this.invoice_link, formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.invoices = response.data.data;
                    }else{
                        this.invoices = [];
                    }
                    this.empty_result_checker();
                })
                .catch((error) => {
                    console.log(error);
                });
            }

        }
    }
</script>