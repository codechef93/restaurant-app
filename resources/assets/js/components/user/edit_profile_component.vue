<template>
    <div class="row">
        <div class="col-md-12">
            
            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                    <span class="text-title">{{ $t("Edit Profile") }}</span>
                </div>
            </div>

            <form @submit.prevent="update_profile_photo()" class="mb-4">
                <div class="d-flex">
                    <div>
                        <img :src="profile_image" class="rounded-circle profile-image-large">
                        <input type="file" v-on:change="update_profile_image" ref="file_selector" accept="image/x-png,image/jpeg"  hidden>
                    </div>
                    <div class="ml-3 align-self-center">
                        <span class="text-subhead-bold"> {{ fullname }} </span><br>
                        <span class="btn-label" @click="$refs.file_selector.click()">Change photo</span>
                        <span class="btn-label ml-3" v-show="profile_image_exists == true" @click="remove_profile_image()">Remove</span>
                    </div>
                </div>
            </form>

            <p v-html="server_errors" v-bind:class="[error_class]"></p>

            <form @submit.prevent="update_basic_profile('basic_profile_form')" data-vv-scope="basic_profile_form" class="mb-4">
                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Basic Information") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="basic_profile_form.processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="basic_profile_form.processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>

                <p v-html="basic_profile_form.server_errors" v-bind:class="[basic_profile_form.error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="fullname">{{ $t("Fullname") }}</label>
                        <input type="text" name="fullname" v-model="fullname" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter fullname')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('basic_profile_form.fullname') }">{{ errors.first('basic_profile_form.fullname') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">{{ $t("Email") }}</label>
                        <input type="text" name="email" v-model="email" v-validate="'required|email|max:150'" class="form-control form-control-custom" :placeholder="$t('Please enter email')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('basic_profile_form.email') }">{{ errors.first('basic_profile_form.email') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="phone">{{ $t("Contact No.") }}</label>
                        <input type="text" name="phone" v-model="phone" v-validate="'required|min:10|max:15'" class="form-control form-control-custom" :placeholder="$t('Please enter Contact Number')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('basic_profile_form.phone') }">{{ errors.first('basic_profile_form.phone') }}</span> 
                    </div>
                </div>
            </form>

            <form @submit.prevent="update_password('password_form')" data-vv-scope="password_form" class="mb-4">
                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Change Password") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="password_form.processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="password_form.processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>

                <p v-html="password_form.server_errors" v-bind:class="[password_form.error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="password">{{ $t("Current Password") }}</label>
                        <input type="password" name="current_password" v-model="current_password" v-validate="'required|alpha_dash|min:6|max:100'" class="form-control form-control-custom" :placeholder="$t('Please enter your current password')">
                        <span v-bind:class="{ 'error' : errors.has('password_form.current_password') }">{{ errors.first('password_form.current_password') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="password">{{ $t("New Password") }}</label>
                        <input type="password" name="new_password" v-model="new_password" v-validate="'required|alpha_dash|min:6|max:100'" class="form-control form-control-custom" :placeholder="$t('Please enter your new password')">
                        <span v-bind:class="{ 'error' : errors.has('password_form.new_password') }">{{ errors.first('password_form.new_password') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="password">{{ $t("Re Enter New Password") }}</label>
                        <input type="password" name="new_password_confirmation" v-model="new_password_confirmation" v-validate="'required|alpha_dash|min:6|max:100'" class="form-control form-control-custom" :placeholder="$t('Please re enter your new password')">
                        <span v-bind:class="{ 'error' : errors.has('password_form.new_password_confirmation') }">{{ errors.first('password_form.new_password_confirmation') }}</span>  
                    </div>
                </div>
            </form>
        
        </div>
    </div>
</template>

<script>
    'use strict';
    
    export default {
        data(){
            return{

                server_errors : '',
                error_class   : '',
                processing    : false,

                basic_profile_form : {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,
                },

                password_form : {
                    server_errors : '',
                    error_class   : '',
                    processing    : false,
                },
                
                slack           : this.user.slack,
                email           : this.user.email,
                fullname        : this.user.fullname,
                phone           : this.user.phone,
                current_password : '',
                new_password: '',
                new_password_confirmation : '',

                profile_image_exists : (this.user.profile_image != null && this.user.profile_image != '')?true:false,
                profile_image : this.user.profile_image_path
            }
        },
        props: {
            user: [Array, Object]
        },
        mounted() {
            console.log('Edit profile page loaded');
        },
        methods: {
            update_basic_profile(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.basic_profile_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("fullname", (this.fullname == null)?'':this.fullname);
                        formData.append("email", (this.email == null)?'':this.email);
                        formData.append("phone", (this.phone == null)?'':this.phone);

                        axios.post('/api/update_basic_profile', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.show_response_message(response.data.msg, 'SUCCESS');
                                
                                setTimeout(function(){
                                    location.reload();
                                }, 1000);
                            }else{
                                this.basic_profile_form.processing = false;
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.basic_profile_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.basic_profile_form.server_errors = response.data.msg;
                                }
                                this.basic_profile_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },

            update_password(scope){
                this.$validator.validateAll(scope).then((result) => {
                    if (result) {
                        this.password_form.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("current_password", (this.current_password == null)?'':this.current_password);
                        formData.append("new_password", (this.new_password == null)?'':this.new_password);
                        formData.append("new_password_confirmation", (this.new_password_confirmation == null)?'':this.new_password_confirmation);

                        axios.post('/api/update_password', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.show_response_message(response.data.msg, 'SUCCESS');
                                
                                setTimeout(function(){
                                    location.reload();
                                }, 1000);
                            }else{
                                this.password_form.processing = false;
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.password_form.server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.password_form.server_errors = response.data.msg;
                                }
                                this.password_form.error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },

            update_profile_image(){
                
                var file = this.$refs.file_selector.files[0];
                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("profile_photo", file);

                axios.post('/api/update_profile_image', formData).then((response) => {
                    this.processing = false;
                    if(response.data.status_code == 200) {
                        location.reload();
                    }else{
                        try{
                            var error_json = JSON.parse(response.data.msg);
                            this.server_errors = this.loop_api_errors(error_json);
                        }catch(err){
                            this.server_errors = response.data.msg;
                        }
                        this.error_class = 'error';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            remove_profile_image(){
                var formData = new FormData();

                formData.append("access_token", window.settings.access_token);

                axios.post('/api/remove_profile_image', formData).then((response) => {
                    this.processing = false;
                    if(response.data.status_code == 200) {
                        location.reload();
                    }else{
                        try{
                            var error_json = JSON.parse(response.data.msg);
                            this.server_errors = this.loop_api_errors(error_json);
                        }catch(err){
                            this.server_errors = response.data.msg;
                        }
                        this.error_class = 'error';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>