<template>
  <div class="row m-0">
    <div class="col-md-8 p-0 bg-white border-right">
      <div class="p-0 border-bottom">
        <div class="d-flex flex-nowrap justify-content-between p-3 horizontal-scroll hide-horizontal-scroll">
          <div class="mr-auto">
            <span class="text-title" v-if="order_slack == ''">{{
                $t("New Order")
            }}</span>
            <span class="text-title" v-else>{{ $t("Order") }} # {{ order_number }}</span>
          </div>

          <!-- <button class="btn btn-primary btn-light ml-3" v-on:click="testing_handler()">
            Testing
          </button> -->

          <button class="btn btn-primary btn-light ml-3" v-on:click="get_keyboard_shortcuts()"
            v-if="keyboard_shortcuts.length > 0">
            <i class="fas fa-keyboard"></i>
          </button>

          <div class="col-md-2 pr-0" v-if="store_restaurant_mode == true" title="Bill Type">
            <select name="billing_type" v-model="billing_type" class="form-control form-control-custom custom-select"
              :disabled="
                order_restaurant_mode == 1 &&
                order_slack != '' &&
                this.order_data.order.current_status.value_constant !=
                'CUSTOMER_ORDER'
              ">
              <option v-for="(billing_types_item, index) in billing_types"
                v-bind:value="billing_types_item.billing_type_constant" v-bind:key="index">
                {{ billing_types_item.label }}
              </option>
            </select>
          </div>

          <button class="btn btn-primary btn-light ml-3" v-on:click="get_digital_menu_orders_list(1)"
            v-if="store_restaurant_mode == true">
            {{ $t("Digital Menu Orders") }}
          </button>

          <button class="btn btn-primary btn-light ml-3" v-on:click="get_running_orders_list(1)"
            v-if="store_restaurant_mode == true">
            {{ $t("Running Orders") }}
          </button>

          <button class="btn btn-primary btn-light ml-3" v-on:click="get_hold_list">
            {{ $t("Hold List") }}
          </button>

          <button class="btn btn-danger ml-3" v-on:click="close_register">
            <i class="fas fa-cash-register"></i>&ensp;{{ $t("Close Register") }}
          </button>
        </div>
      </div>
      <div class="d-flex">
        <div class="col-md-9 p-0 border-right">
          <div class="d-flex flex-column p-3 border-bottom product-info-form">
            <form @submit.prevent="product_info_form">
              <div class="form-row mb-2">
                <div class="form-group col-md-5">
                  <label for="barcode">{{ $t("Barcode") }}</label>
                  <input type="text" name="barcode" v-model="barcode" class="form-control form-control-lg" ref="barcode"
                    :placeholder="$t('Scan Barcode')" autocomplete="off" />
                </div>
                <div class="form-group col-md-5">
                  <label for="product_title">{{ $t("Product Title") }}</label>
                  <input type="text" name="product_title" v-model="product_title" class="form-control form-control-lg"
                    :placeholder="$t('Product Title')" autocomplete="off" />
                </div>
                <div class="form-group col-md-2">
                  <button type="submit" class="btn btn-primary find-product-btn btn-lg"
                    v-bind:disabled="product_processing == true">
                    <i class="fa fa-circle-notch fa-spin" v-if="product_processing == true"></i>
                    {{ $t("Go") }}
                  </button>
                </div>
              </div>
            </form>
          </div>

          <div class="d-flex flex-wrap mb-5 p-3 product-list bg-light">
            <div class="col-md-12">
              <div class="row">
                <div class="d-flex align-items-start flex-column p-1 mb-1 col-12 col-md-4 col-lg-3 bg-light product"
                  v-for="(product_list_item, index) in product_list" v-bind:value="product_list_item.product_slack"
                  v-bind:key="index" v-on:click="resolve_variants(product_list_item)">
                  <div class="col-12 p-3 bg-white product-grid" v-shortkey="{
                    left: [keyboard_shortcuts_formatted.ARROW_LEFT],
                    right: [keyboard_shortcuts_formatted.ARROW_RIGHT],
                    choose: [keyboard_shortcuts_formatted.CHOOSE_PRODUCT],
                  }" @shortkey="product_navigate($event)" :class="{ focus: index === product_focus }">
                    <div class="text-center" v-if="product_list_item.images.length > 0">
                      <img :src="
                        product_list_item.images.length > 0
                          ? product_list_item.images[0].thumbnail
                          : '#'
                      " alt="" class="rounded-circle h-50 w-50 product-image" />
                    </div>
                    <div class="text-center">
                      <div class="product-code">
                        <span class="small text-secondary text-break">{{ $t("Product Code") }} :
                          {{ product_list_item.product_code }}</span>
                      </div>
                      <div class="text-bold text-break overflow-hidden product-title">
                        {{ product_list_item.name | truncate(35) }}
                        <span class="ml-1 text-primary" title="Customizable"
                          v-if="product_list_item.customizable == 1"><i class="far fa-plus-square"></i></span>
                        <span class="ml-1 text-primary" title="Variants Available" v-if="
                          typeof product_list_item.variants_by_options_pos !=
                          'undefined' &&
                          product_list_item.variants_by_options_pos != null &&
                          Object.keys(
                            product_list_item.variants_by_options_pos
                          ).length > 0
                        "><i class="far fa-clone"></i></span>
                      </div>
                      <div class="text-bold text-break overflow-hidden">
                        <span v-if="product_list_item.quantity <= 10" class="text-warning text-caption">Only
                          {{ parseInt(product_list_item.quantity) }} stock(s)
                          left</span>
                        <span v-else class="text-success text-caption">{{ parseInt(product_list_item.quantity) }} stocks
                          left</span>
                      </div>
                      <div class="text-bold text-break overflow-hidden" v-show="
                        order_restaurant_mode == 1 &&
                        product_list_item.ingredient_low_stock == true
                      ">
                        <span class="text-warning text-caption">Low on Ingredient stock</span>
                      </div>
                      <div class="text-bold text-break overflow-hidden">
                        <span v-if="
                          product_list_item.discount_code != null &&
                          product_list_item.discount_code
                            .discount_percentage > 0
                        " class="text-success text-caption">{{ $t("Discount") }}
                          {{
                              product_list_item.discount_code.discount_percentage
                          }}%</span>
                      </div>
                      <div class="mt-auto ml-auto pt-3 text-break product-price">
                        <span class="product-price-currency">{{
                            store_currency
                        }}</span>
                        {{ product_list_item.sale_amount_including_tax }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="">
            <draggable v-model="category_list" @change="onDraggableChange">
              <button class="text-left btn w-100 border mt-3 p-2 font-weight-bold" style="background:#9dd8ff;border-radius:8px!important;"
                v-for="(category, index) in category_list" v-bind:key="index"
                @click="product_info_form_category(category.slack)">
                {{ index + 1 }}. {{ category.label }}
              </button>
            </draggable>
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-4 p-0 full-height">
      <div class="cart_form">
        <div class="p-0 border-bottom">
          <div class="d-flex justify-content-between p-3">
            <span class="text-title text-black-50">{{
                $t("Cart")
            }}</span>
            <input type="text" name="additional_note" class="form-control form-control-md w-25"
                    v-model="additional_note" placeholder="Additional note" autocomplete="off" />
            <button class="btn btn-outline-primary" v-on:click="show_customer_modal = true">
              <i class="fas fa-user-edit"></i> {{ $t("Customer") }}:
              {{ customer_label | truncate(26) }}
            </button>
          </div>
        </div>

        <div class="p-0 border-bottom">
          <div class="d-flex flex-wrap justify-content-between p-3">
            <span>{{ item_count }} {{ $t("Items") }} ({{
                quantity_count
            }}
              Qty)</span>
            <span>{{ $t("Order Level Tax") }} :
              {{ store_level_total_tax_amount }}</span>
            <span class="text-success">{{ $t("Order Level Discount") }} :
              {{ store_level_total_discount_amount }}</span>
          </div>
        </div>

        <div class="p-0 cart-list border-left">
          <div class="d-flex flex-column pl-3 pt-3 pb-3 border-bottom" v-for="(cart_item, key, index) in cart"
            v-bind:value="cart_item.product_slack" v-bind:key="index">
            <div class="d-flex mb-2 justify-content-between">
              <span class="small text-secondary">{{ $t("Product Code") }} : {{ cart_item.product_code }}</span>

              <button type="button" v-on:click="remove_from_cart(key)"
                class="close cart-item-remove bg-light mr-2 ml-auto" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="d-flex justify-content-between mb-2">
              <span class="text-bold text-break cart-item-title">
                {{ cart_item.name }}
              </span>
              <input type="number" v-model="cart_item.quantity" v-on:input="validate_quantity(key, $event)"
                class="form-control form-control-custom cart-product-quantity mr-2 ml-3" autocomplete="off" min="0" />
            </div>

            <div class="d-flex flex-row justify-content-between mr-2 cart-item-summary">
              <div class="">
                <div class="d-flex flex-column">
                  <div class="text-success">
                    <i class="fas fa-tags cart-discount-tag"></i>
                    {{ $t("Discount Amount") }}: {{ cart_item.total_discount }}
                  </div>
                  <div class="">
                    {{ $t("Tax Amount") }}: {{ cart_item.total_tax }}
                  </div>
                </div>
              </div>
              <div class="">
                <div class="d-flex flex-column">
                  <div class="text-right">
                    {{ $t("Price") }}: {{ cart_item.quantity }} x
                    {{ cart_item.price }}
                  </div>
                  <div class="text-right">
                    {{ $t("Total") }}: {{ cart_item.total_price }}
                  </div>
                </div>
              </div>
            </div>

            <div class="mt-2" v-if="
              cart_item.selected_addon_products != null &&
              Object.keys(cart_item.selected_addon_products).length != 0
            ">
              <small class="text-bold text-secondary">Add Ons</small>
              <div class="d-flex flex-row justify-content-between mr-2 cart-item-addon" v-for="(
                  addon_cart_item, key, index
                ) in cart_item.selected_addon_products" v-bind:value="addon_cart_item.product_slack"
                v-bind:key="index">
                <div class="">
                  <div class="d-flex flex-column">
                    <div class="">
                      + {{ addon_cart_item.quantity }} x
                      {{ addon_cart_item.name }} &#8226;
                      <span class="text-secondary">{{ $t("Price") }}: {{ addon_cart_item.sale_price_including_tax }}</span>
                      &#8226;
                      <span v-if="
                        addon_cart_item.total_discount != null &&
                        addon_cart_item.total_discount > 0
                      " class="text-success text-caption"><i class="fas fa-tags cart-discount-tag"></i>
                        {{ addon_cart_item.total_discount }}</span>
                    </div>
                  </div>
                </div>
                <div class="ml-4">
                  <div class="d-flex flex-column">
                    <div class="text-right">
                      {{ addon_cart_item.sale_price_including_tax }}
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <button type="button" v-on:click="show_addon_groups(key, cart_item.product_slack)"
              class="btn btn-link customize-btn text-bold mr-auto p-0 text-primary text-decoration-none"
              v-if="cart_item.customizable == 1">
              {{ $t("Customize") }}
            </button>
          </div>
        </div>

        <div class="d-flex flex-column p-3 ml-auto fixed-bottom col-md-4 border-top cart-summary">
          <div class="d-flex justify-content-center show-more-billing-data bg-white cursor"
            v-on:click="toggle_calculation">
            <span class="show-more-billing-data-text">
              <span v-if="toggle_calculation_data == 0"><i
                  class="fas fa-angle-double-up show-more-billing-data-icon"></i>
                Show more</span>
              <span v-else><i class="fas fa-angle-double-down show-more-billing-data-icon"></i>
                Show less</span>
            </span>
          </div>
          <div v-show="toggle_calculation_data == 1">
            <div class="d-flex justify-content-between mb-2 cart-summary-label mt-0">
              <span class="">{{ $t("Sub total") }}</span>
              <span class="">{{ sub_total }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 cart-summary-label mt-0">
              <span class="">{{ $t("Discount") }}</span>
              <span class="">{{ discount_total }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 cart-summary-label mt-0">
              <span class="d-inline-flex">
                {{ $t("Addt'l Discount") }}
                <input type="number" v-model="additional_discount_percentage" v-on:input="update_prices()"
                  class="form-control form-control-sm ml-3 mr-1 additional_discount" :placeholder="$t('Discount')"
                  min="0" max="100" />%
              </span>
              <span class="">{{ additional_discount_amount }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 cart-summary-label mt-0">
              <span class="">{{ $t("Total After Discount") }}</span>
              <span class="">{{ total_after_discount }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 cart-summary-label">
              <span class="">{{ $t("Total Tax") }}</span>
              <span class="">{{ total_tax }}</span>
            </div>
          </div>
          <div class="d-flex justify-content-between mb-2 cart-total">
            <span class="">{{ $t("Total") }}</span>
            <span class="">{{ total }}</span>
          </div>

          <div v-if="processing == false">
            <div v-if="order_restaurant_mode == 1">
              <div class="mt-2" v-if="order_slack == ''">
                <div class="d-flex mt-2" v-if="billing_type == 'FINE_DINE'">
                  <div class="flex-grow-1">
                    <button type="submit" class="btn btn-primary btn-lg btn-block"
                      v-shortkey="keyboard_shortcuts_formatted.SEND_TO_KITCHEN" @shortkey="create_order('IN_KITCHEN')"
                      @click.stop.prevent="create_order('IN_KITCHEN')" v-bind:disabled="processing == true">
                      <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
                      {{ $t("Send to Kitchen") }}
                    </button>
                  </div>
                </div>

                <div class="d-flex mt-2" v-if="billing_type == 'QUICK_BILL'">
                  <div class="mr-2">
                    <button type="submit" class="btn btn-light btn-lg btn-block"
                      v-shortkey="keyboard_shortcuts_formatted.HOLD_ORDER" @shortkey="create_order('HOLD')"
                      @click.stop.prevent="create_order('HOLD')" v-bind:disabled="processing == true">
                      <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
                      {{ $t("Hold Order") }}
                    </button>
                  </div>
                  <div class="flex-grow-1">
                    <button type="submit" class="btn btn-primary btn-lg btn-block"
                      v-shortkey="keyboard_shortcuts_formatted.CLOSE_ORDER" @shortkey="create_order('CLOSE')"
                      @click.stop.prevent="create_order('CLOSE')" v-bind:disabled="processing == true">
                      <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
                      {{ $t("Close Order") }}
                    </button>
                  </div>
                </div>
              </div>
              <div class="mt-2" v-if="order_slack != ''">
                <div class="d-flex flex-row" v-if="
                  this.order_data.order.current_status.value_constant !=
                  'CUSTOMER_ORDER'
                ">
                  <div class="mr-2" v-if="
                    order_slack != '' &&
                    restaurant_mode_statuses.includes(
                      this.order_data.order.current_status.value_constant
                    )
                  ">
                    <button type="submit" class="btn btn-light btn-lg btn-block"
                      v-shortkey="keyboard_shortcuts_formatted.SEND_TO_KITCHEN" @shortkey="create_order('IN_KITCHEN')"
                      @click.stop.prevent="create_order('IN_KITCHEN')" v-bind:disabled="processing == true">
                      <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
                      {{ $t("Update Order") }}
                    </button>
                  </div>
                  <div class="mr-2">
                    <button type="submit" class="btn btn-light btn-lg btn-block"
                      v-shortkey="keyboard_shortcuts_formatted.HOLD_ORDER" @shortkey="create_order('HOLD')"
                      @click.stop.prevent="create_order('HOLD')" v-bind:disabled="processing == true">
                      <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
                      {{ $t("Hold Order") }}
                    </button>
                  </div>
                  <div class="flex-grow-1">
                    <button type="submit" class="btn btn-primary btn-lg btn-block"
                      v-shortkey="keyboard_shortcuts_formatted.CLOSE_ORDER" @shortkey="create_order('CLOSE')"
                      @click.stop.prevent="create_order('CLOSE')" v-bind:disabled="processing == true">
                      <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
                      {{ $t("Close Order") }}
                    </button>
                  </div>
                </div>

                <div v-if="
                  this.order_data.order.current_status.value_constant ==
                  'CUSTOMER_ORDER'
                ">
                  <div class="d-flex mt-2" v-if="billing_type == 'FINE_DINE'">
                    <div class="flex-grow-1">
                      <button type="submit" class="btn btn-primary btn-lg btn-block" v-shortkey="
                        keyboard_shortcuts_formatted.SEND_TO_KITCHEN
                      " @shortkey="create_order('IN_KITCHEN')" @click.stop.prevent="create_order('IN_KITCHEN')"
                        v-bind:disabled="processing == true">
                        <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
                        {{ $t("Send to Kitchen") }}
                      </button>
                    </div>
                  </div>

                  <div class="d-flex mt-2" v-if="billing_type == 'QUICK_BILL'">
                    <div class="mr-2">
                      <button type="submit" class="btn btn-light btn-lg btn-block"
                        v-shortkey="keyboard_shortcuts_formatted.HOLD_ORDER" @shortkey="create_order('HOLD')"
                        @click.stop.prevent="create_order('HOLD')" v-bind:disabled="processing == true">
                        <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
                        {{ $t("Hold Order") }}
                      </button>
                    </div>
                    <div class="flex-grow-1">
                      <button type="submit" class="btn btn-primary btn-lg btn-block"
                        v-shortkey="keyboard_shortcuts_formatted.CLOSE_ORDER" @shortkey="create_order('CLOSE')"
                        @click.stop.prevent="create_order('CLOSE')" v-bind:disabled="processing == true">
                        <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
                        {{ $t("Close Order") }}
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-else>
              <div class="d-flex mt-2" v-if="order_restaurant_mode == 0">
                <div class="mr-2">
                  <button type="submit" class="btn btn-light btn-lg btn-block"
                    v-shortkey="[keyboard_shortcuts_formatted.HOLD_ORDER]" @shortkey="create_order('HOLD')"
                    @click.stop.prevent="create_order('HOLD')" v-bind:disabled="processing == true">
                    <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
                    {{ $t("Hold Order") }}
                  </button>
                </div>
                <div class="flex-grow-1">
                  <button type="submit" class="btn btn-primary btn-lg btn-block"
                    v-shortkey="[keyboard_shortcuts_formatted.CLOSE_ORDER]" @shortkey="create_order('CLOSE')"
                    @click.stop.prevent="create_order('CLOSE')" v-bind:disabled="processing == true">
                    <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
                    {{ $t("Close Order") }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex mt-2" v-if="processing == true">
            <span><i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i> </span>&nbsp;Processing your
            order...
          </div>
        </div>
      </div>
    </div>

    <modalcomponent v-show="show_customer_modal" v-on:close="show_customer_modal = false" ref="customer">
      <template v-slot:modal-header>
        {{ $t("Provide Customer Details") }}
      </template>
      <template v-slot:modal-body>
        <div v-if="customer == null">
          <div class="form-group">
            <label for="filter_customer">{{ $t("Search Customer") }}</label>
            <cool-select type="text" name="filter_customer" v-model="customer_filter" class=""
              :placeholder="$t('Name or Email or Contact No.')" autocomplete="off" :items="customer_list"
              item-text="fullname" itemValue="slack" :resetSearchOnBlur="false" ref="filter_customer"
              disable-filtering-by-search @search="load_customers($event, 'all')" @select="set_customer($event)">
              <template #item="{ item }">
                <div class="d-flex justify-content-start">
                  <div>
                    {{ item.name }} - {{ item.phone }}, {{ item.email }}
                  </div>
                </div>
              </template>
            </cool-select>
          </div>

          <div class="d-flex justify-content-center p-2">
            <span class="">Or</span>
          </div>

          <div class="form-group">
            <label for="customer_name">{{ $t("Customer Name") }}</label>
            <input type="text" name="customer_name" v-model="customer_name" v-validate="'max:255'" class="form-control"
              :placeholder="$t('Please provide Name')" autocomplete="off" />
            <span v-bind:class="{ error: errors.has('customer_name') }">{{
                errors.first("customer_name")
            }}</span>
            <span class="small text-secondary text-break"><i class="fas fa-exclamation-circle text-primary"></i>
              {{ $t("Track only customer name") }}</span>
          </div>

          <div class="d-flex justify-content-center p-2">
            <span class="">Or</span>
          </div>

          <div class="d-flex justify-content-center p-2">
            <span class="text-primary text-bold cursor" v-on:click="new_customer_form()"><i
                class="fas fa-user-plus"></i>
              {{ $t("Add New Customer") }}</span>
          </div>

          <addcustomerordercomponent v-show="show_new_customer_form == true" v-bind:class="'mt-2 border-top'"
            :customer_status="customer_status"></addcustomerordercomponent>
        </div>
        <div v-else>
          <div class="">
            <div class="d-flex justify-content-center mb-2">
              <img src="/images/customer_default.svg" class="rounded-circle customer-image" />
            </div>
            <div class="d-flex justify-content-center mb-0">
              <span class="text-secondary">{{ $t("Customer Name") }}</span>&nbsp;
              <span class="d-block">{{
                  customer.name ? customer.name : "-"
              }}</span>
            </div>
            <div class="d-flex justify-content-center mb-0">
              <span class="text-secondary">{{ $t("Contact Number") }}</span>&nbsp;
              <span class="d-block">{{
                  customer.phone && customer.type != "DEFAULT"
                    ? customer.phone
                    : "-"
              }}</span>
            </div>
            <div class="d-flex justify-content-center mb-0">
              <span class="text-secondary">{{ $t("Email") }}</span>&nbsp;
              <span class="d-block">{{
                  customer.email && customer.type != "DEFAULT"
                    ? customer.email
                    : "-"
              }}</span>
            </div>
            <div class="d-flex justify-content-center mt-4 mb-2">
              <span class="text-danger text-bold cursor" v-on:click="dismiss_customer()"><i
                  class="fas fa-times-circle fa-2x align-bottom"></i>
                Remove</span>
            </div>
          </div>
        </div>
      </template>
      <template v-slot:modal-footer>
        <button type="button" class="btn btn-light" v-shortkey="keyboard_shortcuts_formatted.SKIP_CUSTOMER"
          @shortkey="select_customer('skip')" @click="select_customer('skip')">
          {{ $t("Skip") }}
        </button>
        <button type="button" class="btn btn-primary" v-shortkey="keyboard_shortcuts_formatted.PROCEED_CUSTOMER"
          @shortkey="select_customer('proceed')" @click="select_customer('proceed')"
          v-bind:disabled="processing == true">
          <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
          {{ $t("Proceed") }}
        </button>
      </template>
    </modalcomponent>

    <modalcomponent v-show="show_creation_modal" v-on:close="show_creation_modal = false"
      :modal_width="'modal-container-xl'" ref="restaurant">
      <template v-slot:modal-header> Confirm </template>
      <template v-slot:modal-body>
        <p v-show="server_errors" v-html="server_errors" v-bind:class="[error_class]"></p>
        <form data-vv-scope="confirmation_form">
          <div v-if="order_status == 'CLOSE'">
            <div class="form-group">
              <label for="payment_method d-block">{{
                  $t("Payment Method")
              }}</label>
              <div class="d-flex flex-wrap">
                <div class="row flex-fill">
                  <div class="col-md-6" v-for="(payment_method_item, index) in payment_methods" v-bind:key="index">
                    <input type="radio" class="check d-none" name="payment_method" v-model="payment_method"
                      v-bind:value="payment_method_item.slack" v-bind:id="'payment_method_check' + index"
                      v-validate="order_status == 'CLOSE' ? 'required' : ''" key="payment_method" />
                    <label class="check-buttons w-100 text-truncate" v-bind:for="'payment_method_check' + index"
                      v-shortkey="{
                        scroll:
                          keyboard_shortcuts_formatted.SCROLL_PAYMENT_METHODS,
                        choose:
                          keyboard_shortcuts_formatted.CHOOSE_PAYMENT_METHOD,
                      }" @shortkey="
  order_confirm_navigate(
    $event,
    'payment_method',
    payment_method_item
  )
" :class="{ confirm_focus: index === payment_method_focus }">{{ payment_method_item.label
}}</label>
                  </div>
                </div>
              </div>
              <span v-bind:class="{
                error: errors.has('confirmation_form.payment_method'),
              }">{{ errors.first("confirmation_form.payment_method") }}</span>
            </div>
            <div class="form-row mb-2" v-if="show_balance_calc_form">
              <div class="form-group col-md-4">
                <label for="calc_received_amount">{{
                    $t("Received Amount")
                }}</label>
                <input type="number" name="calc_received_amount" v-model="calc_received_amount"
                  class="form-control form-control-lg" :placeholder="$t('Received Amount')" autocomplete="off"
                  v-on:input="calculate_balance_amount" />
              </div>
              <div class="form-group col-md-4">
                <label for="calc_order_amount">{{ $t("Order Value") }}</label>
                <input type="number" name="calc_order_amount" v-model="calc_order_amount"
                  class="form-control form-control-lg" :placeholder="$t('Order Value')" autocomplete="off" readonly />
              </div>
              <div class="form-group col-md-4">
                <label for="calc_balance_amount">
                  {{ calc_balace_type }} {{ $t("Balance Amount") }}</label>
                <input type="number" name="calc_balance_amount" v-model="calc_balance_amount"
                  class="form-control form-control-lg" :placeholder="$t('Balance Amount')" autocomplete="off"
                  readonly />
              </div>
            </div>
            <div class="form-group">
              <label for="business_account">{{ $t("Business Account") }}</label>
              <div class="d-flex flex-wrap">
                <div class="row flex-fill">
                  <div class="col-md-6" v-for="(business_account_item, index) in business_accounts"
                    v-bind:value="business_account_item.slack" v-bind:key="index">
                    <input type="radio" class="check d-none" name="business_account" v-model="business_account"
                      v-bind:value="business_account_item.slack" v-bind:id="'business_account_check' + index"
                      v-validate="order_status == 'CLOSE' ? 'required' : ''" key="business_account" />
                    <label class="check-buttons w-100 text-truncate" v-bind:for="'business_account_check' + index"
                      :class="{
                        confirm_focus: index === business_account_focus,
                      }" v-shortkey="{
  scroll:
    keyboard_shortcuts_formatted.SCROLL_BUSINESS_ACCOUNTS,
  choose:
    keyboard_shortcuts_formatted.CHOOSE_BUSINESS_ACCOUNT,
}" @shortkey="
  order_confirm_navigate(
    $event,
    'business_account',
    business_account_item
  )
