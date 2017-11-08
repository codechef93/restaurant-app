<template>
    <div class="container pt-3 entry_form">
        <div class="d-flex justify-content-center pt-5">
            <div class="col-sm-12 col-md-6 col-lg-4 col-lg-4">
                <img :src="company_logo" class="d-block mb-4 entry_logo" alt="appsthing">
                <span class="text-display-0 d-block mb-3">Sign in</span>
                <p v-html="server_errors" v-bind:class="[error_class]"></p>
                {{ message }}
                <form @submit.prevent="submit_form" class="mb-3">
                    <div class="form-group">
                        <label for="email">{{ $t("Email") }}</label>
                        <input type="email" name="email" v-model="email" v-validate="'required|email'" class="form-control form-control-lg" :placeholder="$t('Please enter your registered email')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('email') }">{{ errors.first('email') }}</span> 
                    </div>
                    <div class="form-group">
                        <label for="password">{{ $t("Password") }}</label>
                        <input type="password" name="password" v-model="password" v-validate="'required'" class="form-control form-control-lg" :placeholder="$t('Please enter your password')">
                        <span v-bind:class="{ 'error' : errors.has('password') }">{{ errors.first('password') }}</span> 
                    </div>
                    <div class="form-group">
                        <a href="/forgot_password" class="btn-label">Forgot Password</a>
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Sign in</button>
                </form>
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
                email           : (this.is_demo == true && this.preview_mode == true)?'admin@appsthing.com':'',
                password        : (this.is_demo == true && this.preview_mode == true)?'administrator':'',
                message         : this.prop_message
            }
        },
        props: {
            prop_message: String,
            is_demo: Boolean,
            preview_mode: Boolean,
            company_logo: String
        },
        mounted() {
            console.log('Sign in page loaded');
        },
        methods: {
            submit_form(){
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.processing = true;

                        var formData = new FormData();
                        formData.append("email", (this.email == null)?'':this.email);
                        formData.append("password", (this.password == null)?'':this.password);

                        axios.post('/api/user/authenticate', formData).then((response) => {
                            this.processing = false;
                            if(response.data.status_code === 200) {
                                window.location.href = response.data.link;
                            }else{
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
                    }
                });
            }
        }
    }
</script>
