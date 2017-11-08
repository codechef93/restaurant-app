<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                    <span class="text-title">{{ $t("App Settings") }}</span>
                </div>
                <div class="">
                    <a v-bind:href="edit_link" class="btn btn-primary"> {{ $t("Edit") }}</a>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="company_name">{{ $t("Company Name") }}</label>
                    <p>{{ company_name }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="app_title">{{ $t("App Title") }}</label>
                    <p>{{ app_title }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="app_title">{{ $t("App Timezone") }}</label>
                    <p>{{ app_timezone }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="app_date_format">{{ $t("App Date Time Format") }}</label>
                    <p>{{ app_date_time_format }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="app_date_format">{{ $t("App Date Format") }}</label>
                    <p>{{ app_date_format }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (updated_by == null)?'-':updated_by['fullname']+' ('+updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ updated_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Company Logo") }}</label>
                    <p><img :src="company_logo" class="company-logo-image"></p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Invoice Print Logo") }}</label>
                    <p><img :src="invoice_print_logo" class="company-logo-image"></p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Top Navbar Logo") }}</label>
                    <p><img :src="navbar_logo" class="company-logo-image"></p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Favicon") }}</label>
                    <p><img :src="favicon" class="company-logo-image"></p>
                </div>
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
                edit_link       : '/edit_app_setting',
                
                company_name     : (this.app_setting.length == 0)?'-':this.app_setting.company_name,
                app_title        : (this.app_setting.length == 0)?'-':this.app_setting.app_title,
                app_timezone     : (this.app_setting.length == 0)?'-':this.app_setting.timezone,
                app_date_time_format  : (this.app_setting.length == 0)?'-':this.app_setting.app_date_time_format,
                app_date_format  : (this.app_setting.length == 0)?'-':this.app_setting.app_date_format,
                updated_by       : (this.app_setting.length == 0)?null:this.app_setting.updated_by,
                updated_at_label : (this.app_setting.length == 0)?'-':this.app_setting.updated_at_label,
                company_logo     : (this.app_setting.length == 0)?'-':this.app_setting.company_logo_path,
                invoice_print_logo : (this.app_setting.length == 0)?'-':this.app_setting.invoice_print_logo_path,
                navbar_logo      : (this.app_setting.length == 0)?'-':this.app_setting.navbar_logo_path,
                favicon          : (this.app_setting.length == 0)?'-':this.app_setting.favicon_path,
            }
        },
        props: {
            app_setting: [Array, Object]
        },
        mounted() {
            console.log('App setting page loaded');
        },
        methods: {
           
        }
    }
</script>