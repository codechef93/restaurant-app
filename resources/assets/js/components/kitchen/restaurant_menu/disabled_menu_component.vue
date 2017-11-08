<template>
    <div class="">
        <div class="container mt-5 pt-5 pl-0 pr-0">
            <div class="d-flex justify-content-center pt-5">
                
                <div class="text-center" v-if="menu_enabled == false">
                    <img src="/images/blocked_menu/menu_blocked.png" alt="" class="h-50 mb-5">
                    <span class="text-title d-block">{{ $t("Currently not accepting orders") }}</span>
                </div>

                <div class="text-center" v-else-if="inside_menu_schedule == false">
                    <img src="/images/blocked_menu/clock.png" alt="" class="h-50 mb-5">
                    <span class="text-title d-block">{{ $t("Menu timing") }} : {{ menu_schedule }}</span>
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
            }
        },
        props: {
            menu_enabled: Boolean,
            inside_menu_schedule: Boolean,
            menu_schedule: String
        },
        mounted() {
            console.log('Disabled menu page loaded');
        },
        methods: {
            
        }
    }
</script>