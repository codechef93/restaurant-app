<template>
    <nav class="navbar navbar-expand-lg top-nav p-2">
        <div class="container-fluid">
            
            <a class="navbar-brand" href="/">
                <img :src="navbar_logo" class="d-inline-block align-top top-nav-logo  ml-lg-1 ml-sm-4" alt="appsthing"/>
            </a>

            <div id="menu-toggle" class="menu-toggler mr-auto" v-if="order_page == false">
                <div class="bar-1"></div>
                <div class="bar-2"></div>
                <div class="bar-3"></div>
            </div>

            <button class="navbar-toggler dropdown-toggle" type="button" data-toggle="collapse" data-target="#small_menu_toogler" aria-controls="small_menu_toogler" aria-expanded="false" aria-label="Toggle actions">
                <img :src="logged_user_data.profile_image" class="d-inline-block rounded-circle mr-2 top-nav-profile" alt="">
            </button>


            <div class="collapse navbar-collapse" id="small_menu_toogler">
                
                <ul class="navbar-nav mt-lg-0 ml-md-5 pt-1">
                    
                    <li class="nav-item text-right" v-if="user_slack != ''">
                        <storeselectorcomponent :stores="stores" :selected_store="selected_store"></storeselectorcomponent>
                    </li>
                </ul>

                <ul class="navbar-nav ml-auto mt-lg-0 pt-1" v-if="user_slack != ''">
                    <li class="nav-item text-right pl-md-4 pl-lg-4 pl-xl-4" v-if="quick_link_array != null && quick_link_array.length>0">
                        <div class="">
                            <a href="/waiter" class="nav-link nav-link-dark text-bold" id="quicklink_dropdown">
                                Waiter View
                            </a>
                        </div>
                    </li>

                    <li class="nav-item text-right pl-md-4 pl-lg-4 pl-xl-4" v-if="quick_link_array != null && quick_link_array.length>0">
                        <div class="">
                            <a href="/orders" class="nav-link nav-link-dark text-bold" id="quicklink_dropdown">
                                Orders
                            </a>
                        </div>
                    </li>

                    <li class="nav-item text-right pl-md-4 pl-lg-4 pl-xl-4" v-if="quick_link_array != null && quick_link_array.length>0">
                        <div class="dropdown">
                            <a href="#" class="nav-link quick-link dropdown-toggle text-bold" id="quicklink_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-bolt"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="quicklink_dropdown">
                                <a class="dropdown-item" v-for="(quick_link, index) in quick_link_array" v-bind:key="index" :href="quick_link.route">{{ $t(quick_link.label) }}</a>
                            </div>
                        </div>
                    </li>

                    <li class="nav-item text-right pl-md-4 pl-lg-4 pl-xl-4">
                        <a href="/search" class="nav-link nav-link-dark text-bold"><i class="fas fa-search search-icon"></i> {{ $t("Search") }}</a>
                    </li>

                    <li class="nav-item text-right pl-md-4 pl-lg-4 pl-xl-4" v-if="new_order_access == true">
                        <a :href="new_order_link" class="nav-link nav-link-dark text-bold">+ {{ $t("New Order") }}</a>
                    </li>

                    <li class="nav-item text-right pl-md-4 pl-lg-4 pl-xl-4">
                        <notificationcomponent :unread_notifications="unread_notifications_data" :all_notifications_link="all_notifications_link_data"></notificationcomponent>
                    </li>

                    <li class="nav-item text-right pl-md-4 pl-lg-4 pl-xl-4">
                        <div class="dropdown">
                            <a href="#" class="nav-link nav-link-dark dropdown-toggle text-bold" id="user_menu_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img :src="logged_user_data.profile_image" class="d-inline-block rounded-circle mr-2 top-nav-profile" :class="{ 'superadmin-profile': is_superadmin }" alt="">
                                {{ user_fullname }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="user_menu_dropdown" >
                                <a class="dropdown-item" :href="profile_link"> {{ $t("Profile") }}</a>
                                <a class="dropdown-item" :href="logout_link">{{ $t("Logout") }}</a>
                            </div>
                        </div>
                    </li>

                </ul>
            </div>

            <notifications 
            group="notification_bar"
            classes="n-light" 
            :duration="55000"
            :width="500"
            position="top right"/>
            
        </div>
    </nav>
</template>

<script>
    'use strict';
    
    export default {
        data(){
            return{
                stores: this.logged_user_data.user_stores,
                selected_store: this.logged_user_data.selected_store,
                quick_link_array: this.quick_links,
                new_order_access: this.logged_user_data.new_order_access,
                new_order_link: this.logged_user_data.new_order_link,
                unread_notifications_data: this.logged_user_data.unread_notifications,
                all_notifications_link_data: this.logged_user_data.all_notifications_link,
                profile_link: this.logged_user_data.profile_link,
                logout_link: this.logged_user_data.logout_link,
                user_fullname: this.logged_user_data.fullname
            }
        },
        props: {
            user_slack: String,
            logged_user_data: [Array, Object],
            navbar_logo: String,
            quick_links: [Array, Object],
            order_page: Boolean,
            is_superadmin: Boolean,
        },
        mounted() {
            console.log('Top Navigation loaded');
        },
        methods: {
           
        }
    }
</script>