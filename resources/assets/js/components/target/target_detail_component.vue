<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Monthly Target") }}</span> {{ target.month_label }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <button type="button" class="btn btn-danger mr-1" v-if="delete_target_access == true" v-on:click="delete_target()" v-bind:disabled="target_delete_processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="target_delete_processing == true"></i> {{ $t("Delete Target") }}</button>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="month">{{ $t("Month") }}</label>
                    <p>{{ target.month_label  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="income">{{ $t("Income") }} ({{ store.currency_code }})</label>
                    <p>{{ target.income }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="expense">{{ $t("Expense") }} ({{ store.currency_code }})</label>
                    <p>{{ target.expense }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="sales">{{ $t("Sales") }} ({{ store.currency_code }})</label>
                    <p>{{ target.sales }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="net_profit">{{ $t("Net Profit") }} ({{ store.currency_code }})</label>
                    <p>{{ target.net_profit }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (target.created_by == null)?'-':target.created_by['fullname']+' ('+target.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (target.updated_by == null)?'-':target.updated_by['fullname']+' ('+target.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ target.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ target.updated_at_label }}</p>
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
                processing      : false,
                show_modal      : false,
                target_delete_processing: false, 
                delete_target_api_link: '/api/delete_target/'+this.target_data.slack,

                target: this.target_data,
            }
        },
        props: {
            target_data: [Array, Object],
            store: [Array, Object],
            delete_target_access: Boolean
        },
        mounted() {
            console.log('Target detail page loaded');
        },
        methods: {
           delete_target(){

                this.$off("submit");
                this.$off("close");
                this.show_modal = true;

                this.$on("submit",function () {       
                    this.processing = true;
                    this.target_delete_processing = true;

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.delete_target_api_link, formData).then((response) => {

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
                        this.target_delete_processing = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                });

                this.$on("close",function () {
                    this.show_modal = false;
                });
            },
        }
    }
</script>