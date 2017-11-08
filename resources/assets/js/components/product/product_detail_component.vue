<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Product") }} <span v-if="restaurant_mode == 1 && product.is_ingredient == 1">{{ $t("Ingredient") }}</span><span v-if="product.is_addon_product == 1">{{ $t("Add-on") }}</span></span> {{ product.name }} ({{ product.product_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    
                    <span v-bind:class="product.status.color">{{ product.status.label }}</span>
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4">

                <div class="ml-auto">
                    <button type="button" class="btn btn-outline-primary mr-1" v-on:click="view_addons()" v-if="product.is_ingredient == 0 && product.is_addon_product == 0 && product.addon_groups.length>0"> {{ $t("View Add-ons") }}</button>
                    <button type="button" class="btn btn-outline-primary mr-1" v-on:click="view_ingredients()" v-if="restaurant_mode == 1 && product.is_ingredient == 0 && product.is_addon_product == 0"> {{ $t("View Ingredients") }}</button>
                </div>

            </div>
            <hr>
            
            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="product_code">{{ $t("Product Code") }}</label>
                    <p>{{ product.product_code }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">{{ $t("Name") }}</label>
                    <p>{{ product.name }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">{{ $t("Supplier") }}</label>
                    <p>{{ product.supplier.name }} ({{product.supplier.supplier_code}})</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="phone">{{ $t("Category") }}</label>
                    <p>{{ product.category.label }} ({{product.category.category_code}})</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (product.created_by == null)?'-':product.created_by['fullname']+' ('+product.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (product.updated_by == null)?'-':product.updated_by['fullname']+' ('+product.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ product.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ product.updated_at_label }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Price and Quantity Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="purchase_amount_excluding_tax">{{ $t("Purchase Price Excluding Tax") }}</label>
                    <p>{{ product.purchase_amount_excluding_tax }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="sale_amount_excluding_tax">{{ $t("Sale Price Excluding Tax") }}</label>
                    <p>{{ product.sale_amount_excluding_tax }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="sale_amount_excluding_tax">{{ $t("Sale Price Including Tax") }}</label>
                    <p>{{ product.sale_amount_including_tax }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="quantity">{{ $t("Quantity") }}</label>
                    <p>{{ product.quantity }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="alert_quantity">{{ $t("Stock Alert Quantity") }}</label>
                    <p>{{ product.alert_quantity }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-3">
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Tax Information") }}</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="tax_code">{{ $t("Tax Code") }}</label>
                        <p>{{ product.tax_code.tax_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_percentage">{{ $t("Tax Percentage") }}</label>
                        <p>{{ product.tax_code.total_tax_percentage }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_percentage">{{ $t("Tax Type") }}</label>
                        <p>{{ product.tax_code.tax_type_label }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_code_label">{{ $t("Tax Name") }}</label>
                        <p>{{ product.tax_code.label }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_code_description">{{ $t("Tax Description") }}</label>
                        <p>{{ (product.tax_code.description)?product.tax_code.description:'-' }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="table-responsive" v-if="product.tax_code.total_tax_percentage > 0">
                            <table class="table display nowrap text-nowrap w-100">
                                <thead>
                                    <tr>
                                    <th scope="col">{{ $t("Tax Type") }}</th>
                                    <th scope="col">{{ $t("Tax Percentage") }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tax_component, key, index) in product.tax_code.tax_components" v-bind:key="index">
                                        <td>{{ tax_component.tax_type }}</td>
                                        <td>{{ tax_component.tax_percentage }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <span class="mb-2" v-else>No Tax Components</span>
                    </div>
                </div>
            </div>
            <hr>

            <div class="mb-3">
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Discount Information") }}</span>
                </div>
                <div class="form-row mb-2" v-if="product.discount_code != null">
                    <div class="form-group col-md-3">
                        <label for="discount_code">{{ $t("Discount Code") }}</label>
                        <p>{{ product.discount_code.discount_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_percentage">{{ $t("Discount Percentage") }}</label>
                        <p>{{ product.discount_code.discount_percentage }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code_label">{{ $t("Discount Name") }}</label>
                        <p>{{ product.discount_code.label }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code_description">{{ $t("Discount Description") }}</label>
                        <p>{{ product.discount_code.description }}</p>
                    </div>
                </div>
                <div class="mb-3" v-else>No Discount Information</div>
            </div>
            <hr>

            <div class="form-row mb-2">
                <div class="form-group col-md-6">
                    <label for="description">{{ $t("Product Description") }}</label>
                    <p>{{ product.description }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-3" v-if="Object.keys(product.variants_by_options).length>0">
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Variants") }}</span>
                </div>
                <div class="form-row mb-2" v-for="(variants_by_option_item, option) in product.variants_by_options" :key="option">
                    <span class="mb-1 pl-1 text-subhead-bold">{{ option }}</span>
                    <div class="col-md-12" v-for="(variants_by_option_product, index) in variants_by_option_item" :key="index">
                        <div class="mb-1">
                            {{ variants_by_option_product.product.product_code }} - {{ variants_by_option_product.product.name }} 
                            <a :href="variants_by_option_product.product.detail_link" v-show="variants_by_option_product.product.detail_link != ''" class="ml-2" target="_blank">
                                <i class="fas fa-link"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <hr>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-12">
                    <label for="images">{{ $t("Images") }}</label>
                    <div class="d-flex flex-wrap">
                        <div class="" v-for="(image, index) in images" v-bind:value="image.slack" v-bind:key="index">
                            <div v-if="image.filename!=''">
                                <img :src="image.thumbnail" alt="" class="rounded mr-3 mb-3" v-on:click="open_image(image.filename)">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <modalcomponent v-if="show_ingredient_modal" v-on:close="show_ingredient_modal = false" :modal_width="'modal-container-xl'">
            <template v-slot:modal-header>
                Ingredients
            </template>
            <template v-slot:modal-body>
                <div class="row">
                    <div class="col-md-12">
                        <div v-if="ingredient_list.length>0">
                            <div class="form-row">
                                <div class="form-group col-md-5 mb-1">
                                    <label for="name">{{ $t("Name & Description") }}</label>
                                </div>
                                <div class="form-group col-md-2 mb-1">
                                    <label for="quantity">{{ $t("Quantity") }}</label>  
                                </div>
                                <div class="form-group col-md-3 mb-1">
                                    <label for="tax_percentage">{{ $t("Measuring Unit") }}</label>  
                                </div>
                            </div>

                            <div class="form-row mb-2" v-for="(ingredient, index) in ingredient_list" :key="index">
                                <div class="form-group col-md-5">
                                    {{ ingredient.ingredient_product.product_code }} - {{ ingredient.ingredient_product.name }}
                                </div>
                                <div class="form-group col-md-2">
                                    {{ ingredient.quantity }}
                                </div>
                                <div class="form-group col-md-3">
                                    {{ (ingredient.measurement_unit == null)?'-':ingredient.measurement_unit.label }}
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <p>No ingredients added</p>    
                        </div>   
                    </div> 
                </div>
            </template>
            <template v-slot:modal-footer>
            </template>
        </modalcomponent>

        <modalcomponent v-if="show_addons_modal" v-on:close="show_addons_modal = false" >
            <template v-slot:modal-header>
                Add-ons
            </template>
            <template v-slot:modal-body>
                <div class="row">
                    <div class="col-md-12">
                        <div v-if="product.addon_groups.length>0">
                            <div class="mb-3" v-for="(addon_group_data, key, index) in product.addon_groups" v-bind:key="index">
                                <p class="text-subhead-bold">{{ addon_group_data.addon_group.label }} <span class="small text-muted">({{ (addon_group_data.addon_group.multiple_selection == 1)?'Choose Multiple':'Choose One' }})</span></p>
                                <div class="pl-2" v-for="(addon_group_data_product, key, index) in addon_group_data.addon_group.products" v-bind:key="index">
                                    <p>{{ addon_group_data_product.product.product_code }} - {{ addon_group_data_product.product.name }}</p>
                                </div>
                            </div>
                        </div>
                        <div v-else>
                            <p>No Add-ons added</p>    
                        </div>   
                    </div> 
                </div>
            </template>
            <template v-slot:modal-footer>
            </template>
        </modalcomponent>
    </div>
</template>  

<script>
    'use strict';
    
    export default {
        data(){
            return{
                product : this.product_data,
                images : (this.product_data == null)?[]:this.product_data.images,

                restaurant_mode : window.settings.restaurant_mode,
                show_ingredient_modal: false,
                show_addons_modal: false,
                ingredient_list: (this.product_data != null)?((this.product_data.ingredients != null)?this.product_data.ingredients:[]):[]
            }
        },
        props: {
            product_data: [Array, Object]
        },
        mounted() {
            console.log('Product detail page loaded');
        },
        methods: {
            open_image(image_link){
                window.open(image_link, '_blank');
            },

            view_ingredients(){
                this.show_ingredient_modal = true;
            },

            view_addons(){
                this.show_addons_modal = true;
            }
        }
    }
</script>