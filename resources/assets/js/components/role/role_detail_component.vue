<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Role") }}</span> {{ role.label }} ({{ role.role_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="role.status.color">{{ role.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="role_code">{{ $t("Role Code") }}</label>
                    <p>{{ role.role_code  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">{{ $t("Name") }}</label>
                    <p>{{ role.label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (role.created_by == null)?'-':role.created_by['fullname']+' ('+role.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (role.updated_by == null)?'-':role.updated_by['fullname']+' ('+role.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ role.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ role.updated_at_label }}</p>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Access settings") }}</span>
            </div>
            <div class="mb-2">
                <div class="mb-3" v-for="(menu, index) in menus" v-bind:key="index">
                
                    <label class="" v-bind:for="menu.menu_key">
                        <span v-if="menu_selected.includes(menu.menu_key)"><i class="fas fa-check-square text-success"></i></span> {{ $t(menu.label) }}
                    </label>
                    <div class="mb-2 pl-4">
                        <div class="" v-for="(submenu_item, index) in menu.sub_menu" v-bind:key="index">
                            
                            <label class="" v-bind:for="submenu_item.menu_key">
                                <span v-if="menu_selected.includes(submenu_item.menu_key)"><i class="fas fa-check-square text-success"></i></span> {{ $t(submenu_item.label) }}
                            </label>

                            <div class="mb-2 pl-5">
                                <div class="" v-for="(action_item, index) in submenu_item.actions" v-bind:key="index">
                                    
                                    <label class="" v-bind:for="action_item.menu_key">
                                        <span v-if="menu_selected.includes(action_item.menu_key)"><i class="fas fa-check-square text-success"></i></span> {{ $t(action_item.label) }}
                                    </label>
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
    
    export default {
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                
                role            : this.role_data,
                menu_selected   : this.role_data.menus,
                menus           : this.access_menus,
            }
        },
        props: {
            role_data: [Array, Object],
            access_menus: [Array, Object],
        },
        mounted() {
            console.log('Role detail page loaded');
        },
        methods: {
           
        }
    }
</script>