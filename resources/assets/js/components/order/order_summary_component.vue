<template>
  <div class="">
    <div class="col-md-12 pl-0 pr-0">
      <div class="d-flex flex-wrap p-4 border-bottom">
        <div class="d-flex">
          <div class="mr-4 pt-1 d-none d-sm-block">
            <span v-bind:class="order_basic.status.color" class="mr-2">{{
              order_basic.status.label
            }}</span>
            <span
              v-if="
                order_basic.restaurant_mode == 1 &&
                order_basic.kitchen_status != null
              "
              v-bind:class="order_basic.kitchen_status.color"
              >{{ order_basic.kitchen_status.label }}</span
            >
          </div>
          <div class="mr-4">
            <span class="text-title">
              {{ $t("Order") }} #{{ order_basic.order_number }}
            </span>
          </div>
          <div class="mr-3">
            <span class="text-title text-primary"
              >{{ order_basic.currency_code }}
              {{ order_basic.total_order_amount }}</span
            >
          </div>
        </div>
        <div class="ml-auto">
          <button
            class="btn btn-outline-primary ml-3"
            v-if="order_detail_access == true && printnode_enabled == true"
            v-on:click="printnode_print('POS_INVOICE')"
            v-bind:disabled="processing == true"
          >
            <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
            {{ $t("Print Invoice") }}
          </button>

          <button
            class="btn btn-outline-primary ml-3"
            v-if="
              order_detail_access == true &&
              print_kot_link != '' &&
              printnode_enabled == true
            "
            v-on:click="printnode_print('KOT')"
            v-bind:disabled="kot_processing == true"
          >
            <i
              class="fa fa-circle-notch fa-spin"
              v-if="kot_processing == true"
            ></i>
            {{ $t("Print KOT") }}
          </button>

          <a
            :href="edit_order_link"
            class="btn btn-outline-primary ml-3"
            v-if="edit_order_access == true && order_status != 'CLOSED'"
            >{{ $t("Edit") }}</a
          >

          <a
            :href="order_detail_link"
            class="btn btn-outline-primary ml-3"
            v-if="order_detail_access == true"
            >{{ $t("Order Details") }}</a
          >

          <div
            class="dropdown d-inline ml-3"
            v-if="order_detail_access == true"
          >
            <button
              class="btn btn-outline-primary text-decoration-none dropdown-toggle"
              id="pdf_download"
              data-toggle="dropdown"
              aria-haspopup="true"
              aria-expanded="false"
            >
              <span>{{ $t("PDF") }}</span>
            </button>
            <div
              class="dropdown-menu dropdown-menu-right"
              aria-labelledby="pdf_download"
            >
              <a
                :href="print_order_link"
                class="dropdown-item"
                type="button"
                v-if="order_detail_access == true"
                >{{ $t("Invoice") }}</a
              >
              <a
                :href="print_kot_link"
                class="dropdown-item"
                type="button"
                v-if="order_detail_access == true && print_kot_link != ''"
                >{{ $t("KOT") }}</a
              >
            </div>
          </div>

          <a
            :href="new_order_link"
            class="btn btn-lg btn-primary ml-3"
            v-if="new_order_access == true"
            >+ {{ $t("New Order") }}</a
          >
        </div>
      </div>

      <div class="d-flex mb-4">
        <iframe
          class="pdf_iframe"
          title="Order Summary Print PDF"
          :src="pdf_print"
        ></iframe>
      </div>
    </div>

    <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
      <template v-slot:modal-header>
        {{ $t("Something went wrong!") }}
      </template>
      <template v-slot:modal-body>
        <p v-html="server_errors" v-bind:class="[error_class]"></p>
      </template>
      <template v-slot:modal-footer>
        <button type="button" class="btn btn-light" @click="$emit('close')">
          Ok
        </button>
      </template>
    </modalcomponent>

    <notifications
      group="notification_bar"
      classes="n-light"
      :duration="55000"
      :width="500"
      position="top right"
    />
  </div>
</template>

<script>
"use strict";

export default {
  data() {
    return {
      server_errors: "",
      error_class: "",
      kot_processing: false,
      processing: false,
      show_modal: false,

      slack: this.order_data.slack,
      order_basic: this.order_data,

      order_status: this.order_data.status.constant,

      printnode_api_link: "/api/print_with_printnode",
    };
  },
  props: {
    order_data: [Array, Object],
    pdf_print: String,
    new_order_link: String,
    order_detail_link: String,
    order_detail_access: Boolean,
    new_order_access: Boolean,
    print_order_link: String,
    print_kot_link: String,
    edit_order_access: Boolean,
    edit_order_link: String,
    printnode_enabled: Boolean,
  },
  mounted() {
    console.log("Order summary page loaded");
  },
  methods: {
    printnode_print(type) {
      type == "POS_INVOICE"
        ? (this.processing = true)
        : (this.kot_processing = true);

      var formData = new FormData();

      formData.append("access_token", window.settings.access_token);
      formData.append("print_type", type);
      formData.append("slack", this.slack);

      axios
        .post(this.printnode_api_link, formData)
        .then((response) => {
          if (response.data.status_code == 200) {
            this.show_response_message(
              response.data.msg + " (Job ID: " + response.data.data + ")",
              "SUCCESS"
            );
            type == "POS_INVOICE"
              ? (this.processing = false)
              : (this.kot_processing = false);
          } else {
            this.show_modal = true;
            type == "POS_INVOICE"
              ? (this.processing = false)
              : (this.kot_processing = false);
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

      this.$on("close", function () {
        this.show_modal = false;
      });
    },
  },
};
</script>
