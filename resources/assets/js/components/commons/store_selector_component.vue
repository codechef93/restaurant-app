<template>
    <div class="dropdown">
        <a href="#" class="nav-link nav-link-dark dropdown-toggle text-bold" id="user_store_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-if="selected_store.length != 0">
            <span class="text-thin">{{  $t("Store") }}</span> {{ selected_store.store_code +' - '+ selected_store.name  | truncate(45) }}
        </a>
        <div class="dropdown-menu dropdown-menu-left store-selector-dropdown" aria-labelledby="user_store_dropdown">
            <small class="dropdown-header">CHOOSE FROM {{ stores.length }} STORE(S)</small>
            <a class="dropdown-item mb-2" href="#" v-for="(store, index) in stores" v-bind:value="store.store_slack" v-bind:key="index" v-on:click="select_store(store.store_slack)" v-bind:class="{'border-left border-primary bg-light': store.store_slack == selected_store.store_slack}">
                <span class="mr-2 text-primary" v-if="store.store_slack == selected_store.store_slack"><i class="fas fa-check-circle"></i></span>
                <span class="text-bold">{{ store.store_code }}</span> - {{ store.name }} <br>
                <span class="text-muted">{{ store.address }}</span>
            </a>
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
            }
        },
        props: {
            stores: Array,
            selected_store: [Array, Object]
        },
        mounted() {
            console.log('Store selector loaded');
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
        methods: {
           select_store(store_slack){
                
                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("store", store_slack);
                
                axios.post('/api/update_profile_store', formData)
                .then((response) => {
                    this.processing = false;
                    if(response.data.status_code === 200) {
                        window.location.href = response.data.link;
                    }else{
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
            }
        }
    }
</script>