<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Transaction") }}</span> #{{ transaction.transaction_code }}</span>
                        </div>
                    </div>
                </div>
                <div class="">
                    
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4">

                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="ml-auto">
                    
                    <button type="submit" class="btn btn-danger" v-show="!block_delete_transaction.includes(transaction.bill_to)" v-if="delete_transaction_access == true" v-on:click="delete_transaction()" v-bind:disabled="transaction_delete_processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="transaction_delete_processing == true"></i> Delete Transaction</button>

                </div>

            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Billing Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="bill_to">{{ $t("Bill To") }}</label>
                    <p>{{ transaction.bill_to  }}</p>
                </div>
                <div class="form-group col-md-3" v-show='bill_to_link != ""'>
                    <label for="bill_to">{{ $t("Bill To Link") }}</label>
                    <p><a v-bind:href="bill_to_link" target="_blank">{{ bill_to_link_text  }}</a></p>
                </div>
                <div class="form-group col-md-3">
                    <label for="bill_to_name">{{ $t("Name") }}</label>
                    <p>{{ transaction.bill_to_name }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="bill_to_contact">{{ $t("Contact") }}</label>
                    <p>{{ (transaction.bill_to_contact)?transaction.bill_to_contact:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="bill_to_address">{{ $t("Address") }}</label>
                    <p>{{ (transaction.bill_to_address)?transaction.bill_to_address:'-' }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Payment Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="currency_code">{{ $t("Currency Code") }}</label>
                    <p>{{ transaction.currency_code }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="amount">{{ $t("Amount") }}</label>
                    <p>{{ transaction.amount }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="payment_method">{{ $t("Payment Method") }}</label>
                    <p>{{ transaction.payment_method }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="pg_transaction_id">{{ $t("Payment Gateway Reference Id") }}</label>
                    <p>{{ (transaction.pg_transaction_id)?transaction.pg_transaction_id:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="pg_transaction_status">{{ $t("Payment Gateway Status") }}</label>
                    <p>{{ (transaction.pg_transaction_status)?transaction.pg_transaction_status:'-' }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Transaction Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="transaction_type">{{ $t("Transaction Type") }}</label>
                    <p>{{ transaction.transaction_type_data.label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="transaction_code">{{ $t("Transaction Code") }}</label>
                    <p>{{ transaction.transaction_code  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="transaction_date">{{ $t("Transaction Date") }}</label>
                    <p>{{ transaction.transaction_date }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="account">{{ $t("Account") }}</label>
                    <p v-if="transaction.account != null">{{ transaction.account.label }} ({{ transaction.account.account_type_data.account_type_constant }})</p>
                    <p v-else>-</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (transaction.created_by == null)?'-':transaction.created_by['fullname']+' ('+transaction.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ transaction.created_at_label }}</p>
                </div>
            </div>
            <hr>

            <div class="form-row mb-2">
                <div class="form-group col-md-6">
                    <label for="notes">{{ $t("Notes") }}</label>
                    <p>{{ (transaction.notes)?transaction.notes:'-' }}</p>
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
                transaction_delete_processing: false,
                processing      : false,
                show_modal      : false,
                delete_transaction_api_link : '/api/delete_transaction/'+this.transaction_data.slack,

                transaction : this.transaction_data,
                bill_to_link : '',
                bill_to_link_text : '',
                block_delete_transaction : ['POS_ORDER']
            }
        },
        props: {
            transaction_data: [Array, Object],
            delete_transaction_access: Boolean
        },
        mounted() {
            console.log('Transaction detail page loaded');
        },
        created(){
            this.get_bill_to_link();
        },
        methods: {
           get_bill_to_link(){
                switch(this.transaction.bill_to){
                    case 'POS_ORDER':
                        this.bill_to_link = this.transaction.order.detail_link;
                        this.bill_to_link_text = this.transaction.order.order_number;
                    break;
                    case 'INVOICE':
                        this.bill_to_link = this.transaction.invoice.detail_link;
                        this.bill_to_link_text = this.transaction.invoice.invoice_number;
                    break;
                }
            },

            delete_transaction(){

                this.$off("submit");
                this.$off("close");
                this.show_modal = true;

                this.$on("submit",function () {       
                    this.processing = true;
                    this.transaction_delete_processing = true;

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.delete_transaction_api_link, formData).then((response) => {

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
                        this.transaction_delete_processing = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                });

                this.$on("close",function () {
                    this.show_modal = false;
                });
            }
        }
    }
</script>