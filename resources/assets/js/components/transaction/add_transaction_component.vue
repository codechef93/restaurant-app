<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="transaction_slack == ''">{{ $t("Add Transaction") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Transaction") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="transaction_date">{{ $t("Transaction Date") }}</label>
                        <date-picker :format="date.format" :lang='date.lang' v-model="transaction_date" v-validate="'required|date_format:yyyy-MM-dd'" input-class="form-control form-control-custom bg-white" ref="transaction_date" name="transaction_date" :placeholder="$t('Please enter transaction date')" autocomplete="off"></date-picker>
                        <span v-bind:class="{ 'error' : errors.has('transaction_date') }">{{ errors.first('transaction_date') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="bill_to">{{ $t("Bill To") }}</label>
                        <select name="bill_to" v-model="bill_to" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Bill To..</option>
                            <option v-for="(bill_to_item, index) in bill_to_master_list" v-bind:value="bill_to_item" v-bind:key="index">
                                {{ bill_to_item }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('bill_to') }">{{ errors.first('bill_to') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="bill_to_slack">{{ $t("Choose Customer or Supplier") }}</label>
                        <cool-select type="text" name="bill_to_slack" v-validate="'required'" :placeholder="$t('Please choose Customer or Supplier')"  autocomplete="off" v-model="bill_to_slack" :items="bill_to_list" item-text="label" itemValue='slack' @search='load_bill_to_list' ref="bill_to_label">
                        </cool-select>
                        <span v-bind:class="{ 'error' : errors.has('bill_to_slack') }">{{ errors.first('bill_to_slack') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="account">{{ $t("Account") }}</label>
                        <select name="account" v-model="account" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Account..</option>
                            <option v-for="(account, index) in accounts" v-bind:value="account.slack" v-bind:key="index">
                                {{ account.label }} ({{ account.account_type_label }})
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('account') }">{{ errors.first('account') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="account">{{ $t("Transaction Type") }}</label>
                        <select name="transaction_type" v-model="transaction_type" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Transaction Type..</option>
                            <option v-for="(transaction_type_item, index) in transaction_type_data" v-bind:value="transaction_type_item.transaction_type_constant" v-bind:key="index">
                                {{ transaction_type_item.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('transaction_type') }">{{ errors.first('transaction_type') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="amount">{{ $t("Amount") }}</label>
                        <input type="number" name='amount' v-model="amount" v-validate="'required|decimal'" class="form-control form-control-custom" :placeholder="$t('Please enter the amount')"  autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('amount') }">{{ errors.first('amount') }}</span> 
                    </div>

                    <div class="form-group col-md-3">
                        <label for="payment_method">{{ $t("Payment Method") }}</label>
                        <select name="payment_method" v-model="payment_method" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Payment Method..</option>
                            <option v-for="(payment_method, index) in payment_methods" v-bind:value="payment_method.slack" v-bind:key="index">
                                {{ payment_method.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('payment_method') }">{{ errors.first('payment_method') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="notes">{{ $t("Notes") }}</label>
                        <textarea name="notes" v-model="notes" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Enter notes')"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('notes') }">{{ errors.first('notes') }}</span>
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
    
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    import moment from "moment";
    import { CoolSelect } from "vue-cool-select";
    import 'vue-cool-select/dist/themes/bootstrap.css';
    
    export default {
        data(){
            return{
                date:{
                    lang : 'en',
                    format : "YYYY-MM-DD",
                },

                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.transaction_data == null)?'/api/add_transaction':'/api/update_transaction/'+this.transaction_data.slack,

                bill_to_master_list : ['SUPPLIER', 'CUSTOMER', 'STAFF'],
                bill_to_list   : [],

                transaction_slack : (this.transaction_data == null)?'':this.transaction_data.slack,
                bill_to         : (this.transaction_data == null)?'':this.transaction_data.bill_to,
                bill_to_slack   : '',
                bill_to_label   : (this.transaction_data == null)?'':this.transaction_data.bill_to_name,
                transaction_date : (this.transaction_data == null)?'':(this.transaction_data.transaction_date != null)?new Date(this.transaction_data.transaction_date):'',
                account         : (this.transaction_data == null)?'':this.transaction_data.account.slack,
                transaction_type: (this.transaction_data == null)?'':this.transaction_data.transaction_type_data.transaction_type_constant,
                payment_method  : (this.transaction_data == null)?'':this.transaction_data.payment_method_data.slack,
                amount          : (this.transaction_data == null)?'':this.transaction_data.amount,
                notes           : (this.transaction_data == null)?'':this.transaction_data.notes,
            }
        },
        props: {
            transaction_type_data: Array,
            accounts: Array,
            payment_methods : Array,
            transaction_data: [Array, Object]
        },
        mounted() {
            console.log('Add transaction page loaded');
        },
        created() {
            //update bill_to_slack variable
        },
        methods: {
            convert_date_format(date){
                return (date != '')?moment(date).format("YYYY-MM-DD"):'';
            },
            load_bill_to_list (keywords) {
                if(typeof keywords != 'undefined'){
                    if (keywords.length > 0) {

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("keywords", keywords);
                        formData.append("type", this.bill_to);

                        axios.post('/api/load_bill_to_list', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.bill_to_list = response.data.data;
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
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
                            formData.append("bill_to", (this.bill_to == null)?'':this.bill_to);
                            formData.append("bill_to_slack", (this.bill_to_slack == null)?'':this.bill_to_slack);
                            formData.append("transaction_date", (this.transaction_date == null)?'':this.convert_date_format(this.transaction_date));
                            formData.append("account", (this.account == null)?'':this.account);
                            formData.append("transaction_type", (this.transaction_type == null)?'':this.transaction_type);
                            formData.append("amount", (this.amount == null)?'':this.amount);
                            formData.append("payment_method", (this.payment_method == null)?'':this.payment_method);
                            formData.append("notes", (this.notes == null)?'':this.notes);

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