">{{ business_account_item.label }} ({{
    business_account_item.account_type_label
}})</label>
                  </div>
                </div>
              </div>
              <small id="business_account_help" class="form-text text-muted">Transaction will be saved under this
                account</small>
              <span v-bind:class="{
                error: errors.has('confirmation_form.business_account'),
              }">{{ errors.first("confirmation_form.business_account") }}</span>
            </div>
          </div>

          <div v-if="order_restaurant_mode == 1">
            <div class="form-group">
              <label for="restaurant_order_type">{{ $t("Order Type") }}</label>
              <div class="d-flex flex-wrap">
                <div class="row flex-fill">
                  <div class="col-md-4" v-for="(
                      restaurant_order_type_item, index
                    ) in restaurant_order_types" v-bind:value="restaurant_order_type.order_type_constant"
                    v-bind:key="index">
                    <input type="radio" class="check d-none" name="restaurant_order_type"
                      v-model="restaurant_order_type" v-bind:value="
                        restaurant_order_type_item.order_type_constant
                      " v-bind:id="'restaurant_order_type_check' + index" v-validate="'required'"
                      v-on:click="set_table_based_on_order_type()" key="restaurant_order_type" />
                    <label class="check-buttons w-100 text-truncate" v-bind:for="'restaurant_order_type_check' + index"
                      :class="{
                        confirm_focus: index === restaurant_order_type_focus,
                      }" v-shortkey="{
  scroll: keyboard_shortcuts_formatted.SCROLL_ORDER_TYPES,
  choose: keyboard_shortcuts_formatted.CHOOSE_ORDER_TYPE,
}" @shortkey="
  order_confirm_navigate(
    $event,
    'order_type',
    restaurant_order_type_item
  )
