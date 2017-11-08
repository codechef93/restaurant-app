<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="user_slack == ''">{{ $t("Add User") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit User") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Basic Information") }}</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="email">{{ $t("Email") }}</label>
                        <input type="text" name="email" v-model="email" v-validate="'required|email|max:150'" class="form-control form-control-custom" :placeholder="$t('Please enter email')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('email') }">{{ errors.first('email') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="firstname">{{ $t("Fullname") }}</label>
                        <input type="text" name="fullname" v-model="fullname" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter fullname')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('fullname') }">{{ errors.first('fullname') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="phone">{{ $t("Contact No.") }}</label>
                        <input type="text" name="phone" v-model="phone" v-validate="'required|min:10|max:15'" class="form-control form-control-custom" :placeholder="$t('Please enter Contact Number')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('phone') }">{{ errors.first('phone') }}</span> 
                    </div>
                </div>


                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Role Information") }}</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="lastname">{{ $t("Role") }}</label>
                        <select name="role" v-model="role" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Role..</option>
                            <option v-for="(role, index) in roles" v-bind:value="role.slack" v-bind:key="index">
                                {{ role.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('role') }">{{ errors.first('role') }}</span> 
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

                <div class="mb-2" v-show="is_super_admin == true && user_slack != ''">
                    <div class="mb-2">
                        <span class="text-subhead">{{ $t("Password Reset") }}</span>
                    </div>
                    <div class="mb-2">
                        <button type="button" class="btn btn-outline-primary" v-bind:disabled="reset_password_form.processing == true" v-on:click="reset_password"> <i class='fa fa-circle-notch fa-spin'  v-if="reset_password_form.processing == true"></i> {{ $t("Reset Current Password") }}</button>
                    </div>
                </div>

                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Store Access") }}</span>
                </div>
                <div class="mb-2">
                    <div class="custom-control custom-checkbox mb-1" v-for="(store, index) in stores" v-bind:key="index">
                        <input class="custom-control-input" type="checkbox" v-model="stores_selected" v-bind:value="store.slack" v-bind:id="store.slack">
                        <label class="custom-control-label" v-bind:for="store.slack">
                            <span class="text-bold">{{ store.store_code }}</span>, {{ store.name }}, {{ store.address }}
                        </label>
                    </div>
                </div>

            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">You are making the user inactive.</p>
                {{ $t("Are you sure you want to proceed?") }}
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="$emit('close')">Cancel</button>
                <button type="button" class="btn btn-primary" @click="$emit('submit')" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Continue</button>
            </template>
        </modalcomponent>

        <modalcomponent v-if="show_password_reset_confirm" v-on:close="show_password_reset_confirm = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                Are you sure you want to reset the password?
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="$emit('close')">Cancel</button>
                <button type="button" class="btn btn-primary" @click="$emit('submit')" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Continue</button>
            </template>
        </modalcomponent>

        <modalcomponent v-if="show_new_password" v-on:close="show_new_password = false">
            <template v-slot:modal-header>
                New Password
            </template>
            <template v-slot:modal-body>
               New password for the user : <code>{{ new_password }}</code>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-primary" @click="$emit('close')">Ok</button>
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

                reset_password_form : {
                    processing : false,
                },
                show_password_reset_confirm : false,
                show_new_password : false,
                new_password    : '',

                api_link        : (this.user_data == null)?'/api/add_user':'/api/update_user/'+this.user_data.slack,
                password_reset_api_link : (this.user_data == null)?'':'/api/reset_user_password/'+this.user_data.slack,

                user_slack      : (this.user_data == null)?'':this.user_data.slack,
                email           : (this.user_data == null)?'':this.user_data.email,
                fullname        : (this.user_data == null)?'':this.user_data.fullname,
                phone           : (this.user_data == null)?'':this.user_data.phone,
                role            : (this.user_data == null)?'':(this.user_data.role == null)?'':this.user_data.role.slack,
                status          : (this.user_data == null)?'':(this.user_data.status == null)?'':this.user_data.status.value,
                stores_selected : (this.user_data == null)?[]:(this.user_data.selected_stores == null)?[]:this.user_data.selected_stores,
            }
        },
        props: {
            roles: Array,
            statuses: Array,
            stores: Array,
            user_data: [Array, Object],
            is_super_admin : Boolean
        },
        mounted() {
            console.log('Add user page loaded');
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
                            formData.append("fullname", (this.fullname == null)?'':this.fullname);
                            formData.append("email", (this.email == null)?'':this.email);
                            formData.append("phone", (this.phone == null)?'':this.phone);
                            formData.append("role", (this.role == null)?'':this.role);
                            formData.append("status", (this.status == null)?'':this.status);
                            formData.append("user_stores", this.stores_selected);

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

            reset_password(){

                this.$off("submit");
                this.$off("close");

                this.show_password_reset_confirm = true;

                this.$on("submit",function () {

                    this.reset_password_form.processing = true;
                    var formData = new FormData();

                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.password_reset_api_link, formData).then((response) => {
                
                        if(response.data.status_code == 200) {
                            
                            this.show_response_message(response.data.msg, 'SUCCESS');
                            this.reset_password_form.processing = false;
                            this.show_password_reset_confirm = false;

                            this.new_password = response.data.data['secret'];
                            this.show_new_password = true;
                        
                        }else{
                            this.show_password_reset_confirm = false;
                            this.reset_password_form.processing = false;
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
                    this.show_password_reset_confirm = false;
                    this.show_new_password = false;
                });
            }
        }
    }
</script>