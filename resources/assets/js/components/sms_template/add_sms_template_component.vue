<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title"> <span class='text-muted'>{{ $t("Edit SMS Template") }}</span> {{ template_key }} </span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="message">{{ $t("Message") }}</label>
                        <textarea name="message" v-model="message" v-validate="'required|max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Enter message')"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('message') }">{{ errors.first('message') }}</span>
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
                        <label for="flow_id">{{ $t("Flow ID (MSG91)") }}</label>
                        <input type="text" name="flow_id" v-model="flow_id" v-validate="'max:100'" class="form-control form-control-custom" :placeholder="$t('Enter Flow ID')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('flow_id') }">{{ errors.first('flow_id') }}</span>
                    </div>
                </div>

                <p>
                    <i class="fas fa-info-circle"></i> {{ $t("Use following variables in the message") }} : <code>{{ available_variables }}</code>
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
                api_link        : '/api/update_sms_template/'+this.sms_template_data.slack,

                sms_template_slack  : (this.sms_template_data == null)?'':this.sms_template_data.slack,
                template_key   : (this.sms_template_data == null)?'':this.sms_template_data.template_key,
                flow_id    : (this.sms_template_data == null)?'':this.sms_template_data.flow_id,
                message             : (this.sms_template_data == null)?'':this.sms_template_data.message,
                available_variables : (this.sms_template_data == null)?'':this.sms_template_data.available_variables,
                description     : (this.sms_template_data == null)?'':this.sms_template_data.description,
                status          : (this.sms_template_data == null)?'':(this.sms_template_data.status == null)?'':this.sms_template_data.status.value,
            }
        },
        props: {
            statuses: Array,
            sms_template_data: [Array, Object]
        },
        mounted() {
            console.log('Edit sms template page loaded');
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
                            formData.append("flow_id", (this.flow_id == null)?'':this.flow_id);
                            formData.append("message", (this.message == null)?'':this.message);
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