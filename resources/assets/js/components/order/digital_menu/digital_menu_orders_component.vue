<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title">{{ $t("Digital Menu Orders") }} <span class="text-muted" v-show="digital_menu_order_list_filtered.length > 0">{{ digital_menu_order_list_filtered.length }} {{ $t("Orders") }}</span> <span v-if="processing == true"><i class='fa fa-circle-notch fa-spin'></i> Loading..</span></span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div class="d-flex">

                        <div class="custom-control custom-switch ml-3 mr-3 mt-1">
                            <input type="checkbox" class="custom-control-input" id="auto_load_switch" v-model="auto_refresh">
                            <label class="custom-control-label" for="auto_load_switch">{{ $t("Auto Refresh Every 1 Min") }}</label>
                        </div>

                        <button class="btn btn-light" v-on:click="load_order_list">{{ $t("Refresh") }}</button>
                    </div>
                </div>
            </div>
            
            <p v-if="server_errors" v-html="server_errors" v-bind:class="[error_class]"></p>
            
            <div class="d-flex flex-wrap mb-4">
                <div class="col-md-12">
                    <div class="row">
                        
                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-sm-6 col-md-6 col-lg-4 col-xl-4" v-for="(digital_menu_order_list_item, digital_menu_order_list_item_key, index) in digital_menu_order_list_filtered" v-bind:value="digital_menu_order_list_item.slack" v-bind:key="index">
                            <div class="col-12 p-4 bg-light">
                                
                                <div class="d-flex justify-content-between mb-1">
                                    <span>
                                        <span class="text-subtitle text-truncate mr-2">{{ digital_menu_order_list_item.order_number }}</span>
                                        <a :href="edit_order_link+'/'+digital_menu_order_list_item.slack" class="text-primary" target="_blank" v-if="edit_order_access == true && digital_menu_order_list_item.status.constant != 'CLOSED'"><i class="fas fa-pencil-alt"></i> {{ $t("Edit") }}</a>
                                    </span>
                                    <div>
                                        <span class="timer-circle bg-light"><span class="timer-dot mr-1"></span> {{ digital_menu_order_list_item.duration }} Minute</span>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between mb-4">
                                    <span class="text-bold text-truncate">{{ digital_menu_order_list_item.order_type }}</span>
                                    
                                    <div v-show="digital_menu_order_list_item.table != ''">
                                        {{ $t("Table") }}: {{ digital_menu_order_list_item.table }}
                                    </div>
                                </div>

                                <hr class="custom-separator">

                                <div class="d-flex justify-content-between mb-4">
                                    <div class="col-md-6 p-0">
                                        <label class="mb-2 d-block custom-label">{{ $t("Total Items") }}</label>
                                        <span class="stat-label">
                                            {{ digital_menu_order_list_item.products.length }} 
                                            <span class="text-bold text-primary cursor ml-2" v-on:click="show_items(digital_menu_order_list_item)">{{ $t("See Items") }}</span>
                                        </span>
                                    </div>
                                    <div class="col-md-6 p-0 text-right">
                                        <label class="mb-2 d-block custom-label">{{ $t("Order Value") }}</label>
                                        <span class="stat-label">
                                            <small>{{ digital_menu_order_list_item.currency_code }}</small>
                                            {{ digital_menu_order_list_item.total_order_amount }}
                                        </span>
                                    </div>
                                </div>

                                <hr class="custom-separator">

                                <div class="d-flex justify-content-between mb-1" >
                                    <div class="col-md-12 p-0">
                                        <label class="mb-2 d-block custom-label">{{ $t("Customer") }}</label>
                                        <span class="">{{ digital_menu_order_list_item.customer_email }}</span>
                                    </div>
                                </div>
                                
                                <hr class="custom-separator">

                                <div class="d-flex justify-content-right mb-1" v-if="typeof digital_menu_order_list_item.item_processing == 'undefined' || digital_menu_order_list_item.item_processing == false">
                                    <button type="button" class="btn btn-outline-danger mr-2" v-on:click="delete_order(digital_menu_order_list_item.slack, digital_menu_order_list_item_key)" v-bind:disabled="digital_menu_order_list_item.item_processing == true"><i class='fa fa-circle-notch fa-spin' v-if="digital_menu_order_list_item.item_processing == true"></i>  {{ $t("Delete") }}</button>
                                    <button type="button" class="btn btn-success flex-grow-1 ml-auto" v-on:click="approve(digital_menu_order_list_item.slack, digital_menu_order_list_item_key)" v-bind:disabled="digital_menu_order_list_item.item_processing == true"><i class='fa fa-circle-notch fa-spin' v-if="digital_menu_order_list_item.item_processing == true"></i>  {{ $t("Approve & Send To Kitchen") }}</button>
                                </div>
                                <div v-else>
                                    <i class='fa fa-circle-notch fa-spin'></i>  {{ $t("Processing..") }}
                                </div>

                            </div>
                        </div>

                        <div v-if="digital_menu_order_list_filtered.length == 0 && processing == false">
                            <span>Zero orders in queue!</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <modalcomponent v-if="show_items_modal" v-on:close="show_items_modal = false">
            <template v-slot:modal-header>
                Order Items
            </template>
            <template v-slot:modal-body>
                <div class="d-flex flex-wrap mb-2">
                    <span class="mr-auto text-subtitle text-black-50">Items</span>
                    <span class="text-subtitle text-black-50">Qty</span>
                </div>
                <div class="d-flex justify-content-between mb-2 pt-1 pb-1" v-for="(order_item, key, item_index) in order_items.products" v-bind:key="item_index">
                    <span class="text-break kitchen-item-title">{{ order_item.name }}</span>
                    <span class="text-break">{{ order_item.quantity }}</span>
                </div>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="$emit('close')">Ok</button>
            </template>
        </modalcomponent>

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
    
    import moment from "moment";

    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                send_to_kitchen_processing      : false,
                show_modal      : false,

                order_list        : [],

                total_orders     : 0,
                list_populated : false,

                filter_order_no  : '',

                auto_refresh     : true,

                show_items_modal : false,
                order_items      : []
            }
        },
        props: {
            edit_order_access: Boolean,
            edit_order_link: String
        },
        computed: {
            digital_menu_order_list_filtered(){
                if(this.filter_order_no){
                    return this.order_list.filter((digital_menu_order_list_item)=>{
                        return this.filter_order_no.toLowerCase().split(' ').every(v => digital_menu_order_list_item.order_number.toLowerCase().includes(v) || digital_menu_order_list_item.customer_phone.toLowerCase().includes(v) || digital_menu_order_list_item.customer_email.toLowerCase().includes(v) || digital_menu_order_list_item.table.toLowerCase().includes(v))
                    })
                }else{
                    return this.order_list;
                }
            }
        },
        mounted() {
            console.log('Digital menu order page loaded');
            this.tick_update_duration_for_products();
            this.tick_update_order_list();
        },
        created(){
            this.load_order_list();
        },
        methods: {
            load_order_list(){
                this.processing = true;

                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);

                axios.post('/api/get_digital_menu_orders', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.order_list = response.data.data;
                        this.update_duration_for_products();
                        this.processing = false;
                        this.list_populated = (this.order_list.length > 0)?true:false;
                        this.total_orders = this.order_list.length;
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

            update_duration_for_products(){
                for(var i = 0; i < this.order_list.length; i++){   
                    var duration = this.calculate_duration(this.order_list[i].create_at_utc);
                    this.$set(this.order_list[i], 'duration', duration);
                }
            },

            tick_update_duration_for_products(){
                setInterval(() => {
                    this.update_duration_for_products();
                }, 1000);
            },

            tick_update_order_list(){
                setInterval(() => {
                    if(this.auto_refresh == true){
                        this.load_order_list();
                    }
                }, 60000);
            },

            approve(order_slack, item_key){
                
                this.$set(this.order_list[item_key], 'item_processing', true);

                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("order_slack", order_slack);

                axios.post('/api/send_order_to_kitchen', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.$delete(this.order_list, item_key);
                    }else{
                        this.$set(this.order_list[item_key], 'item_processing', false);
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

            delete_order(order_slack, item_key){

                this.$off("submit");
                this.$off("close");

                this.show_modal = true;
                this.$on("submit",function () {

                    this.processing = true;

                    this.$set(this.order_list[item_key], 'item_processing', true);

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post('/api/delete_order/'+order_slack, formData).then((response) => {
                        if(response.data.status_code == 200) {
                            this.$delete(this.order_list, item_key);
                            this.show_modal = false;
                            this.processing = false;
                        }else{
                            this.show_modal = false;
                            this.processing = false;
                            this.$set(this.order_list[item_key], 'item_processing', false);
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
                });
                        
                this.$on("close",function () {
                    this.show_modal = false;
                });
            },

            show_items(order_items){

                this.$off("submit");
                this.$off("close");

                this.order_items = order_items;
                this.show_items_modal = true;

                this.$on("close",function () {
                    this.show_items_modal = false;
                });
            }
        }
    }
</script>