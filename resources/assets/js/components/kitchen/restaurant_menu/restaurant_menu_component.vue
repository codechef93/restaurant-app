<template>
    <div class="">
        <div class="container pl-0 pr-0">
            <div class="d-flex justify-content-center">
                <div class="col-md-10 col-sm-12 pl-0 pr-0">

                    <div class="d-flex justify-content-between pt-4 pb-4 pl-3 pr-3 border-bottom">
                        <div class="">
                            <img :src="navbar_logo" alt="" class="rounded-circle nav-logo-image mr-2" style="height:50px">
                            <span class="store">{{ store.name }}</span>
                        </div>
                        <div class="d-flex flex-row">
                            <div class="mr-4">
                                <i class="fas fa-history top-icons cursor" v-on:click="show_order_history"
                                    title="Your orders"></i>
                            </div>
                            <div class="mr-2" v-show="show_cart_button">
                                <i class="fas fa-shopping-bag top-icons cursor" v-on:click="show_cart_section"></i>
                                <span class="cart_counter cart-badge">{{ item_count }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Items Section -->
                    <div class="bg-white" v-if="show_cart == false && order_success == false && order_history == false">

                        <div class="mr-auto">
                            <div class="d-flex flex-column">
                                <div class="border-left border-right border-bottom">
                                    <input type="text" name="filter" v-model="filter"
                                        class="form-control form-control-custom border-0 search bg-light"
                                        :placeholder="$t('Search items')" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="mr-auto">
                            <div class="d-flex flex-column">
                                <div class="border-bottom">
                                    <span class="row flex-nowrap menu-scroll mt-3 mb-3 ml-2 mr-2">
                                        <a class="d-flex flex-column text-nowrap badge badge-pill badge-primary mr-2"
                                            :href="'#' + category_array_item.label"
                                            v-for="(category_array_item, index) in category_array" v-bind:key="index">{{
                                                    category_array_item.label
                                            }}</a>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mr-auto pl-3 pr-3 pt-3" v-show="table.length != 0">
                            <div class="d-flex flex-column">
                                <span>
                                    <span class="text-secondary mr-2">{{ $t("Table") }}</span>
                                    <span class="text-bold">{{ table.table_number }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="pl-3 pr-3 pt-3 body-spacing">
                            <div class="mb-2" v-for="(category_products_item, index) in filtered_category_array"
                                v-bind:key="index" :id="category_products_item.label">

                                <div class="mr-auto">
                                    <div class="d-flex flex-column">
                                        <div>
                                            <span class="category-title"> {{ category_products_item.label }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex flex-column">
                                        <div class="justify-content-between border-bottom"
                                            v-for="(product_list_item, index) in category_products_item.products"
                                            v-bind:value="product_list_item.product_slack" v-bind:key="index">
                                            <div class="d-flex justify-content-start mt-3 mb-3">
                                                <div class="pr-3"
                                                    v-if="product_list_item.images.length > 0 && product_list_item.images[0].thumbnail != ''">
                                                    <img :src="(product_list_item.images.length > 0) ? product_list_item.images[0].thumbnail : '#'"
                                                        alt="" class="rounded-circle item-image">
                                                </div>
                                                <div class="pr-3" v-else>
                                                    <img src="/images/placeholder_images/menu_placeholder.png" alt=""
                                                        class="rounded-circle item-image">
                                                </div>
                                                <div class="pr-2 align-self-center">
                                                    <span class="text-success added-to-cart"
                                                        v-if="cart[product_list_item.slack] != null"><i
                                                            class="fas fa-check"></i> {{ $t("Added to cart") }}</span>
                                                    <div class="text-break overflow-hidden product-title d-block">
                                                        {{ product_list_item.name | truncate(35) }}
                                                    </div>
                                                    <div class="text-break overflow-hidden d-block">
                                                        <span class="product-price-currency">{{ store_currency }}</span>
                                                        {{ product_list_item.sale_amount_including_tax }}
                                                        &nbsp;<span
                                                            v-if="product_list_item.discount_code != null && product_list_item.discount_code.discount_percentage > 0"
                                                            class="text-success text-caption"><i
                                                                class="fas fa-tags"></i> {{
                                                                        ((product_list_item.discount_code.discount_percentage % 1)
                                                                            ==
                                                                            0) ? Math.round(product_list_item.discount_code.discount_percentage) : product_list_item.discount_code.discount_percentage
                                                                }}%</span>
                                                    </div>
                                                </div>
                                                <div class="ml-auto align-self-center">
                                                    <button class="btn btn-primary cursor"
                                                        v-on:click="resolve_variants(product_list_item)"><i
                                                            class="fas fa-plus"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Cart Section -->
                    <div class="bg-white p-3"
                        v-if="show_cart == true && order_success == false && order_history == false">

                        <div class="mr-auto">
                            <div class="d-flex flex-column">
                                <div>
                                    <span class="page-title" v-on:click="show_shopping_list">
                                        <span class="mr-1"><i class="fas fa-arrow-left"></i></span>
                                        {{ $t("Cart") }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div v-if="Object.keys(this.cart).length > 0">
                            <div class="d-flex flex-column border-bottom mb-2" v-for="(cart_item, key, index) in cart"
                                v-bind:value="cart_item.product_slack" v-bind:key="index">
                                <div class="d-flex mb-1">
                                    <button type="button" v-on:click="remove_from_cart(key)"
                                        class="close cart-item-remove bg-light ml-auto" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="d-flex justify-content-start mt-0 mb-3">

                                    <div class="pr-3">
                                        <img :src="cart_item.image" alt="" class="rounded-circle item-image">
                                    </div>
                                    <div class="pr-2 align-self-center">
                                        <div class="text-break overflow-hidden product-title d-block">
                                            {{ cart_item.name | truncate(100) }}
                                        </div>
                                        <div class="text-break overflow-hidden d-block cart-item-description">
                                            <span class="product-price-currency-total mb-1"><span
                                                    class="product-price-currency">{{ store_currency }}</span> {{
                                                            cart_item.total_price
                                                    }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-auto align-self-center text-right">

                                        <div class="d-flex justify-content-between">

                                            <span class="cart-quantity mr-3 mt-1">{{ cart_item.quantity }}</span>

                                            <button type="button"
                                                v-on:click="validate_quantity_change(key, $event, '-')"
                                                class="cart-item-quantity-counter-btn btn btn-outline-primary mr-2">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button"
                                                v-on:click="validate_quantity_change(key, $event, '+')"
                                                class="cart-item-quantity-counter-btn btn btn-outline-primary">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-2"
                                    v-if="cart_item.selected_addon_products != null && Object.keys(cart_item.selected_addon_products).length != 0">
                                    <small class="text-bold text-secondary">Add Ons</small>
                                    <div class="d-flex flex-row justify-content-between mr-2 cart-item-addon"
                                        v-for="(addon_cart_item, key, index) in cart_item.selected_addon_products"
                                        v-bind:value="addon_cart_item.product_slack" v-bind:key="index">
                                        <div class="">
                                            <div class="d-flex flex-column">
                                                <div class="">+ {{ addon_cart_item.quantity }} x {{ addon_cart_item.name
                                                }} &#8226; <span class="text-secondary">Price: {{
        addon_cart_item.price
}}</span> &#8226;
                                                    <span
                                                        v-if="addon_cart_item.total_discount != null && addon_cart_item.total_discount > 0"
                                                        class="text-success text-caption"><i
                                                            class="fas fa-tags cart-discount-tag"></i> {{
                                                                    addon_cart_item.total_discount
                                                            }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="d-flex flex-column">
                                                <div class="text-right">{{ addon_cart_item.total_price }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button type="button"
                                    v-on:click="show_addon_groups(key, cart_item.product_slack, 'restaurant_menu')"
                                    class="btn btn-link customize-btn text-bold mr-auto p-0 text-primary text-decoration-none mt-2 mb-2"
                                    v-if="cart_item.customizable == 1">
                                    Customize
                                </button>

                            </div>

                            <div class="d-flex flex-column p-0 ml-auto mb-3 mt-3 cart-summary">
                                <div class="d-flex justify-content-between mb-2 cart-summary-label mt-0">
                                    <span class="">{{ $t("Sub total") }}</span>
                                    <span class="">{{ sub_total }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 cart-summary-label mt-0">
                                    <span class="">{{ $t("Discount") }}</span>
                                    <span class="">{{ discount_total }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 cart-summary-label mt-0">
                                    <span class="">{{ $t("Total After Discount") }}</span>
                                    <span class="">{{ total_after_discount }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 cart-summary-label">
                                    <span class="">{{ $t("Total Tax") }}</span>
                                    <span class="">{{ tax_total }}</span>
                                </div>
                                <div class="d-flex justify-content-between cart-total">
                                    <span class="">{{ $t("Total") }}</span>
                                    <span class=""><span class="product-price-currency">{{ store_currency }}</span> {{
                                            total
                                    }}</span>
                                </div>

                                <div class="d-flex mt-2" v-if="processing == true">
                                    <span><i class='fa fa-circle-notch fa-spin' v-if="processing == true"></i>
                                    </span>&nbsp;Processing your order...
                                </div>
                            </div>

                            <form data-vv-scope="confirmation_form">
                                <div class="form-group mb-1">
                                    <label for="restaurant_order_type">{{ $t("Order Type") }}</label>
                                    <div class="d-flex flex-wrap">
                                        <div class="row flex-fill">
                                            <div class="col-4"
                                                v-for="(restaurant_order_type_item, index) in restaurant_order_types"
                                                v-bind:value="restaurant_order_type.order_type_constant"
                                                v-bind:key="index">
                                                <input type="radio" class="check d-none" name="restaurant_order_type"
                                                    v-model="restaurant_order_type"
                                                    v-bind:value="restaurant_order_type_item.order_type_constant"
                                                    v-bind:id="'restaurant_order_type_check' + index"
                                                    v-validate="'required'" key='restaurant_order_type'>
                                                <label class="check-buttons w-100 text-truncate"
                                                    v-bind:for="'restaurant_order_type_check' + index">{{
                                                            restaurant_order_type_item.label
                                                    }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <span
                                        v-bind:class="{ 'error': errors.has('confirmation_form.restaurant_order_type') }">{{
                                                errors.first('confirmation_form.restaurant_order_type')
                                        }}</span>
                                </div>

                                <div class="form-group">
                                    <div class="d-flex flex-column mb-2">
                                        <div class="">
                                            <label class="d-block" for="customer_name">{{ $t("Name") }}</label>
                                        </div>
                                        <div class="d-flex">
                                            <input type="text" name="customer_name" v-model="customer_name"
                                                v-validate="'required|max:250'" class="form-control"
                                                :placeholder="$t('Please provide Name')" autocomplete="off">
                                        </div>
                                        <span
                                            v-bind:class="{ 'error': errors.has('confirmation_form.customer_name') }">{{
                                                    errors.first('confirmation_form.customer_name')
                                            }}</span>
                                    </div>
                                    <div class="d-flex flex-column mb-2">
                                        <div class="">
                                            <label class="d-block" for="contact_number">{{ $t("Contact Number")
                                            }}</label>
                                        </div>
                                        <div class="d-flex">
                                            <input type="text" name="contact_number" v-model="contact_number"
                                                v-validate="'min:10|max:15'" class="form-control"
                                                :placeholder="$t('Please provide Contact Number')" autocomplete="off">
                                        </div>
                                        <span
                                            v-bind:class="{ 'error': errors.has('confirmation_form.contact_number') }">{{
                                                    errors.first('confirmation_form.contact_number')
                                            }}</span>
                                    </div>
                                    <div class="d-flex flex-column mb-2">
                                        <div class="">
                                            <label class="d-block" for="customer_email">{{ $t("Email") }}</label>
                                        </div>
                                        <div class="d-flex">
                                            <div class="flex-grow-1">
                                                <input type="text" name="customer_email" v-model="customer_email"
                                                    v-validate="`${email_required}|email`"
                                                    class="form-control form-control-custom"
                                                    :placeholder="$t('Please provide your Email')" autocomplete="off">
                                            </div>
                                            <div class="ml-2" v-if="enable_digital_menu_otp_verification == true">
                                                <button type="button" class="btn btn-primary" v-on:click="generate_otp"
                                                    v-bind:disabled="otp_processing == true"> <i
                                                        class='fa fa-circle-notch fa-spin'
                                                        v-if="otp_processing == true && otp_success == false"></i> {{
                                                                $t((otp_success) ? otp_button_success : otp_button_default)
                                                        }} <span
                                                        class="small" v-show="otp_success == true">(Wait {{
                                                                otp_start_counter
                                                        }} Sec)</span></button>
                                            </div>
                                        </div>
                                        <span
                                            v-bind:class="{ 'error': errors.has('confirmation_form.customer_email') }">{{
                                                    errors.first('confirmation_form.customer_email')
                                            }}</span>
                                        <div class="mt-2" v-if="enable_digital_menu_otp_verification == true">
                                            <input type="text" name="otp" v-model="otp" v-validate="'required'"
                                                class="form-control form-control-custom col-4"
                                                :placeholder="$t('Enter OTP')" autocomplete="off">
                                            <span v-bind:class="{ 'error': errors.has('confirmation_form.otp') }">{{
                                                    errors.first('confirmation_form.otp')
                                            }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column mb-2" v-if="restaurant_order_type == 'DELIVERY'">
                                        <div class="">
                                            <label class="d-block" for="address">{{ $t("Address") }}</label>
                                        </div>
                                        <div class="d-flex">
                                            <textarea type="text" name="address" v-model="address"
                                                v-validate="'max:65535'" class="form-control"
                                                :placeholder="$t('Please provide Delivery Address')"
                                                autocomplete="off"></textarea>
                                            <span
                                                v-bind:class="{ 'error': errors.has('confirmation_form.address') }">{{
                                                        errors.first('confirmation_form.address')
                                                }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mr-auto mt-4">
                                    <div class="d-flex flex-column">
                                        <div class="flex-grow-1">
                                            <p v-show="server_errors" v-html="server_errors"
                                                v-bind:class="[error_class]"></p>
                                            <button type="submit" class="btn btn-primary btn-lg btn-block"
                                                @click.stop.prevent="create_order('CUSTOMER_ORDER')"
                                                v-bind:disabled="processing == true"> <i
                                                    class='fa fa-circle-notch fa-spin' v-if="processing == true"></i> {{
                                                            $t("Submit Order")
                                                    }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div v-if="Object.keys(this.cart).length == 0">
                            <div class="rounded mt-3 bg-white p-5">
                                <p class="text-center"><i class="fas fa-shopping-bag text-muted"></i> {{ $t("Your cart is empty") }}</p>
                            </div>
                        </div>

                    </div>

                    <div class="bg-white d-flex flex-column text-center align-self-center p-5"
                        v-if="order_success == true">
                        <div class="mb-3">
                            <i class="fas fa-check-circle order-success-tick text-success"></i>
                        </div>
                        <p>{{ $t("Your order has been submitted successfully!") }}</p>
                        <p class="order-number">{{ $t("Order Number is") }} {{ order_data.order_number }}</p>
                        <p class="mt-2"><span class="text-bold cursor" v-on:click="new_order">{{ $t("New Order") }}
                                +</span></p>

                        <p class="mt-2 mb-2"><span class="text-primary text-bold">{{ $t("Do you wish to pay now?")
                        }}</span></p>

                        <div class="d-flex flex-column" v-for="(payment_link, index) in payment_links"
                            v-bind:value="payment_link.slack" v-bind:key="index">
                            <div class="p-1"><a :href="payment_link.link">{{ $t("Pay using") }} {{ payment_link.label
                            }}</a></div>
                        </div>
                    </div>

                    <div class="bg-white d-flex flex-column pt-4 pb-4 pl-3 pr-3" v-if="order_history == true">
                        <div class="mr-auto mb-3">
                            <div class="d-flex flex-column">
                                <div>
                                    <span class="page-title" v-on:click="show_shopping_list">
                                        <span class="mr-1"><i class="fas fa-arrow-left"></i></span>
                                        {{ $t("Orders") }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-if="order_history_processing == true">
                            <span class="" v-if="order_history_processing == true"><i class='fa fa-circle-notch fa-spin'
                                    v-if="order_history_processing == true"></i> Your orders are loading, please
                                wait..</span>
                        </div>
                        <div v-else>
                            <div v-if="Object.keys(this.order_history_data).length > 0">
                                <div class="list-item mb-3"
                                    v-for="(order_history_data_item, index) in order_history_data"
                                    v-bind:value="order_history_data_item.slack" v-bind:key="index">
                                    <div class="mb-3">
                                        <div class="mr-auto menu-order-status">
                                            <span v-if="order_history_data_item.kitchen_status != null"
                                                v-bind:class="order_history_data_item.kitchen_status.color"
                                                class="mt-1 mr-2">{{ order_history_data_item.kitchen_status.label
                                                }}</span>
                                            <span v-if="order_history_data_item.payment_status != null"
                                                v-bind:class="order_history_data_item.payment_status.color"
                                                class="mt-1 mr-2">{{ order_history_data_item.payment_status.label
                                                }}</span>
                                            <span v-bind:class="order_history_data_item.status.color"
                                                class="mt-1 mr-2">{{ order_history_data_item.status.label }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1">
                                        <p class="mb-0 text-subhead-bold">#{{ order_history_data_item.order_number }}
                                        </p>
                                        <p class="mb-0 text-subhead-bold">{{ (order_history_data_item.currency_code !=
                                                "") ? order_history_data_item.currency_code : ""
                                        }} {{
        (order_history_data_item.total_order_amount !=
            "") ? order_history_data_item.total_order_amount : "-"
}}</p>
                                    </div>
                                    <div class="p-0 menu-order-items mb-2">
                                        <div class="d-flex flex-wrap pb-1">
                                            <span class="mr-auto text-black-50">Items</span>
                                            <span class="text-black-50">Qty</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1"
                                            v-for="(item_list_value, key, item_index) in order_history_data_item.products"
                                            v-bind:key="item_index">
                                            <span class="text-break">
                                                <div><span v-if="item_list_value.parent_order_product == false"><span
                                                            class="label blue-label addon-label ml-1">Add-on</span>&nbsp;</span>{{
                                                                    item_list_value.name
                                                            }}</div>
                                            </span>
                                            <span class="text-break">{{ item_list_value.quantity }}</span>
                                        </div>
                                    </div>
                                    <div class="p-3 mb-2 bg-light"
                                        v-if="order_history_data_item.payment_status != null && order_history_data_item.payment_status.constant == 'PAYMENT_PENDING'">
                                        {{ $t("Do you wish to pay now?") }}
                                        <div class="d-flex flex-column"
                                            v-for="(payment_link, index) in order_history_data_item.payment_links"
                                            v-bind:value="payment_link.slack" v-bind:key="index">
                                            <div class="pt-1 pb-1"><a :href="payment_link.link">{{ $t("Pay using") }} {{
                                                    payment_link.label
                                            }}</a></div>
                                        </div>
                                    </div>
                                    <hr>
                                </div>
                            </div>
                            <div v-else>
                                <div class="rounded mt-3 bg-white p-5">
                                    <p class="text-center">{{ $t("Your order history is empty") }}</p>
                                </div>
                            </div>
                        </div>

                        <span class="small text-secondary text-break text-center"><i
                                class="fas fa-exclamation-circle text-primary"></i> {{ $t("This page uses cookies for showing order history") }}</span>

                    </div>

                </div>
            </div>
        </div>

        <modalcomponent v-if="show_addon_group_modal" v-on:close="show_addon_group_modal = false"
            :modal_width="'modal-container-md'">
            <template v-slot:modal-header>
                Customize
            </template>
            <template v-slot:modal-body>
                <div v-if="addon_product_group_processing == true">
                    <span class="" v-if="addon_product_group_processing == true"><i class='fa fa-circle-notch fa-spin'
                            v-if="addon_product_group_processing == true"></i> Add-ons loading, please wait..</span>
                </div>
                <div v-else>
                    <div class="mb-3"
                        v-for="(choosed_addon_product_group_item, key, index) in choosed_addon_product_group"
                        v-bind:key="index">
                        <p class='text-subhead-bold mb-1'>{{ choosed_addon_product_group_item.label }} <span
                                class="small text-muted">({{ (choosed_addon_product_group_item.multiple_selection ==
                                        1) ? 'Choose Multiple' : 'Choose One'
                                }})</span></p>
                        <div class="mb-2">
                            <div class="custom-control custom-checkbox"
                                v-for="(choosed_addon_product, index) in choosed_addon_product_group_item.products"
                                v-bind:key="index">

                                <input :class="'custom-control-input ' + choosed_addon_product_group_item.label"
                                    type="checkbox" :id="'add_on_product_' + choosed_addon_product.product['slack']"
                                    v-model="selected_addon_slack_array" :value="choosed_addon_product.product['slack']"
                                    v-on:click="choose_addon_multiple_identifier(choosed_addon_product.label, choosed_addon_product_group_item.multiple_selection, $event)">

                                <label class="custom-control-label"
                                    :for="'add_on_product_' + choosed_addon_product.product['slack']">
                                    {{ choosed_addon_product.product['name'] }}
                                </label>

                                <span class="float-right">{{ choosed_addon_product.product['sale_amount_including_tax']
                                }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-slot:modal-footer>
                <div v-if="addon_product_group_processing == true">
                    <span class="" v-if="addon_product_group_processing == true"><i class='fa fa-circle-notch fa-spin'
                            v-if="addon_product_group_processing == true"></i></span>
                </div>
                <div v-else>
                    <button type="button" class="btn btn-light" @click="skip_addon_to_product()">Skip</button>
                    <button type="button" class="btn btn-primary" @click="add_addon_to_product(product_slack)"> Update
                        Cart</button>
                </div>
            </template>
        </modalcomponent>

        <modalcomponent v-if="show_variant_modal" v-on:close="show_variant_modal = false"
            :modal_width="'modal-container-xl'">
            <template v-slot:modal-header>
                {{ $t("Choose Variant") }}
            </template>
            <template v-slot:modal-body>
                <div class="d-flex flex-column">
                    <div v-for="(selected_variant_list_item, option) in selected_variant_list" :key="option">
                        <div class="mb-1">
                            <span class="text-subhead-bold d-block">{{ option }}</span>
                        </div>
                        <div class="justify-content-between"
                            v-for="(variant_product_list_item, index) in selected_variant_list_item"
                            v-bind:value="variant_product_list_item.product.product_slack" v-bind:key="index">
                            <div class="d-flex justify-content-start mt-3 mb-3">
                                <div class="pr-3"
                                    v-if="variant_product_list_item.product.images.length > 0 && variant_product_list_item.product.images[0].thumbnail != ''">
                                    <img :src="(variant_product_list_item.product.images.length > 0) ? variant_product_list_item.product.images[0].thumbnail : '#'"
                                        alt="" class="rounded-circle item-image">
                                </div>
                                <div class="pr-3" v-else>
                                    <img src="/images/placeholder_images/menu_placeholder.png" alt=""
                                        class="rounded-circle item-image">
                                </div>
                                <div class="pr-2 align-self-center">
                                    <span class="text-success added-to-cart"
                                        v-if="cart[variant_product_list_item.product.slack] != null"><i
                                            class="fas fa-check"></i> {{ $t("Added to cart") }}</span>
                                    <div class="text-break overflow-hidden product-title d-block">
                                        {{ variant_product_list_item.product.name | truncate(35) }}
                                    </div>
                                    <div class="text-break overflow-hidden d-block">
                                        <span class="product-price-currency">{{ store_currency }}</span> {{
                                                variant_product_list_item.product.sale_amount_including_tax
                                        }}
                                        &nbsp;<span
                                            v-if="variant_product_list_item.product.discount_code != null && variant_product_list_item.product.discount_code.discount_percentage > 0"
                                            class="text-success text-caption"><i class="fas fa-tags"></i> {{
                                                    ((variant_product_list_item.product.discount_code.discount_percentage % 1)
                                                        ==
                                                        0) ? Math.round(variant_product_list_item.product.discount_code.discount_percentage) : variant_product_list_item.product.discount_code.discount_percentage
                                            }}%</span>
                                    </div>
                                </div>
                                <div class="ml-auto align-self-center">
                                    <button class="btn btn-primary cursor"
                                        v-on:click="resolve_variants(variant_product_list_item.product)"><i
                                            class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-primary" @click="skip_variant()">{{ $t("Skip Variants") }}</button>
            </template>
        </modalcomponent>
    </div>
</template>
<script>
'use strict';

import { order_mixin } from '../../../order_mixin';

export default {
    mixins: [order_mixin],
    data() {
        return {
            server_errors: '',
            error_class: '',
            processing: false,
            otp_processing: false,
            order_history_processing: false,
            filter: '',
            api_link: '/api/add_customer_order',
            otp_api_link: '/api/generate_otp',

            cart: {},

            store_slack: this.store.slack,
            store_currency: this.store.currency_code,
            table_slack: (this.table.length != 0) ? this.table.slack : '',

            item_count: 0,
            quantity_count: 0,

            sub_total: 0.00,
            store_level_total_tax_percentage: this.store_tax_percentage,
            store_level_total_tax_amount: 0.00,
            product_level_total_tax_amount: 0.00,
            tax_total: 0.00,
            store_level_total_discount_percentage: this.store_discount_percentage,
            store_level_total_discount_amount: 0.00,
            product_level_total_discount_amount: 0.00,
            discount_total: 0.00,
            additional_discount_percentage: 0.00,
            additional_discount_amount: 0.00,
            total_after_discount: 0.00,
            total: 0.00,

            customer_name: '',
            customer_email: '',
            customer_number: '',
            contact_number: '',
            address: '',
            otp: '',
            otp_success: false,
            otp_button_default: 'Get OTP',
            otp_button_success: 'OTP Sent',
            otp_start_counter: 30,

            billing_type: (this.store_billing_type) ? this.store_billing_type : 'FINE_DINE',

            restaurant_order_type: '',

            show_cart: false,
            show_cart_button: true,
            order_success: false,
            order_history: false,
            order_data: {},

            show_addon_group_modal: false,
            product_slack: null,
            base_product_slack: null,
            addon_product_group_processing: false,
            choosed_addon_product_group: {},
            selected_addon_slack_array: [],
            payment_links: {},
            qr_orders: {},
            order_history_data: {},

            email_required: (this.enable_digital_menu_otp_verification == true) ? 'required' : '',

            show_variant_modal: false,
            selected_variant_list: {},
            selected_variant_keys: [],
            variant_parent_selected: null,
            enable_vairants_popup: (this.store != null && this.store.enable_variants_popup == true) ? true : false,
        }
    },
    props: {
        category_products: [Array, Object],
        category_array: [Array, Object],
        company_logo: String,
        navbar_logo: String,
        store: [Array, Object],
        store_tax_percentage: String,
        store_discount_percentage: String,
        store_restaurant_mode: Boolean,
        restaurant_order_types: [Array, Object],
        billing_types: [Array, Object],
        store_billing_type: String,
        table: [Array, Object],
        enable_digital_menu_otp_verification: Boolean,
        payment_methods: [Array, Object],
        base_url: String
    },
    computed: {
        filtered_category_array() {
            return Object.keys(this.category_products).reduce((a, key) => {
                var item_filtered = {};
                var category_data = this.category_products[key];
                const data = this.product_list_filtered(this.category_products[key]['products']);
                if (data.length) {
                    item_filtered.products = data;
                    item_filtered.label = category_data.label;
                    item_filtered.slack = category_data.slack;

                    a[key] = item_filtered;
                }
                return a;
            }, {});
        }
    },
    filters: {
        truncate: function (value, limit) {
            if (!value) return '';
            if (value.length > limit) {
                value = value.substring(0, (limit - 3)) + '...';
            }
            return value;
        }
    },
    mounted() {
        console.log('Restaurant menu page loaded');
    },
    methods: {
        show_cart_section() {
            this.show_cart = true;
            this.order_success = false;
            this.order_history = false;
        },

        show_shopping_list() {
            this.show_cart_button = true;
            this.show_cart = false;
            this.order_success = false;
            this.order_history = false;
        },

        show_order_success() {
            this.show_cart = false;
            this.order_success = true;
            this.show_cart_button = false;
            this.order_history = false;
            this.cart = {};
            this.update_prices();
            this.set_qr_order_cookie();
        },

        show_order_history() {
            this.show_cart = false;
            this.order_success = false;
            this.order_history = true;
            var current_digital_orders = this.$cookies.get('digital_menu_orders');
            var order_slack = (typeof current_digital_orders != 'undefined' && current_digital_orders != null) ? Object.keys(current_digital_orders) : '';

            var formData = new FormData();
            formData.append("orders", order_slack);
            this.order_history_processing = true;
            axios.post('/api/get_qr_order_history', formData).then((response) => {
                if (response.data.status_code == 200) {
                    this.order_history_processing = false;
                    this.order_history_data = response.data.data.data;
                }
            })
                .catch((error) => {
                    console.log(error);
                });
        },

        set_qr_order_cookie() {
            var current_cookie = this.$cookies.get('digital_menu_orders');
            var qr_orders = (current_cookie) ? current_cookie : {};
            this.$set(qr_orders, this.order_data.slack, this.order_data.slack);
            this.$cookies.set('digital_menu_orders', qr_orders, "5D");
        },

        validate_quantity_change: _.debounce(function (product_slack, event, type) {
            if (type == '+') {
                this.cart[product_slack]['quantity']++;
            } else {
                this.cart[product_slack]['quantity']--;
            }
            var entered_quantity = this.cart[product_slack]['quantity'];
            if (entered_quantity > 0) {
                var quantity = this.cart[product_slack]['quantity'];
                var selected_addons = this.cart[product_slack]['selected_addon_products'];
                if (typeof selected_addons != 'undefined' && selected_addons != null) {
                    for (var qkey in selected_addons) {
                        this.$set(selected_addons[qkey], 'quantity', parseFloat(quantity));
                    }
                }
                this.update_prices();
            } else {
                this.$delete(this.cart, product_slack);
                this.update_prices();
            }
        }, 100),

        create_order(order_status) {

            if (Object.keys(this.cart).length <= 0) {
                return;
            }

            this.server_errors = '';
            this.order_status = order_status;

            var customer_data = {};
            customer_data.slack = '';
            customer_data.name = this.customer_name;
            customer_data.phone = this.contact_number;
            customer_data.email = this.customer_email;

            this.$validator.validateAll('confirmation_form').then((isValid) => {
                if (isValid) {
                    this.processing = true;
                    var formData = new FormData();

                    formData.append("order_status", this.order_status);
                    formData.append("store_slack", this.store_slack);
                    formData.append("customer", (customer_data != null) ? JSON.stringify(customer_data) : '');
                    formData.append("restaurant_mode", 1);
                    formData.append("restaurant_order_type", this.restaurant_order_type);
                    formData.append("billing_type", (this.billing_type != null) ? this.billing_type : '');
                    formData.append("restaurant_table", this.table_slack);
                    formData.append("cart", JSON.stringify(this.cart));
                    formData.append("email", this.customer_email);
                    formData.append("contact_number", this.contact_number);
                    formData.append("address", this.address);
                    formData.append("otp", this.otp);

                    axios.post(this.api_link, formData).then((response) => {
                        if (response.data.status_code == 200) {
                            this.show_response_message(response.data.msg, 'SUCCESS');
                            this.processing = false;
                            this.order_data = response.data.data;
                            this.show_order_success();
                            this.payment_links = response.data.data.payment_links;
                        } else {
                            this.processing = false;
                            try {
                                var error_json = JSON.parse(response.data.msg);
                                this.loop_api_errors(error_json);
                            } catch (err) {
                                this.server_errors = response.data.msg;
                            }
                            this.error_class = 'label red-label error';
                        }
                    })
                        .catch((error) => {
                            console.log(error);
                        });
                }
            });
        },

        generate_otp() {
            if (this.customer_email != '') {
                this.otp_processing = true;

                var formData = new FormData();
                formData.append("email", this.customer_email);
                formData.append("event_type", 'QR_CUSTOMER_ORDER');

                axios.post(this.otp_api_link, formData).then((response) => {

                    if (response.data.status_code == 200) {
                        this.otp_success = true;
                        setTimeout(() => {
                            this.otp_processing = false;
                            this.otp_success = false;
                        }, 30000);
                        var timer_interval = setInterval(() => {
                            if (this.otp_start_counter > 0) {
                                this.otp_start_counter -= 1;
                            } else {
                                clearInterval(timer_interval);
                                this.otp_start_counter = 30;
                            }
                        }, 1000);
                    } else {
                        this.otp_processing = false;
                        try {
                            var error_json = JSON.parse(response.data.msg);
                            this.loop_api_errors(error_json);
                        } catch (err) {
                            this.server_errors = response.data.msg;
                        }
                        this.error_class = 'label red-label error';
                    }
                })
                    .catch((error) => {
                        console.log(error);
                    });
            }
        },

        product_list_filtered(products) {
            let category_products = products;
            if (this.filter != '') {
                return category_products.filter((category_product) => {
                    return this.filter.toLowerCase().split(' ').every(v => category_product.name.toLowerCase().includes(v))
                })
            }
            return category_products;
        },

        new_order() {
            window.location.reload();
        }
    }
}
</script>