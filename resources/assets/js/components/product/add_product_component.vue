<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <div v-if="typeof stock_transfer_product_slack == 'undefined' || stock_transfer_product_slack == ''">
                            <span class="text-title" v-if="product_slack == ''">{{ $t("Add Product") }}</span>
                            <span class="text-title" v-else>{{ $t("Edit Product") }}</span>
                        </div>
                        <div v-else>
                            <span class="text-title">{{ $t("Add Stock Transfer Product") }}</span>
                        </div>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div v-if="(typeof stock_transfer_product_slack != 'undefined') && stock_transfer_product_slack != ''">
                    <div class="d-flex flex-wrap mb-1">
                        <div class="mr-auto">
                            <span class="text-subhead">{{ $t("Stock Transfer Information") }}</span>
                        </div>
                        <div class="">
                            
                        </div>
                    </div>
                    <div class="form-row mb-3 border-bottom">
                        <div class="form-group col-md-3">
                            <label for="stock_transfer_product_code">{{ $t("Source Store Product Code") }}</label>
                            <p>{{ stock_transfer_product.product_code }}</p>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="stock_transfer_product_name">{{ $t("Source Store Product Name") }}</label>
                            <p>{{ stock_transfer_product.product_name }}</p>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="stock_transfer_quantity">{{ $t("Transferred Quantity") }}</label>
                            <p>{{ stock_transfer_product.quantity }}</p>
                        </div>
                        <div class="form-group col-md-3">
                            <label for="stock_transfer_quantity">{{ $t("Current Status") }}</label>
                            <p><span v-bind:class="stock_transfer_product.status.color">{{ stock_transfer_product.status.label }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Product Identifier Information (Optional)") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-6">
                        <div class="custom-control custom-switch ml-1">
                            <input type="checkbox" class="custom-control-input" id="update_is_addon_product" v-model="is_addon_product" v-on:click="choose_product_identifier('IS_ADDON', $event)">
                            <label class="custom-control-label" for="update_is_addon_product">{{ $t("This is an Add-on Product") }}</label>
                            <small class="form-text text-muted">{{ $t("If this option is enabled, product will be considered as an add-on product. Add-on products can only be tagged to a billing product via add-on groups") }}</small>
                        </div>
                    </div>
                    <div class="form-group col-md-6 border-left" v-if="restaurant_mode == 1">
                        <div class="custom-control custom-switch ml-1">
                            <input type="checkbox" class="custom-control-input" id="update_is_ingredient" v-model="is_ingredient" v-on:click="choose_product_identifier('IS_INGREDIENT', $event)">
                            <label class="custom-control-label" for="update_is_ingredient">{{ $t("This Product is an Ingredient") }}</label>
                            <small class="form-text text-muted">{{ $t("If this option is enabled, product will be added as an ingredient") }}</small>
                        </div>
                    </div>
                </div>
                <hr>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Product Information") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="name">{{ $t("Name") }}</label>
                        <input type="text" name="name" v-model="product_name" v-validate="'required|max:250'" class="form-control form-control-custom" :placeholder="$t('Please enter product name')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('name') }">{{ errors.first('name') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="product_code">{{ $t("Product Code") }}</label>
                        <input type="text" name="product_code" v-model="product_code" v-validate="'required|alpha_dash|max:30'" class="form-control form-control-custom" :placeholder="$t('Please enter product code')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('product_code') }">{{ errors.first('product_code') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="supplier">{{ $t("Supplier") }}</label>
                        <select name="supplier" v-model="supplier" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Supplier..</option>
                            <option v-for="(supplier, index) in suppliers" v-bind:value="supplier.slack" v-bind:key="index">
                               {{ supplier.name }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('supplier') }">{{ errors.first('supplier') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="category">{{ $t("Category") }}</label>
                        <select name="category" v-model="category" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Category..</option>
                            <option v-for="(category, index) in categories" v-bind:value="category.slack" v-bind:key="index">
                                {{ category.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('category') }">{{ errors.first('category') }}</span> 
                    </div>
                    
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="status">{{ $t("Status") }}</label>
                        <select name="status" v-model="status" v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('status') }">{{ errors.first('status') }}</span> 
                    </div>
                </div>

                <hr>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Tax & Discount Information") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="tax_code">{{ $t("Tax Code") }}</label>
                        <select name="tax_code" v-model="tax_code" v-validate="'required'" class="form-control form-control-custom custom-select" v-on:change="check_tax_type($event)">
                            <option value="">Choose Tax Code..</option>
                            <option v-for="(taxcode, index) in taxcodes" v-bind:value="taxcode.slack" v-bind:key="index" v-bind:data-tax_type="taxcode.tax_type" v-bind:data-tax_percentage="taxcode.total_tax_percentage">
                                {{ taxcode.tax_code }} - {{ taxcode.label }} [Tax%: {{ taxcode.total_tax_percentage }}] ({{ taxcode.tax_type }})
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('tax_code') }">{{ errors.first('tax_code') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code">{{ $t("Discount Code") }}</label>
                        <select name="discount_code" v-model="discount_code" v-validate="''" class="form-control form-control-custom custom-select">
                            <option value="">Choose Discount Code..</option>
                            <option v-for="(discount_code, index) in discount_codes" v-bind:value="discount_code.slack" v-bind:key="index">
                                {{ discount_code.discount_code }} - {{ discount_code.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('discount_code') }">{{ errors.first('discount_code') }}</span> 
                    </div>
                </div>

                <hr>

                <div class="d-flex flex-wrap mb-1">
                    <div class="mr-auto">
                        <span class="text-subhead">{{ $t("Price & Quantity Information") }}</span>
                    </div>
                    <div class="">
                        
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="purchase_price">{{ $t("Purchase Price Excluding Tax") }} ({{ currency_code }})</label>
                        <input type="number" name='purchase_price' v-model="purchase_price" v-validate="'required|decimal'" class="form-control form-control-custom" :placeholder="$t('Please enter purchase price excluding tax')" autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('purchase_price') }">{{ errors.first('purchase_price') }}</span> 
                    </div>
                    <div class="form-group col-md-3" >
                        <label for="sale_price">{{ $t("Sale Price Excluding Tax") }} ({{ currency_code }})</label>
                        <input type="number" name='sale_price' v-model="sale_price" v-validate="'required|decimal'" class="form-control form-control-custom" :placeholder="$t('Please enter sale price excluding tax')" autocomplete="off" step="0.01" min="0" v-on:input="calculate_sale_prices" :readonly="is_taxcode_inclusive == true">
                        <span v-bind:class="{ 'error' : errors.has('sale_price') }">{{ errors.first('sale_price') }}</span> 
                    </div>
                    <div class="form-group col-md-3" >
                        <label for="sale_price">{{ $t("Sale Price Including Tax") }} ({{ currency_code }})</label>
                        <input type="number" name='sale_price_including_tax' v-model="sale_price_including_tax" v-validate="{required:is_taxcode_inclusive, decimal:true}" class="form-control form-control-custom" :placeholder="$t('Please enter sale price including tax')" autocomplete="off" step="0.01" min="0" v-on:input="calculate_sale_prices" :readonly="is_taxcode_inclusive == false">
                        <span v-bind:class="{ 'error' : errors.has('sale_price_including_tax') }">{{ errors.first('sale_price_including_tax') }}</span> 
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="status">{{ $t("Quantity") }}</label>
                        <input type="number" name='quantity' v-model="quantity" v-validate="quantity_validate" class="form-control form-control-custom" :placeholder="$t('Please enter quantity')"  autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('quantity') }">{{ errors.first('quantity') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">{{ $t("Stock Alert Quantity") }}</label>
                        <input type="number" name='alert_quantity' v-model="alert_quantity" v-validate="'decimal'" class="form-control form-control-custom" :placeholder="$t('Please enter stock alert quantity')"  autocomplete="off" step="0.01" min="0">
                        <span v-bind:class="{ 'error' : errors.has('alert_quantity') }">{{ errors.first('alert_quantity') }}</span> 
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="description">{{ $t("Description") }}</label>
                        <textarea name="description" v-model="description" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Enter description')"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('description') }">{{ errors.first('description') }}</span>
                    </div>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="product_image">{{ $t("Product Image")+" (jpeg, jpg, png, webp)" }}</label>
                        <input type="file" class="form-control-file form-control form-control-custom file-input" name="product_image" ref="product_image" accept="image/x-png,image/jpeg,image/webp" v-validate="'ext:jpg,jpeg,png,webp|size:1500'" multiple="multiple">
                        <small class="form-text text-muted mb-1">Allowed file size per file is 1.5 MB</small>
                        <small class="form-text text-muted">Hold down CTRL or Command for choosing multiple files</small>
                        <span v-bind:class="{ 'error' : errors.has('product_image') }">{{ errors.first('product_image') }}</span> 
                    </div>
                </div>

                <div class="mb-2">
                    <div class="d-flex flex-row flex-wrap">
                        <div class="" v-for="(image, index) in images" v-bind:value="image.slack" v-bind:key="index">
                            <div v-if="image.filename!=''">
                                <button type="button" aria-label="Close" class="close bg-light image-remove" v-on:click="remove_image(image.slack)">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                <img :src="image.thumbnail" alt="" class="rounded mr-3 mb-3" v-on:click="open_image(image.filename)">
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr>

                <div v-if="is_addon_product == 0 && is_ingredient == 0">

                    <div class="form-row mb-2" v-if="variants.length != 0">
                        <div class="form-group col-md-3">
                            <label for="tax_code">{{ $t("Variant Option for Current Product") }}</label>
                            <select name="parent_variant_option" v-model="parent_variant_option" v-validate="'required'" class="form-control form-control-custom custom-select" data-vv-as="Variant Option">
                                <option value="">Choose Variant Option..</option>
                                <option v-for="(variant_option, index) in variant_options" v-bind:value="variant_option.slack" v-bind:key="index">
                                    {{ variant_option.label }}
                                </option>
                            </select>
                            <span v-bind:class="{ 'error' : errors.has('parent_variant_option') }">{{ errors.first('parent_variant_option') }}</span> 
                        </div>
                    </div>

                    <div class="d-flex flex-wrap mb-1">
                        <div class="mr-auto">
                            <span class="text-subhead">{{ $t("Product Variants") }}</span>
                        </div>
                        <div class="">
                            
                        </div>
                    </div>

                    <div>
                        <div class="form-row mb-2">
                            <div class="form-group col-md-4">
                                <label for="variants">{{ $t("Search and Add Variant Products") }}</label>
                                <cool-select type="text" v-model="search_variants"  autocomplete="off" inputForTextClass="form-control form-control-custom" :items="variant_list" item-text="name" itemValue='name' :resetSearchOnBlur="false" disable-filtering-by-search @search='load_variants' @select='add_variant_to_list' :placeholder="$t('Start Typing..')">
                                        <template #item="{ item }">
                                        <div class='d-flex justify-content-start'>
                                        <div>
                                            {{ item.product_code }} - {{ item.name }}
                                        </div>
                                        </div>
                                    </template>
                                </cool-select>
                            </div>
                        </div>

                        <div v-if="variants.length != 0">
                            <div class="form-row"> 
                                <div class="form-group col-md-3 mb-1">
                                    <label for="name">{{ $t("Variant Option") }}</label>
                                </div>
                                <div class="form-group col-md-6 mb-1">
                                    <label for="name">{{ $t("Name & Description") }}</label>
                                </div>
                                <div class="form-group col-md-2 mb-1">
                                    <label for="sale_price">{{ $t("Sale Price") }} ({{ currency_code }})</label>  
                                </div>
                            </div>
                            <div class="form-row mb-1" v-for="(variant, index) in variants" :key="index">
                                <div class="form-group col-md-3">
                                    <select v-bind:name="'variant.variant_option_'+index" v-model="variant.variant_option_slack" v-validate="'required'" class="form-control form-control-custom custom-select" data-vv-as="Variant Option">
                                        <option value="">Choose Variant Option..</option>
                                        <option v-for="(variant_option, index) in variant_options" v-bind:value="variant_option.slack" v-bind:key="index">
                                            {{ variant_option.label }}
                                        </option>
                                    </select>
                                    <span v-bind:class="{ 'error' : errors.has('variant.variant_option_'+index) }">{{ errors.first('variant.variant_option_'+index) }}</span> 
                                </div>
                                <div class="form-group col-md-6">
                                    <input type="text" v-bind:name="'variant.name_'+index" v-model="variant.product_code+'-'+variant.name" v-validate="'max:250'" data-vv-as="Name" class="form-control form-control-custom" autocomplete="off" readonly="true">
                                    <span v-bind:class="{ 'error' : errors.has('variant.name_'+index) }">{{ errors.first('variant.name_'+index) }}</span> 
                                </div>
                                <div class="form-group col-md-2">
                                    <input type="number" v-bind:name="'variant.sale_price_'+index" v-model="variant.sale_price" v-validate="variant.name != ''?'required|decimal|min_value:0.01':''" data-vv-as="Sale Price" class="form-control form-control-custom" autocomplete="off" step="0.01" min="0" readonly="true">
                                    <span v-bind:class="{ 'error' : errors.has('variant.sale_price_'+index) }">{{ errors.first('variant.sale_price_'+index) }}</span> 
                                </div>
                                <div class="form-group col-md-1" v-if="variant.variant_slack != product_slack">
                                    <button type="button" class="btn btn-outline-danger" @click="remove_variant(index, variant.product_variant_slack)"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        <div v-if="variants.length == 0">
                            <span class="text-muted">No variants selected!</span>
                        </div>
                    </div>
                </div>
                
                <hr>
                
                <div v-if="is_addon_product == 0 && is_ingredient == 0">
                    <div class="d-flex flex-wrap mb-1">
                        <div class="mr-auto">
                            <span class="text-subhead">{{ $t("Choose Add-on Groups") }}</span>
                        </div>
                        <div class="">
                            
                        </div>
                    </div>

                    <div class="form-row mb-2">
                        <div class="form-group col-md-3">
                            <label for="description">{{ $t("Add-on Groups") }}</label>
                            <multiselect  v-model="addon_group_values" :options="addon_groups" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Choose Add-on Groups" label="label" track-by="slack" :preselect-first="false">
                                <template slot="selection" class="form-control" slot-scope="{ values, isOpen }"><span class="multiselect__single" v-if="values.length &amp;&amp; !isOpen">{{ values.length }} options selected</span></template>
                            </multiselect>
                        </div>
                    </div>
                    <hr>
                </div>

                <div v-if="restaurant_mode == 1 && is_ingredient == 0 && is_addon_product == 0">
                    <div class="d-flex flex-wrap mb-1">
                        <div class="mr-auto">
                            <span class="text-subhead">{{ $t("Ingredient Information") }}</span>
                        </div>
                        <div class="">
                            
                        </div>
                    </div>

                    <div>
                        <div class="form-row mb-2">
                            <div class="form-group col-md-4">
                                <label for="ingredients">{{ $t("Search and Add Ingredients") }}</label>
                                <cool-select type="text" v-model="search_ingredients"  autocomplete="off" inputForTextClass="form-control form-control-custom" :items="ingredient_list" item-text="name" itemValue='name' :resetSearchOnBlur="false" disable-filtering-by-search @search='load_ingredients' @select='add_ingredient_to_list' :placeholder="$t('Start Typing..')">
                                     <template #item="{ item }">
                                        <div class='d-flex justify-content-start'>
                                        <div>
                                            {{ item.product_code }} - {{ item.name }}
                                        </div>
                                        </div>
                                    </template>
                                </cool-select>
                                <small class="form-text text-muted">Choose ingredients for preparing 1 Unit or Quantity of the product</small>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-4 mb-1">
                                <label for="name">{{ $t("Name & Description") }}</label>
                            </div>
                            <div class="form-group col-md-2 mb-1">
                                <label for="purchase_price">{{ $t("Purchase Price of 1 Unit") }}</label>  
                            </div>
                            <div class="form-group col-md-2 mb-1">
                                <label for="sale_price">{{ $t("Sale Price of 1 Unit") }}</label>  
                            </div>
                            <div class="form-group col-md-1 mb-1">
                                <label for="quantity">{{ $t("Quantity") }}</label>  
                            </div>
                            <div class="form-group col-md-2 mb-1">
                                <label for="measurement_unit">{{ $t("Measuring Unit") }}</label>  
                            </div>
                        </div>

                        <div class="form-row mb-2" v-for="(ingredient, index) in ingredients" :key="index">
                            <div class="form-group col-md-4">
                                <input type="text" v-bind:name="'ingredient.name_'+index" v-model="ingredient.name" v-validate="'max:250'" data-vv-as="Name" class="form-control form-control-custom" autocomplete="off" readonly="true">
                                <span v-bind:class="{ 'error' : errors.has('ingredient.name_'+index) }">{{ errors.first('ingredient.name_'+index) }}</span> 
                            </div>
                            <div class="form-group col-md-2">
                                <input type="number" v-bind:name="'ingredient.purchase_price_'+index" v-model="ingredient.purchase_price" v-validate="ingredient.name != ''?'required|decimal|min_value:0.01':''" data-vv-as="Purchase Price" class="form-control form-control-custom" autocomplete="off" step="0.01" min="0" readonly="true">
                                <span v-bind:class="{ 'error' : errors.has('ingredient.purchase_price_'+index) }">{{ errors.first('ingredient.purchase_price_'+index) }}</span> 
                            </div>
                            <div class="form-group col-md-2">
                                <input type="number" v-bind:name="'ingredient.sale_price_'+index" v-model="ingredient.sale_price" v-validate="ingredient.name != ''?'required|decimal|min_value:0.01':''" data-vv-as="Sale Price" class="form-control form-control-custom" autocomplete="off" step="0.01" min="0" readonly="true">
                                <span v-bind:class="{ 'error' : errors.has('ingredient.sale_price_'+index) }">{{ errors.first('ingredient.sale_price_'+index) }}</span> 
                            </div>
                            <div class="form-group col-md-1">
                                <input type="number" v-bind:name="'ingredient.quantity_'+index" v-model="ingredient.quantity" v-validate="ingredient.name != ''?'required|decimal|min_value:0.01':''" data-vv-as="Quantity" class="form-control form-control-custom" autocomplete="off" step="0.01" min="0" v-on:change="update_ingredient_prices">
                                <span v-bind:class="{ 'error' : errors.has('ingredient.quantity_'+index) }">{{ errors.first('ingredient.quantity_'+index) }}</span> 
                            </div>
                            <div class="form-group col-md-2">
                                <select v-bind:name="'ingredient.unit_'+index" v-model="ingredient.unit_slack" v-validate="''" class="form-control form-control-custom custom-select">
                                    <option value="">Choose Measurement Unit..</option>
                                    <option v-for="(measurement_unit, index) in measurement_units" v-bind:value="measurement_unit.slack" v-bind:key="index">
                                        {{ measurement_unit.unit_code }} - {{ measurement_unit.label }}
                                    </option>
                                </select>
                                <span v-bind:class="{ 'error' : errors.has('ingredient.unit_'+index) }">{{ errors.first('ingredient.unit_'+index) }}</span> 
                            </div>
                            <div class="form-group col-md-1" >
                                <button type="button" class="btn btn-outline-danger" @click="remove_ingredient(index)"><i class="fas fa-times"></i></button>
                            </div>
                        </div>

                        <div class="form-row mb-2">
                            <div class="form-group col-md-3">
                                <label for="description">{{ $t("Total Ingredient Purchase Price") }}</label>
                                <p>{{ currency_code }} {{ ingredient_purchase_price }}</p>
                            </div>
                            <div class="form-group col-md-3">
                                <label for="description">{{ $t("Total Ingredient Selling Price Excluding Tax") }}</label>
                                <p>{{ currency_code }} {{ ingredient_selling_price }}</p>
                            </div>
                        </div>
                        <div class="form-row mb-2">
                            <div class="form-group col-md-6">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_ingredient_price" v-model="is_ingredient_price" @change='update_product_prices()' :disabled="is_taxcode_inclusive == true">
                                    <label class="custom-control-label" for="is_ingredient_price">{{ $t("Set Product Price as Ingredient Cost") }}</label>
                                    <small class="form-text text-muted" v-show="is_taxcode_inclusive == false">{{ $t("If this option is enabled, product sale price and purchase price will be replaced with ingredient cost") }}</small>
                                    <small class="form-text text-muted" v-show="is_taxcode_inclusive == true"><i class="fas fa-info-circle text-warning"></i> {{ $t("Option not available if the tax is INCLUSIVE") }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </form>
                
        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">Product status is inactive.</p>
                {{ $t("Are you sure you want to proceed?") }}
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="$emit('close')">Cancel</button>
                <button type="button" class="btn btn-primary" @click="$emit('submit')" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Continue</button>
            </template>
        </modalcomponent>
        
    </div>
</template>

<script>
    'use strict';
    
    import { CoolSelect } from "vue-cool-select";
    import 'vue-cool-select/dist/themes/bootstrap.css';

    import Multiselect from 'vue-multiselect';
    import 'vue-multiselect/dist/vue-multiselect.min.css';

    export default {
        components: { Multiselect },
        data(){
            return{
                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.product_data == null)?'/api/add_product':'/api/update_product/'+this.product_data.slack,

                product_slack   : (this.product_data == null)?'':this.product_data.slack,
                product_name    : (this.product_data == null)?'':this.product_data.name,
                product_code    : (this.product_data == null)?'':this.product_data.product_code,
                description     : (this.product_data == null)?'':this.product_data.description,
                supplier        : (this.product_data == null)?'':(this.product_data.supplier == null)?'':this.product_data.supplier.slack,
                category        : (this.product_data == null)?'':(this.product_data.category == null)?'':this.product_data.category.slack,
                tax_code        : (this.product_data == null)?'':(this.product_data.tax_code == null)?'':this.product_data.tax_code.slack,
                discount_code   : (this.product_data == null)?'':(this.product_data.discount_code == null)?'':this.product_data.discount_code.slack,
                quantity        : (this.product_data == null)?'':this.product_data.quantity,
                alert_quantity  : (this.product_data == null)?'':this.product_data.alert_quantity,
                sale_price      : (this.product_data == null)?'':this.product_data.sale_amount_excluding_tax,
                sale_price_including_tax : (this.product_data == null)?'':this.product_data.sale_amount_including_tax,
                purchase_price  : (this.product_data == null)?'':this.product_data.purchase_amount_excluding_tax,    
                status          : (this.product_data == null)?'':this.product_data.status.value,
                images          : (this.product_data == null)?'':this.product_data.images,
                currency_code   : window.settings.currency_code,

                stock_transfer_max_quantity : (this.stock_transfer_data == null)?'':this.stock_transfer_product_data.quantity,
                stock_transfer_product_slack : (this.stock_transfer_data == null)?'':this.stock_transfer_product_data.slack,
                stock_transfer : (this.stock_transfer_data == null)?'':this.stock_transfer_data,
                stock_transfer_product : (this.stock_transfer_product_data == null)?'':this.stock_transfer_product_data,

                quantity_validate : {
                    required : true,
                    decimal : true
                },

                is_ingredient : (this.product_data == null)?false:(this.product_data.is_ingredient != null)?((this.product_data.is_ingredient == 1)?true:false):false,

                ingredient_list   : [],
                search_ingredients : '',
                ingredient_template : {
                    ingredient_slack: '',
                    name : '',
                    sale_price: '',
                    purchase_price: '',
                    quantity : '',
                    unit_slack : ''
                },

                product_ingredient_list : (this.product_data != null)?this.product_data.ingredients:[],

                ingredients : [],

                restaurant_mode : window.settings.restaurant_mode,
                ingredient_purchase_price: 0,
                ingredient_selling_price: 0,

                is_ingredient_price : (this.product_data == null)?false:(this.product_data.is_ingredient_price != null)?((this.product_data.is_ingredient_price == 1)?true:false):false,

                is_addon_product : (this.product_data == null)?false:(this.product_data.is_addon_product != null)?((this.product_data.is_addon_product == 1)?true:false):false,

                addon_group_values : [],

                variant_list   : [],
                search_variants : '',
                variant_template : {
                    product_variant_slack : '',
                    variant_option_slack: '',
                    variant_slack: '',
                    name : '',
                    product_code : '',
                    sale_price: '',
                },

                product_variant_list : (this.product_data != null)?this.product_data.variants:[],

                variants : [],

                parent_variant_option : (this.product_data == null)?'':this.product_data.parent_variant_option,

                // is_taxcode_inclusive : (typeof this.taxcode_inclusive  != 'undefined')?this.taxcode_inclusive:false,
                is_taxcode_inclusive : false,
                selected_tax_percentage : (typeof this.taxcode_percentage  != 'undefined')?this.taxcode_percentage:0,
            }
        },
        props: {
            statuses: [Array, Object],
            suppliers: [Array, Object],
            categories: [Array, Object],
            taxcodes: [Array, Object],
            discount_codes: [Array, Object],
            product_data: [Array, Object],
            stock_transfer_data: [Array, Object],
            stock_transfer_product_data: [Array, Object],
            measurement_units: [Array, Object],
            addon_groups: [Array, Object],
            variant_options: [Array, Object],
            taxcode_inclusive: Boolean,
            taxcode_percentage: [String, Number]
        },
        mounted() {
            console.log('Add product page loaded');
        },
        created(){
            this.set_product_quantity_validation();
            this.update_ingredient_list(this.product_ingredient_list);
            if(this.product_data != null && this.product_data.addon_groups != null){
                this.update_addon_group_list(this.product_data.addon_groups);
            }
            this.update_variant_list(this.product_variant_list);
        },
        methods: {
            submit_form(){

                this.$off("submit");
                this.$off("close");

                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();

                            for( var i = 0; i < this.$refs.product_image.files.length; i++ ){
                                let file = this.$refs.product_image.files[i];
                                formData.append('product_images[' + i + ']', file);
                            }

                            formData.append("access_token", window.settings.access_token);
                            formData.append("product_name", (this.product_name == null)?'':this.product_name.toUpperCase());
                            formData.append("product_code", (this.product_code == null)?'':this.product_code);
                            formData.append("supplier", (this.supplier == null)?'':this.supplier);
                            formData.append("category", (this.category == null)?'':this.category);
                            formData.append("tax_code", (this.tax_code == null)?'':this.tax_code);
                            formData.append("discount_code", (this.discount_code == null)?'':this.discount_code);
                            formData.append("status", (this.status == null)?'':this.status);
                            formData.append("quantity", (this.quantity == null)?'':this.quantity);
                            formData.append("alert_quantity", (this.alert_quantity == null)?'':this.alert_quantity);
                            formData.append("sale_price", (this.sale_price == null)?'':this.sale_price);
                            formData.append("sale_amount_including_tax", (this.sale_price_including_tax == null)?'':this.sale_price_including_tax);
                            formData.append("purchase_price", (this.purchase_price == null)?'':this.purchase_price);
                            formData.append("description", (this.description == null)?'':this.description);
                            formData.append("is_ingredient", (this.is_ingredient == true)?1:0);
                            formData.append("ingredients", (this.is_ingredient == false)?JSON.stringify(this.ingredients):[]); 
                            formData.append("is_ingredient_price", (this.is_ingredient_price == true)?1:0);
                            formData.append("stock_transfer_product_slack", (this.stock_transfer_product_data == null)?'':this.stock_transfer_product_data.slack);
                            formData.append("is_addon_product", (this.is_addon_product == true)?1:0);
                            formData.append("addon_group_values", (this.addon_group_values.length == 0)?[]:JSON.stringify(this.addon_group_values));
                            formData.append("variants", (this.variants.length == 0)?[]:JSON.stringify(this.variants));
                            formData.append("parent_variant_option", (this.parent_variant_option == null)?'':this.parent_variant_option);

                            axios.post(this.api_link, formData).then((response) => {
                                if(response.data.status_code == 200) {
                                    this.show_response_message(response.data.msg, 'SUCCESS');

                                    if(typeof response.data.link != 'undefined' && response.data.link != ""){

                                        if(response.data.new_tab == true){
                                            window.open(response.data.link, '_blank');
                                        }else{
                                            window.location.href = response.data.link;
                                        }

                                        setTimeout(function(){
                                            location.reload();
                                        }, 1000);
                                    }else{
                                        setTimeout(function(){
                                            location.reload();
                                        }, 1000);
                                    }
                                    
                                }else{
                                    this.show_modal = false;
                                    this.processing = false;
                                    try{
                                        var error_json = JSON.parse(response.data.msg);
                                        this.loop_api_errors(error_json);
                                    }catch(err){
                                        this.server_errors = response.data.msg;
                                    }
                                    this.error_class = 'error';
                                }
                            })
                            .catch((error) => {
                                console.log(error);
                            });

                        });
                        this.$on("close",function () {
                            this.show_modal = false;
                        });
                    }
                });
            },

            set_product_quantity_validation(){
                if(typeof this.stock_transfer_product_slack != 'undefined' && this.stock_transfer_product_slack != ''){
                    this.quantity_validate = {
                        required : true,
                        decimal : true,
                        min_value: 0.01,
                        max_value: this.stock_transfer_max_quantity
                    }
                }
            },

            remove_image(image_slack){
                if (image_slack != '') {

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);
                    formData.append("image_slack", image_slack);

                    axios.post('/api/delete_product_image', formData).then((response) => {
                        if(response.data.status_code == 200) {
                            this.show_response_message(response.data.msg, 'SUCCESS');
                            setTimeout(function(){
                                location.reload();
                            }, 1000);
                        }else{
                            try{
                                var error_json = JSON.parse(response.data.msg);
                                this.loop_api_errors(error_json);
                            }catch(err){
                                this.server_errors = response.data.msg;
                            }
                            this.error_class = 'error';
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                }
            },

            open_image(image_link){
                window.open(image_link, '_blank');
            },

            load_ingredients (keywords) {
                if(typeof keywords != 'undefined'){
                    if (keywords.length > 0) {

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("keywords", keywords);

                        axios.post('/api/load_ingredients', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.ingredient_list = response.data.data;
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                }
            },

            add_ingredient_to_list(item) {
                
                if( item.slack != '' ){
                    var current_ingredient = {
                        ingredient_slack : item.slack,
                        name : item.name,
                        quantity : 1,
                        sale_price: item.sale_amount_excluding_tax,
                        purchase_price: item.purchase_amount_excluding_tax,
                        unit_slack : ''
                    };
                }

                var item_found = false;
                for(var i = 0; i < this.ingredients.length; i++){   
                    if(this.ingredients[i].ingredient_slack == item.slack){
                        this.ingredients[i].quantity++;
                        item_found = true;
                    }
                }

                if(this.ingredients[0].name == '' && this.ingredients[0].quantity == ''){
                    this.$set(this.ingredients, 0, current_ingredient);
                }else{
                    if(item_found == false){
                        this.ingredients.push(current_ingredient);
                    }
                }
                this.ingredient_list = [];
                this.update_ingredient_prices();
            },

            remove_ingredient(index) {
                this.ingredients.splice(index, 1);
                if(index == 0){
                    this.update_ingredient_list();
                }
            },

            update_ingredient_list(ingredient_list) {
                if(ingredient_list != null && ingredient_list.length > 0){
                    this.ingredients = [];
                    for (let i = 0; i < ingredient_list.length; i++) {
                        var individual_product = {
                            ingredient_slack: ingredient_list[i].ingredient_product.slack,
                            name : ingredient_list[i].ingredient_product.name,
                            quantity : ingredient_list[i].quantity,
                            sale_price: ingredient_list[i].ingredient_product.sale_amount_excluding_tax,
                            purchase_price: ingredient_list[i].ingredient_product.purchase_amount_excluding_tax,
                            unit_slack : (ingredient_list[i].measurement_unit == null)?'':ingredient_list[i].measurement_unit.slack
                        };
                        this.ingredients.push(individual_product);
                    }
                }else{
                    this.ingredients = [];
                    this.ingredients.push(this.ingredient_template);
                }
                this.update_ingredient_prices();
            },

            update_ingredient_prices(){

                this.ingredient_purchase_price = 0.00;
                this.ingredient_selling_price = 0.00;

                for (let i = 0; i < this.ingredients.length; i++) {
                    var ingredient_data = this.ingredients[i];
                    if(ingredient_data.quantity != ""){
                        
                        var quantity = parseFloat(ingredient_data.quantity);
                        if(!isNaN(quantity)){
                            var selling_price = parseFloat(quantity)*parseFloat(ingredient_data.sale_price);
                            var purchase_price = parseFloat(quantity)*parseFloat(ingredient_data.purchase_price);
                            this.ingredient_selling_price += parseFloat(selling_price);
                            this.ingredient_purchase_price += parseFloat(purchase_price);
                        }
                    }
                }
                this.ingredient_selling_price = this.ingredient_selling_price.toFixed(2);
                this.ingredient_purchase_price = this.ingredient_purchase_price.toFixed(2);
                this.update_product_prices();
            },

            choose_product_identifier(type, event) {
                var checked = event.target.checked;

                switch(type){
                    case 'IS_INGREDIENT':
                        if (checked) {
                            this.is_addon_product = false;
                            this.is_ingredient = true;
                        }
                    break;
                    case 'IS_ADDON':
                        if (checked) {
                            this.is_ingredient = false;
                            this.is_addon_product = true;
                        }
                    break;
                }
            },

            update_addon_group_list(selected_addon_groups) {
                if(selected_addon_groups.length>0){
                    for (let i = 0; i < selected_addon_groups.length; i++) {
                        var add_on_values = {
                            'slack' : selected_addon_groups[i].addon_group.slack,
                            'label' : selected_addon_groups[i].addon_group.label,
                            'addon_group_code' : selected_addon_groups[i].addon_group.addon_group_code,
                        };
                        this.addon_group_values.push(add_on_values);
                    }
                }
            },

            update_product_prices(){
                if(this.is_ingredient_price == true){
                    this.sale_price = this.ingredient_selling_price;
                    this.purchase_price = this.ingredient_purchase_price;
                    this.calculate_sale_prices();
                }
            },

            load_variants (keywords) {
                if(typeof keywords != 'undefined'){
                    if (keywords.length > 0) {

                        var formData = new FormData();
                        formData.append("access_token", window.settings.access_token);
                        formData.append("keywords", keywords);
                        formData.append("current_product", this.product_slack);

                        axios.post('/api/load_variant_products', formData).then((response) => {
                            if(response.data.status_code == 200) {
                                this.variant_list = response.data.data;
                            }
                        })
                        .catch((error) => {
                            console.log(error);
                        });
                    }
                }
            },

            add_variant_to_list(item) {
                
                if( item.slack != '' ){
                    var current_variant = {
                        product_variant_slack : '',
                        variant_slack : item.slack,
                        name : item.name,
                        product_code : item.product_code,
                        sale_price: item.sale_amount_excluding_tax,
                        variant_option_slack: '',
                    };
                }

                var item_found = false;
                for(var i = 0; i < this.variants.length; i++){   
                    if(this.variants[i].variant_slack == item.slack){
                        item_found = true;
                    }
                }
                if(item_found == false){
                    this.variants.push(current_variant);
                }
                this.variant_list = [];
            },

            remove_variant(index, product_variant_slack = '') {
                this.variants.splice(index, 1);

                if (product_variant_slack != '') {

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);
                    formData.append("variant_slack", product_variant_slack);

                    axios.post('/api/remove_variant_product', formData).then((response) => {
                        if(response.data.status_code == 200) {
                            this.show_response_message(response.data.msg, 'SUCCESS');
                        }
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                }
            },

            update_variant_list(variant_list) {
                if(variant_list != null && variant_list.length > 0){
                    this.variants = [];
                    for (let i = 0; i < variant_list.length; i++) {
                        var individual_product = {
                            product_variant_slack : variant_list[i].slack,
                            variant_slack : variant_list[i].product['slack'],
                            name : variant_list[i].product['name'],
                            product_code : variant_list[i].product['product_code'],
                            sale_price: variant_list[i].product['sale_amount_excluding_tax'],
                            variant_option_slack: variant_list[i].variant_option['slack']
                        };
                        this.variants.push(individual_product);
                    }
                }
            },

            check_tax_type(tax_type_item){
                var data = tax_type_item.target.options[tax_type_item.target.options.selectedIndex].dataset;
                // var tax_type = data.tax_type;
                var tax_type = 'EXCLUSIVE'
                var tax_percentage = data.tax_percentage;
                this.selected_tax_percentage = tax_percentage;

                if(tax_type == 'INCLUSIVE'){
                    this.is_taxcode_inclusive = true;
                    this.is_ingredient_price = 0;
                }else{
                    this.is_taxcode_inclusive = false;
                }

                this.sale_price = '';
                this.sale_price_including_tax = '';
            },

            calculate_tax(item_total, tax_percentage){
                var tax_amount = (parseFloat(tax_percentage)/100)*parseFloat(item_total);
                return tax_amount.toFixed(2);
            },

            calculate_sale_prices(){
                if(this.is_taxcode_inclusive == true){
                    var calculated_tax = this.calculate_tax(this.sale_price_including_tax, this.selected_tax_percentage);
                    var sale_price_excluding_tax = parseFloat(this.sale_price_including_tax)-parseFloat(calculated_tax);
                    this.sale_price = sale_price_excluding_tax; 
                }else{
                    var calculated_tax = this.calculate_tax(this.sale_price, this.selected_tax_percentage);
                    var sale_price_including_tax = parseFloat(this.sale_price)+parseFloat(calculated_tax);
                    this.sale_price_including_tax = sale_price_including_tax;
                }
            }
        }
    }
</script>