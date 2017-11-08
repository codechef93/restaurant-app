<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Table") }}</span> {{ table.table_number }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="table.status.color">{{ table.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="table_number">{{ $t("Table Name or Number") }}</label>
                    <p>{{ table.table_number }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="no_of_occupants">{{ $t("No. of Occupants") }}</label>
                    <p>{{ table.no_of_occupants }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="waiter">{{ $t("Waiter") }}</label>
                    <p>{{ waiter }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (table.created_by == null)?'-':table.created_by['fullname']+' ('+table.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (table.updated_by == null)?'-':table.updated_by['fullname']+' ('+table.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ table.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ table.updated_at_label }}</p>
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
                table : this.table_data,
                waiter: (this.table_data == null)?'':(this.table_data.waiter == null)?'-':this.table_data.waiter.user_code+' - '+this.table_data.waiter.fullname,
            }
        },
        props: {
            table_data: [Array, Object]
        },
        mounted() {
            console.log('Table detail page loaded');
        },
        methods: {
           
        }
    }
</script>