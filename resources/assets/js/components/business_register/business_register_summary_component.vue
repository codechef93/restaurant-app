<template>
    <div class="">
        <div class="col-md-12 pl-0 pr-0">

            <div class="d-flex flex-wrap p-4 border-bottom">
                <div class="d-flex">
                    <div class="mr-4">
                        <span class="text-title"> {{ $t("Register") }} {{ register_data_basic.billing_counter['billing_counter_code'] }} - {{ register_data_basic.billing_counter['counter_name'] }}</span>
                    </div>
                </div>
                <div class="ml-auto">
                    <button class="btn btn-outline-primary mr-1" v-if="printnode_enabled == true" v-on:click="printnode_print('BUSINESS_REGISTER_REPORT')" v-bind:disabled="printing_processing == true"> <i class='fa fa-circle-notch fa-spin' v-if="printing_processing == true"></i> {{ $t("Print") }}</button>

                    <a :href="new_order_link" class="btn btn-lg btn-primary ml-3" v-if="new_order_access == true">+ {{ $t("New Order") }}</a>
                </div>
            </div>
            
            <div class="d-flex mb-4">
                <iframe class="pdf_iframe" title="Register Summary Print PDF" :src="pdf_print"></iframe>
            </div>
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                {{ $t("Something went wrong!") }}
            </template>
            <template v-slot:modal-body>
                <p v-html="server_errors" v-bind:class="[error_class]"></p>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="$emit('close')">Ok</button>
            </template>
        </modalcomponent>

        <notifications 
            group="notification_bar"
            classes="n-light" 
            :duration="55000"
            :width="500"
            position="top right"/>
    </div>
</template>

<script>
    'use strict';
    
    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                order_processing: false,
                processing      : false,
                printing_processing: false,
                show_modal : false,

                printnode_api_link : '/api/print_with_printnode',
                
                slack : this.register_data.slack,
                register_data_basic : this.register_data,
            }
        },
        props: {
            register_data: [Array, Object],
            pdf_print: String,
            new_order_link: String,
            new_order_access: Boolean,
            printnode_enabled: Boolean
        },
        mounted() {
            console.log('Register summary page loaded');
        },
        methods: {
           printnode_print(type){

                this.printing_processing = true;

                var formData = new FormData();

                formData.append("access_token", window.settings.access_token);
                formData.append("print_type", type);
                formData.append("slack", this.slack);

                axios.post(this.printnode_api_link, formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.show_response_message(response.data.msg + ' (Job ID: '+response.data.data+')', 'SUCCESS');
                        this.printing_processing = false;
                    }else{
                        this.show_modal = true;
                        this.printing_processing = false;
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

                this.$on("close",function () {
                    this.show_modal = false;
                });
            }
        }
    }
</script>