<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                    <span class="text-title">{{ gateway_type }} {{ $t("SMS Settings") }}</span>
                </div>
                <div class="">
                    
                </div>
            </div>

            <div class="form-row mb-2" v-if="gateway_type == 'TWILIO'">
                <div class="form-group col-md-3">
                    <label for="account_sid">{{ $t("Account SID") }}</label>
                    <p class="text-truncate">{{ account_sid }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="auth_token">{{ $t("Auth Token") }}</label>
                    <p class="text-truncate">{{ auth_token | hide_sensitive_info(10) }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="twilio_number">Twilio {{ $t("Number") }}</label>
                    <p>{{ twilio_number }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="status">{{ $t("Status") }}</label>
                    <p><span v-bind:class="status_color">{{ status_label }}</span></p>
                </div>
            </div>

            <div class="form-row mb-2" v-else-if="gateway_type == 'MSG91'">
                <div class="form-group col-md-3">
                    <label for="account_sid">{{ $t("Auth Key") }}</label>
                    <p class="text-truncate">{{ auth_key | hide_sensitive_info(10) }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="auth_token">{{ $t("Sender ID") }}</label>
                    <p class="text-truncate">{{ sender_id  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="status">{{ $t("Status") }}</label>
                    <p><span v-bind:class="status_color">{{ status_label }}</span></p>
                </div>
            </div>

            <div class="form-row mb-2" v-else-if="gateway_type == 'TEXTLOCAL'">
                <div class="form-group col-md-3">
                    <label for="account_sid">{{ $t("API Key") }}</label>
                    <p class="text-truncate">{{ api_key | hide_sensitive_info(10) }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="auth_token">{{ $t("Sender ID") }}</label>
                    <p class="text-truncate">{{ sender_id  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="status">{{ $t("Status") }}</label>
                    <p><span v-bind:class="status_color">{{ status_label }}</span></p>
                </div>
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
                
                account_sid     : (this.sms_setting_data.length == 0)?'-':this.sms_setting_data.account_sid,
                auth_token      : (this.sms_setting_data.length == 0)?'-':this.sms_setting_data.token,
                twilio_number   : (this.sms_setting_data.length == 0)?'-':this.sms_setting_data.twilio_number,
                auth_key        : (this.sms_setting_data.length == 0)?'':this.sms_setting_data.auth_key,
                sender_id       : (this.sms_setting_data.length == 0)?'':this.sms_setting_data.sender_id,
                api_key         : (this.sms_setting_data.length == 0)?'':this.sms_setting_data.auth_key,
                status_label    : (this.sms_setting_data.length == 0)?'-':this.sms_setting_data.status.label,
                status_color    : (this.sms_setting_data.length == 0)?'':this.sms_setting_data.status.color,

                gateway_type    : (this.sms_setting_data.length == 0)?'':this.sms_setting_data.gateway_type,
            }
        },
        props: {
            sms_setting_data: [Array, Object]
        },
        mounted() {
            console.log('SMS setting page loaded');
        },
        filters: {
            hide_sensitive_info: function(value, limit) {
                if (!value) return '';
                if (value.length > limit) {
                    value = value.substring(0, (limit - 3)) + '***';
                }
                return value;
            }
        },
        methods: {
           
        }
    }
</script>