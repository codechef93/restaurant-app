<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="setting_slack == ''">{{ $t("Add Email Setting") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Email Setting") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="driver">{{ $t("Driver") }}</label>
                        <input type="text" name="driver" v-model="driver" v-validate="'required|max:50'" class="form-control form-control-custom" :placeholder="$t('Please enter Driver')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('driver') }">{{ errors.first('driver') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="encryption">{{ $t("Encryption") }}</label>
                        <input type="text" name="encryption" v-model="encryption" v-validate="'required|max:50'" class="form-control form-control-custom" :placeholder="$t('Please enter Encryption')" autocomplete="off">
                        <small class="form-text text-muted">TLS, SSL etc.</small>
                        <span v-bind:class="{ 'error' : errors.has('encryption') }">{{ errors.first('encryption') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="host">{{ $t("Host") }}</label>
                        <input type="text" name="host" v-model="host" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter Host')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('host') }">{{ errors.first('host') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="port">{{ $t("Port") }}</label>
                        <input type="text" name="port" v-model="port" v-validate="'required|max:50'" class="form-control form-control-custom" :placeholder="$t('Please enter Port')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('port') }">{{ errors.first('port') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="username">{{ $t("Username") }}</label>
                        <input type="text" name="username" v-model="username" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter Username')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('username') }">{{ errors.first('username') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="password">{{ $t("Password") }}</label>
                        <input type="text" name="password" v-model="password" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter Password')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('password') }">{{ errors.first('password') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="from_email">{{ $t("From Email") }}</label>
                        <input type="text" name="from_email" v-model="from_email" v-validate="'required|max:250|email'" class="form-control form-control-custom" :placeholder="$t('Please enter From email')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('from_email') }">{{ errors.first('from_email') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="from_email_name">{{ $t("From Email Name") }}</label>
                        <input type="text" name="from_email_name" v-model="from_email_name" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter From email name')" autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('from_email_name') }">{{ errors.first('from_email_name') }}</span> 
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

            <hr>

            <form data-vv-scope="test_email_form" v-show="setting_slack != ''">
                <div class="mb-2">
                    <div class="mb-2">
                        <label for="test_email">{{ $t("Send Test Email") }}</label>
                    </div>
                    <div class="form-row mb-2">
                        <div class="form-group col-md-3">
                            <input type="text" name="email" v-model="email" v-validate="'required|max:250|email'" class="form-control form-control-custom" :placeholder="$t('Please enter email')" autocomplete="off">
                            <span v-bind:class="{ 'error' : errors.has('test_email_form.email') }">{{ errors.first('test_email_form.email') }}</span>
                        </div>
                        <div class="form-group col-md-3">
                            <button type="button" class="btn btn-outline-primary" v-bind:disabled="test_email_processing == true" v-on:click="send_test_email"> <i class='fa fa-circle-notch fa-spin'  v-if="test_email_processing == true"></i> {{ $t("Send Test Email") }}</button>
                        </div>
                    </div>
                    <small class="form-text text-muted">{{ $t("Test email will be send to given mail address") }}</small>
                    <p v-html="test_email_server_errors" v-bind:class="[test_email_error_class]"></p>
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
                api_link        : (this.setting_data.length == 0)?'/api/add_setting_email':'/api/update_setting_email/'+this.setting_data.slack,

                setting_slack   : (this.setting_data.length == 0)?'':this.setting_data.slack,
                driver          : (this.setting_data.length == 0)?'':this.setting_data.driver,
                host            : (this.setting_data.length == 0)?'':this.setting_data.host,
                port            : (this.setting_data.length == 0)?'':this.setting_data.port,
                username        : (this.setting_data.length == 0)?'':this.setting_data.username,
                password        : (this.setting_data.length == 0)?'':this.setting_data.password,
                encryption      : (this.setting_data.length == 0)?'':this.setting_data.encryption,
                from_email      : (this.setting_data.length == 0)?'':this.setting_data.from_email,
                from_email_name : (this.setting_data.length == 0)?'':this.setting_data.from_email_name,
                status          : (this.setting_data.length == 0)?'':this.setting_data.status.value,

                test_email_processing : false,
                test_email_server_errors   : '',
                test_email_error_class     : '',
                email   : ''
            }
        },
        props: {
            statuses: Array,
            setting_data: [Array, Object]
        },
        mounted() {
            console.log('Edit Email setting page loaded');
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
                            formData.append("driver", (this.driver == null)?'':this.driver);
                            formData.append("host", (this.host == null)?'':this.host);
                            formData.append("port", (this.port == null)?'':this.port);
                            formData.append("username", (this.username == null)?'':this.username);
                            formData.append("password", (this.password == null)?'':this.password);
                            formData.append("encryption", (this.encryption == null)?'':this.encryption);
                            formData.append("from_email", (this.from_email == null)?'':this.from_email);
                            formData.append("from_email_name", (this.from_email_name == null)?'':this.from_email_name);
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
            },

            send_test_email(){
                this.$validator.validateAll('test_email_form').then((result) => {
                    if (result) {
                        this.test_email_processing = true;
                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("setting_slack", this.setting_slack);
                        formData.append("email", this.email);
                        axios.post('/api/send_test_email', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.show_response_message(response.data.msg, 'SUCCESS');
                                this.test_email_processing = false;
                            }else{
                                this.test_email_processing = false;
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.test_email_server_errors = this.loop_api_errors(error_json);
                                }catch(err){
                                    this.test_email_server_errors = response.data.msg;
                                }
                                this.test_email_error_class = 'error';
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