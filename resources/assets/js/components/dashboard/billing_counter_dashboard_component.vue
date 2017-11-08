<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> {{ $t("Billing Counter Dashboard") }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    Today
                </div>
            </div>
            
            <div class="d-flex flex-wrap mb-4">
                <div class="col-md-12">
                    <div class="row">
                        
                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-sm-6 col-md-6 col-lg-4 col-xl-4" v-for="(billing_counter, index) in billing_counter_stats" v-bind:value="billing_counter.slack" v-bind:key="index">
                            <div class="col-12 p-4 bg-light">
                                
                                <div class="d-flex justify-content-between mb-4">
                                    <span class="text-subtitle text-truncate">{{ billing_counter.billing_counter_code }} - {{ billing_counter.counter_name }}</span>
                                    
                                    <div v-if="billing_counter.business_register != null">
                                        <span class="label green-label">Open</span>
                                    </div>
                                    <div v-else>
                                        <span class="label red-label">Closed</span>
                                    </div>
                                </div>
                                
                                <hr class="custom-billing-counter-separator">

                                <div class="d-flex justify-content-between mb-4">
                                    <div class="col-md-6 p-0">
                                        <label class="mb-2 d-block custom-label">{{ $t("Total Orders") }}</label>
                                        <span class="stat-label">{{ (billing_counter.order_data.order_count != null)?billing_counter.order_data.order_count:0 }}</span>
                                    </div>
                                    <div class="col-md-6 p-0 text-right">
                                        <label class="mb-2 d-block custom-label">{{ $t("Order Value") }}</label>
                                        <span class="stat-label">
                                            <small>{{ store_currency }}</small>
                                            {{ (billing_counter.order_data.order_value != null)?billing_counter.order_data.order_value:0 }}
                                        </span>
                                    </div>
                                </div>

                                <hr class="custom-billing-counter-separator">

                                <div class="d-flex flex-column">
                                    <div class="">
                                        <div class="d-flex justify-content-between mb-1">
                                            <div class="col-md-4 p-0">
                                                <label class="mb-2 d-block custom-label text-truncate">{{ $t("Payment Method") }}</label>
                                            </div>
                                            <div class="col-md-4 p-0 text-right">
                                                <label class="mb-2 d-block custom-label">{{ $t("No of Orders") }}</label>
                                            </div>
                                            <div class="col-md-4 p-0 text-right">
                                                <label class="mb-2 d-block custom-label">{{ $t("Order Value") }}</label>
                                            </div>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1" v-for="(payment_method_item, index) in billing_counter.payment_method_data" v-bind:value="payment_method_item.payment_method" v-bind:key="index">
                                            <div class="col-md-4 p-0">
                                                <p>{{ payment_method_item.payment_method }}</p>
                                            </div>
                                            <div class="col-md-4 p-0 text-right">
                                                <p>{{ (payment_method_item.order_count != null)?payment_method_item.order_count:0 }}</p>
                                            </div>
                                            <div class="col-md-4 p-0 text-right">
                                                <p>
                                                    <small>{{ store_currency }}</small>
                                                    {{ (payment_method_item.value != null)?payment_method_item.value:0 }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="custom-billing-counter-separator">

                                <div class="d-flex justify-content-between mb-1" v-if="billing_counter.business_register != null">
                                    <div class="col-md-6 p-0">
                                        <label class="mb-2 d-block custom-label">{{ $t("Recently Opened On") }}</label>
                                        <span class="">{{ billing_counter.business_register.opening_date_label }}</span>
                                    </div>
                                    <div class="col-md-6 p-0 text-right">
                                        <label class="mb-2 d-block custom-label">{{ $t("Opened By") }}</label>
                                        <span class="">{{ billing_counter.business_register.created_by.fullname }} ({{ billing_counter.business_register.created_by.user_code }})</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</template>  

<script>

    'use strict';
    
    import DatePicker from 'vue2-datepicker';
    import Chart from 'chart.js';
    import moment from "moment";
    
    export default {
        data(){
            return{
                date:{
                    lang : 'en',
                    format : "YYYY-MM",
                },

                dashboard_month : new Date(moment().format("YYYY-MM")),
                dashboard_month_formatted : new Date(moment().format("YYYY-MM")),
                store_currency: this.store.currency_code,

                stats_processing: false,

                billing_counter_stats : []
            }
        },
        props: {
            store: [Array, Object],
        },
        mounted() {
            console.log('Billing counter dashboard loaded');
            this.get_recent_trasactions();
        },
        methods: {
            set_form_data(){
                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                return formData;
            },

            get_recent_trasactions(){
                
                var formData = this.set_form_data();

                axios.post('/api/get_billing_counter_stats', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.billing_counter_stats = response.data.data;
                    }else{
                        
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>