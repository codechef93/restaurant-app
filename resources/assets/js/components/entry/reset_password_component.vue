<template>
    <div class="container pt-3 entry_form">
        <div class="d-flex justify-content-center pt-5">
            <div class="col-sm-12 col-md-6 col-lg-4 col-lg-4">
                <img :src="company_logo" class="d-block mb-4 entry_logo" alt="appsthing">
                <span class="text-display-0 d-block mb-3">Reset Password</span>
                <p v-html="server_errors" v-bind:class="[error_class]"></p>
                <form @submit.prevent="submit_form" class="mb-3" v-if="password_reset_response == false">
                    <div class="form-group">
                        <label for="password">{{ $t("New Password") }}</label>
                        <input type="password" name="new_password" v-model="new_password" v-validate="'required|alpha_dash|min:6|max:100'" class="form-control form-control-custom" :placeholder="$t('Please enter your new password')">
                        <span v-bind:class="{ 'error' : errors.has('new_password') }">{{ errors.first('new_password') }}</span> 
                    </div>
                    <div class="form-group">
                        <label for="password">{{ $t("Re Enter New Password") }}</label>
                        <input type="password" name="new_password_confirmation" v-model="new_password_confirmation" v-validate="'required|alpha_dash|min:6|max:100'" class="form-control form-control-custom" :placeholder="$t('Please re enter your new password')">
                        <span v-bind:class="{ 'error' : errors.has('new_password_confirmation') }">{{ errors.first('new_password_confirmation') }}</span>  
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Forgot Password</button>
                </form>
                <p v-else>
                    Successfully reset your password! Try signing in.
                </p>
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
                new_password    : '',
                new_password_confirmation    : '',

                password_reset_response : false
            }
        },
        props: {
            user_slack: String,
            password_reset_token: String,
            company_logo: String
        },
        mounted() {
            console.log('Reset password page loaded');
        },
        methods: {
            submit_form(){
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.processing = true;

                        var formData = new FormData();
                        formData.append("user_slack", (this.user_slack == null)?'':this.user_slack);
                        formData.append("password_reset_token", (this.password_reset_token == null)?'':this.password_reset_token);
                        formData.append("new_password", (this.new_password == null)?'':this.new_password);
                        formData.append("new_password_confirmation", (this.new_password_confirmation == null)?'':this.new_password_confirmation);

                        axios.post('/api/user/reset_password', formData).then((response) => {
                            this.processing = false;
                            if(response.data.status_code === 200) {
                                this.password_reset_response = true;
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
