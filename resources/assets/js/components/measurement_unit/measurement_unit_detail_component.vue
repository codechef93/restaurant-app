<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Measurement Unit") }}</span> {{ measurement_unit.label }} ({{ measurement_unit.unit_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="measurement_unit.status.color">{{ measurement_unit.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="unit_code">{{ $t("Unit Code") }}</label>
                    <p>{{ measurement_unit.unit_code  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">{{ $t("Label") }}</label>
                    <p>{{ measurement_unit.label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (measurement_unit.created_by == null)?'-':measurement_unit.created_by['fullname']+' ('+measurement_unit.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (measurement_unit.updated_by == null)?'-':measurement_unit.updated_by['fullname']+' ('+measurement_unit.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ measurement_unit.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ measurement_unit.updated_at_label }}</p>
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
                measurement_unit : this.measurement_unit_data,
            }
        },
        props: {
            measurement_unit_data: [Array, Object]
        },
        mounted() {
            console.log('Measurement unit detail page loaded');
        },
        methods: {
           
        }
    }
</script>