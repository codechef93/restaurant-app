<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title">{{ $t("Edit App Setting") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="company_name">{{ $t("Company Name") }}</label>
                        <input type="text" name="company_name" v-model="company_name" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter Company Name')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('company_name') }">{{ errors.first('company_name') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="app_title">{{ $t("App Title") }}</label>
                        <input type="text" name="app_title" v-model="app_title" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter App Title')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('app_title') }">{{ errors.first('app_title') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="app_timezone">{{ $t("App Timezone") }}</label>
                        <select name="app_timezone" v-model="app_timezone" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose App Timezone..</option>
                            <option v-for="(timezone, index) in timezones" v-bind:value="timezone" v-bind:key="index">
                                {{ timezone }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('app_timezone') }">{{ errors.first('app_timezone') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="app_date_time_format">{{ $t("Date Time format") }}</label>
                        <select name="app_date_time_format" v-model="app_date_time_format" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Date Time format..</option>
                            <option v-for="(date_time_format, index) in date_time_formats" v-bind:value="date_time_format.date_format_value" v-bind:key="index">
                                {{ date_time_format.date_format_label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('app_date_time_format') }">{{ errors.first('app_date_time_format') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="app_date_format">{{ $t("Date Format") }}</label>
                        <select name="app_date_format" v-model="app_date_format" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Date Format..</option>
                            <option v-for="(date_format, index) in date_formats" v-bind:value="date_format.date_format_value" v-bind:key="index">
                                {{ date_format.date_format_label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('app_date_format') }">{{ errors.first('app_date_format') }}</span> 
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="company_logo">{{ $t("Company Logo (jpeg, jpg, png)") }}</label>
                        <input type="file" class="form-control-file form-control form-control-custom file-input" name="company_logo" ref="company_logo" accept="image/x-png,image/jpeg" v-validate="'ext:jpg,jpeg,png|size:150'">
                        <small class="form-text text-muted">Allowed file size is max 150KB</small>
                        <small class="form-text text-muted">Optional: Recommended image height is 40px</small>
                        <span v-bind:class="{ 'error' : errors.has('company_logo') }">{{ errors.first('company_logo') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="company_logo">{{ $t("Current Company Logo") }}</label>
                        <div class="d-block">
                            <img :src="company_logo" class="company-logo-image">
                            <span class="btn-label ml-3" v-show="company_logo_exists == true" @click="remove_logo('company_logo')">Remove</span>
                        </div>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="invoice_print_logo">{{ $t("Invoice Print Logo (jpeg, jpg, png)") }}</label>
                        <input type="file" class="form-control-file form-control form-control-custom file-input" name="invoice_print_logo" ref="invoice_print_logo" accept="image/x-png,image/jpeg" v-validate="'ext:jpg,jpeg,png|dimensions:200,100|size:150'">
                        <small class="form-text text-muted">Allowed file size is max 150KB and dimensions must be 200px x 100px</small>
                        <span v-bind:class="{ 'error' : errors.has('invoice_print_logo') }">{{ errors.first('invoice_print_logo') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="invoice_print_logo">{{ $t("Current Invoice Print Logo") }}</label>
                        <div class="d-block">
                            <img :src="invoice_print_logo" class="company-logo-image">
                            <span class="btn-label ml-3" v-show="invoice_print_logo_exists == true" @click="remove_logo('invoice_print_logo')">Remove</span>
                        </div>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="navbar_logo">{{ $t("Top Navbar Logo (jpeg, jpg, png)") }}</label>
                        <input type="file" class="form-control-file form-control form-control-custom file-input" name="navbar_logo" ref="navbar_logo" accept="image/x-png,image/jpeg" v-validate="'ext:jpg,jpeg,png|dimensions:30,30|size:50'">
                        <small class="form-text text-muted">Allowed file size is max 50KB and dimensions must be 30px x 30px</small>
                        <span v-bind:class="{ 'error' : errors.has('navbar_logo') }">{{ errors.first('navbar_logo') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="navbar_logo">{{ $t("Current Top Navbar Logo") }}</label>
                        <div class="d-block">
                            <img :src="navbar_logo" class="company-logo-image">
                            <span class="btn-label ml-3" v-show="navbar_logo_exists == true" @click="remove_logo('navbar_logo')">Remove</span>
                        </div>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="favicon">{{ $t("Favicon (jpeg, jpg, png)") }}</label>
                        <input type="file" class="form-control-file form-control form-control-custom file-input" name="favicon" ref="favicon" accept="image/x-png,image/jpeg" v-validate="'ext:jpg,jpeg,png|dimensions:30,30|size:10'">
                        <small class="form-text text-muted">Allowed file size is max 10KB and dimensions must be 30px x 30px</small>
                        <span v-bind:class="{ 'error' : errors.has('favicon') }">{{ errors.first('favicon') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="favicon">{{ $t("Current Favicon") }}</label>
                        <div class="d-block">
                            <img :src="favicon" class="company-logo-image">
                            <span class="btn-label ml-3" v-show="favicon_exists == true" @click="remove_logo('favicon')">Remove</span>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Cache and Storage") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="mb-2">
                    <div class="mb-2">
                        <label for="clear_cache">{{ $t("Clear Cache") }}</label>
                    </div>
                    <div class="mb-2">
                        <button type="button" class="btn btn-outline-primary" v-bind:disabled="clear_cache_processing == true" v-on:click="clear_cache"> <i class='fa fa-circle-notch fa-spin'  v-if="clear_cache_processing == true"></i> Clear App Cache</button>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="mb-2">
                        <label for="clear_old_files">{{ $t("Clear Old Files From Storage") }}</label>
                    </div>
                    <div class="mb-2">
                        <button type="button" class="btn btn-outline-primary" v-bind:disabled="clear_storage_processing == true" v-on:click="clear_storage"> <i class='fa fa-circle-notch fa-spin'  v-if="clear_storage_processing == true"></i> Clear Old Files</button>
                    </div>
                    <small class="form-text text-muted">{{ $t("This option will delete files (older than 3 days) from reports and order stroage folder") }}</small>
                </div>

                <hr>

                <div class="mb-2" v-if="deactivation_eligible == true">
                    <button type="button" class="btn btn-outline-danger" v-bind:disabled="deactivate_processing == true" v-on:click="deactivate_product"> <i class='fa fa-circle-notch fa-spin'  v-if="deactivate_processing == true"></i> Deactivate Product</button>
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

        <modalcomponent v-if="show_deactivation_modal" v-on:close="show_deactivation_modal = false">
            <template v-slot:modal-header>
                Deactivate Product
            </template>
            <template v-slot:modal-body>
                <form data-vv-scope="purchase_code_form">
                    <p v-html="purchase_code_server_errors" v-bind:class="[purchase_code_error_class]"></p>
                    <div class="form-group">
                        <label for="purchase_code">{{ $t("Purchase Code") }}</label>
                        <input type="text" name="purchase_code" v-model="purchase_code" v-validate="'required|max:200'" class="form-control form-control-custom" :placeholder="$t('Please enter Purchase Code')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('purchase_code_form.purchase_code') }">{{ errors.first('purchase_code_form.purchase_code') }}</span> 
                    </div>
                </form>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="deactivate_cancel">Cancel</button>
                <button type="button" class="btn btn-primary" @click="deactivate_proceed" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Continue</button>
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
                api_link        : '/api/update_setting_app',

                company_name    : (this.setting_data.length == 0)?'':this.setting_data.company_name,
                app_title    : (this.setting_data.length == 0)?'':this.setting_data.app_title,
                app_timezone    : (this.setting_data.length == 0)?'':this.setting_data.timezone,
                app_date_time_format     : (this.setting_data.length == 0)?'':this.setting_data.app_date_time_format,
                app_date_format     : (this.setting_data.length == 0)?'':this.setting_data.app_date_format,
                company_logo     : (this.setting_data.length == 0)?'-':this.setting_data.company_logo_path,
                company_logo_exists : (this.setting_data.length == 0)?false:((this.setting_data.company_logo != '' && this.setting_data.company_logo != null)?true:false),
                invoice_print_logo     : (this.setting_data.length == 0)?'-':this.setting_data.invoice_print_logo_path,
                invoice_print_logo_exists : (this.setting_data.length == 0)?false:((this.setting_data.invoice_print_logo != '' && this.setting_data.invoice_print_logo != null)?true:false),
                navbar_logo     : (this.setting_data.length == 0)?'-':this.setting_data.navbar_logo_path,
                navbar_logo_exists : (this.setting_data.length == 0)?false:((this.setting_data.navbar_logo != '' && this.setting_data.navbar_logo != null)?true:false),
                favicon     : (this.setting_data.length == 0)?'-':this.setting_data.favicon_path,
                favicon_exists : (this.setting_data.length == 0)?false:((this.setting_data.favicon != '' && this.setting_data.favicon != null)?true:false),

                clear_cache_processing : false,
                clear_storage_processing : false,

                deactivate_api_link     : '/api/deactivate',
                purchase_code_server_errors   : '',
                purchase_code_error_class     : '',
                purchase_code : '',
                show_deactivation_modal: false,
                deactivate_processing : false
            }
        },
        props: {
            setting_data: [Array, Object],
            date_time_formats: Array,
            date_formats: Array,
            timezones: [Array, Object],
            deactivation_eligible: Boolean,
            chost: String,
            cip: String,
        },
        mounted() {
            console.log('Edit App setting page loaded');
        },
        methods: {
            submit_form(){
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();
                            var company_logo_file = (this.$refs.company_logo.files.length>0)?this.$refs.company_logo.files[0]:'';
                            var invoice_print_logo_file = (this.$refs.invoice_print_logo.files.length>0)?this.$refs.invoice_print_logo.files[0]:'';
                            var navbar_logo_file = (this.$refs.navbar_logo.files.length>0)?this.$refs.navbar_logo.files[0]:'';
                            var favicon_file = (this.$refs.favicon.files.length>0)?this.$refs.favicon.files[0]:'';
                            
                            formData.append("access_token", window.settings.access_token);
                            formData.append("company_name", (this.company_name == null)?'':this.company_name);
                            formData.append("app_title", (this.app_title == null)?'':this.app_title);
                            formData.append("timezone", (this.app_timezone == null)?'':this.app_timezone);
                            formData.append("date_time_format", (this.app_date_time_format == null)?'':this.app_date_time_format);
                            formData.append("date_format", (this.app_date_format == null)?'':this.app_date_format);
                            formData.append("company_logo", company_logo_file);
                            formData.append("invoice_print_logo", invoice_print_logo_file);
                            formData.append("navbar_logo", navbar_logo_file);
                            formData.append("favicon", favicon_file);

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

            remove_logo(type){
                var formData = new FormData();

                formData.append("access_token", window.settings.access_token);
                formData.append("type", type);

                axios.post('/api/remove_company_logo', formData).then((response) => {
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

            clear_cache(){
                this.clear_cache_processing = true;
                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                axios.post('/api/clear_app_cache', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        location.reload();
                    }else{
                        this.clear_cache_processing = false;
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

            clear_storage(){
                this.clear_storage_processing = true;
                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                axios.post('/api/clear_app_storage', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        location.reload();
                    }else{
                        this.clear_storage_processing = false;
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

            deactivate_product(){
                this.show_deactivation_modal = true;
            },

            deactivate_proceed(){
                this.$validator.validateAll('purchase_code_form').then((isValid) => {
                    if (isValid) {
                        this.processing = true;

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("pcode", this.purchase_code);
                        formData.append("chost", this.chost);
                        formData.append("cip", this.cip);

                        axios.post(this.deactivate_api_link, formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.show_response_message(response.data.msg, 'SUCCESS');
                                this.processing = false;
                                location.reload();
                            }else{
                                this.processing = false;
                                try{
                                    var error_json = JSON.parse(response.data.msg);
                                    this.loop_api_errors(error_json);
                                }catch(err){
                                    this.purchase_code_server_errors = response.data.msg;
                                }
                                this.purchase_code_error_class = 'error';
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                });
            },
            
            deactivate_cancel(){
                this.purchase_code = '';
                this.show_deactivation_modal = false;
            },
            
        }
    }
</script>