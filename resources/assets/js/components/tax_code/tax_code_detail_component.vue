<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Tax Code") }}</span> {{ tax_code.label }} ({{ tax_code.tax_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="tax_code.status.color">{{ tax_code.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="tax_code">{{ $t("Tax Code") }}</label>
                    <p>{{ tax_code.tax_code  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">{{ $t("Name") }}</label>
                    <p>{{ tax_code.label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="tax_percentage">{{ $t("Total Tax Percentage") }}</label>
                    <p>{{ tax_code.total_tax_percentage }} %</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">{{ $t("Tax Type") }}</label>
                    <p>{{ tax_code.tax_type_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (tax_code.created_by == null)?'-':tax_code.created_by['fullname']+' ('+tax_code.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (tax_code.updated_by == null)?'-':tax_code.updated_by['fullname']+' ('+tax_code.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ tax_code.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ tax_code.updated_at_label }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-3">
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Tax Components") }}</span>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="table-responsive" v-if="tax_code.tax_components != null">
                            <table class="table display nowrap text-nowrap w-100">
                                <thead>
                                    <tr>
                                    <th scope="col">{{ $t("Tax Type") }}</th>
                                    <th scope="col">{{ $t("Tax Percentage") }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tax_component, key, index) in tax_code.tax_components" v-bind:key="index">
                                        <td>{{ tax_component.tax_type }}</td>
                                        <td>{{ tax_component.tax_percentage }}%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <span class="mb-2" v-else>No Tax Components</span>
                    </div>
                </div>
            </div>
            <hr>

            <div class="form-row mb-2">
                <div class="form-group col-md-6">
                    <label for="description">{{ $t("Description") }}</label>
                    <p>{{ tax_code.description }}</p>
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
                tax_code : this.tax_code_data,
            }
        },
        props: {
            tax_code_data: [Array, Object]
        },
        mounted() {
            console.log('Tax Code detail page loaded');
        },
        methods: {
           
        }
    }
</script>