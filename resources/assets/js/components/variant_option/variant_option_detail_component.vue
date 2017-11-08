<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Variant Option") }}</span> {{ variant_option.label }} ({{ variant_option.variant_option_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="variant_option.status.color">{{ variant_option.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="variant_option_code">{{ $t("Variant Option Code") }}</label>
                    <p>{{ variant_option.variant_option_code  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">{{ $t("Name") }}</label>
                    <p>{{ variant_option.label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (variant_option.created_by == null)?'-':variant_option.created_by['fullname']+' ('+variant_option.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (variant_option.updated_by == null)?'-':variant_option.updated_by['fullname']+' ('+variant_option.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ variant_option.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ variant_option.updated_at_label }}</p>
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
                variant_option : this.variant_option_data,
            }
        },
        props: {
            variant_option_data: [Array, Object]
        },
        mounted() {
            console.log('Variant option detail page loaded');
        },
        methods: {
           
        }
    }
</script>