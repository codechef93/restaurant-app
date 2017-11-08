<template>
  <div class="row">
    <div class="col-md-12">
      <div class="d-flex flex-wrap mb-4">
        <div class="mr-auto">
          <div class="d-flex">
            <div>
              <span class="text-title">
                {{ $t("Order") }} #{{ order_basic.order_number }}
              </span>
            </div>
          </div>
        </div>
        <div class="">
          <span
            v-if="
              order_basic.restaurant_mode == 1 &&
              order_basic.kitchen_status != null
            "
            v-bind:class="order_basic.kitchen_status.color"
            class="mr-2"
            >{{ order_basic.kitchen_status.label }}</span
          >
          <span
            v-if="order_basic.payment_status != null"
            v-bind:class="order_basic.payment_status.color"
            class="mr-2"
            >{{ order_basic.payment_status.label }}</span
          >
          <span v-bind:class="order_basic.status.color">{{
            order_basic.status.label
          }}</span>
        </div>
      </div>

      <div class="d-flex flex-wrap mb-4">
        <p v-html="server_errors" v-bind:class="[error_class]"></p>

        <div class="ml-auto">
          <button
            type="submit"
            class="btn btn-danger mr-1"
            v-if="delete_order_access == true && logged_user_id == 1"
            v-on:click="delete_order()"
            v-bind:disabled="order_processing == true"
          >
            <i
              class="fa fa-circle-notch fa-spin"
              v-if="order_processing == true"
            ></i>
            {{ $t("Delete Order") }}
            
          </button>

          <button
            type="button"
            class="btn btn-outline-primary mr-1"
            v-if="
              share_invoice_sms_access == true && order_basic.status.value == 1
            "
            v-on:click="share_invoice_as_sms()"
            v-bind:disabled="send_sms_processing == true"
          >
            <i
              class="fa fa-circle-notch fa-spin"
              v-if="send_sms_processing == true"
            ></i>
            {{ $t("Share Invoice as SMS") }}
          </button>

          <button
            type="button"
            class="btn btn-outline-primary mr-1"
            v-if="merge_order_access == true && order_basic.status.value != 1"
            v-on:click="merge_order()"
          >
            <i class="fas fa-clone"></i> {{ $t("Merge Orders") }}
          </button>

          <button
            type="button"
            class="btn btn-outline-primary mr-1"
            v-if="unmerge_order_access == true && order_basic.status.value == 4"
            v-on:click="unmerge_order()"
          >
            <i class="far fa-clone"></i> {{ $t("Unmerge Order") }}
          </button>

          <a
            class="btn btn-outline-primary mr-1"
            v-bind:href="print_order_link"
            target="_blank"
            >{{ $t("PDF") }}</a
          >

          <button
            class="btn btn-outline-primary mr-1"
            v-if="printnode_enabled == true"
            v-on:click="printnode_print('POS_INVOICE')"
            v-bind:disabled="processing == true"
          >
            <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
            {{ $t("Print Invoice") }}
          </button>

          <button
            class="btn btn-outline-primary"
            v-if="print_kot_link != '' && printnode_enabled == true"
            v-on:click="printnode_print('KOT')"
            v-bind:disabled="kot_processing == true"
          >
            <i
              class="fa fa-circle-notch fa-spin"
              v-if="kot_processing == true"
            ></i>
            {{ $t("Print KOT") }}
          </button>
        </div>
      </div>

      <div v-show="order_basic.restaurant_mode == 1">
        <div class="mb-2">
          <span class="text-subhead">{{
            $t("Restaurant Mode Information")
          }}</span>
        </div>
        <div
          class="form-row mb-2"
          v-if="
            order_basic.contact_number != null || order_basic.address != null
          "
        >
          <div class="form-group col-md-6">
            <label for="email">{{ $t("Delivery Details") }}</label>
            <span class="d-block"
              ><span class="text-muted small">{{ $t("Contact Number") }}</span>
              {{ order_basic.contact_number }}</span
            >
            <span class="d-block"
              ><span class="text-muted small">{{ $t("Address") }}</span>
              <span class="custom-pre">{{
                order_basic.address ? order_basic.address : "-"
              }}</span></span
            >
          </div>
        </div>
        <div class="form-row mb-2">
          <div
            class="form-group col-md-3"
            v-show="order_basic.restaurant_mode == 1"
          >
            <label for="email">{{ $t("Order From") }}</label>
            <p>
              {{
                order_basic.order_origin == "DIGITAL_MENU"
                  ? "Digital Menu"
                  : "POS Screen"
              }}
            </p>
          </div>
          <div class="form-group col-md-3">
            <label for="email">{{ $t("Order Type") }}</label>
            <p>{{ order_basic.order_type }}</p>
          </div>
          <div class="form-group col-md-3">
            <label for="email">{{ $t("Billing Type") }}</label>
            <p>
              {{
                order_basic.billing_type_data != null
                  ? order_basic.billing_type_data.label
                  : "-"
              }}
            </p>
          </div>
          <div class="form-group col-md-3">
            <label for="email">{{ $t("Table Number or Name") }}</label>
            <p>
              {{
                order_basic.table != null && order_basic.table != ""
                  ? order_basic.table
                  : "-"
              }}
            </p>
          </div>
          <div class="form-group col-md-3">
            <label for="email">{{ $t("Waiter") }}</label>
            <p>
              {{
                order_basic.waiter_data != null
                  ? order_basic.waiter_data.fullname +
                    " (" +
                    order_basic.waiter_data.user_code +
                    ")"
                  : "-"
              }}
            </p>
          </div>
        </div>
      </div>
      <hr />

      <div class="mb-2">
        <span class="text-subhead">{{ $t("Basic Information") }}</span>
      </div>
      <div class="form-row mb-2">
        <div class="form-group col-md-3">
          <label for="email">{{ $t("Name") }}</label>
          <p>
            {{
              order_basic.customer_name == null
                ? "-"
                : order_basic.customer_name
            }}
          </p>
        </div>
        <div class="form-group col-md-3">
          <label for="email">{{ $t("Email") }}</label>
          <p>
            {{
              order_basic.customer_email == null
                ? "-"
                : order_basic.customer_email
            }}
          </p>
        </div>
        <div class="form-group col-md-3">
          <label for="email">{{ $t("Phone") }}</label>
          <p>
            {{
              order_basic.customer_phone == null
                ? "-"
                : order_basic.customer_phone
            }}
          </p>
        </div>
        <div class="form-group col-md-3">
          <label for="email">{{ $t("Payment Mode") }}</label>
          <p>
            {{
              order_basic.payment_method == null
                ? "-"
                : order_basic.payment_method
            }}
          </p>
        </div>
        <div class="form-group col-md-3">
          <label for="created_by">{{ $t("Created By") }}</label>
          <p>
            {{
              order_basic.created_by == null
                ? "-"
                : order_basic.created_by["fullname"] +
                  " (" +
                  order_basic.created_by["user_code"] +
                  ")"
            }}
          </p>
        </div>
        <div class="form-group col-md-3">
          <label for="updated_by">{{ $t("Updated By") }}</label>
          <p>
            {{
              order_basic.updated_by == null
                ? "-"
                : order_basic.updated_by["fullname"] +
                  " (" +
                  order_basic.updated_by["user_code"] +
                  ")"
            }}
          </p>
        </div>
        <div class="form-group col-md-3">
          <label for="created_on">{{ $t("Created On") }}</label>
          <p>{{ order_basic.created_at_label }}</p>
        </div>
        <div class="form-group col-md-3">
          <label for="updated_on">{{ $t("Updated On") }}</label>
          <p>{{ order_basic.updated_at_label }}</p>
        </div>
      </div>
      <hr />

      <div class="mb-3">
        <div class="mb-2">
          <span class="text-subhead">{{
            $t("Order Level Tax Information")
          }}</span>
        </div>
        <div
          class="form-row mb-2"
          v-if="order_basic.order_level_tax_percentage > 0"
        >
          <div class="form-group col-md-3">
            <label for="tax_code">{{ $t("Tax Code") }}</label>
            <p>{{ order_basic.order_level_tax_code }}</p>
          </div>
          <div class="form-group col-md-3">
            <label for="tax_percentage">{{ $t("Tax Percentage") }}</label>
            <p>{{ order_basic.order_level_tax_percentage }}</p>
          </div>
          <div class="form-group col-md-3">
            <label for="tax_amount">{{ $t("Tax Amount") }}</label>
            <p>{{ order_basic.order_level_tax_amount }}</p>
          </div>
        </div>

        <div class="col-md-6">
          <div class="row">
            <div
              class="table-responsive"
              v-if="order_basic.order_level_tax_percentage > 0"
            >
              <table class="table display nowrap text-nowrap w-100">
                <thead>
                  <tr>
                    <th scope="col">{{ $t("Tax Type") }}</th>
                    <th scope="col">{{ $t("Tax Percentage") }}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(
                      tax_component, key, index
                    ) in order_basic.order_level_tax_components"
                    v-bind:key="index"
                  >
                    <td>{{ tax_component.tax_type }}</td>
                    <td>{{ tax_component.tax_percentage }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <span class="mb-2" v-else>No Order Level Tax Components</span>
          </div>
        </div>
      </div>
      <hr />

      <div class="mb-3">
        <div class="mb-2">
          <span class="text-subhead">{{
            $t("Order Level Discount Information")
          }}</span>
        </div>
        <div
          class="form-row mb-2"
          v-if="order_basic.order_level_discount_percentage > 0"
        >
          <div class="form-group col-md-3">
            <label for="discount_code">{{ $t("Discount Code") }}</label>
            <p>{{ order_basic.order_level_discount_code }}</p>
          </div>
          <div class="form-group col-md-3">
            <label for="discount_percentage">{{
              $t("Discount Percentage")
            }}</label>
            <p>{{ order_basic.order_level_discount_percentage }}</p>
          </div>
          <div class="form-group col-md-3">
            <label for="discount_code_label">{{ $t("Discount Amount") }}</label>
            <p>{{ order_basic.order_level_discount_amount }}</p>
          </div>
        </div>
        <div class="mb-3" v-else>No Order Level Discount Information</div>
      </div>
      <hr />

      <div class="mb-2">
        <span class="text-subhead">{{ $t("Product Information") }}</span>
      </div>
      <div class="table-responsive mb-2">
        <table class="table table-striped display nowrap text-nowrap w-100">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">{{ $t("Product Code") }}</th>
              <th scope="col">{{ $t("Product") }}</th>
              <th scope="col" class="text-right">{{ $t("Quantity") }}</th>
              <th scope="col" class="text-right">
                {{ $t("Price") }} (EXCL Tax)
              </th>
              <th scope="col" class="text-right">{{ $t("Discount %") }}</th>
              <th scope="col" class="text-right">
                {{ $t("Discount Amount") }}
              </th>
              <th scope="col" class="text-right">{{ $t("Tax %") }}</th>
              <th scope="col" class="text-right">{{ $t("Tax Amount") }}</th>
              <th scope="col" class="text-right">{{ $t("Total") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(order_product, key, index) in products"
              v-bind:value="order_product.product_slack"
              v-bind:key="index"
            >
              <th scope="row" v-if="order_product.parent_order_product">
                {{ order_product.counter }}
              </th>
              <th scope="row" v-else>
                <span v-html="addon_label" class="pl-4"></span>
              </th>
              <td>{{ order_product.product_code }}</td>
              <td>{{ order_product.name }}</td>
              <td class="text-right">{{ order_product.quantity }}</td>
              <td class="text-right">{{ order_product.price }}</td>
              <td class="text-right">
                {{ order_product.discount_percentage }}
              </td>
              <td class="text-right">{{ order_product.discount_amount }}</td>
              <td class="text-right">
                {{ order_product.tax_percentage }}
                <span
                  class="d-block small"
                  v-for="(
                    tax_component, key, index
                  ) in order_product.tax_components"
                  v-bind:key="index"
                >
                  <span v-show="tax_component.tax_percentage > 0"
                    >{{ tax_component.tax_type }} :
                    {{ tax_component.tax_percentage }}%</span
                  >
                </span>
              </td>
              <td class="text-right">
                {{ order_product.tax_amount }}
                <span
                  class="d-block small"
                  v-for="(
                    tax_component, key, index
                  ) in order_product.tax_components"
                  v-bind:key="index"
                >
                  <span v-show="tax_component.tax_percentage > 0"
                    >{{ tax_component.tax_type }} :
                    {{ tax_component.tax_amount.toFixed(2) }}</span
                  >
                </span>
              </td>
              <td class="text-right">{{ order_product.total_price }}</td>
            </tr>
            <tr>
              <td colspan="9" class="text-right">
                {{ $t("Sub Total") }} (EXCL Tax)
              </td>
              <td class="text-right">
                {{ order_basic.sale_amount_subtotal_excluding_tax }}
              </td>
            </tr>
            <tr>
              <td colspan="9" class="text-right">{{ $t("Discount") }}</td>
              <td class="text-right">
                {{ order_basic.total_discount_before_additional_discount }}
              </td>
            </tr>
            <tr>
              <td colspan="9" class="text-right">
                {{ $t("Additional Discount") }} ({{
                  order_basic.additional_discount_percentage
                }}%)
              </td>
              <td class="text-right">
                {{ order_basic.additional_discount_amount }}
              </td>
            </tr>
            <tr>
              <td colspan="9" class="text-right">{{ $t("Total Discount") }}</td>
              <td class="text-right">
                {{ order_basic.total_discount_amount }}
              </td>
            </tr>
            <tr>
              <td colspan="9" class="text-right">
                {{ $t("Total After Discount") }}
              </td>
              <td class="text-right">{{ order_basic.total_after_discount }}</td>
            </tr>
            <tr>
              <td colspan="9" class="text-right">
                {{ $t("Total Tax") }}
                <small
                  v-if="order_basic.product_level_total_tax > 0"
                  class="d-block"
                >
                  Product Tax : {{ order_basic.product_level_total_tax }}
                </small>
                <small
                  v-if="order_basic.order_level_tax_percentage > 0"
                  class="d-block"
                >
                  Overall Tax :
                  <span
                    v-for="(
                      tax_component, key, index
                    ) in order_basic.order_level_tax_components"
                    v-bind:key="index"
                  >
                    {{ tax_component.tax_type }} :
                    {{ tax_component.tax_amount.toFixed(2) }} ({{
                      tax_component.tax_percentage
                    }}%) &middot;
                  </span>
                </small>
              </td>
              <td class="text-right">{{ order_basic.total_tax_amount }}</td>
            </tr>
            <tr>
              <td colspan="9" class="text-right text-bold">
                {{ $t("Total") }} (INCL Tax)
              </td>
              <td class="text-right text-bold">
                {{ order_basic.currency_code }}
                {{ order_basic.total_order_amount }}
              </td>
            </tr>
            <tr v-if="order_basic.order_difference != ''">
              <td colspan="9" class="text-right">
                {{ $t("Merge Difference Amount") }}
              </td>
              <td class="text-right">
                {{ order_basic.order_difference }}<br />
                <small>{{
                  order_basic.order_difference.order_difference_exists ==
                  "positive"
                    ? "Receive from Customer"
                    : "Return to Customer"
                }}</small>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div
        v-if="
          order_data.merged_orders != null &&
          order_data.merged_orders.length != 0
        "
      >
        <hr />
        <div class="mb-2">
          <span class="text-subhead">{{ $t("Merged Orders") }}</span>
        </div>
        <div class="d-flex flex-wrap mb-4">
          <span
            v-for="(merged_order, key, index) in order_data.merged_orders"
            v-bind:value="merged_order.slack"
            v-bind:key="index"
          >
            <span v-if="merged_order.detail_link != ''"
              ><a v-bind:href="merged_order.detail_link" target="_blank">{{
                merged_order.order_number
              }}</a></span
            ><span v-else>{{ merged_order.order_number }}</span>
            &nbsp;&middot;&nbsp;
          </span>
        </div>
      </div>

      <hr />
      <transactionlistcomponent
        :transaction_list="transactions"
      ></transactionlistcomponent>
    </div>

    <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
      <template v-slot:modal-header>
        {{ $t("Confirm") }}
      </template>
      <template v-slot:modal-body>
        {{ $t("Are you sure you want to proceed?") }}
      </template>
      <template v-slot:modal-footer>
        <button type="button" class="btn btn-light" @click="$emit('close')">
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-primary"
          @click="$emit('submit')"
          v-bind:disabled="processing == true"
        >
          <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
          Continue
        </button>
      </template>
    </modalcomponent>

    <modalcomponent
      v-if="show_share_invoice_sms_modal"
      v-on:close="show_share_invoice_sms_modal = false"
    >
      <template v-slot:modal-header>
        {{ $t("Confirm") }}
      </template>
      <template v-slot:modal-body>
        Are you sure you want to share the invoice as SMS to
        {{ order_basic.customer_phone }}?
      </template>
      <template v-slot:modal-footer>
        <button type="button" class="btn btn-light" @click="$emit('close')">
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-primary"
          @click="$emit('submit')"
          v-bind:disabled="processing == true"
        >
          <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
          Continue
        </button>
      </template>
    </modalcomponent>

    <modalcomponent
      v-if="show_merge_order_modal"
      v-on:close="show_merge_order_modal = false"
      :modal_width="'modal-container-md'"
    >
      <template v-slot:modal-header>
        {{ $t("Merge Orders") }}
      </template>
      <template v-slot:modal-body>
        <p v-html="merge_order_server_errors" v-bind:class="[error_class]"></p>
        <form data-vv-scope="merge_order_form">
          <div class="mb-3">
            <small class="form-text text-secondary"
              ><i class="fas fa-info-circle text-primary"></i>&nbsp;{{
                $t(
                  "Only closed orders are allowed to merge. You won't be able to choose already merged orders again."
                )
              }}</small
            >
            <small class="form-text text-secondary pl-3">{{
              $t(
                "Additional discounts and store-level tax or discounts will be calculated based on parent orders."
              )
            }}</small>
            <small class="form-text text-secondary pl-3">{{
              $t("Similar products won't be grouped after merging.")
            }}</small>
          </div>

          <hr class="ml-1 mr-1" />

          <div class="form-row mb-2 mt-2">
            <div class="form-group col-md-6">
              <label for="barcode">{{ $t("Search and Choose Orders") }}</label>
              <cool-select
                type="text"
                v-model="search_order"
                autocomplete="off"
                inputForTextClass="form-control form-control-custom"
                :items="order_list"
                :resetSearchOnBlur="false"
                disable-filtering-by-search
                @search="load_orders"
                @select="add_order_to_merge_list"
                :placeholder="$t('Start Typing..')"
              >
                <template #item="{ item }">
                  <div class="d-flex justify-content-start">
                    <div>
                      {{ item.order_number }}
                    </div>
                  </div>
                </template>
              </cool-select>
              <small class="form-text text-muted">{{
                $t("Filter by order number, customer email or contact number")
              }}</small>
            </div>
          </div>

          <hr class="ml-1 mr-1" />

          <span class="text-subhead">{{ $t("Selected Orders") }}</span>

          <div class="form-row mt-2">
            <div class="form-group col-md-3 mb-1">
              <label for="name">{{ $t("Order Number") }}</label>
            </div>
            <div class="form-group col-md-3 mb-1">
              <label for="amount">{{ $t("Amount") }}</label>
            </div>
            <div class="form-group col-md-4 mb-1">
              <label for="name">{{ $t("Order Date") }}</label>
            </div>
            <div class="form-group col-md-2 mb-1"></div>
          </div>
          <div
            class="form-row mb-2"
            v-for="(merge_order_item, index) in merge_order_list"
            :key="index"
          >
            <div class="form-group col-md-3 mb-1 mt-2">
              {{ merge_order_item.order_number }}
            </div>
            <div class="form-group col-md-3 mb-1 mt-2">
              {{ merge_order_item.currency_code }}
              {{ merge_order_item.total_order_amount }}
            </div>
            <div class="form-group col-md-4 mb-1 mt-2">
              {{ merge_order_item.created_at_label }}
            </div>
            <div class="form-group col-md-2 text-right">
              <button
                type="button"
                class="btn btn-outline-danger"
                @click="remove_order(index)"
              >
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </form>
      </template>
      <template v-slot:modal-footer>
        <button type="button" class="btn btn-light" @click="$emit('close')">
          Cancel
        </button>
        <button
          type="button"
          class="btn btn-primary"
          @click="$emit('submit')"
          v-bind:disabled="processing == true"
        >
          <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
          Continue
        </button>
      </template>
    </modalcomponent>

    <modalcomponent
      v-show="show_merge_response_modal"
      v-on:close="reload_window()"
    >
      <template v-slot:modal-header> Merge Response </template>
      <template v-slot:modal-body>
        <p>
          {{
            $t(
              "There is a difference in order amount after merge. Merged order has a difference of amount"
            )
          }}
          {{ order_difference }}
        </p>
        <p>
          Please
          {{ order_difference_type == "positive" ? "receive" : "return" }}
          {{ order_basic.currency_code }} {{ order_difference }}
          {{ order_difference_type == "positive" ? "from" : "to" }} the
          customer.
        </p>
        <p>You can ignore this dialogue, if the amount is minimal.</p>
      </template>
      <template v-slot:modal-footer>
        <button type="button" class="btn btn-primary" @click="reload_window()">
          Ok
        </button>
      </template>
    </modalcomponent>
  </div>
