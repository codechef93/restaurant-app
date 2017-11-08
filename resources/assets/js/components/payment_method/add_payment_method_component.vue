<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="payment_method_slack == ''">{{ $t("Add Payment Method") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Payment Method") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="payment_method">{{ $t("Payment Method") }}</label>
                        <input type="text" name="payment_method" v-model="payment_method" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter payment method')"  autocomplete="off" :readonly="uneditable.includes(payment_method.toUpperCase())">
                        <span v-bind:class="{ 'error' : errors.has('payment_method') }">{{ errors.first('payment_method') }}</span> 
                    </div>
                    <div class="form-group col-md-3" v-if="payment_method_slack != ''">
                        <label for="activate_on_digital_menu">{{ $t("Activate On QR Menu") }}</label>
                        <select name="activate_on_digital_menu" v-model="activate_on_digital_menu" v-validate="'numeric'" class="form-control form-control-custom custom-select" :disabled="!uneditable.includes(payment_method.toUpperCase())">
                            <option value="">Choose Activate on QR Menu..</option>
                            <option v-for="(activate_on_digital_menu_option, index) in activate_on_digital_menu_options" v-bind:value="index" v-bind:key="index">
                                {{ activate_on_digital_menu_option }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('activate_on_digital_menu') }">{{ errors.first('activate_on_digital_menu') }}</span>  
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
                        <label for="key_1">{{ key_1_label }}</label>
                        <textarea name="key_1" v-model="key_1" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Please enter key 1')"  autocomplete="off"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('key_1') }">{{ errors.first('key_1') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="key_2">{{ key_2_label }}</label>
                        <textarea name="key_2" v-model="key_2" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Please enter key 2')"  autocomplete="off"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('key_2') }">{{ errors.first('key_2') }}</span> 
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
                <p v-if="status == 0">You are making the payment method inactive.</p>
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
                api_link        : (this.payment_method_data == null)?'/api/add_payment_method':'/api/update_payment_method/'+this.payment_method_data.slack,

                payment_method_slack  : (this.payment_method_data == null)?'':this.payment_method_data.slack,
                payment_method   : (this.payment_method_data == null)?'':this.payment_method_data.label,
                description     : (this.payment_method_data == null)?'':this.payment_method_data.description,
                activate_on_digital_menu: (this.payment_method_data == null)?'':(this.payment_method_data.activate_on_digital_menu == null)?'':this.payment_method_data.activate_on_digital_menu,
                status          : (this.payment_method_data == null)?'':this.payment_method_data.status.value,
                uneditable      : ['STRIPE', 'PAYPAL', 'RAZORPAY'],
                key_1_label     : 'Key 1',
                key_2_label     : 'Key 2',
                key_1           : (this.payment_method_data == null)?'':this.payment_method_data.key_1,
                key_2           : (this.payment_method_data == null)?'':this.payment_method_data.key_2,

                activate_on_digital_menu_options : { 
                    '1' : 'Yes',
                    '0' : 'No'  
                },
            }
        },
        props: {
            statuses: Array,
            payment_method_data: [Array, Object]
        },
        mounted() {
            console.log('Add payment method page loaded');
        },
        created(){
            this.payment_gateway();
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
                            formData.append("payment_method_name", (this.payment_method == null)?'':this.payment_method);
                            formData.append("key_1", (this.key_1 == null)?'':this.key_1);
                            formData.append("key_2", (this.key_2 == null)?'':this.key_2);
                            formData.append("description", (this.description == null)?'':this.description);
                            formData.append("activate_on_digital_menu", (this.activate_on_digital_menu == null)?'':this.activate_on_digital_menu);
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
            },

            payment_gateway(){
                switch(this.payment_method.toUpperCase()){
                    case "STRIPE":
                        this.key_1_label = 'Secret key';
                        this.key_2_label = 'Publishable key';
                    break;
                    case "PAYPAL":
                        this.key_1_label = 'Secret';
                        this.key_2_label = 'Client ID';
                    break;
                    case "RAZORPAY":
                        this.key_1_label = 'Key ID';
                        this.key_2_label = 'Key Secret';
                    break;
                }
            }
        }
    }
</script>