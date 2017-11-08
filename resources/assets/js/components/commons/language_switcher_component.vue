<template>
    <div class="dropdown" v-if="languages.length > 0">
        <div class="btn-group dropup">
            <button type="button" class="btn btn-link text-decoration-none dropdown-toggle text-body" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
                <i class="fas fa-globe mr-1 text-secondary"></i> {{ selected_language }}
            </button>
            <div class="dropdown-menu dropdown-menu-right mr-2">
                <button class="dropdown-item" v-for="(language, index) in languages" v-bind:key="index" v-on:click="change_language(language.language_constant)">{{ language.language }} - {{ language.language_code }}</button>
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
            }
        },
        props: {
            languages: [Array, Object],
            selected_language: String,
        },
        mounted() {
            console.log('Language switcher loaded');
        },
        methods: {
           change_language(language_constant){
                
                this.processing = true;

                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("lang_code", language_constant);
                
                axios.post('/api/update_profile_language', formData)
                .then((response) => {
                    this.processing = false;
                    if(response.data.status_code === 200) {
                        location.reload();
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