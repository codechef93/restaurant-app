<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="role_slack == ''">{{ $t("Add Role") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Role") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="role">{{ $t("Role Name") }}</label>
                        <input type="text" name="role" v-model="role" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter role name')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('role') }">{{ errors.first('role') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">{{ $t("Status") }}</label>
                        <select name="status" v-model="status" v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('status') }">{{ errors.first('status') }}</span> 
                    </div>
                </div>


                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Access settings") }}</span>
                </div>
                <div class="mb-2">
                    <div class="custom-control custom-checkbox mb-3" v-for="(menu, index) in menus" v-bind:key="index">
                        <input class="custom-control-input" type="checkbox" v-model="menu_selected" v-bind:value="menu.menu_key" v-bind:id="menu.menu_key" @click="menu_checker( {'type': menu.type, 'current': menu.menu_key, 'parent': '', 'grand_parent':'', 'children': menu.childs, 'siblings': menu.siblings, 'event': $event})">
                        <label class="custom-control-label" v-bind:for="menu.menu_key">
                            {{ $t(menu.label) }}
                        </label>
                        <div class="mb-2">
                            <div class="custom-control custom-checkbox" v-for="(submenu_item, index) in menu.sub_menu" v-bind:key="index">
                                <input class="custom-control-input" type="checkbox" v-model="menu_selected" v-bind:value="submenu_item.menu_key" v-bind:id="submenu_item.menu_key"  @click="menu_checker({'type': submenu_item.type, 'current': submenu_item.menu_key, 'parent': menu.menu_key, 'grand_parent':'', 'children': submenu_item.childs, 'siblings': submenu_item.siblings, 'event': $event})">
                                <label class="custom-control-label" v-bind:for="submenu_item.menu_key">
                                    {{ $t(submenu_item.label) }}
                                </label>
                                <div class="mb-2">
                                    <div class="custom-control custom-checkbox form-check-inline" v-for="(action_item, index) in submenu_item.actions" v-bind:key="index">
                                        <input class="custom-control-input" type="checkbox" v-model="menu_selected" v-bind:value="action_item.menu_key" v-bind:id="action_item.menu_key"  @click="menu_checker({'type': action_item.type, 'current': action_item.menu_key, 'parent': submenu_item.menu_key, 'grand_parent':menu.menu_key, 'children': '', 'siblings': action_item.siblings, 'event': $event})">
                                        <label class="custom-control-label" v-bind:for="action_item.menu_key">
                                            {{ $t(action_item.label) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>

            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">If role is inactive all the users with this role will lose access to the system.</p>
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
                modal           : false,
                show_modal      : false,
                api_link        : (this.role_data == null)?'/api/add_role':'/api/update_role/'+this.role_data.slack,

                role_slack      : (this.role_data == null)?'':this.role_data.slack,
                role            : (this.role_data == null)?'':this.role_data.label,
                status          : (this.role_data == null)?'':this.role_data.status.value,
                menu_selected   : (this.role_data == null)?[]:this.role_data.menus,
            }
        },
        props: {
            statuses: Array,
            menus: [Array, Object],
            role_data: [Array, Object]
        },
        mounted() {
            console.log('Add role page loaded');
        },
        methods: {
            array_remove(arr, value) {
                return arr.filter(function(ele){
                    return ele != value;
                });
            },
            menu_checker(payload){
                if(payload.event.target.checked == true){
                    if(payload.type == 'ACTIONS'){
                        this.menu_selected.push(payload.current);
                        this.menu_selected.push(payload.parent);
                        this.menu_selected.push(payload.grand_parent);
                    }
                    if(payload.type == 'SUB_MENU'){
                        this.menu_selected.push(payload.current);
                        this.menu_selected.push(payload.parent);
                        this.menu_selected = this.menu_selected.concat(payload.children);
                    }
                    if(payload.type == 'MAIN_MENU'){
                        this.menu_selected.push(payload.current);
                        this.menu_selected = this.menu_selected.concat(payload.children);
                    }
                }else{
                    if(payload.type == 'ACTIONS'){
                        this.menu_selected = this.array_remove(this.menu_selected, payload.current);
                        
                        var sibling_count = 0;
                        for(var i=0; i < payload.siblings.length; i++){
                            if(this.menu_selected.includes(payload.siblings[i])){
                                sibling_count++;
                            }
                        }
                        if(sibling_count == 0){
                            this.menu_selected = this.array_remove(this.menu_selected, payload.parent);
                        }
                        
                        if(typeof this.menus[payload.grand_parent] != "undefined"){
                            var children_selected_check_count = 0;
                            var children = this.menus[payload.grand_parent]['childs'];
                            for(var i=0; i<children.length;i++){
                                if(this.menu_selected.includes(children[i])){
                                    children_selected_check_count++;
                                }
                            }
                            if(children_selected_check_count == 0){
                                this.menu_selected = this.array_remove(this.menu_selected, payload.grand_parent);
                            }
                        }
                    }
                    if(payload.type == 'SUB_MENU'){
                        this.menu_selected = this.array_remove(this.menu_selected, payload.current);
                        for(var i=0; i < payload.children.length; i++){
                            this.menu_selected = this.array_remove(this.menu_selected, payload.children[i]);
                        }
                        var sibling_count = 0;
                        for(var i=0; i < payload.siblings.length; i++){
                            if(this.menu_selected.includes(payload.siblings[i])){
                                sibling_count++;
                            }
                        }
                        if(sibling_count == 0){
                            this.menu_selected = this.array_remove(this.menu_selected, payload.parent);
                            
                        }

                    }
                    if(payload.type == 'MAIN_MENU'){
                        this.menu_selected = this.array_remove(this.menu_selected, payload.current);
                        for(var i=0; i < payload.children.length; i++){
                            this.menu_selected = this.array_remove(this.menu_selected, payload.children[i]);
                        }
                    }

                }
            },
            submit_form(){
                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();

                            formData.append("access_token", window.settings.access_token);
                            formData.append("role_label", (this.role == null)?'':this.role);
                            formData.append("status", (this.status == null)?'':this.status);
                            formData.append("role_menus", this.menu_selected);

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
            }
        }
    }
</script>