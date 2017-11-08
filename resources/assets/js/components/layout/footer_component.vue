<template>
    <footer class="container-fluid p-3" v-bind:class="fixed_footer">
        <div class="d-flex justify-content-between">
            <span>&nbsp;© {{ year }} {{ company }} &middot; <span class="text-muted">{{ version }}</span></span>
            <languageswitchercomponent :languages="languages" :selected_language="selected_language"></languageswitchercomponent>
        </div>
    </footer>
</template>

<script>
    'use strict';
    
    export default {
        data(){
            return{
                store_slack : window.settings.logged_user_store_slack,
                user_slack  : window.settings.logged_in_user
            }
        },
        props: {
            languages: [Array, Object],
            selected_language: String,
            fixed_footer: String,
            year: String,
            company: String,
            version: String
        },
        mounted() {
            console.log('Footer loaded');

            this.listen_events();
        },
        methods: {
           listen_events(){
                
                if(process.env.MIX_PUSHER_APP_KEY == ''){
                    return;
                }

                Echo.private(`new-order-chef.${this.store_slack}`)
                .listen('NewOrderReceived', (data) => {

                    if(typeof data.order_number == 'undefined'){
                        return;
                    }

                    this.play_notification();
                    
                    var title = `<i class="fas fa-bell"></i> New Order Received!`;
                    var message = 'Order Number #'+ data.order_number +' ['+ data.order_type + ']<br><small>'+data.created_at+'</small>';
                    this.show_response_message(message, title, -1);
                
                });

                Echo.private(`new-order-waiter.${this.store_slack}.${this.user_slack}`)
                .listen('NewOrderReceived', (data) => {
                    
                    if(typeof data.order_number == 'undefined'){
                        return;
                    }

                    this.play_notification();
                    
                    var title = `<i class="fas fa-bell"></i> New Order Received!`;
                    var message = 'Order Number #'+data.order_number +' ['+ data.order_type + ']<br><small>'+data.created_at+'</small>';
                    this.show_response_message(message, title, -1);
                    
                });

            }
        }
    }
</script>