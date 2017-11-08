<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="measurement_unit_slack == ''">{{ $t("Add Measurement Unit") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Measurement Unit") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="unit_code">{{ $t("Unit Code") }}</label>
                        <input type="text" name="unit_code" v-model="unit_code" v-validate="'required|alpha_dash|max:30'" class="form-control form-control-custom" :placeholder="$t('Please enter unit code')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('unit_code') }">{{ errors.first('unit_code') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="label">{{ $t("Label") }}</label>
                        <input type="text" name="label" v-model="label" v-validate="'required|max:150'" class="form-control form-control-custom" :placeholder="$t('Please enter label')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('label') }">{{ errors.first('label') }}</span> 
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

            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">If measurement unit is inactive all the products using this measurement unit will get affected.</p>
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
                api_link        : (this.measurement_unit_data == null)?'/api/add_measurement_unit':'/api/update_measurement_unit/'+this.measurement_unit_data.slack,

                measurement_unit_slack  : (this.measurement_unit_data == null)?'':this.measurement_unit_data.slack,
                label : (this.measurement_unit_data == null)?'':this.measurement_unit_data.label,
                unit_code : (this.measurement_unit_data == null)?'':this.measurement_unit_data.unit_code,
                status : (this.measurement_unit_data == null)?'':(this.measurement_unit_data.status == null)?'':this.measurement_unit_data.status.value,
            }
        },
        props: {
            statuses: Array,
            measurement_unit_data: [Array, Object]
        },
        mounted() {
            console.log('Add measurement unit page loaded');
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
                            formData.append("unit_code", (this.unit_code == null)?'':this.unit_code);
                            formData.append("label", (this.label == null)?'':this.label);
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