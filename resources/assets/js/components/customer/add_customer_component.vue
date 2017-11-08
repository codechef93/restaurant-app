<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="customer_slack == ''">{{ $t("Add Customer") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Customer") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="mb-2">
                    <span class="text-subhead">Basic Information</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="name">{{ $t("Fullname") }}</label>
                        <input type="text" name="name" v-model="name" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter fullname')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('name') }">{{ errors.first('name') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">{{ $t("Email") }}</label>
                        <input type="text" name="email" v-model="email" v-validate="{ required: this.email_required, email: true, max: 150 }" class="form-control form-control-custom" :placeholder="$t('Please enter email')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('email') }">{{ errors.first('email') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="phone">{{ $t("Contact No.") }}&nbsp;<small class="text-muted">(With Country Code)</small></label>
                        <input type="text" name="phone" v-model="phone" v-validate="{ required: this.phone_required, min: 10, max: 15 }" class="form-control form-control-custom" :placeholder="$t('Please enter Contact Number')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('phone') }">{{ errors.first('phone') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="dob">{{ $t("Date of Birth") }}</label>
                        <date-picker :format="date.format" :lang='date.lang' v-model="dob" v-validate="'date_format:yyyy-MM-dd'" input-class="form-control form-control-custom bg-white" ref="dob" name="dob" :placeholder="$t('Please enter Date of Birth')" autocomplete="off"></date-picker>
                        <span v-bind:class="{ 'error' : errors.has('dob') }">{{ errors.first('dob') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="address">{{ $t("Address") }}</label>
                        <textarea name="address" v-model="address" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Enter Address')"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('address') }">{{ errors.first('address') }}</span>
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
                <p v-if="status == 0">You are making the customer inactive.</p>
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
    import 'vue2-datepicker/index.css';
    import moment from "moment";
    
    export default {
        data(){
            return{
                date:{
                    lang : 'en',
                    format : "YYYY-MM-DD",
                },
                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.customer_data == null)?'/api/add_customer':'/api/update_customer/'+this.customer_data.slack,

                customer_slack  : (this.customer_data == null)?'':this.customer_data.slack,
                email           : (this.customer_data == null)?'':this.customer_data.email,
                name            : (this.customer_data == null)?'':this.customer_data.name,
                phone           : (this.customer_data == null)?'':this.customer_data.phone,
                address         : (this.customer_data == null)?'':this.customer_data.address,
                dob             : (this.customer_data == null)?'':(this.customer_data.dob_raw != null)?new Date(this.customer_data.dob_raw):'',
                status          : (this.customer_data == null)?'':(this.customer_data.status == null)?'':this.customer_data.status.value,
            }
        },
        props: {
            statuses: Array,
            customer_data: [Array, Object]
        },
        mounted() {
            console.log('Add customer page loaded');
        },
        computed: {
            email_required(){
                if(this.phone === '')
                    return true;
                return false;
            },
            phone_required(){
                if(this.email === '')
                    return true;
                return false;
            }
        },
        methods: {
            convert_date_format(date){
                return (date != '')?moment(date).format("YYYY-MM-DD"):'';
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
                            formData.append("name", (this.name == null)?'':this.name);
                            formData.append("email", (this.email == null)?'':this.email);
                            formData.append("phone", (this.phone == null)?'':this.phone);
                            formData.append("address", (this.address == null)?'':this.address);
                            formData.append("dob", (this.dob == null)?'':this.convert_date_format(this.dob));
                            formData.append("status", (this.status == null)?'':this.status);

                            axios.post(this.api_link, formData).then((response) => {
                                
                                if(response.data.status_code == 200) {
                                    this.show_response_message(response.data.msg, 'SUCCESS');
                                    setTimeout(function(){
                                        location.reload();
                                    }, 1000);
                                }else{
                                    this.processing = false;
                                    this.show_modal = false;
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