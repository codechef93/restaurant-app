<template>
    <div class="container pt-3 entry_form">
        <div class="d-flex justify-content-center pt-5">
            <div class="col-sm-12 col-md-6 col-lg-4 col-lg-4">
                <img :src="company_logo" class="d-block mb-4 entry_logo" alt="appsthing">
                <span class="text-display-0 d-block mb-3">Forgot Password</span>
                <p>Enter your email address and we will send you a link to reset your password.</p>
                <p v-html="server_errors" v-bind:class="[error_class]"></p>
                <form @submit.prevent="submit_form" class="mb-3" v-if="email_response_sent == false">
                    <div class="form-group">
                        <label for="email">{{ $t("Email") }}</label>
                        <input type="email" name="email" v-model="email" v-validate="'required|email'" class="form-control form-control-custom" :placeholder="$t('Please enter your registered email')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('email') }">{{ errors.first('email') }}</span> 
                    </div>
                    <button type="submit" class="btn btn-primary btn-lg" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Forgot Password</button>
                </form>
                <p v-else>
                    Check your email for a link to reset your password. If it doesn’t appear within a few minutes, check your spam folder.
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
                email           : '',

                email_response_sent : false
            }
        },
        props: {
            company_logo: String
        },
        mounted() {
            console.log('Forgot password page loaded');
        },
        methods: {
            submit_form(){
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.processing = true;

                        var formData = new FormData();
                        formData.append("email", (this.email == null)?'':this.email);

                        axios.post('/api/user/forgot_password', formData).then((response) => {
                            this.processing = false;
                            if(response.data.status_code === 200) {
                                this.email_response_sent = true;
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
