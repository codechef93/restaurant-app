<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Business Register") }}</span> {{ business_register.user.fullname }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4">

                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="ml-auto">
                    
                    <button type="submit" class="btn btn-danger mr-1" v-if="delete_register_access == true && business_register.closing_date != null" v-on:click="delete_register()" v-bind:disabled="delete_processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="delete_processing == true"></i> Delete Register</button>

                    <a class="btn btn-outline-primary" v-bind:href="print_register_report_link" target="_blank">{{ $t("PDF") }}</a>
                </div>

            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="user">{{ $t("User") }}</label>
                    <p>{{ business_register.user.fullname  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="opening_date">{{ $t("Opened On") }}</label>
                    <p>{{ business_register.opening_date_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">{{ $t("Closed On") }}</label>
                    <p>{{ (business_register.closing_date_label)?business_register.closing_date_label:'-' }}</p>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="opening_amount">{{ $t("Opening Amount") }}</label>
                    <p>{{ business_register.opening_amount  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="closing_amount">{{ $t("Closing Amount") }}</label>
                    <p>{{ (business_register.closing_amount)?business_register.closing_amount:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="credit_card_slips">{{ $t("Total Credit Card Slips") }}</label>
                    <p>{{ business_register.credit_card_slips }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="cheques">{{ $t("Total Cheques") }}</label>
                    <p>{{ business_register.cheques }}</p>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (business_register.created_by == null)?'-':business_register.created_by['fullname']+' ('+business_register.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (business_register.updated_by == null)?'-':business_register.updated_by['fullname']+' ('+business_register.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ business_register.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ business_register.updated_at_label }}</p>
                </div>
            </div>

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
                delete_processing: false,
                processing      : false,
                show_modal      : false,
                delete_register_api_link : '/api/delete_register/'+this.business_register_data.slack,

                business_register : this.business_register_data,
            }
        },
        props: {
            business_register_data: [Array, Object],
            delete_register_access: Boolean,
            print_register_report_link: String
        },
        mounted() {
            console.log('Business register detail page loaded');
        },
        methods: {
           delete_register(){

                this.$off("submit");
                this.$off("close");
                this.show_modal = true;

                this.$on("submit",function () {       
                    this.processing = true;
                    this.delete_processing = true;

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.delete_register_api_link, formData).then((response) => {

                        if(response.data.status_code == 200) {
                            if(typeof response.data.link != 'undefined' && response.data.link != ""){
                                window.location.href = response.data.link;
                            }else{
                                location.reload();
                            }
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
                        this.delete_processing = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                });

                this.$on("close",function () {
                    this.show_modal = false;
                });
            }
        }
    }
</script>