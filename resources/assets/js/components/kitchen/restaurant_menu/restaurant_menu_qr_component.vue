<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title">{{ $t("QR Restaurant Menu") }}</span>
                       
                    </div>
                    <div class="">
                       
                    </div>
                </div>

                <qrcomponent :menu_link="menu_link"></qrcomponent>

            </form>
                
        </div>
        
    </div>
</template>

<script>
    'use strict';

    import QrcodeVue from 'qrcode.vue'
    
    export default {
        data(){
            return{
                menu_route  : this.menu_link,
            }
        },
        props: {
            menu_link: String
        },
        mounted() {
            console.log('QR menu page loaded');
        },
        methods: {
            
        }
    }
</script>