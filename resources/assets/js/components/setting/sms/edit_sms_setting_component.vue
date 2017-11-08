<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title">{{ $t("Edit SMS Setting") }} - {{ gateway_type }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2" v-if="gateway_type == 'TWILIO'">
                    <div class="form-group col-md-3">
                        <label for="account_sid">{{ $t("Account SID") }}</label>
                        <input type="text" name="account_sid" v-model="account_sid" v-validate="'required|max:150'" class="form-control form-control-custom" :placeholder="$t('Please enter account sid')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('account_sid') }">{{ errors.first('account_sid') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="auth_token">{{ $t("Auth Token") }}</label>
                        <input type="text" name="auth_token" v-model="auth_token" v-validate="'required|max:150'" class="form-control form-control-custom" :placeholder="$t('Please enter auth token')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('auth_token') }">{{ errors.first('auth_token') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="twilio_number">Twilio {{ $t("Number") }}</label>
                        <input type="text" name="twilio_number" v-model="twilio_number" v-validate="'required|max:50'" class="form-control form-control-custom" :placeholder="$t('Please enter twilio number')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('twilio_number') }">{{ errors.first('twilio_number') }}</span> 
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

                <div class="form-row mb-2" v-else-if="gateway_type == 'MSG91'">
                    <div class="form-group col-md-3">
                        <label for="auth_key">{{ $t("Auth Key") }}</label>
                        <input type="text" name="auth_key" v-model="auth_key" v-validate="'required|max:100'" class="form-control form-control-custom" :placeholder="$t('Please enter Auth Key')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('auth_key') }">{{ errors.first('auth_key') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="sender_id">{{ $t("Sender ID") }}</label>
                        <input type="text" name="sender_id" v-model="sender_id" v-validate="'required|min:6|max:10'" class="form-control form-control-custom" :placeholder="$t('Please enter Sender ID')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('sender_id') }">{{ errors.first('sender_id') }}</span> 
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


                <div class="form-row mb-2" v-else-if="gateway_type == 'TEXTLOCAL'">
                    <div class="form-group col-md-3">
                        <label for="api_key">{{ $t("API Key") }}</label>
                        <input type="text" name="api_key" v-model="api_key" v-validate="'required|max:100'" class="form-control form-control-custom" :placeholder="$t('Please enter API Key')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('api_key') }">{{ errors.first('api_key') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="sender_id">{{ $t("Sender ID") }}</label>
                        <input type="text" name="sender_id" v-model="sender_id" v-validate="'required|min:6|max:6'" class="form-control form-control-custom" :placeholder="$t('Please enter Sender ID')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('sender_id') }">{{ errors.first('sender_id') }}</span> 
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

                <p v-show="gateway_type == 'MSG91'">
                    <i class="fas fa-info-circle"></i> We use route value as 4 for transactional messages by default
                </p>

                <p>
                    <i class="fas fa-info-circle"></i> It is recomended to use the country code with the customer/user mobile number
                </p>

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
    
    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : '/api/update_setting_sms/'+this.setting_data.slack,

                setting_slack   : (this.setting_data.length == 0)?'':this.setting_data.slack,
                account_sid     : (this.setting_data.length == 0)?'':this.setting_data.account_sid,
                auth_token      : (this.setting_data.length == 0)?'':this.setting_data.token,
                twilio_number   : (this.setting_data.length == 0)?'':this.setting_data.twilio_number,
                auth_key        : (this.setting_data.length == 0)?'':this.setting_data.auth_key,
                sender_id       : (this.setting_data.length == 0)?'':this.setting_data.sender_id,
                api_key         : (this.setting_data.length == 0)?'':this.setting_data.auth_key,
                status          : (this.setting_data.length == 0)?'':this.setting_data.status.value,

                gateway_type    : (this.setting_data.length == 0)?'':this.setting_data.gateway_type,
            }
        },
        props: {
            statuses: Array,
            setting_data: [Array, Object]
        },
        mounted() {
            console.log('Edit SMS setting page loaded');
        },
        methods: {
            submit_form(){
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();

                            formData.append("access_token", window.settings.access_token);
                            formData.append("status", (this.status == null)?'':this.status);
                            formData.append("gateway_type", (this.gateway_type == null)?'':this.gateway_type);
                            
                            switch(this.gateway_type){
                                case 'TWILIO':
                                    formData.append("account_id", (this.account_sid == null)?'':this.account_sid);
                                    formData.append("token", (this.auth_token == null)?'':this.auth_token);
                                    formData.append("twilio_number", (this.twilio_number == null)?'':this.twilio_number);
                                break;
                                case 'MSG91':
                                    formData.append("auth_key", (this.auth_key == null)?'':this.auth_key);
                                    formData.append("sender_id", (this.sender_id == null)?'':this.sender_id);
                                break;
                                case 'TEXTLOCAL':
                                    formData.append("api_key", (this.api_key == null)?'':this.api_key);
                                    formData.append("sender_id", (this.sender_id == null)?'':this.sender_id);
                                break;
                            }

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
                            this.$off("submit");
                        });
                        
                        this.$on("close",function () {
                            this.show_modal = false;
                            this.$off("close");
                        });
                    }
                });
            }
        }
    }
</script>