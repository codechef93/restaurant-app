<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> {{ $t("Stock Quantity Alert") }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <div v-if="restaurant_mode == 1">
                        <select name="product_type_filter" v-model="product_type_filter" class="form-control form-control-custom custom-select" @change="fire_request">
                            <option value="all">All</option>
                            <option value="billing_products">Billing Products</option>
                            <option value="ingredients">Ingredients</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table id="listing-table" class="table display nowrap w-100">
                    <thead>
                        <tr>
                            <th>{{ $t("Product Code") }}</th>
                            <th>{{ $t("Product") }}</th>
                            <th>{{ $t("Alert Quantity") }}</th>
                            <th>{{ $t("Available Quantity") }}</th>
                        </tr>
                    </thead>
                    <tbody>
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
                restaurant_mode : window.settings.restaurant_mode,

                product_type_filter: 'billing_products'
            }
        },
        props: {

        },
        mounted() {
            console.log('Product quantity alert report page loaded');
        },
        methods: {
            fire_request(){
                event = new CustomEvent("product_type", { "detail": this.product_type_filter });
                document.dispatchEvent(event);
            }
        }
    }
</script>