">{{ restaurant_order_type_item.label }}</label>
                  </div>
                </div>
              </div>
              <span v-bind:class="{
                error: errors.has('confirmation_form.restaurant_order_type'),
              }">{{
    errors.first("confirmation_form.restaurant_order_type")
}}</span>
            </div>

            <div class="form-group" v-if="restaurant_order_type == 'DELIVERY'">
              <div class="form-row">
                <div class="form-group col-md-4">
                  <label for="contact_number">{{
                      $t("Customer Contact Number")
                  }}</label>
                  <input type="text" name="contact_number" v-model="contact_number" v-validate="'min:10|max:15'"
                    class="form-control" :placeholder="$t('Contact Number')" autocomplete="off" />
                  <span v-bind:class="{
                    error: errors.has('confirmation_form.contact_number'),
                  }">{{
    errors.first("confirmation_form.contact_number")
}}</span>
                </div>
                <div class="form-group col-md-8">
                  <label for="address">{{ $t("Customer Address") }}</label>
                  <textarea name="address" v-model="address" v-validate="'max:65535'"
                    class="form-control textarea-small" :placeholder="$t('Address')" autocomplete="off"></textarea>
                  <span v-bind:class="{
                    error: errors.has('confirmation_form.address'),
                  }">{{ errors.first("confirmation_form.address") }}</span>
                </div>
              </div>
            </div>

            <div class="form-group col-md-12 p-0">
              <label for="waiter">{{ $t("Waiter") }}</label>
              <div class="d-flex flex-wrap">
                <div class="row flex-fill" v-if="
                  typeof waiters != 'undefined' &&
                  waiters != null &&
                  waiters.length > 0
                ">
                  <div class="col-md-3" v-for="(waiter_user, index) in waiters" v-bind:key="index">
                    <input type="radio" class="check d-none" name="waiter" v-model="waiter"
                      v-bind:value="waiter_user.slack" v-bind:id="'waiter' + index" />
                    <label class="check-buttons w-100 text-truncate" v-bind:for="'waiter' + index">{{
                        waiter_user.user_code
                    }} -
                      {{ waiter_user.fullname }}</label>
                  </div>
                </div>
                <div v-else class="text-muted small">
                  Waiters are not available! Add waiters / provide store access
                  to waiters / choose waiter role for store.
                </div>
              </div>
            </div>

            <div v-if="restaurant_order_type == 'DINEIN'">
              <div class="form-group" v-if="
                typeof vacant_tables != 'undefined' &&
                vacant_tables != null &&
                vacant_tables.length > 0
              " v-bind:class="vacant_tables.length >= 48 ? 'col-md-6 p-0' : ''">
                <label for="restaurant_table">{{ $t("Table") }}</label>

                <div class="d-flex flex-wrap" v-if="vacant_tables.length <= 48">
                  <div class="row flex-fill pl-3 pr-3">
                    <div class="col-md-2 p-1 pb-0 mb-0" v-for="(vacant_table, index) in vacant_tables"
                      v-bind:value="vacant_table.slack" v-bind:key="index">
                      <input type="radio" class="check d-none" name="restaurant_table" v-model="restaurant_table"
                        v-bind:value="vacant_table.slack" v-bind:id="'vacant_table_check' + index"
                        v-validate="'required'" key="restaurant_table" />
                      <label class="check-buttons w-100 text-truncate" v-bind:for="'vacant_table_check' + index"
                        v-shortkey="{
                          scroll:
                            keyboard_shortcuts_formatted.SCROLL_RESTAURANT_TABLES,
                          choose:
                            keyboard_shortcuts_formatted.CHOOSE_RESTAURANT_TABLE,
                        }" @shortkey="
  order_confirm_navigate(
    $event,
    'restaurant_table',
    vacant_table
  )
