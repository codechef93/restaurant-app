<template>
    <div class="row">
        <div class="container-lg">
            <div class="col-md-12 pt-4">
                
                <div class="d-flex justify-content-between mb-5">
                    <div>
                        <img v-bind:src="company_logo" class="company_logo">   
                    </div>
                    <div>
                        <img src="/images/heart.svg" class="heart">
                        <span class="d-block-inline text-left pt-1 pl-3"> Thank you for shopping!</span>
                    </div>
                </div>

                <div class="d-flex flex-wrap mb-5">
                    <div class="mr-auto">
                        <div class="d-flex">
                            <div>
                                <span class="text-title"> {{ $t("Order") }} #{{ order_basic.order_number }} </span>
                            </div>
                        </div>
                    </div>
                    <div class="">
                        <div class="d-flex">
                            <div class="">
                                <span class="text-title">{{ order_basic.currency_code }} {{ order_basic.total_order_amount }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column flex-md-row justify-content-between"> 
                    <div class="flex-fill">
                        <div class="mb-3">
                            <span class="text-sub-title">{{ $t("Store") }}</span>
                        </div>
                        <div class="mb-5">
                            <div class="mr-auto">
                                <div class="">
                                    <span class="text-bold">{{ order_basic.store.store_code }} - {{ order_basic.store.name }}</span>
                                    <div class="address">
                                        <span class="d-block">{{ order_basic.store.address }}</span>
                                        <span class="d-block">Pincode - {{ (order_basic.store.pincode == null)?'-':order_basic.store.pincode }}</span>
                                        <span class="d-block">{{ order_basic.store.country.name }}</span>
                                        <span class="d-block" v-if="order_basic.store.tax_number != ''">GST : {{ order_basic.store.tax_number }}</span>
                                        <span class="d-block" v-if="order_basic.store.primary_contact != '' || order_basic.store.secondary_contact != ''">Contact : {{ (order_basic.store.primary_contact == null)?'-':order_basic.store.primary_contact }} &middot; {{ (order_basic.store.secondary_contact == null)?'-':order_basic.store.secondary_contact }}</span>
                                        <span class="d-block" v-if="order_basic.store.primary_email != '' || order_basic.store.secondary_email != ''">Email : {{ (order_basic.store.primary_email == null)?'-':order_basic.store.primary_email }} &middot; {{ (order_basic.store.secondary_email == null)?'-':order_basic.store.secondary_email }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-fill">
                        <div class="mb-3">
                            <span class="text-sub-title">{{ $t("Customer") }}</span>
                        </div>
                        <div class="d-flex flex-wrap mb-5">
                            <div class="mr-auto">
                                <div class="d-flex">
                                    <div class="">
                                        <img src="/images/customer_default.svg" class="rounded-circle customer-image">
                                    </div>

                                    <div class="ml-3 align-self-center customer">
                                        <span class="d-block text-left"> {{ (order_basic.customer == null)?'-':order_basic.customer.name }} </span>
                                        <div class="d-block"> 
                                            <span class="d-block text-left" v-if="order_basic.customer_email != 'walkincustomer@appsthing.com'">Email : {{ (order_basic.customer_email == null)?'-':order_basic.customer_email }} </span>
                                            <span class="d-block text-left" v-if="order_basic.customer_phone != '0000000000'">Contact : {{ (order_basic.customer_phone == null)?'-':order_basic.customer_phone }} </span>
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-sub-title">{{ $t("Details") }}</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="created_on">{{ $t("Bill Date") }}</label>
                        <p>{{ order_basic.created_at_label }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="created_by">{{ $t("Billed By") }}</label>
                        <p>{{ (order_basic.created_by == null)?'-':order_basic.created_by['fullname']+' ('+order_basic.created_by['user_code']+')' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">{{ $t("Payment Mode") }}</label>
                        <p>{{ order_basic.payment_method }}</p>
                    </div>
                </div>

                <div class="form-row mb-2" v-show="order_basic.restaurant_mode == 1">
                    <div class="form-group col-md-3">
                        <label for="email">{{ $t("Order Type") }}</label>
                        <p>{{ order_basic.order_type }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">{{ $t("Billing Type") }}</label>
                        <p>{{ (order_basic.billing_type_data != null)?order_basic.billing_type_data.label:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">{{ $t("Table Number or Name") }}</label>
                        <p>{{ (order_basic.table != null && order_basic.table != '')?order_basic.table:'-' }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">{{ $t("Waiter") }}</label>
                        <p>{{ (order_basic.waiter_data != null)?order_basic.waiter_data.fullname + ' (' +order_basic.waiter_data.user_code+ ')' :'-' }}</p>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-sub-title">{{ $t("Items") }}</span>
                </div>
                <p>Prices are in {{ order_basic.currency_name }} ({{ order_basic.currency_code }})</p>
                <div class="table-responsive mb-2">
                    <table class="table  display nowrap text-nowrap w-100">
                        <thead>
                            <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{ $t("Product Description") }}</th>
                            <th scope="col" class="text-right">{{ $t("Quantity") }}</th>
                            <th scope="col" class="text-right">{{ $t("Price") }} (EXCL Tax)</th>
                            <th scope="col" class="text-right">{{ $t("Discount Amount") }}</th>
                            <th scope="col" class="text-right">{{ $t("Tax Amount") }}</th>
                            <th scope="col" class="text-right">{{ $t("Total") }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(order_product, key, index) in products" v-bind:value="order_product.product_slack" v-bind:key="index">
                                <th class="align-middle" v-if="order_product.parent_order_product">{{ order_product.counter }}</th>
                                <th class="align-middle" v-else><span v-html="addon_label" class="pl-4"></span></th>
                                <td class="align-middle">{{ order_product.product_code }} - {{ order_product.name }}</td>
                                <td class="align-middle text-right">{{ order_product.quantity }}</td>
                                <td class="align-middle text-right">{{ order_product.price }}</td>
                                <td class="align-middle text-right">
                                    {{ order_product.discount_amount }}
                                    <span class="d-block components-small" v-show="order_product.discount_percentage>0">Discount: {{ order_product.discount_percentage }}%</span>
                                </td>
                                <td class="align-middle text-right">
                                    {{ order_product.tax_amount }}
                                    <span class="d-block components-small" v-for="(tax_component, key, index) in order_product.tax_components" v-bind:key="index">
                                        <span v-show="tax_component.tax_percentage>0">{{ tax_component.tax_type }} : {{ tax_component.tax_amount.toFixed(2) }} ({{ tax_component.tax_percentage }}%)</span>
                                    </span>
                                </td>
                                <td class="align-middle text-right">{{ order_product.total_price }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">{{ $t("Sub Total") }} (EXCL Tax)</td>
                                <td class="text-right">{{ order_basic.sale_amount_subtotal_excluding_tax }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">{{ $t("Discount") }}</td>
                                <td class="text-right">{{ order_basic.total_discount_before_additional_discount }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">{{ $t("Additional Discount") }}</td>
                                <td class="text-right">{{ order_basic.additional_discount_amount }}</td>
                                <small v-if="order_basic.additional_discount_percentage>0" class="d-block">
                                    {{ order_basic.additional_discount_percentage }}%
                                </small>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">{{ $t("Total Discount") }}</td>
                                <td class="text-right">{{ order_basic.total_discount_amount }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">{{ $t("Total After Discount") }}</td>
                                <td class="text-right">{{ order_basic.total_after_discount }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">
                                    {{ $t("Total Tax") }}
                                    <small v-if="order_basic.product_level_total_tax>0" class="d-block">
                                        Product Tax : {{ order_basic.product_level_total_tax }}
                                    </small>
                                    <small v-if="order_basic.order_level_tax_percentage>0" class="d-block">
                                        Overall Tax: 
                                        <span v-for="(tax_component, key, index) in order_basic.order_level_tax_components" v-bind:key="index">
                                            {{ tax_component.tax_type }} : {{ tax_component.tax_amount.toFixed(2) }} ({{ tax_component.tax_percentage }}%) &middot;
                                        </span>
                                    </small>
                                </td>
                                <td class="text-right">{{ order_basic.total_tax_amount }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="text-right">{{ $t("Total") }} (INCL Tax)</td>
                                <td class="text-right text-title">{{ order_basic.total_order_amount }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mb-5">
                    Powered by <img src="/images/logo_word_mark.png" class="powered-by ml-2">
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
                order_processing: false,
                processing      : false,
                
                slack           : this.order_data.slack,
                order_basic     : this.order_data,
                products        : this.order_data.products,
                transactions    : this.order_data.transactions,

                addon_label     : '<i class="fas fa-level-up-alt fa-rotate-90 text-success"></i>'
            }
        },
        props: {
            order_data: [Array, Object],
            company_logo: String
        },
        mounted() {
            console.log('Public order detail page loaded');
        },
        methods: {
           
        }
    }
</script>