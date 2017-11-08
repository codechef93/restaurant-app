<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("SMS Template") }}</span> {{ sms_template.template_key }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="sms_template.status.color">{{ sms_template.status.label }}</span>
                </div>
            </div>

            <p><i class="fas fa-info-circle"></i> {{ sms_template.description }}</p>

            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="template_name">{{ $t("Template Name") }}</label>
                    <p class="">{{ sms_template.template_key  }}</p>
                </div>
                <div class="form-group col-md-9">
                    <label for="message">{{ $t("Message") }}</label>
                    <p>{{ sms_template.message }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (sms_template.created_by == null)?'-':sms_template.created_by['fullname']+' ('+sms_template.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (sms_template.updated_by == null)?'-':sms_template.updated_by['fullname']+' ('+sms_template.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ sms_template.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ sms_template.updated_at_label }}</p>
                </div>
            </div>

            <div class="form-row mb-2" v-show="sms_template.flow_id != ''">
                <div class="form-group col-md-3">
                    <label for="template_name">{{ $t("Flow ID") }}</label>
                    <p class="">{{ sms_template.flow_id  }}</p>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-12">
                    <label for="available_variables">{{ $t("Available Variables") }}</label>
                    <p class="">{{ sms_template.available_variables  }}</p>
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
                sms_template : this.sms_template_data,
            }
        },
        props: {
            sms_template_data: [Array, Object]
        },
        mounted() {
            console.log('SMS template detail page loaded');
        },
        methods: {
           
        }
    }
</script>