" :class="{
  confirm_focus: index === restaurant_table_focus,
}">{{ vacant_table.table_number }}
                        <span class="float-right" title="No of Occupants"><i class="fas fa-users text-muted"></i>
                          {{ vacant_table.no_of_occupants }}</span></label>
                    </div>
                  </div>
                </div>

                <select name="restaurant_table" v-model="restaurant_table" v-validate="'required'"
                  class="form-control form-control-custom custom-select" v-if="vacant_tables.length > 48"
                  key="restaurant_table">
                  <option value="">Choose Table..</option>
                  <option v-for="(vacant_table, index) in vacant_tables" v-bind:value="vacant_table.slack"
                    v-bind:key="index">
                    {{ vacant_table.table_number }} (Capacity:
                    {{ vacant_table.no_of_occupants }})
                  </option>
                </select>

                <span v-bind:class="{
                  error: errors.has('confirmation_form.restaurant_table'),
                }">
                  {{errors.first("confirmation_form.restaurant_table")}}
                </span>
              </div>
              <div v-else class="text-muted small">
                Tables are not available! Add new tables / tables might be
                occupied
              </div>
            </div>
          </div>
        </form>
        Are you sure you want to proceed?
      </template>
      <template v-slot:modal-footer>
        <input v-if="order_slack != '' && show_balance_calc_form || billing_type == 'QUICK_BILL'" class="" type="radio" value="POS_INVOICE" v-model="print_type" name="flexRadioDefault">
        <label v-if="order_slack != ''&& show_balance_calc_form || billing_type == 'QUICK_BILL'" class="form-check-label" for="invoice">Print Invoice</label>
        <input v-if="order_slack != ''&& show_balance_calc_form || billing_type == 'QUICK_BILL'" class="" type="radio" value="KOT" v-model="print_type" name="flexRadioDefault">
        <label v-if="order_slack != ''&& show_balance_calc_form || billing_type == 'QUICK_BILL'" class="" for="kot">Print Kot</label>
        
        <button type="button" class="btn btn-light" v-shortkey="keyboard_shortcuts_formatted.CANCEL"
          @shortkey="$emit('close')" @click="$emit('close')">
          Cancel
        </button>
        <button type="button" class="btn btn-primary" v-shortkey="keyboard_shortcuts_formatted.CONTINUE"
          @shortkey="$emit('submit')" @click="$emit('submit')" v-bind:disabled="processing == true">
          <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
          Continue
        </button>
      </template>
    </modalcomponent>

    <modalcomponent v-if="show_close_register_modal" v-on:close="show_close_register_modal = false">
      <template v-slot:modal-header>
        <label for="waiter">{{ $t("Close Register") }}</label>
      </template>
      <template v-slot:modal-body>
        <p v-html="close_register_server_errors" v-bind:class="[error_class]"></p>
        <form data-vv-scope="close_register_form">
          <div>
            <div class="form-group">
              <label for="closing_amount">{{ $t("Total Amount") }}</label>
              <input type="number" name="closing_amount" v-model="closing_amount" v-validate="'decimal|min_value:0'"
                class="form-control form-control-custom" :placeholder="$t('Please provide total cash')"
                autocomplete="off" />
              <span v-bind:class="{
                error: errors.has('close_register_form.closing_amount'),
              }">{{ errors.first("close_register_form.closing_amount") }}</span>
            </div>
            <div class="form-group">
              <label for="credit_card_slips">{{
                  $t("Total Credit Card Slips")
              }}</label>
              <input type="number" name="credit_card_slips" v-model="credit_card_slips"
                v-validate="'decimal|min_value:0'" class="form-control form-control-custom"
                :placeholder="$t('Please provide total credit card slips')" autocomplete="off" />
              <span v-bind:class="{
                error: errors.has('close_register_form.credit_card_slips'),
              }">{{
    errors.first("close_register_form.credit_card_slips")
}}</span>
            </div>
            <div class="form-group">
              <label for="cheques">{{ $t("Total Cheques") }}</label>
              <input type="number" name="cheques" v-model="cheques" v-validate="'decimal|min_value:0'"
                class="form-control form-control-custom" :placeholder="$t('Please provide total cheques')"
                autocomplete="off" />
              <span v-bind:class="{
                error: errors.has('close_register_form.cheques'),
              }">{{ errors.first("close_register_form.cheques") }}</span>
            </div>
          </div>
        </form>
        Are you sure you want to close register?
      </template>
      <template v-slot:modal-footer>
        <div v-if="register_amount_processing == true">
          <span class="" v-if="register_amount_processing == true"><i class="fa fa-circle-notch fa-spin"
              v-if="register_amount_processing == true"></i>
            Amount loading, please wait..</span>
        </div>
        <div v-else>
          <button type="button" class="btn btn-light" @click="$emit('close')">
            Cancel
          </button>
          <button type="button" class="btn btn-primary" @click="$emit('submit')" v-bind:disabled="processing == true">
            <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
            Continue
          </button>
        </div>
      </template>
    </modalcomponent>

    <modalcomponent v-if="show_addon_group_modal" v-on:close="show_addon_group_modal = false"
      :modal_width="'modal-container-md'">
      <template v-slot:modal-header>
        {{ $t("Customize") }}
      </template>
      <template v-slot:modal-body>
        <div v-if="addon_product_group_processing == true">
          <span class="" v-if="addon_product_group_processing == true"><i class="fa fa-circle-notch fa-spin"
              v-if="addon_product_group_processing == true"></i>
            Add-ons loading, please wait..</span>
        </div>
        <div v-else>
          <div class="mb-3" v-for="(
              choosed_addon_product_group_item, key, index
            ) in choosed_addon_product_group" v-bind:key="index">
            <p class="text-subhead-bold mb-1">
              {{ choosed_addon_product_group_item.label }}
              <span class="small text-muted">({{
                  choosed_addon_product_group_item.multiple_selection == 1
                    ? "Choose Multiple"
                    : "Choose One"
              }})</span>
            </p>
            <div class="mb-2">
              <div class="custom-control custom-checkbox" v-for="(
                  choosed_addon_product, index
                ) in choosed_addon_product_group_item.products" v-bind:key="index">
                <input :class="
                  'custom-control-input ' +
                  choosed_addon_product_group_item.label
                " type="checkbox" :id="
  'add_on_product_' + choosed_addon_product.product['slack']
" v-model="selected_addon_slack_array" :value="choosed_addon_product.product['slack']" v-on:click="
  choose_addon_multiple_identifier(
    choosed_addon_product.label,
    choosed_addon_product_group_item.multiple_selection,
    $event
  )
