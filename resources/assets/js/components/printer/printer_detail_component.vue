<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Printer") }}</span> {{ printer.printer_name }} ({{ printer.printer_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="printer.status.color">{{ printer.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="printer_name">{{ $t("Printer Code") }}</label>
                    <p>{{ printer.printer_code  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="printer_id">{{ $t("Printer ID") }}</label>
                    <p>{{ printer.printer_id  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="printer_name">{{ $t("Printer Name") }}</label>
                    <p>{{ printer.printer_name  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (printer.created_by == null)?'-':printer.created_by['fullname']+' ('+printer.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (printer.updated_by == null)?'-':printer.updated_by['fullname']+' ('+printer.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ printer.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ printer.updated_at_label }}</p>
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
                printer : this.printer_data,
            }
        },
        props: {
            printer_data: [Array, Object]
        },
        mounted() {
            console.log('Printer detail page loaded');
        },
        methods: {
           
        }
    }
</script>