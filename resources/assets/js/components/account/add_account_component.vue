<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="account_slack == ''">{{ $t("Add Account") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Account") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="account_name">{{ $t("Account Name") }}</label>
                        <input type="text" name="account_name" v-model="account_name" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter account name')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('account_name') }">{{ errors.first('account_name') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="account_type">{{ $t("Account Type") }}</label>
                        <select name="account_type" v-model="account_type" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Account Type..</option>
                            <option v-for="(account_type, index) in account_types" v-bind:value="account_type.account_type_constant" v-bind:key="index">
                                {{ account_type.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('account_type') }">{{ errors.first('account_type') }}</span> 
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
                    <div class="form-group col-md-3">
                        <label for="initial_balance">{{ $t("Initial Balance") }}</label>
                        <input type="number" name="initial_balance" v-model="initial_balance" v-validate="'required|decimal|min_value:0'" class="form-control form-control-custom" :placeholder="$t('Please enter initial balance')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('initial_balance') }">{{ errors.first('initial_balance') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="pos_default">{{ $t("POS Default Account") }}</label>
                        <select name="pos_default" v-model="pos_default" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose POS Default Account..</option>
                            <option v-for="(pos_default_option, index) in pos_default_options" v-bind:value="index" v-bind:key="index">
                                {{ pos_default_option}}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('pos_default') }">{{ errors.first('pos_default') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="description">{{ $t("Description") }}</label>
                        <textarea name="description" v-model="description" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Enter description')"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('description') }">{{ errors.first('description') }}</span>
                    </div>
                </div>

            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">If account is inactive all the transactions will get affected.</p>
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
                modal           : false,
                show_modal      : false,
                api_link        : (this.account_data == null)?'/api/add_account':'/api/update_account/'+this.account_data.slack,
                
                pos_default_options : { 
                    '1' : 'Yes',
                    '0' : 'No'  
                },

                account_slack  : (this.account_data == null)?'':this.account_data.slack,
                account_type   : (this.account_data == null)?'':this.account_data.account_type_data.account_type_constant,
                account_name   : (this.account_data == null)?'':this.account_data.label,
                initial_balance : (this.account_data == null)?'':this.account_data.initial_balance,
                description     : (this.account_data == null)?'':this.account_data.description,
                pos_default     : (this.account_data == null)?'':this.account_data.pos_default,
                status          : (this.account_data == null)?'':(this.account_data.status == null)?'':this.account_data.status.value,
            }
        },
        props: {
            statuses: Array,
            account_types: Array,
            account_data: [Array, Object]
        },
        mounted() {
            console.log('Add account page loaded');
        },
        methods: {
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
                            formData.append("account_name", (this.account_name == null)?'':this.account_name);
                            formData.append("account_type", (this.account_type == null)?'':this.account_type);
                            formData.append("initial_balance", (this.initial_balance == null)?'':this.initial_balance);
                            formData.append("description", (this.description == null)?'':this.description);
                            formData.append("pos_default", (this.pos_default == null)?'':this.pos_default);
                            formData.append("status", (this.status == null)?'':this.status);

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