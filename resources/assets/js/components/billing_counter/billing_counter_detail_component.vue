<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Billing Counter") }}</span> {{ billing_counter.counter_name }} ({{ billing_counter.billing_counter_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="billing_counter.status.color">{{ billing_counter.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="billing_counter_code">{{ $t("Billing Counter Code") }}</label>
                    <p>{{ billing_counter.billing_counter_code  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="counter_name">{{ $t("Counter Name") }}</label>
                    <p>{{ billing_counter.counter_name }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (billing_counter.created_by == null)?'-':billing_counter.created_by['fullname']+' ('+billing_counter.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (billing_counter.updated_by == null)?'-':billing_counter.updated_by['fullname']+' ('+billing_counter.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ billing_counter.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ billing_counter.updated_at_label }}</p>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-6">
                    <label for="description">{{ $t("Description") }}</label>
                    <p>{{ (billing_counter.description)?billing_counter.description:'-' }}</p>
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
                billing_counter : this.billing_counter_data,
            }
        },
        props: {
            billing_counter_data: [Array, Object]
        },
        mounted() {
            console.log('Billing Counter detail page loaded');
        },
        methods: {
           
        }
    }
</script>