" />

                <label class="custom-control-label" :for="
                  'add_on_product_' + choosed_addon_product.product['slack']
                ">
                  {{ choosed_addon_product.product["name"] }}
                </label>

                <span class="float-right">{{
                    choosed_addon_product.product["sale_amount_including_tax"]
                }}</span>
              </div>
            </div>
          </div>
        </div>
      </template>
      <template v-slot:modal-footer>
        <div v-if="addon_product_group_processing == true">
          <span class="" v-if="addon_product_group_processing == true"><i class="fa fa-circle-notch fa-spin"
              v-if="addon_product_group_processing == true"></i></span>
        </div>
        <div v-else>
          <button type="button" class="btn btn-light" @click="skip_addon_to_product()">
            Skip
          </button>
          <button type="button" class="btn btn-primary" @click="add_addon_to_product(product_slack)">
            Update Cart
          </button>
        </div>
      </template>
    </modalcomponent>

    <modalcomponent v-if="show_variant_modal" v-on:close="show_variant_modal = false"
      :modal_width="'modal-container-xl'">
      <template v-slot:modal-header>
        {{ $t("Choose Variant") }}
      </template>
      <template v-slot:modal-body>
        <div class="d-flex flex-row mb-2" v-if="selected_variant_keys.length > 1">
          <span class="mr-3">Jump to</span>
          <div class="mr-2" v-for="(selected_variant_key, option) in selected_variant_keys" :key="option">
            <a :href="'#' + selected_variant_key" class="">{{
                selected_variant_key
            }}</a>
            &middot;
          </div>
        </div>

        <div class="d-flex flex-column" v-for="(selected_variant_list_item, option) in selected_variant_list"
          :key="option" :id="option">
          <div class="mb-1">
            <span class="text-subhead-bold d-block">{{ option }}</span>
          </div>
          <div class="d-flex flex-wrap mb-1">
            <div class="col-md-12">
              <div class="row">
                <div class="d-flex align-items-start p-1 mb-1 col-md-4 product" v-for="(
                    variant_product_list_item, index
                  ) in selected_variant_list_item" v-bind:value="variant_product_list_item.product.slack"
                  v-bind:key="index" v-on:click="add_to_cart(variant_product_list_item.product)">
                  <div class="col-12 p-3 bg-white product-grid h-100" v-shortkey="{
                    left: [keyboard_shortcuts_formatted.ARROW_LEFT],
                    right: [keyboard_shortcuts_formatted.ARROW_RIGHT],
                    choose: [keyboard_shortcuts_formatted.CHOOSE_PRODUCT],
                  }" @shortkey="product_navigate($event)" :class="{ focus: index === product_focus }">
                    <div class="d-flex flex-row-reverse" v-if="variant_product_list_item.product.images.length > 0">
                      <img :src="
                        variant_product_list_item.product.images.length > 0
                          ? variant_product_list_item.product.images[0]
                            .thumbnail
                          : '#'
                      " alt="" class="rounded-circle h-75 product-image position-absolute d-lg-block" />
                    </div>
                    <div class="product-code">
                      <span class="small text-secondary text-break">{{ $t("Product Code") }} :
                        {{
                            variant_product_list_item.product.product_code
                        }}</span>
                    </div>
                    <div class="text-bold text-break overflow-hidden product-title">
                      {{
                          variant_product_list_item.product.name | truncate(35)
                      }}
                      <span class="ml-1 text-primary" title="Customizable" v-if="
                        variant_product_list_item.product.customizable == 1
                      "><i class="far fa-plus-square"></i></span>
                    </div>
                    <div class="text-bold text-break overflow-hidden"
                      v-show="variant_product_list_item.product.quantity <= 10">
                      <span class="text-warning text-caption">Only
                        {{ variant_product_list_item.product.quantity }}
                        stock(s) left</span>
                    </div>
                    <div class="text-bold text-break overflow-hidden" v-show="
                      order_restaurant_mode == 1 &&
                      variant_product_list_item.product
                        .ingredient_low_stock == true
                    ">
                      <span class="text-warning text-caption">Low on Ingredient stock</span>
                    </div>
                    <div class="text-bold text-break overflow-hidden">
                      <span v-if="
                        variant_product_list_item.product.discount_code !=
                        null &&
                        variant_product_list_item.product.discount_code
                          .discount_percentage > 0
                      " class="text-success text-caption">{{ $t("Discount") }}
                        {{
                            variant_product_list_item.product.discount_code
                              .discount_percentage
                        }}%</span>
                    </div>
                    <div class="mt-auto ml-auto pt-3 text-break product-price">
                      <span class="product-price-currency">{{
                          store_currency
                      }}</span>
                      {{
                          variant_product_list_item.product
                            .sale_amount_including_tax
                      }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
      <template v-slot:modal-footer>
        <button type="button" class="btn btn-primary" @click="skip_variant()">
          {{ $t("Skip Variants") }}
        </button>
      </template>
    </modalcomponent>

    <quickpanelcomponent v-show="show_running_order_quickpanel"
      v-on:close-quick-panel="show_running_order_quickpanel = false" :panel_class="'col-md-6 col-xl-3'">
      <template v-slot:quick-panel-header>
        {{ $t("Running Orders") }} ({{ running_order_total_records }})
      </template>
      <template v-slot:quick-panel-body>
        <span class="" v-if="running_order_list_processing == true"><i class="fa fa-circle-notch fa-spin"
            v-if="running_order_list_processing == true"></i>
          Loading..</span>

        <div v-show="running_order_list_processing == false">
          <input type="text" v-model="search_running_list" class="form-control form-control-custom mb-3"
            :placeholder="$t('Search by order, customer, table..')" autocomplete="off" />

          <div v-if="running_order_list_filtered.length > 0">
            <div class="list-item mb-3" v-for="(
                running_order_list_item, index
              ) in running_order_list_filtered" v-bind:value="running_order_list_item.slack" v-bind:key="index"
              v-on:click="
                go_to_order(
                  running_order_list_item.status.constant,
                  running_order_list_item.detail_link,
                  running_order_list_item.edit_link
                )
              ">
              <div class="d-flex justify-content-between mb-2">
                <div class="mr-auto">
                  <span class="timer-circle bg-light"><span class="timer-dot mr-1"></span>
                    {{ running_order_list_item.duration }} Minute</span>
                  <span class="ml-2"><label for="order">Order</label> #{{
                      running_order_list_item.order_number
                  }}</span>
                </div>
                <div class="ml-auto">
                  <!-- <span>{{running_order_list_item.status.constant}}</span> -->
                  <span v-if="running_order_list_item.kitchen_status != null"
                    v-bind:class="running_order_list_item.kitchen_status.color">{{
                        running_order_list_item.kitchen_status.label
                    }}</span>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-4 mb-0">
                  <label for="type">{{ $t("Type") }}</label>
                  <p class="mb-0">{{ running_order_list_item.order_type }}</p>
                </div>
                <div class="form-group col-md-4 mb-0">
                  <label for="table">{{ $t("Table") }}</label>
                  <p class="mb-0">
                    {{
                        running_order_list_item.table != ""
                          ? running_order_list_item.table
                          : "-"
                    }}
                  </p>
                </div>
                <div class="form-group col-md-4 mb-0">
                  <label for="table">{{ $t("Waiter") }}</label>
                  <p class="mb-0">
                    {{
                        running_order_list_item.waiter_data != null
                          ? running_order_list_item.waiter_data.fullname +
                          " (" +
                          running_order_list_item.waiter_data.user_code +
                          ")"
                          : "-"
                    }}
                  </p>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-center pl-4 pr-4">
              <span class="text-centered btn-label mt-2" v-show="running_order_has_more_items"
                v-on:click="get_running_orders_list(running_order_next_page)">
                <span class="" v-if="running_order_list_processing == true"><i
                    class="fa fa-circle-notch fa-spin"></i></span>
                {{ $t("Load More") }}
              </span>
            </div>
          </div>
          <div v-else class="text-muted">There are 0 running orders</div>
        </div>
      </template>
    </quickpanelcomponent>

    <quickpanelcomponent v-show="show_hold_list_modal" v-on:close-quick-panel="show_hold_list_modal = false"
      :panel_class="'col-md-6 col-xl-3'">
      <template v-slot:quick-panel-header>
        {{ $t("Hold orders from last 2 days") }} ({{ hold_order_list.length }})
      </template>
      <template v-slot:quick-panel-body>
        <span class="" v-if="hold_list_processing == true"><i class="fa fa-circle-notch fa-spin"
            v-if="hold_list_processing == true"></i>
          Loading..</span>

        <div v-show="hold_list_processing == false">
          <input type="text" v-model="search_hold_list" class="form-control form-control-custom mb-3"
            :placeholder="$t('Search by order, customer ..')" autocomplete="off" />

          <div v-if="hold_list_filtered.length > 0">
            <div class="list-item mb-3" v-for="(hold_order_list_item, key, index) in hold_list_filtered"
              v-bind:key="index" v-on:click="
                go_to_order(
                  hold_order_list_item.status.constant,
                  hold_order_list_item.detail_link,
                  hold_order_list_item.edit_link
                )
              ">
              <div class="form-row">
                <div class="form-group col-md-6 mb-0">
                  <label for="type" class="mb-0">{{ $t("Order") }}</label>
                  <p class="mb-0">#{{ hold_order_list_item.order_number }}</p>
                </div>
                <div class="form-group col-md-6 mb-0">
                  <label for="time" class="mb-0">{{ $t("Time") }}</label>
                  <p class="mb-0">
                    {{ hold_order_list_item.created_at_label }}
                  </p>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6 mb-0">
                  <label for="type" class="mb-0">{{
                      $t("Customer Name")
                  }}</label>
                  <p class="mb-0">
                    {{
                        hold_order_list_item.customer_name != "" &&
                          hold_order_list_item.customer_name != null
                          ? hold_order_list_item.customer_name
                          : "-"
                    }}
                  </p>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6 mb-0">
                  <label for="type" class="mb-0">{{
                      $t("Customer Phone")
                  }}</label>
                  <p class="mb-0">
                    {{
                        hold_order_list_item.customer_phone != ""
                          ? hold_order_list_item.customer_phone
                          : "-"
                    }}
                  </p>
                </div>
                <div class="form-group col-md-6 mb-0">
                  <label for="table" class="mb-0">{{ $t("Email") }}</label>
                  <p class="mb-0 text-truncate">
                    {{
                        hold_order_list_item.customer_email != ""
                          ? hold_order_list_item.customer_email
                          : "-"
                    }}
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div v-else class="text-muted">There are 0 orders on hold.</div>
        </div>
      </template>
    </quickpanelcomponent>

    <quickpanelcomponent v-show="show_keyboard_shortcuts_quickpanel"
      v-on:close-quick-panel="show_keyboard_shortcuts_quickpanel = false" :panel_class="'col-md-6 col-xl-3'">
      <template v-slot:quick-panel-header>
        {{ $t("Keyboard Shortcuts") }}
      </template>
      <template v-slot:quick-panel-body>
        <div class="d-flex border-bottom" v-for="(keyboard_shortcut, key, index) in keyboard_shortcuts"
          v-bind:value="keyboard_shortcut.keyboard_constant" v-bind:key="index">
          <div class="p-2 col-4">
            {{ keyboard_shortcut.keyboard_shortcut_label }}
          </div>
          <div class="p-2 col-8">{{ keyboard_shortcut.description }}</div>
        </div>
      </template>
    </quickpanelcomponent>

    <quickpanelcomponent v-show="show_digital_menu_order_quickpanel"
      v-on:close-quick-panel="show_digital_menu_order_quickpanel = false" :panel_class="'col-md-6 col-xl-3'">
      <template v-slot:quick-panel-header>
        {{ $t("Digital Menu Orders") }} ({{ digital_menu_order_total_records }})
      </template>
      <template v-slot:quick-panel-body>
        <span class="" v-if="digital_menu_order_list_processing == true"><i class="fa fa-circle-notch fa-spin"
            v-if="digital_menu_order_list_processing == true"></i>
          Loading..</span>

        <div v-show="digital_menu_order_list_processing == false">
          <input type="text" v-model="search_digital_menu_list" class="form-control form-control-custom mb-3"
            :placeholder="$t('Search by order, customer, table..')" autocomplete="off" />

          <div v-if="digital_menu_order_list_filtered.length > 0">
            <div class="list-item mb-3" v-for="(
                digital_menu_order_list_item, index
              ) in digital_menu_order_list_filtered" v-bind:value="digital_menu_order_list_item.slack"
              v-bind:key="index" v-on:click="
                go_to_order(
                  digital_menu_order_list_item.status.constant,
                  digital_menu_order_list_item.detail_link,
                  digital_menu_order_list_item.edit_link
                )
              ">
              <div class="d-flex justify-content-between mb-2">
                <div class="mr-auto">
                  <span class="timer-circle bg-light"><span class="timer-dot mr-1"></span>
                    {{ digital_menu_order_list_item.duration }} Minute</span>
                  <span class="ml-2"><label for="order">Order</label> #{{
                      digital_menu_order_list_item.order_number
                  }}</span>
                </div>
                <div class="ml-auto">
                  <span v-if="digital_menu_order_list_item.kitchen_status != null" v-bind:class="
                    digital_menu_order_list_item.kitchen_status.color
                  ">{{
    digital_menu_order_list_item.kitchen_status.label
}}</span>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-4 mb-0">
                  <label for="type">{{ $t("Type") }}</label>
                  <p class="mb-0">
                    {{ digital_menu_order_list_item.order_type }}
                  </p>
                </div>
                <div class="form-group col-md-4 mb-0">
                  <label for="table">{{ $t("Table") }}</label>
                  <p class="mb-0">
                    {{
                        digital_menu_order_list_item.table != ""
                          ? digital_menu_order_list_item.table
                          : "-"
                    }}
                  </p>
                </div>
                <div class="form-group col-md-4 mb-0">
                  <label for="table">{{ $t("Waiter") }}</label>
                  <p class="mb-0">
                    {{
                        digital_menu_order_list_item.waiter_data != null
                          ? digital_menu_order_list_item.waiter_data.fullname +
                          " (" +
                          digital_menu_order_list_item.waiter_data.user_code +
                          ")"
                          : "-"
                    }}
                  </p>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-center pl-4 pr-4">
              <span class="text-centered btn-label mt-2" v-show="digital_menu_order_has_more_items" v-on:click="
                get_digital_menu_orders_list(digital_menu_order_next_page)
              ">
                <span class="" v-if="digital_menu_order_list_processing == true"><i
                    class="fa fa-circle-notch fa-spin"></i></span>
                {{ $t("Load More") }}
              </span>
            </div>
          </div>
          <div v-else class="text-muted">There are 0 digital orders</div>
        </div>
      </template>
    </quickpanelcomponent>
  </div>
