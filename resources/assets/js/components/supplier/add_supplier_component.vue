<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="supplier_slack == ''">{{ $t("Add Supplier") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Supplier") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="supplier_name">{{ $t("Supplier Name") }}</label>
                        <input type="text" name="supplier_name" v-model="supplier_name" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter supplier name')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('supplier_name') }">{{ errors.first('supplier_name') }}</span> 
                    </div>
                </div>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Contact Information") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="phone">{{ $t("Contact No.") }}</label>
                        <input type="text" name="phone" v-model="phone" v-validate="'min:10|max:15'" class="form-control form-control-custom" :placeholder="$t('Please enter contact number')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('phone') }">{{ errors.first('phone') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">{{ $t("Email") }}</label>
                        <input type="text" name="email" v-model="email" v-validate="'email'" class="form-control form-control-custom" :placeholder="$t('Please enter contact email')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('email') }">{{ errors.first('email') }}</span> 
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="address">{{ $t("Address") }}</label>
                        <textarea name="address" v-model="address" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Enter Address')"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('address') }">{{ errors.first('address') }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="pincode">{{ $t("Pincode") }}</label>
                        <input type="text" name="pincode" v-model="pincode" v-validate="'max:15'" class="form-control form-control-custom" :placeholder="$t('Enter Pincode')">
                        <span v-bind:class="{ 'error' : errors.has('pincode') }">{{ errors.first('pincode') }}</span>
                    </div>
                </div>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Status Information") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
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
                <p v-if="status == 0">If supplier is inactive all the products with this supplier will get affected.</p>
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
                server_errors   : "",
                error_class     : "",
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.supplier_data == null)?'/api/add_supplier':'/api/update_supplier/'+this.supplier_data.slack,

                supplier_slack  : (this.supplier_data == null)?"":this.supplier_data.slack,
                supplier_name   : (this.supplier_data == null)?"":this.supplier_data.name,
                status          : (this.supplier_data == null)?"":(this.supplier_data.status == null)?'':this.supplier_data.status.value,
                address         : (this.supplier_data == null)?[]:this.supplier_data.address,
                phone           : (this.supplier_data == null)?[]:this.supplier_data.phone,
                email           : (this.supplier_data == null)?[]:this.supplier_data.email,
                pincode         : (this.supplier_data == null)?[]:this.supplier_data.pincode,
            }
        },
        props: {
            statuses: Array,
            supplier_data: [Array, Object]
        },
        mounted() {
            console.log('Add supplier page loaded');
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
                            formData.append("supplier_name", (this.supplier_name == null)?'':this.supplier_name);
                            formData.append("address", (this.address == null)?'':this.address);
                            formData.append("phone", (this.phone == null)?'':this.phone);
                            formData.append("email", (this.email == null)?'':this.email);
                            formData.append("pincode", (this.pincode == null)?'':this.pincode);
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