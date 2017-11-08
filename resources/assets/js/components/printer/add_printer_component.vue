<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="printer_slack == ''">{{ $t("Add Printer") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Printer") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="printer_id">{{ $t("Printer ID") }} ({{ $t("PrintNode Printer ID") }})</label>
                        <input type="text" name="printer_id" v-model="printer_id" v-validate="'required|max:50'" class="form-control form-control-custom" :placeholder="$t('Please enter printer ID')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('printer_id') }">{{ errors.first('printer_id') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="printer_name">{{ $t("Printer Name") }}</label>
                        <input type="text" name="printer_name" v-model="printer_name" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter printer name')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('printer_name') }">{{ errors.first('printer_name') }}</span> 
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
                api_link        : (this.printer_data == null)?'/api/add_printer':'/api/update_printer/'+this.printer_data.slack,

                printer_slack  : (this.printer_data == null)?'':this.printer_data.slack,
                printer_id     : (this.printer_data == null)?'':this.printer_data.printer_id,
                printer_name   : (this.printer_data == null)?'':this.printer_data.printer_name,
                status          : (this.printer_data == null)?'':(this.printer_data.status == null)?'':this.printer_data.status.value,
            }
        },
        props: {
            statuses: Array,
            printer_data: [Array, Object]
        },
        mounted() {
            console.log('Add printer page loaded');
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
                            formData.append("printer_id", (this.printer_id == null)?'':this.printer_id);
                            formData.append("printer_name", (this.printer_name == null)?'':this.printer_name);
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