</template>

<script>
"use strict";

import { CoolSelect } from "vue-cool-select";
import "vue-cool-select/dist/themes/bootstrap.css";

export default {
  data() {
    return {
      server_errors: "",
      error_class: "",
      order_processing: false,
      processing: false,
      kot_processing: false,
      show_modal: false,

      show_share_invoice_sms_modal: false,
      send_sms_processing: false,
      show_merge_order_modal: false,
      show_merge_response_modal: false,

      delete_order_api_link: "/api/delete_order/" + this.order_data.slack,
      sms_invoice_api_link: "/api/share_invoice_sms/" + this.order_data.slack,
      merge_order_api_link: "/api/merge_order",
      unmerge_order_api_link: "/api/unmerge_order",
      printnode_api_link: "/api/print_with_printnode",

      slack: this.order_data.slack,
      order_basic: this.order_data,
      products: this.order_data.products,
      transactions: this.order_data.transactions,

      addon_label:
        '<i class="fas fa-level-up-alt fa-rotate-90 text-success"></i>',

      search_order: "",
      order_list: [],
      merge_order_list: [],

      merge_order_server_errors: "",
      order_difference: "",
      order_difference_type: "",
    };
  },
  props: {
    order_data: [Array, Object],
    logged_user_id: Number,
    delete_order_access: Boolean,
    share_invoice_sms_access: Boolean,
    print_order_link: String,
    merge_order_access: Boolean,
    unmerge_order_access: Boolean,
    print_kot_link: String,
    printnode_enabled: Boolean,
  },
  mounted() {
    console.log(this.order_data);
    console.log("Order detail page loaded");
  },
  methods: {
    printnode_print(type) {
      if(type == 'POS_INVOICE')
        this.processing = true;
      if(type == 'KOT')
        this.kot_processing = true;
      
      var formData = new FormData();
      formData.append("access_token", window.settings.access_token);
      formData.append("print_type", type);
      formData.append("slack", this.slack);
      formData.append("logged_store_user_slack", this.order_basic.store.slack);

      axios
        .post(this.printnode_api_link, formData)
        .then((response) => {
          if (response.data.status_code == 200) {
            this.show_response_message(response.data.msg, "SUCCESS");
          } else {
            try {
              var error_json = JSON.parse(response.data.msg);
              this.loop_api_errors(error_json);
            } catch (err) {
              this.server_errors = response.data.msg;
            }
            this.error_class = "error";
          }
          if(type == 'POS_INVOICE')
            this.processing = false;
          if(type == 'KOT')
            this.kot_processing = false;
        })
        .catch((error) => {
          console.log(error);
        });
    },
    delete_order() {
      this.$off("submit");
      this.$off("close");
      this.show_modal = true;

      this.$on("submit", function () {
        this.processing = true;
        this.order_processing = true;

        var formData = new FormData();
        formData.append("access_token", window.settings.access_token);

        axios
          .post(this.delete_order_api_link, formData)
          .then((response) => {
            if (response.data.status_code == 200) {
              if (response.data.link != "") {
                window.location.href = response.data.link;
              } else {
                location.reload();
              }
            } else {
              this.show_modal = false;
              this.processing = false;
              try {
                var error_json = JSON.parse(response.data.msg);
                this.loop_api_errors(error_json);
              } catch (err) {
                this.server_errors = response.data.msg;
              }
              this.error_class = "error";
            }
            this.order_processing = false;
          })
          .catch((error) => {
            console.log(error);
          });
      });

      this.$on("close", function () {
        this.show_modal = false;
      });
    },

    share_invoice_as_sms() {
      this.$off("submit");
      this.$off("close");
      this.show_share_invoice_sms_modal = true;

      this.$on("submit", function () {
        this.processing = true;
        this.send_sms_processing = true;

        var formData = new FormData();
        formData.append("access_token", window.settings.access_token);

        axios
          .post(this.sms_invoice_api_link, formData)
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
                  window.location.reload();
                }, 1000);
              } else {
                setTimeout(function () {
                  window.location.reload();
                }, 1000);
              }
            } else {
              this.show_share_invoice_sms_modal = false;
              this.processing = false;
              try {
                var error_json = JSON.parse(response.data.msg);
                this.loop_api_errors(error_json);
              } catch (err) {
                this.server_errors = response.data.msg;
              }
              this.error_class = "error";
            }
            this.send_sms_processing = false;
          })
          .catch((error) => {
            console.log(error);
          });
      });

      this.$on("close", function () {
        this.show_share_invoice_sms_modal = false;
      });
    },

    load_products(keywords) {
      if (typeof keywords != "undefined") {
        var formData = new FormData();
        formData.append("access_token", window.settings.access_token);
        formData.append("keywords", keywords);

        axios
          .post("/api/load_product_for_po", formData)
          .then((response) => {
            if (response.data.status_code == 200) {
              this.product_list = response.data.data;
            }
          })
          .catch((error) => {
            console.log(error);
          });
      }
    },

    load_orders(keywords) {
      if (typeof keywords != "undefined") {
        if (keywords.length > 0) {
          var formData = new FormData();
          formData.append("access_token", window.settings.access_token);
          formData.append("keywords", keywords);
          formData.append("parent_order_slack", this.slack);

          axios
            .post("/api/get_merge_order_list", formData)
            .then((response) => {
              if (response.data.status_code == 200) {
                this.order_list = response.data.data;
              }
            })
            .catch((error) => {
              console.log(error);
            });
        }
      }
    },

    add_order_to_merge_list(item) {
      var item_found = false;
      for (var i = 0; i < this.merge_order_list.length; i++) {
        if (this.merge_order_list[i].slack == item.slack) {
          item_found = true;
        }
      }

      if (item_found == false) {
        this.merge_order_list.push(item);
      }
      this.order_list = [];
    },

    remove_order(index) {
      this.merge_order_list.splice(index, 1);
    },

    merge_order() {
      this.$off("submit");
      this.$off("close");

      this.show_merge_order_modal = true;

      this.$on("submit", function () {
        var child_orders = [];
        this.merge_order_list.forEach((item) => {
          child_orders.push(item.slack);
        });

        if (child_orders.length == 0) {
          return false;
        }

        this.processing = true;
        var formData = new FormData();

        formData.append("access_token", window.settings.access_token);
        formData.append("parent_slack", this.slack);
        formData.append("children", child_orders);

        axios
          .post(this.merge_order_api_link, formData)
          .then((response) => {
            if (response.data.status_code == 200) {
              this.show_response_message(response.data.msg, "SUCCESS");

              if (parseFloat(response.data.data.order_difference) != 0) {
                this.order_difference = response.data.data.order_difference;
                this.order_difference_type =
                  response.data.data.order_difference_exists;
                this.show_merge_order_modal = false;
                this.show_merge_response_modal = true;
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
                this.merge_order_server_errors = response.data.msg;
              }
              this.error_class = "error";
            }
          })
          .catch((error) => {
            console.log(error);
          });
      });

      this.$on("close", function () {
        this.show_merge_order_modal = false;
      });
    },

    unmerge_order() {
      this.$off("submit");
      this.$off("close");

      this.show_modal = true;

      this.$on("submit", function () {
        this.processing = true;
        var formData = new FormData();

        formData.append("access_token", window.settings.access_token);
        formData.append("order_slack", this.slack);

        axios
          .post(this.unmerge_order_api_link, formData)
          .then((response) => {
            if (response.data.status_code == 200) {
              this.show_response_message(response.data.msg, "SUCCESS");
              setTimeout(function () {
                window.location.reload();
              }, 1000);
            } else {
              this.show_modal = false;
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
      });

      this.$on("close", function () {
        this.show_modal = false;
      });
    },

    reload_window() {
      window.location.reload();
    },
  },
};
</script>
