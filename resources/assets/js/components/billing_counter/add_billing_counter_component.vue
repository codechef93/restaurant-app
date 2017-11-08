<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="billing_counter_slack == ''">{{ $t("Add Billing Counter") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Billing Counter") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="counter_code">{{ $t("Billing Counter Code") }}</label>
                        <input type="text" name="billing_counter_code" v-model="billing_counter_code" v-validate="'required|alpha_dash|max:30'" class="form-control form-control-custom" :placeholder="$t('Please enter counter code')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('billing_counter_code') }">{{ errors.first('billing_counter_code') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="billing_counter_name">{{ $t("Billing Counter Name") }}</label>
                        <input type="text" name="billing_counter_name" v-model="billing_counter_name" v-validate="'required|max:150'" class="form-control form-control-custom" :placeholder="$t('Please enter counter name')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('billing_counter_name') }">{{ errors.first('billing_counter_name') }}</span> 
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
                api_link        : (this.billing_counter_data == null)?'/api/add_billing_counter':'/api/update_billing_counter/'+this.billing_counter_data.slack,

                billing_counter_slack  : (this.billing_counter_data == null)?'':this.billing_counter_data.slack,
                billing_counter_code   : (this.billing_counter_data == null)?'':this.billing_counter_data.billing_counter_code,
                billing_counter_name   : (this.billing_counter_data == null)?'':this.billing_counter_data.counter_name,
                description     : (this.billing_counter_data == null)?'':this.billing_counter_data.description,
                status          : (this.billing_counter_data == null)?'':(this.billing_counter_data.status == null)?'':this.billing_counter_data.status.value,
            }
        },
        props: {
            statuses: Array,
            billing_counter_data: [Array, Object]
        },
        mounted() {
            console.log('Add billing counter page loaded');
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
                            formData.append("billing_counter_code", (this.billing_counter_code == null)?'':this.billing_counter_code);
                            formData.append("billing_counter_name", (this.billing_counter_name == null)?'':this.billing_counter_name);
                            formData.append("description", (this.description == null)?'':this.description);
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