</template>

<script>
"use strict";

import moment from "moment";
import { CoolSelect } from "vue-cool-select";
import "vue-cool-select/dist/themes/bootstrap.css";
import draggable from "vuedraggable";
import { order_mixin } from "../../order_mixin";
import addcustomerordercomponent from "../customer/add_customer_order_component.vue";
import { event_bus } from "../../event_bus.js";

export default {
  components: {
    CoolSelect,
    addcustomerordercomponent,
    draggable,
  },
  mixins: [order_mixin],
  data() {
    return {
      print_type:"POS_INVOICE",
      additional_note: this.order_data == null ? "" : this.order_data.order["additional_note"],
      category_list: this.categories,
      server_errors: "",
      error_class: "",
      processing: false,
      product_processing: false,
      register_amount_processing: false,
      close_register_server_errors: "",

      show_creation_modal: false,
      show_customer_modal: false,
      show_hold_list_modal: false,
      show_close_register_modal: false,
      show_running_order_quickpanel: false,
      show_keyboard_shortcuts_quickpanel: false,
      show_digital_menu_order_quickpanel: false,
      show_addon_group_modal: false,
      show_new_customer_form: false,
      show_variant_modal: false,

      new_order_route: this.new_order_link,
      api_link:
        this.order_data == null
          ? "/api/add_order"
          : "/api/update_order/" + this.order_data.slack,
      close_register_api_link: "/api/close_register",
      default_label: "Walkin Customer",
      current_date_time: moment().format("MMM Do YYYY, h:mm:ss a"),

      customer_label: "-",
      barcode: "",
      product_title: "",
      category: "",


      order_slack: this.order_data == null ? "" : this.order_data.slack,
      order_number:
        this.order_data == null ? "" : this.order_data.order["order_number"],

      sub_total:
        this.order_data == null
          ? 0.0
          : this.order_data.order["sale_amount_subtotal_excluding_tax"],

      store_level_total_tax_percentage: this.store_tax_percentage,
      store_level_total_tax_amount:
        this.order_data == null
          ? 0.0
          : this.order_data.order["store_level_total_tax_amount"],
      product_level_total_tax_amount:
        this.order_data == null
          ? 0.0
          : this.order_data.order["product_level_total_tax_amount"],
      total_tax:
        this.order_data == null ? 0.0 : this.order_data.order["tax_total"],

      store_level_total_discount_percentage: this.store_discount_percentage,
      store_level_total_discount_amount:
        this.order_data == null
          ? 0.0
          : this.order_data.order["store_level_total_discount_amount"],
      product_level_total_discount_amount:
        this.order_data == null
          ? 0.0
          : this.order_data.order["product_level_total_discount_amount"],
      discount_total:
        this.order_data == null
          ? 0.0
          : this.order_data.order["discount_amount"],

      additional_discount_percentage:
        this.order_data == null
          ? 0.0
          : this.order_data.order["additional_discount_percentage"],
      additional_discount_amount:
        this.order_data == null
          ? 0.0
          : this.order_data.order["additional_discount_amount"],

      total_after_discount:
        this.order_data == null ? 0.0 : this.order_data.order["total"],

      total: this.order_data == null ? 0.0 : this.order_data.order["total"],

      product_list: [],
      order_status: "",
      payment_method:
        this.order_data == null ? "" : this.order_data.order["payment_method"],
      business_account:
        this.default_business_account == null
          ? ""
          : this.default_business_account.slack,
      waiter: this.order_data == null ? "" : this.order_data.order["waiter"],
      cart:
        this.order_data == null
          ? {}
          : JSON.parse(this.order_data.cart).length == 0
            ? {}
            : JSON.parse(this.order_data.cart),
      item_count: 0,
      quantity_count: 0,

      contact_number:
        this.order_data == null ? "" : this.order_data.order["contact_number"],
      address: this.order_data == null ? "" : this.order_data.order["address"],
      customer:
        this.order_data == null
          ? null
          : this.order_data.order["customer"] != ""
            ? this.order_data.order["customer"]
            : null,
      customer_list: [],
      customer_filter: "",
      customer_name: "",

      hold_order_list: [],
      search_hold_list: "",
      hold_list_processing: false,

      running_order_list: [],
      search_running_list: "",
      running_order_list_processing: false,
      running_order_has_more_items: false,
      running_order_total_records: "",
      running_order_next_page: 1,

      digital_menu_order_list: [],
      search_digital_menu_list: "",
      digital_menu_order_list_processing: false,
      digital_menu_order_has_more_items: false,
      digital_menu_order_total_records: "",
      digital_menu_order_next_page: 1,

      //restaurant
      //order_restaurant_mode : (this.order_data == null)?this.store_restaurant_mode:this.order_data.order['restaurant_mode'],
      order_restaurant_mode: this.store_restaurant_mode,
      billing_type:
        this.order_data != null
          ? this.order_data.order["billing_type"]
          : this.store_billing_type
            ? this.store_billing_type
            : "FINE_DINE",

      show_kitchen_modal: false,
      restaurant_order_type:
        this.order_data == null ? this.order_area_data == null ? "" : this.order_area_data.order_type : this.order_data.order["order_type"],
      restaurant_table:
        this.order_data == null ? this.order_area_data == null ? "" : this.order_area_data.table : this.order_data.order["table"],

      restaurant_mode_statuses: [
        "IN_KITCHEN",
        "HOLD",
        "PAYMENT_PENDING",
        "PAYMENT_FAILED",
      ],

      closing_amount: 0,
      credit_card_slips: 0,
      cheques: 0,

      billing_type_data: this.billing_type,

      product_focus: null,

      payment_method_focus: null,
      payment_method_focus_mode_rev: false,

      business_account_focus: null,
      business_account_focus_mode_rev: false,

      restaurant_order_type_focus: null,
      restaurant_order_type_focus_mode_rev: false,

      restaurant_table_focus: null,
      restaurant_table_focus_mode_rev: false,

      product_slack: null,
      base_product_slack: null,
      addon_product_group_processing: false,
      choosed_addon_product_group: {},
      selected_addon_slack_array: [],

      calc_received_amount: "",
      calc_order_amount: 0,
      calc_balance_amount: 0,
      calc_balace_type: "",
      show_balance_calc_form: false,

      selected_variant_list: {},
      selected_variant_keys: [],
      variant_parent_selected: null,

      pos_order: true,

      toggle_calculation_data:
        $cookies.get("toggle_calculation_data") != null
          ? $cookies.get("toggle_calculation_data")
          : 0,
    };
  },
  props: {
    store_tax_percentage: String,
    store_discount_percentage: String,
    payment_methods: Array,
    categories: Array,
    order_data: [Array, Object],
    order_area_data: [Array, Object],
    store_currency: String,
    default_business_account: [Array, Object],
    business_accounts: [Array, Object],
    store_restaurant_mode: Boolean,
    restaurant_order_types: [Array, Object],
    vacant_tables: [Array, Object],
    new_order_link: String,
    billing_types: [Array, Object],
    store_billing_type: String,
    keyboard_shortcuts: [Array, Object],
    keyboard_shortcuts_formatted: [Array, Object],
    enable_customer_detail_popup: Boolean,
    enable_vairants_popup: Boolean,
    customer_status: [Array, Object],
    waiters: [Array, Object],
    store_tax_type: String,
  },
  filters: {
    truncate: function (value, limit) {
      if (!value) return "";
      if (value.length > limit) {
        value = value.substring(0, limit - 3) + "...";
      }
      return value;
    },
  },
  computed: {
    hold_list_filtered() {
      if (this.search_hold_list) {
        return this.hold_order_list.filter((hold_order_list_item) => {
          return this.search_hold_list
            .toLowerCase()
            .split(" ")
            .every(
              (v) =>
                hold_order_list_item.order_number.toLowerCase().includes(v) ||
                hold_order_list_item.customer_phone.toLowerCase().includes(v) ||
                hold_order_list_item.customer_email.toLowerCase().includes(v)
            );
        });
      } else {
        return this.hold_order_list;
      }
    },
    running_order_list_filtered() {
      if (this.search_running_list) {
        return this.running_order_list.filter((running_order_list_item) => {
          return this.search_running_list
            .toLowerCase()
            .split(" ")
            .every(
              (v) =>
                running_order_list_item.order_number
                  .toLowerCase()
                  .includes(v) ||
                running_order_list_item.customer_phone
                  .toLowerCase()
                  .includes(v) ||
                running_order_list_item.customer_email
                  .toLowerCase()
                  .includes(v) ||
                running_order_list_item.order_type.toLowerCase().includes(v) ||
                running_order_list_item.table.toLowerCase().includes(v) ||
                (running_order_list_item.waiter_data != null
                  ? running_order_list_item.waiter_data.fullname
                    .toLowerCase()
                    .includes(v)
                  : "") ||
                (running_order_list_item.waiter_data != null
                  ? running_order_list_item.waiter_data.user_code
                    .toLowerCase()
                    .includes(v)
                  : "")
            );
        });
      } else {
        return this.running_order_list;
      }
    },
    digital_menu_order_list_filtered() {
      if (this.search_digital_menu_list) {
        return this.digital_menu_order_list.filter(
          (digital_menu_order_list_item) => {
            return this.search_digital_menu_list
              .toLowerCase()
              .split(" ")
              .every(
                (v) =>
                  digital_menu_order_list_item.order_number
                    .toLowerCase()
                    .includes(v) ||
                  digital_menu_order_list_item.customer_phone
                    .toLowerCase()
                    .includes(v) ||
                  digital_menu_order_list_item.customer_email
                    .toLowerCase()
                    .includes(v) ||
                  digital_menu_order_list_item.order_type
                    .toLowerCase()
                    .includes(v) ||
                  digital_menu_order_list_item.table
                    .toLowerCase()
                    .includes(v) ||
                  (digital_menu_order_list_item.waiter_data != null
                    ? digital_menu_order_list_item.waiter_data.fullname
                      .toLowerCase()
                      .includes(v)
                    : "") ||
                  (digital_menu_order_list_item.waiter_data != null
                    ? digital_menu_order_list_item.waiter_data.user_code
                      .toLowerCase()
                      .includes(v)
                    : "")
              );
          }
        );
      } else {
        return this.digital_menu_order_list;
      }
    },
  },
  mounted() {
    this.tick_update_duration_for_products();

    if (this.order_data !== null) {
      console.log('cart1 - ', this.cart)
      this.update_prices();
      this.update_customer();
    } else {
      if (this.show_customer_modal == false) {
        this.select_customer("skip");
      }
    }

    this.product_info_form();

    event_bus.$on("new_customer_data", this.set_new_customer_data);
  },
  created() {
    //if order is already closed, load new order page
    if (
      this.order_data != null &&
      this.order_data.order.current_status.value_constant == "CLOSED"
    ) {
      window.location.href = this.new_order_route;
    }
  },
  methods: {
    onDraggableChange() {
      var formData = new FormData();
      formData.append("access_token", window.settings.access_token);
      formData.append("category_list", JSON.stringify(this.category_list));
      axios
        .post("/api/draggable_change", formData)
        .then((response) => { })
        .catch((error) => {
          console.log(error);
        });
    },
    testing_handler() {
      var formData = new FormData();
      formData.append("access_token", window.settings.access_token);
      axios
        .post("/api/posting", formData)
        .then((response) => {
          console.log("success");
        })
        .catch((error) => {
          console.log(error);
        });
    },
    product_info_form() {
      this.product_processing = true;
      var formData = new FormData();

      formData.append("access_token", window.settings.access_token);
      formData.append("barcode", this.barcode);
      formData.append("product_title", this.product_title);
      axios
        .post("/api/get_product", formData)
        .then((response) => {
          this.product_processing = false;
          this.product_list = response.data.data;
          if (this.barcode != "" && this.product_list.length == 1) {
            this.resolve_variants(this.product_list[0]);
            this.barcode = "";
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },
    product_info_form_category(category) {
      var formData = new FormData();

      formData.append("access_token", window.settings.access_token);

      formData.append("product_category", category ? category : this.category);

      axios
        .post("/api/get_product", formData)
        .then((response) => {
          this.product_list = response.data.data;

          if (this.barcode != "" && this.product_list.length == 1) {
            this.resolve_variants(this.product_list[0]);
            this.barcode = "";
          }
        })
        .catch((error) => {
          console.log(error);
        });
    },

    create_order(order_status) {
      this.$off("submit");
      this.$off("close");
      if (Object.keys(this.cart).length <= 0) {
        return;
      }
      
      this.order_status = order_status;
      this.show_creation_modal = true;

      this.$on("submit", function () {
        this.$validator.validateAll("confirmation_form").then((isValid) => {
          
          if (isValid) {
            this.processing = true;
            var formData = new FormData();

            formData.append("access_token", window.settings.access_token);
            formData.append("order_status", this.order_status);
            formData.append("additional_note", this.additional_note);
            formData.append("print_type", this.print_type);
            formData.append(
              "customer",
              this.customer != null ? JSON.stringify(this.customer) : ""
            );
            formData.append(
              "contact_number",
              this.contact_number != null && this.contact_number != ""
                ? this.contact_number
                : ""
            );
            formData.append(
              "address",
              this.address != null && this.address != "" ? this.address : ""
            );
            formData.append("payment_method", this.payment_method);
            formData.append("business_account", this.business_account);
            formData.append(
              "restaurant_mode",
              this.order_restaurant_mode == true ? 1 : 0
            );
            formData.append(
              "restaurant_order_type",
              this.restaurant_order_type
            );
            formData.append("restaurant_table", this.restaurant_table);
            formData.append("waiter", this.waiter != null ? this.waiter : "");
            formData.append(
              "billing_type",
              this.billing_type != null ? this.billing_type : ""
            );
            formData.append(
              "additional_discount_percentage",
              this.additional_discount_percentage != null
                ? this.additional_discount_percentage
                : 0
            );
            formData.append("cart", JSON.stringify(this.cart));

            axios
              .post(this.api_link, formData)
              .then((response) => {
                if (response.data.status_code == 200) {
                  this.show_response_message(response.data.msg, "SUCCESS");

                  if (
                    typeof response.data.link != "undefined" &&
                    response.data.link != ""
                  ) {
                    if (response.data.new_tab == true) {
                      window.open(response.data.link, "_blank");
                    } else {
                      if (this.order_status == "CLOSE") {
                        window.open(response.data.link, "_self");
                        // setTimeout(function () {
                        //   window.location.href = response.data.orders_link;
                        // }, 500);
                      }
                      else {
                        window.open(response.data.link, "_self");
                        setTimeout(function () {
                          window.location.reload();
                        }, 1000);
                      }

                    }
                  } else {
                    setTimeout(function () {
                      window.location.reload();
                    }, 1000);
                  }
                } else {
                  this.processing = false;
                  try {
                    var error_json = JSON.parse(response.data.msg);
                    this.loop_api_errors(error_json);
                  } catch (err) {
                    this.server_errors = response.data.msg;
                  }
                  this.error_class = "error";
                }
              })
              .catch((error) => {
                console.log(error);
              });
          }
        });
      });

      this.$on("close", function () {
        this.show_creation_modal = false;
      });

      this.calc_order_amount = this.total;
      this.calc_received_amount = this.calc_order_amount;
      if (this.order_status == "CLOSE") {
        this.show_balance_calc_form = true;
      }
    },

    load_customers(keywords, type) {
      if (typeof keywords != "undefined") {
        if (keywords.length > 0) {
          var formData = new FormData();
          formData.append("access_token", window.settings.access_token);
          formData.append("keywords", keywords);
          formData.append("type", type);

          axios
            .post("/api/load_customers", formData)
            .then((response) => {
              if (response.data.status_code == 200) {
                this.customer_list = response.data.data;
              }
            })
            .catch((error) => {
              console.log(error);
            });
        }
      }
    },

    select_customer(action) {
      switch (action) {
        case "skip":
          this.show_customer_modal = false;
          break;
        case "proceed":
          if (this.customer_name != "") {
            var temp_customer = {};
            temp_customer.slack = "";
            temp_customer.name = this.customer_name;
            this.set_customer(temp_customer);
          }
          this.show_customer_modal = false;
          break;
      }
      this.customer_label = this.get_customer_label();
      this.$refs.barcode.focus();
    },

    set_customer(data) {
      this.customer = data;
      this.customer_filter = "";
      this.customer_list = [];
      this.customer_label = this.get_customer_label();
    },

    get_customer_label() {
      var customer_label = "";
      if (this.customer != null && this.customer.name != "") {
        customer_label = this.customer.name;
      } else if (this.customer != null && this.customer.phone != "") {
        customer_label = this.customer.phone;
      } else if (this.customer != null && this.customer.email != "") {
        customer_label = this.customer.email;
      } else {
        customer_label = this.default_label;
      }
      return customer_label;
    },

    dismiss_customer() {
      this.customer = null;
      this.customer_label = this.get_customer_label();
    },

    update_customer() {
      this.customer_label = this.get_customer_label();
    },

    set_new_customer_data(data) {
      this.set_customer(data);
    },

    get_hold_list() {
      this.show_hold_list_modal = true;
      this.hold_list_processing = true;
      this.$off("close");

      var formData = new FormData();
      formData.append("access_token", window.settings.access_token);

      axios
        .post("/api/get_hold_list", formData)
        .then((response) => {
          if (response.data.status_code == 200) {
            this.hold_list_processing = false;
            this.hold_order_list = response.data.data;
          }
        })
        .catch((error) => {
          console.log(error);
        });

      this.$on("close", function () {
        this.show_hold_list_modal = false;
      });
    },

    run_clock() {
      setInterval(() => {
        this.current_date_time = moment().format("DD/MM/YYYY, h:mm:ss a");
      }, 1000);
    },

    set_table_based_on_order_type() {
      if (
        this.restaurant_order_type == "TAKEWAY" ||
        this.restaurant_order_type == "DELIVERY"
      ) {
        this.restaurant_table = "";
      }
    },

    close_register() {
      this.$off("submit");
      this.$off("close");

      this.get_register_total_amount();
      this.show_close_register_modal = true;

      this.$on("submit", function () {
        this.$validator.validateAll("close_register_form").then((isValid) => {
          if (isValid) {
            this.processing = true;
            var formData = new FormData();

            formData.append("access_token", window.settings.access_token);
            formData.append("closing_amount", this.closing_amount);
            formData.append("credit_card_slips", this.credit_card_slips);
            formData.append("cheques", this.cheques);

            axios
              .post(this.close_register_api_link, formData)
              .then((response) => {
                if (response.data.status_code == 200) {
                  this.show_response_message(response.data.msg, "SUCCESS");
                  if (
                    typeof response.data.link != "undefined" &&
                    response.data.link != ""
                  ) {
                    if (response.data.new_tab == true) {
                      window.open(response.data.link, "_blank");
                    } else {
                      window.location.href = response.data.link;
                    }

                    setTimeout(function () {
                      //window.location.reload();
                    }, 1000);
                  } else {
                    setTimeout(function () {
                      window.location.reload();
                    }, 1000);
                  }
                } else {
                  this.processing = false;
                  try {
                    var error_json = JSON.parse(response.data.msg);
                    this.loop_api_errors(error_json);
                  } catch (err) {
                    this.close_register_server_errors = response.data.msg;
                  }
                  this.error_class = "error";
                }
              })
              .catch((error) => {
                console.log(error);
              });
          }
        });
      });

      this.$on("close", function () {
        this.show_close_register_modal = false;
      });
    },

    get_register_total_amount() {
      this.register_amount_processing = true;

      var formData = new FormData();
      formData.append("access_token", window.settings.access_token);

      axios
        .post("/api/get_register_order_amount", formData)
        .then((response) => {
          if (response.data.status_code == 200) {
            this.closing_amount = response.data.data;
            this.register_amount_processing = false;
          }
        })
        .catch((error) => {
          this.register_amount_processing = false;
          console.log(error);
        });
    },

    get_running_orders_list(page) {
      this.show_running_order_quickpanel = true;

      if (typeof page === "undefined") {
        page = 1;
      } 

      if (this.show_running_order_quickpanel == true) {
        this.running_order_list_processing = true;
        var formData = new FormData();
        formData.append("access_token", window.settings.access_token);

        axios
          .post("/api/get_running_order_list?page=" + page, formData)
          .then((response) => {
            if (response.data.status_code == 200) {
              this.running_order_list_processing = false;
              var running_order_list = response.data.data.data;
              if (page == 1) {
                this.running_order_list = [];
              }
              for(let item of running_order_list)
              {
                if(item.status.constant == "CLOSED")
                  continue;
                this.running_order_list.push(item);
              }
              this.running_order_has_more_items =
                response.data.data.links.has_more_items;
              this.running_order_total_records =
                this.running_order_list.length;
                // response.data.data.links.total_records;
              this.running_order_next_page =
                response.data.data.links.has_more_items == true
                  ? response.data.data.links.current_page + 1
                  : 1;
            }
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },

    calculate_duration(created_date) {
      var moment = require("moment-timezone");
      var tz = moment.tz.guess();

      var today = moment();
      var date_obj = new Date(created_date);
      var moment_obj = moment.unix(date_obj).tz(tz);

      var duration = moment.duration(today.diff(moment_obj));
      var minutes = Math.abs(Math.round(duration.as("minutes")));
      minutes = isNaN(minutes) ? "-" : minutes;
      return minutes;
    },

    update_duration_for_products() {
      for (var i = 0; i < this.running_order_list.length; i++) {
        var duration = this.calculate_duration(
          this.running_order_list[i].create_at_utc
        );
        this.$set(this.running_order_list[i], "duration", duration);
      }

      for (var i = 0; i < this.digital_menu_order_list.length; i++) {
        var duration = this.calculate_duration(
          this.digital_menu_order_list[i].create_at_utc
        );
        this.$set(this.digital_menu_order_list[i], "duration", duration);
      }
    },

    tick_update_duration_for_products() {
      setInterval(() => {
        this.update_duration_for_products();
      }, 1000);
    },

    go_to_order(order_status_constant, order_detail_link, order_edit_link) {
      window.location.href =
        order_status_constant == "CLOSED" ? order_edit_link : order_detail_link;
    },

    choose_payment_method(data) {
      this.payment_method = data.slack;
    },

    choose_business_account(data) {
      this.business_account = data.slack;
    },

    choose_restaurant_order_type(data) {
      this.restaurant_order_type = data.order_type_constant;
    },

    choose_restaurant_table(data) {
      this.restaurant_table = data.slack;
    },

    product_navigate(event) {
      var x = document.activeElement.tagName;
      switch (event.srcKey) {
        case "left":
          if (this.product_focus === null) {
            this.product_focus = 0;
          } else if (this.product_focus > 0) {
            this.product_focus--;
          }
          break;
        case "right":
          if (this.product_focus === null) {
            this.product_focus = 0;
          } else if (this.product_focus < this.product_list.length - 1) {
            this.product_focus++;
          }
          break;
        case "choose":
          this.add_to_cart(this.product_list[this.product_focus]);
          break;
      }
    },

    order_confirm_navigate(event, type, data) {
      switch (type) {
        case "payment_method":
          switch (event.srcKey) {
            case "scroll":
              if (this.payment_method_focus === null) {
                this.payment_method_focus = 0;
              } else if (
                this.payment_method_focus <
                this.payment_methods.length - 1
              ) {
                if (this.payment_method_focus_mode_rev == false) {
                  this.payment_method_focus++;
                } else {
                  this.payment_method_focus--;
                  if (this.payment_method_focus < 0) {
                    this.payment_method_focus = Math.abs(
                      this.payment_method_focus
                    );
                    this.payment_method_focus_mode_rev = false;
                  }
                }
              } else if (
                this.payment_method_focus == this.payment_methods.length - 1 &&
                this.payment_method_focus != 0
              ) {
                this.payment_method_focus--;
                this.payment_method_focus_mode_rev = true;
              }
              break;
            case "choose":
              this.choose_payment_method(
                this.payment_methods[this.payment_method_focus]
              );
              break;
          }
          break;
        case "business_account":
          switch (event.srcKey) {
            case "scroll":
              if (this.business_account_focus === null) {
                this.business_account_focus = 0;
              } else if (
                this.business_account_focus <
                this.business_accounts.length - 1
              ) {
                if (this.business_account_focus_mode_rev == false) {
                  this.business_account_focus++;
                } else {
                  this.business_account_focus--;
                  if (this.business_account_focus < 0) {
                    this.business_account_focus = Math.abs(
                      this.business_account_focus
                    );
                    this.business_account_focus_mode_rev = false;
                  }
                }
              } else if (
                this.business_account_focus ==
                this.business_accounts.length - 1 &&
                this.business_account_focus != 0
              ) {
                this.business_account_focus--;
                this.business_account_focus_mode_rev = true;
              }
              break;
            case "choose":
              this.choose_business_account(
                this.business_accounts[this.business_account_focus]
              );
              break;
          }
          break;
        case "order_type":
          switch (event.srcKey) {
            case "scroll":
              if (this.restaurant_order_type_focus === null) {
                this.restaurant_order_type_focus = 0;
              } else if (
                this.restaurant_order_type_focus <
                this.restaurant_order_types.length - 1
              ) {
                if (this.restaurant_order_type_focus_mode_rev == false) {
                  this.restaurant_order_type_focus++;
                } else {
                  this.restaurant_order_type_focus--;
                  if (this.restaurant_order_type_focus < 0) {
                    this.restaurant_order_type_focus = Math.abs(
                      this.restaurant_order_type_focus
                    );
                    this.restaurant_order_type_focus_mode_rev = false;
                  }
                }
              } else if (
                this.restaurant_order_type_focus ==
                this.restaurant_order_types.length - 1 &&
                this.restaurant_order_type_focus != 0
              ) {
                this.restaurant_order_type_focus--;
                this.restaurant_order_type_focus_mode_rev = true;
              }
              break;
            case "choose":
              this.choose_restaurant_order_type(
                this.restaurant_order_types[this.restaurant_order_type_focus]
              );
              break;
          }
          break;
        case "restaurant_table":
          switch (event.srcKey) {
            case "scroll":
              if (this.restaurant_table_focus === null) {
                this.restaurant_table_focus = 0;
              } else if (this.restaurant_table_focus < this.length - 1) {
                if (this.restaurant_table_focus_mode_rev == false) {
                  this.restaurant_table_focus++;
                } else {
                  this.restaurant_table_focus--;
                  if (this.restaurant_table_focus < 0) {
                    this.restaurant_table_focus = Math.abs(
                      this.restaurant_table_focus
                    );
                    this.restaurant_table_focus_mode_rev = false;
                  }
                }
              } else if (
                this.restaurant_table_focus == this.vacant_tables.length - 1 &&
                this.restaurant_table_focus != 0
              ) {
                this.restaurant_table_focus--;
                this.restaurant_table_focus_mode_rev = true;
              }
              break;
            case "choose":
              this.choose_restaurant_table(
                this.vacant_tables[this.restaurant_table_focus]
              );
              break;
          }
          break;
      }
    },

    get_keyboard_shortcuts() {
      this.show_keyboard_shortcuts_quickpanel = true;
    },

    get_digital_menu_orders_list(page) {
      this.show_digital_menu_order_quickpanel = true;

      if (typeof page === "undefined") {
        page = 1;
      }

      if (this.show_digital_menu_order_quickpanel == true) {
        this.digital_menu_order_list_processing = true;
        var formData = new FormData();
        formData.append("access_token", window.settings.access_token);

        axios
          .post("/api/get_digital_menu_orders_list?page=" + page, formData)
          .then((response) => {
            if (response.data.status_code == 200) {
              this.digital_menu_order_list_processing = false;
              var digital_menu_order_list = response.data.data.data;

              if (page == 1) {
                this.digital_menu_order_list = [];
              }
              digital_menu_order_list.forEach((item) => {
                this.digital_menu_order_list.push(item);
              });

              this.digital_menu_order_has_more_items =
                response.data.data.links.has_more_items;
              this.digital_menu_order_total_records =
                response.data.data.links.total_records;
              this.digital_menu_order_next_page =
                response.data.data.links.has_more_items == true
                  ? response.data.data.links.current_page + 1
                  : 1;
            }
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },

    calculate_balance_amount() {
      var balance =
        parseFloat(this.calc_received_amount) -
        parseFloat(this.calc_order_amount);
      this.calc_balace_type = balance > 0 ? "Return" : "Receive";
      this.calc_balance_amount = !isNaN(balance) ? balance.toFixed(2) : "-";
    },

    new_customer_form() {
      this.show_new_customer_form = this.show_new_customer_form ? false : true;
    },

    toggle_calculation() {
      this.toggle_calculation_data = !(this.toggle_calculation_data == true);
      $cookies.set(
        "toggle_calculation_data",
        this.toggle_calculation_data ? 1 : 0
      );
    },
  },
};
</script>
