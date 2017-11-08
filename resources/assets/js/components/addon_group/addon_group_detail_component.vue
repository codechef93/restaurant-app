<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Add-on Group") }}</span> {{ addon_group.label }} ({{ addon_group.addon_group_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="addon_group.status.color">{{ addon_group.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="addon_group_code">{{ $t("Add-on Group Code") }}</label>
                    <p>{{ addon_group.addon_group_code  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">{{ $t("Name") }}</label>
                    <p>{{ addon_group.label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">{{ $t("Choose Multiple") }}</label>
                    <p>{{ multiple_selection }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (addon_group.created_by == null)?'-':addon_group.created_by['fullname']+' ('+addon_group.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (addon_group.updated_by == null)?'-':addon_group.updated_by['fullname']+' ('+addon_group.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ addon_group.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ addon_group.updated_at_label }}</p>
                </div>
            </div>

            <hr>

            <div class="mb-2">
                <span class="text-subhead">Product Information</span>
            </div>
            <div class="table-responsive mb-2">
                <table class="table table-striped display nowrap text-nowrap w-100">
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ $t("Product Code") }}</th>
                        <th scope="col">{{ $t("Product") }}</th>
                        <th scope="col" class="text-right">{{ $t("Price") }} (EXCL Tax)</th>
                        <th scope="col">{{ $t("Stock Status") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(addon_group_product, key, index) in addon_group.products" v-bind:value="addon_group_product.product.slack" v-bind:key="index">
                            <th scope="row">{{ key+1 }}</th>
                            <td>{{ (addon_group_product.product.product_code)?addon_group_product.product.product_code:'-' }}</td>
                            <td>{{ addon_group_product.product.name }}</td>
                            <td class="text-right">{{ addon_group_product.product.sale_amount_excluding_tax }}</td>
                            <td><span v-html="(addon_group_product.low_stock == 1)?low_indicator:normal_indicator"></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</template>  

<script>
    'use strict';
    
    export default {
        data(){
            return{
                addon_group : this.addon_group_data,
                multiple_selection : (this.addon_group_data.multiple_selection == 1)?'Yes':'No',

                low_indicator : '<i class="fas fa-exclamation-circle text-warning" title="Low on stock"></i>',
                normal_indicator : '<i class="fas fa-check-circle text-success" title="Normal stock"></i>',

            }
        },
        props: {
            addon_group_data: [Array, Object]
        },
        mounted() {
            console.log('Add-on group detail page loaded');
        },
        methods: {
           
        }
    }
</script>