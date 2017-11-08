<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="target_slack == ''">{{ $t("Add Target") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Target") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="month">{{ $t("Month") }}</label>
                        <date-picker type="month" :lang='date.lang' :format="date.format" name="month_raw" v-model="month_raw" v-validate="'required'" @change="target_month_change" input-class="form-control bg-white" :placeholder="$t('Select month')"></date-picker>
                        <span v-bind:class="{ 'error' : errors.has('month_raw') }">{{ errors.first('month_raw') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="income">{{ $t("Income") }} ({{ store.currency_code }})</label>
                        <input type="number" name='income' v-model="income" v-validate="'required|decimal'" class="form-control form-control-custom" :placeholder="$t('Please enter income')"  autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('income') }">{{ errors.first('income') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="expense">{{ $t("Expense") }} ({{ store.currency_code }})</label>
                        <input type="number" name='expense' v-model="expense" v-validate="'required|decimal'" class="form-control form-control-custom" :placeholder="$t('Please enter expense')"  autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('expense') }">{{ errors.first('expense') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="sales">{{ $t("Sales") }} ({{ store.currency_code }})</label>
                        <input type="number" name='sales' v-model="sales" v-validate="'required|decimal'" class="form-control form-control-custom" :placeholder="$t('Please enter sales')"  autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('sales') }">{{ errors.first('sales') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="net_profit">{{ $t("Net Profit") }} ({{ store.currency_code }})</label>
                        <input type="number" name='net_profit' v-model="net_profit" v-validate="'required|decimal'" class="form-control form-control-custom" :placeholder="$t('Please enter net profit')"  autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('net_profit') }">{{ errors.first('net_profit') }}</span> 
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
    import moment from "moment";
    
    export default {
        data(){
            return{
                date:{
                    lang : 'en',
                    format : "YYYY-MM",
                },

                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.target_data == null)?'/api/add_target':'/api/update_target/'+this.target_data.slack,

                target_slack    : (this.target_data == null)?'':this.target_data.slack,
                month_raw       : (this.target_data == null)?'':(this.target_data.month != null)?new Date(this.target_data.month):'',
                month_formatted : (this.target_data == null)?'':this.target_data.month,
                income          : (this.target_data == null)?'':this.target_data.income,
                expense         : (this.target_data == null)?'':this.target_data.expense,
                sales           : (this.target_data == null)?'':this.target_data.sales,
                net_profit      : (this.target_data == null)?'':this.target_data.net_profit,
                
            }
        },
        props: {
            target_data: [Array, Object],
            store: [Array, Object]
        },
        mounted() {
            console.log('Add target page loaded');
            this.target_month_change();
        },
        methods: {
            convert_date_format(date){
                return (date != '')?moment(date).format("YYYY-MM"):'';
            },

            target_month_change(){
                this.month_formatted = this.convert_date_format(this.month_raw);
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
                            formData.append("month", (this.month_formatted == null)?'':this.month_formatted);
                            formData.append("income", (this.income == null)?'':this.income);
                            formData.append("expense", (this.expense == null)?'':this.expense);
                            formData.append("sales", (this.sales == null)?'':this.sales);
                            formData.append("net_profit", (this.net_profit == null)?'':this.net_profit);

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