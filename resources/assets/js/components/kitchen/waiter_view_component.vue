<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title">{{ $t("Waiter View") }} <span class="text-muted" v-show="kot_list_filtered.length > 0">{{ kot_list_filtered.length }} {{ $t("Orders") }}</span> <span v-if="processing == true"><i class='fa fa-circle-notch fa-spin'></i> Loading..</span></span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="d-flex">

                        <div class="custom-control custom-switch ml-3 mr-3 mt-1">
                            <input type="checkbox" class="custom-control-input" id="auto_load_switch" v-model="auto_refresh">
                            <label class="custom-control-label" for="auto_load_switch">{{ $t("Auto Refresh Every 1 Min") }}</label>
                        </div>

                        <button class="btn btn-light" v-on:click="load_kot_list">{{ $t("Refresh") }}</button>
                    </div>
                </div>
            </div>
            
            <p v-if="server_errors" v-html="server_errors" v-bind:class="[error_class]"></p>
            
            <div class="d-flex flex-row mb-3" v-if="is_waiter">
                <div class="col-md-12">

                    <div class="d-flex justify-content-center mb-3" v-if="list_populated == true">
                        <input type="text" name="filter_order_no" v-model="filter_order_no" class="form-control form-control-lg col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12" :placeholder="$t('Search by Order No / Table')"  autocomplete="off">
                    </div>

                    <div class="row flex-nowrap kitchen-wrapper" v-bind:class="{ 'bg-light': list_populated }">

                        <div class="d-flex flex-column mb-4 mt-4 ml-4 mr-4 p-0 bg-white col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-8 kitchen-card" v-for="(kot_list_item, kot_list_key, index) in kot_list_filtered" v-bind:key="index">
                            <div class="p-0 border-bottom">
                                <div class="d-flex flex-wrap p-3">
                                    <span class="mr-auto text-subtitle text-black-50">
                                        Order # {{ kot_list_item.order_number }}
                                    </span>
                                    <span><span class="timer-dot mr-2"></span> {{ kot_list_item.duration }} Minute</span>
                                </div>
                            </div>
                            <div class="p-0 border-bottom">
                                <div class="d-flex justify-content-between p-3">
                                    <div class="" v-if="kot_list_item.order_type_data != null"><i v-show="kot_list_item.order_type_data != null" v-bind:class="kot_list_item.order_type_data.icon"></i> {{ kot_list_item.order_type }}</div><div v-else></div>
                                    
                                    <span v-if="kot_list_item.kitchen_status != null">
                                        <span v-bind:class="kot_list_item.kitchen_status.color">{{ kot_list_item.kitchen_status.label }}</span>
                                    </span>
                                    
                                </div>
                            </div>
                            <div class="p-0 border-bottom" v-show="kot_list_item.table != ''">
                                <div class="d-flex justify-content-center p-3">
                                    <div class="">{{ kot_list_item.table }}</div>
                                </div>
                            </div>
                            <div class="p-0">
                                <div class="d-flex flex-wrap pl-3 pt-3 pr-3">
                                    <span class="mr-auto text-subtitle text-black-50">Items</span>
                                    <span class="text-subtitle text-black-50">Qty</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 p-3 border-bottom" v-bind:class="((item_list_value.parent_order_product == false)?'ml-3':'')" v-for="(item_list_value, key, item_index) in kot_list_item.products" v-bind:key="item_index">
                                    <i class="kitchen-item-checker text-success fas fa-check-circle" v-show="item_list_value.is_ready_to_serve == 1"></i>
                                    <span class="text-break kitchen-item-title">
                                    <span v-if="item_list_value.parent_order_product == false" class="label blue-label addon-label">Add-on</span>
                                    {{ item_list_value.name }}
                                    </span>
                                    <span class="text-break">{{ item_list_value.quantity }}</span>
                                </div>
                            </div>
                            <div class="p-0">
                                <div class="d-flex justify-content-center p-2" v-if="kot_list_item.kitchen_status != null && kot_list_item.kitchen_status.constant == 'ORDER_READY'">
                                    <span class="text-danger text-bold cursor" v-on:click="dismiss_order(kot_list_item.slack)"><i class="far fa-times-circle"></i> {{ $t("Dismiss This Order From Screen") }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="kot_list.length == 0 && processing == false">
                            <span>Zero orders in queue!</span>
                        </div>

                    </div>
                </div>
            </div>
            <div v-else>
                <div class="form-group">
                    <label for="payment_method d-block">{{ $t("Choose Waiter") }}</label>
                    <div class="d-flex flex-wrap">
                        <div class="row flex-fill" v-if="typeof users != 'undefined' && users.length>0">
                            <div class="col-md-2" v-for="(user, index) in users" v-bind:key="index">
                                <input type="radio" class="check d-none" name="waiter" v-model="waiter" v-bind:value="user.slack" v-bind:id="'waiter'+index" v-on:click="load_waiter_kot_list(user.slack)">
                                <label class="check-buttons w-100 text-truncate" v-bind:for="'waiter'+index" >{{ user.user_code }} - {{ user.fullname }}</label>
                            </div>
                        </div>
                        <div v-else class="text-muted">Waiters are not available!</div>
                    </div>
                </div>

                <div class="col-md-12">

                    <div class="d-flex justify-content-center mb-3" v-if="list_populated == true">
                        <input type="text" name="filter_order_no" v-model="filter_order_no" class="form-control form-control-lg col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12" :placeholder="$t('Search by Order No / Table')"  autocomplete="off">
                    </div>

                    <div class="row flex-wrap">

                        <div class="d-flex flex-column mb-4 p-1 bg-white col-xl-4 col-lg-6 col-md-6 col-sm-6 col-xs-8" v-for="(kot_list_item, kot_list_key, index) in kot_list_filtered" v-bind:key="index">
                            <div class="kitchen-card bg-light">
                            <div class="p-0 border-bottom">
                                <div class="d-flex flex-wrap p-3">
                                    <span class="mr-auto text-subtitle text-black-50">
                                        Order # {{ kot_list_item.order_number }}
                                    </span>
                                    <span><span class="timer-dot mr-2"></span> {{ kot_list_item.duration }} Minute</span>
                                </div>
                            </div>
                            <div class="p-0 border-bottom">
                                <div class="d-flex justify-content-between p-3">
                                    <div class="" v-if="kot_list_item.order_type_data != null"><i v-show="kot_list_item.order_type_data != null" v-bind:class="kot_list_item.order_type_data.icon"></i> {{ kot_list_item.order_type }}</div><div v-else></div>
                                    
                                    <span v-if="kot_list_item.kitchen_status != null">
                                        <span v-bind:class="kot_list_item.kitchen_status.color">{{ kot_list_item.kitchen_status.label }}</span>
                                    </span>
                                    
                                </div>
                            </div>
                            <div class="p-0 border-bottom" v-show="kot_list_item.table != ''">
                                <div class="d-flex justify-content-center p-3">
                                    <div class="">{{ kot_list_item.table }}</div>
                                </div>
                            </div>
                            <div class="p-0 waiter-card-items">
                                <div class="d-flex flex-wrap pl-3 pt-3 pr-3">
                                    <span class="mr-auto text-subtitle text-black-50">Items ({{ kot_list_item.products.length }} Items)</span>
                                    <span class="text-subtitle text-black-50">Qty</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 p-3 border-bottom" v-bind:class="((item_list_value.parent_order_product == false)?'ml-3':'')" v-for="(item_list_value, key, item_index) in kot_list_item.products" v-bind:key="item_index">
                                    <i class="kitchen-item-checker text-success fas fa-check-circle" v-show="item_list_value.is_ready_to_serve == 1"></i>
                                    <span class="text-break kitchen-item-title">
                                    <span v-if="item_list_value.parent_order_product == false" class="label blue-label addon-label">Add-on</span>
                                    {{ item_list_value.name }}
                                    </span>
                                    <span class="text-break">{{ item_list_value.quantity }}</span>
                                </div>
                            </div>
                            <div class="p-0">
                                <div class="d-flex justify-content-center p-2" v-if="kot_list_item.kitchen_status != null && kot_list_item.kitchen_status.constant == 'ORDER_READY'">
                                    <span class="text-danger text-bold cursor" v-on:click="dismiss_order(kot_list_item.slack)"><i class="far fa-times-circle"></i> {{ $t("Dismiss This Order From Screen") }}</span>
                                </div>
                            </div>
                            </div>
                        </div>

                        <div v-if="kot_list.length == 0 && processing == false">
                            <span>Zero orders in queue!</span>
                        </div>

                    </div>
                </div>
      
            </div>

        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                Are you sure you want to dismiss this order from the screen?
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
    
    import moment from "moment";

    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,

                kot_list        : [],

                total_orders     : 0,
                list_populated : false,

                filter_order_no  : '',

                auto_refresh     : true,

                show_modal      : false,

                dismiss_order_api_link: '/api/toggle_order_dismissed_from_screen_status',

                waiter          : '',
            }
        },
        props: {
            is_waiter: Boolean,
            users: [Array, Object],
            store_slack: String
        },
        computed: {
            kot_list_filtered(){
                if(this.filter_order_no){
                    return this.kot_list.filter((kot_list_item)=>{
                        return this.filter_order_no.toLowerCase().split(' ').every(v => kot_list_item.order_number.toLowerCase().includes(v) || kot_list_item.customer_phone.toLowerCase().includes(v) || kot_list_item.customer_email.toLowerCase().includes(v) || kot_list_item.table.toLowerCase().includes(v))
                    })
                }else{
                    return this.kot_list;
                }
            }
        },
        mounted() {
            console.log('Waiter order page loaded');
            this.tick_update_duration_for_products();
            this.tick_update_kot_list();
        },
        created(){
            if(this.is_waiter == true){
                this.load_kot_list();
            }
        },
        methods: {
            load_kot_list(){
                this.processing = true;

                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("waiter_slack", this.waiter);

                axios.post('/api/get_waiter_order_list', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.kot_list = response.data.data;
                        this.update_duration_for_products();
                        this.processing = false;
                        this.list_populated = (this.kot_list.length > 0)?true:false;
                        this.total_orders = this.kot_list.length;
                    }else{
                        this.processing = false;
                        try{
                            var error_json = JSON.parse(response.data.msg);
                            this.loop_api_errors(error_json);
                        }catch(err){
                            this.server_errors = response.data.msg;
                        }
                        this.error_class = 'error';
                    };
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            load_waiter_kot_list(user_slack = ''){
                this.waiter = user_slack;
                this.load_kot_list();
            },

            calculate_duration(created_date){
                var moment = require('moment-timezone');
                var tz = moment.tz.guess();

                var today = moment();
                var date_obj = new Date(created_date);
                var moment_obj = moment.unix(date_obj).tz(tz);

                var duration = moment.duration(today.diff(moment_obj));
                var minutes = Math.abs(Math.round(duration.as('minutes')));
                minutes = (isNaN(minutes))?'-':minutes;
                return minutes;
            },

            dismiss_order(slack){

                this.$off("submit");
                this.$off("close");
                
                this.show_modal = true;
                this.$on("submit",function () {
                    
                    this.processing = true;
                    var formData = new FormData();

                    formData.append("access_token", window.settings.access_token);
                    formData.append("order_slack", slack);
                    formData.append("screen", 'WAITER_SCREEN');

                    axios.post(this.dismiss_order_api_link, formData).then((response) => {
                        if(response.data.status_code == 200) {
                            this.show_response_message(response.data.msg, 'SUCCESS');
                        
                            this.load_kot_list();

                            this.show_modal = false;
                            this.processing = false;
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
                });
                
                this.$on("close",function () {
                    this.show_modal = false;
                });
            },

            update_duration_for_products(){
                for(var i = 0; i < this.kot_list.length; i++){   
                    var duration = this.calculate_duration(this.kot_list[i].create_at_utc);
                    this.$set(this.kot_list[i], 'duration', duration);
                }
            },

            tick_update_duration_for_products(){
                setInterval(() => {
                    this.update_duration_for_products();
                }, 1000);
            },

            tick_update_kot_list(){
                setInterval(() => {
                    if(this.auto_refresh == true){
                        this.load_kot_list();
                    }
                }, 60000);
            },
        }
    }
</script>