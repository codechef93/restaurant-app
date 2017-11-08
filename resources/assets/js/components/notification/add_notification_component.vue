<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="notification_slack == ''">{{ $t("Add Notification") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Notification") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="lastname">{{ $t("Role") }}</label>
                        <select name="role" v-model="role" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Role..</option>
                            <option v-for="(role, index) in roles" v-bind:value="role.slack" v-bind:key="index">
                                {{ role.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('role') }">{{ errors.first('role') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="users">{{ $t("Choose Users") }}</label>
                        <cool-select type="text" v-model="search_user"  autocomplete="off" inputForTextClass="form-control form-control-custom" :items="user_list" item-text="label" itemValue='label' :resetSearchOnBlur="false" disable-filtering-by-search @search='load_users' @select='add_user_to_list' :placeholder="$t('Choose users..')">
                            <template #item="{ item }">
                                <div class='d-flex justify-content-start'>
                                <div>
                                    {{ item.fullname }} - {{ item.email }}, {{ item.phone }}
                                </div>
                                </div>
                            </template>
                        </cool-select>
                        <span v-bind:class="{ 'error' : errors.has('users') }">{{ errors.first('users') }}</span>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="d-flex flex-row flex-wrap p-1">
                        <div class="text-center mr-2 mb-2" v-for="(user, index) in users" v-bind:value="role.slack" v-bind:key="index">
                            <div>
                                <img :src="user.profile_image" class="rounded-circle notification-profile">
                            </div>
                            <div class="ml-3 align-self-center">
                                <span class=""> {{ user.fullname | truncate(15) }} </span>
                                <button type="button" class="close ml-2 mt-1" aria-label="Close" @click="remove_user(index)">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-6">
                        <label for="notification">{{ $t("Notification") }}</label>
                        <textarea name="notification" v-model="notification" v-validate="'required|max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Enter Notification')"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('notification') }">{{ errors.first('notification') }}</span>
                    </div>
                </div>

            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                <p class="display-block" v-show="role !='' && users.length == 0">Note : All the users with selected role will be notified</p>
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

    import { CoolSelect } from "vue-cool-select";
    import 'vue-cool-select/dist/themes/bootstrap.css';
    
    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.notification_data == null)?'/api/add_notification':'/api/update_notification/'+this.notification_data.slack,
                search_user     : '',

                notification_slack  : (this.notification_data == null)?'':this.notification_data.slack,
                role : '',
                notification   : (this.notification_data == null)?'':this.notification_data.notification_text,

                users : [],
                user_list : []
            }
        },
        props: {
            statuses: Array,
            notification_data: [Array, Object],
            roles: [Array, Object],
        },
        filters: {
            truncate: function(value, limit) {
                if (!value) return '';
                if (value.length > limit) {
                    value = value.substring(0, (limit - 3)) + '...';
                }
                return value;
            }
        },
        mounted() {
            console.log('Add notification page loaded');
        },
        methods: {
            load_users (keywords) {
                if(typeof keywords != 'undefined'){
                    if (keywords.length > 0 ) {

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("keywords", keywords);
                        formData.append("role", this.role);

                        axios.post('/api/load_users', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.user_list = response.data.data;
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                }
            },

            add_user_to_list(item) {
                if( item.slack != '' ){
                    var current_user = {
                        slack : item.slack,
                        fullname: item.fullname,
                        profile_image : item.profile_image
                    };
                }

                var item_found = false;
                for(var i = 0; i < this.users.length; i++){   
                    if(this.users[i].slack == item.slack){
                        item_found = true;
                    }
                }
                if(item_found == false){
                    this.users.push(current_user);
                }
                this.user_list = [];
            },

            remove_user(index) {
                this.users.splice(index, 1);
            },

            submit_form(){

                this.$off("submit");
                this.$off("close");

                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();

                            formData.append("access_token", window.settings.access_token);
                            formData.append("notification_text", (this.notification == null)?'':this.notification);
                            formData.append("role", this.role);
                            formData.append("users", JSON.stringify(this.users));

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
                        });
                        
                        this.$on("close",function () {
                            this.show_modal = false;
                        });
                    }
                });
            }
        }
    }
</script>