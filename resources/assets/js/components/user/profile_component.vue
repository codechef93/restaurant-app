<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <img :src="profile_image" class="rounded-circle profile-image-large">
                        </div>
                        <div class="ml-3 align-self-center">
                            <span class="text-subhead-bold"> {{ fullname }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <a href="/edit_profile" class="btn btn-primary" v-if="slack == logged_user"> {{ $t("Edit") }}</a>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="email">{{ $t("Fullname") }}</label>
                    <p>{{ fullname }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">{{ $t("Email") }}</label>
                    <p>{{ email }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">{{ $t("Phone") }}</label>
                    <p>{{ phone }}</p>
                </div>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="email">{{ $t("Role") }}</label>
                    <p>{{ role }}</p>
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
                
                slack           : this.user.slack,
                email           : this.user.email,
                fullname        : this.user.fullname,
                phone           : this.user.phone,
                role            : this.user.role_label,
                status          : this.user.status,
                profile_image   : this.user.profile_image_path,

                logged_user     : window.settings.logged_in_user
            }
        },
        props: {
            user: [Array, Object]
        },
        mounted() {
            console.log('Profile page loaded');
        },
        methods: {
           
        }
    }
</script>