<template>
  <div class="row">
    <div class="col-md-12">
      <div class="d-flex flex-wrap mb-4">
        <div class="mr-auto">
          <div class="d-flex">
            <div>
              <span class="text-title" v-if="category === 'Food'">{{ $t("Kitchen View") }}
                <span class="text-muted" v-show="kot_list_filtered.length > 0">{{ kot_list_filtered.length }} {{
                    $t("Orders")
                }}</span>
                <span v-if="processing == true"><i class="fa fa-circle-notch fa-spin"></i> Loading..</span></span>
              <span class="text-title" v-if="category === 'Drink'">{{ $t("Bar View") }}
                <span class="text-muted" v-show="kot_list_filtered.length > 0">{{ kot_list_filtered.length }} {{
                    $t("Orders")
                }}</span>
                <span v-if="processing == true"><i class="fa fa-circle-notch fa-spin"></i> Loading..</span></span>
              <span class="text-title" v-if="category === 'Kitchen1'">{{ $t("Kitchen1 View") }}
                <span class="text-muted" v-show="kot_list_filtered.length > 0">{{ kot_list_filtered.length }} {{
                    $t("Orders")
                }}</span>
                <span v-if="processing == true"><i class="fa fa-circle-notch fa-spin"></i> Loading..</span></span>
              <span class="text-title" v-if="category === 'Kitchen2'">{{ $t("Kitchen2 View") }}
                <span class="text-muted" v-show="kot_list_filtered.length > 0">{{ kot_list_filtered.length }} {{
                    $t("Orders")
                }}</span>
                <span v-if="processing == true"><i class="fa fa-circle-notch fa-spin"></i> Loading..</span></span>
              <span class="text-title" v-if="category === 'Kitchen3'">{{ $t("Kitchen3 View") }}
                <span class="text-muted" v-show="kot_list_filtered.length > 0">{{ kot_list_filtered.length }} {{
                    $t("Orders")
                }}</span>
                <span v-if="processing == true"><i class="fa fa-circle-notch fa-spin"></i> Loading..</span></span>
            </div>
          </div>
        </div>
        <div class="">
          <div class="d-flex">
            <div class="custom-control custom-switch ml-3 mr-3 mt-1">
              <input type="checkbox" class="custom-control-input" id="auto_load_switch" v-model="auto_refresh" />
              <label class="custom-control-label" for="auto_load_switch">{{
                  $t("Auto Refresh Every 1 Min")
              }}</label>
            </div>

            <button class="btn btn-light" v-on:click="load_kot_list">
              {{ $t("Refresh") }}
            </button>
          </div>
        </div>
      </div>

      <p v-if="server_errors" v-html="server_errors" v-bind:class="[error_class]"></p>

      <div class="d-flex flex-row mb-3">
        <div class="col-md-12">
          <div class="d-flex justify-content-center mb-3" v-if="list_populated == true">
            <input type="text" name="filter_order_no" v-model="filter_order_no"
              class="form-control form-control-lg col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12"
              :placeholder="$t('Search by Order No / Table')" autocomplete="off" />
          </div>

          <div class="row flex-nowrap kitchen-wrapper" v-bind:class="{ 'bg-light': list_populated }">
            <div v-for="(kot_list_items, kot_list_key) in kot_list_filtered" v-bind:key="'table' + kot_list_key"
              class="h-100 bg-white col-xl-2 col-lg-2 col-md-3 col-sm-4 col-xs-6 d-flex flex-column mx-2 my-4 p-0 py-2 rounded-lg">
              <div v-for="(kot_list_item, kot_list_item_key) in kot_list_items" v-bind:key="'order' + kot_list_item_key"
                class="bg-white border-0 kitchen-card mb-1 ml-4 mr-4 mt-4 p-0">
                <div>
                  <div class="p-0" v-show="kot_list_item.table != '' && kot_list_item_key == 0">
                    <div class="bg-info d-flex justify-content-center mb-3 mb-4 p-3 rounded">
                      <h4 class="mb-0 text-white">
                        {{ kot_list_item.table.toUpperCase() }}
                      </h4>
                    </div>
                  </div>

                  <div class="border-bottom border-top p-0">
                    <div class="d-flex justify-content-between p-2 text-center">
                      <span class="text-subtitle text-black-50">
                        Order # {{ kot_list_item.order_number }}
                      </span>
                      <span class="mx-1 text-danger">{{ kot_list_item.additional_note }}</span>
                      <span>
                        <span class="timer-dot"></span>
                        {{ kot_list_item.duration }} Minute
                      </span>
                    </div>
                  </div>
                  <div class="p-0 border-bottom">
                    <div class="d-flex flex-column text-center p-3">
                      <div class="" v-if="kot_list_item.order_type_data != null">
                        <div class="d-flex justify-content-between">
                          <i v-show="kot_list_item.order_type_data != null"
                            v-bind:class="kot_list_item.order_type_data.icon" class="align-self-center"></i>
                          <span class="mt-1">{{ kot_list_item.order_type }}</span>
                        </div>
                      </div>
                      <div v-else></div>

                      <div v-if="change_kitchen_order_status == true">
                        <div class="" v-show="
                          kot_list_item.kitchen_status_processing == true
                        ">
                          <i class="fa fa-circle-notch fa-spin"></i>&nbsp;Changing Status
                        </div>
                        <div class="dropdown" v-show="
                          kot_list_item.kitchen_status_processing == null ||
                          kot_list_item.kitchen_status_processing == false
                        ">
                          <button class="btn btn-link text-decoration-none dropdown-toggle text-bold p-0"
                            id="user_menu_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span v-if="kot_list_item.kitchen_status != null">
                              <span v-bind:class="
                                kot_list_item.kitchen_status.color
                              ">{{ kot_list_item.kitchen_status.label }}</span>
                            </span>
                            <span v-else>Update Status</span>
                          </button>
                          <div class="dropdown-menu dropdown-menu-right mt-2" aria-labelledby="user_menu_dropdown">
                            <button class="dropdown-item text-capitalize" type="button"
                              v-for="(item, key) in kitchen_statuses" v-bind:key="'kitchen_status_constant' + key"
                              v-on:click="
                                update_kitchen_status(
                                  kot_list_item.slack,
                                  item.value_constant
                                )
                              ">
                              {{ item.label }}
                            </button>
                            <!-- <button class="dropdown-item" type="button" v-on:click="merge_order">
                              Merge
                            </button> -->
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="p-0">
                    <div class="d-flex flex-wrap pl-3 py-1 pr-3">
                      <span class="mr-auto text-subtitle text-black-50">Items</span>
                      <span class="text-subtitle text-black-50">Qty</span>
                    </div>
                    <div class=""  v-for="(item_list_value, key) in kot_list_item.products" v-bind:key="'_' + key">
                      <div v-if="
                        item_list_value.product.category.type === category
                      " class="mb-2 px-3 border-bottom d-flex justify-content-between">
                        <span v-bind:class="item_list_value.parent_order_product == false
                        ? 'invisible'
                        : ''
                    " v-on:click="
                          mark_prepared(
                            item_list_value.slack,
                            kot_list_item.slack,
                            key
                          )
                        ">
                          <span v-show="item_list_value.item_status_loading == true">
                            <i class="fa fa-circle-notch fa-spin kitchen-item-checker"></i>
                          </span>
                          <span v-show="
                            item_list_value.item_status_loading == null ||
                            item_list_value.item_status_loading == false
                          ">
                            <i class="kitchen-item-checker cursor" v-bind:class="{
                              'text-success fas fa-check-circle':
                                item_list_value.is_ready_to_serve == 1,
                              'text-muted far fa-check-circle':
                                item_list_value.is_ready_to_serve == 0,
                            }"></i>
                          </span>
                        </span>
                        <span class="text-break kitchen-item-title">
                          <span v-if="item_list_value.parent_order_product == false"
                            class="label blue-label addon-label">Add-on</span>
                          <div :style="[(item_list_value.name.charAt(0) == '*') ? {'color': 'green'} : {'color':'black'}]">{{ item_list_value.name }}</div>
                        </span>
                        <span class="">{{
                            item_list_value.quantity
                        }}</span>
                      </div>
                    </div>
                  </div>

                  <div class="p-0" v-if="pos_order_edit == true">
                    <div class="d-flex justify-content-around p-2 text-center" v-if="kot_list_item.status.constant != 'CLOSED'">
                      <a v-bind:href="kot_list_item.edit_link" class="text-bold text-decoration-none" target="_blank"><i
                          class="fas fa-cash-register mr-2"></i>
                        {{ $t("Bill or Edit This Order") }}</a>
                    </div>
                    <div class="d-flex justify-content-center p-2" v-if="
                      kot_list_item.kitchen_status != null &&
                      kot_list_item.kitchen_status.constant == 'ORDER_READY'
                    ">
                      <span class="text-danger text-bold cursor center" v-on:click="dismiss_order(kot_list_item.slack)"><i
                          class="far fa-times-circle"></i>
                        {{ $t("Dismiss This Order From Kitchen") }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div v-if="kot_list_orin.length == 0 && processing == false">
              <span>Zero orders in queue!</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
      <template v-slot:modal-header> Confirm </template>
      <template v-slot:modal-body>
        Are you sure you want to dismiss this order from kitchen?
      </template>
      <template v-slot:modal-footer>
        <button type="button" class="btn btn-light" @click="$emit('close')">
          Cancel
        </button>
        <button type="button" class="btn btn-primary" @click="$emit('submit')" v-bind:disabled="processing == true">
          <i class="fa fa-circle-notch fa-spin" v-if="processing == true"></i>
          Continue
        </button>
      </template>
    </modalcomponent>
  </div>
</template>

<script>
"use strict";

import moment from "moment";

export default {
  data() {
    return {
      server_errors: "",
      error_class: "",
      processing: false,

      kot_list: [],
      kot_list_orin: [],
      merge_idx: [],

      total_orders: 0,
      list_populated: false,

      filter_order_no: "",

      auto_refresh: true,

      addon_label: "+ ",

      show_modal: false,

      dismiss_order_api_link: "/api/toggle_order_dismissed_from_screen_status",
      merge_order_api_link: "/api/merge_order",
    };
  },
  props: {
    kitchen_statuses: [Array, Object],
    change_kitchen_order_status: Boolean,
    pos_order_edit: Boolean,
    store_slack: String,
    category: String,
  },
  computed: {
    kot_list_filtered() {
      let kot_list = this.kot_list_orin;
      let groupArr = Array.from(new Set(kot_list.map((item) => item.table)));

      let GroupLists = [];
      groupArr.forEach((item) => {
        let groupLists = kot_list.filter((kl) => kl.table == item);
        GroupLists.push(groupLists);
      });

      kot_list = GroupLists;
      if (this.filter_order_no) {
        return kot_list.filter((kot_list_item) => {
          return this.filter_order_no
            .toLowerCase()
            .split(" ")
            .every(
              (v) =>
                kot_list_item.order_number.toLowerCase().includes(v) ||
                kot_list_item.customer_phone.toLowerCase().includes(v) ||
                kot_list_item.customer_email.toLowerCase().includes(v) ||
                kot_list_item.table.toLowerCase().includes(v)
            );
        });
      } else {
        return kot_list;
      }
    },
  },
  mounted() {
    console.log("Kitchen order ticket page loaded");
    this.tick_update_duration_for_products();
    this.tick_update_kot_list();
  },
  created() {
    this.load_kot_list();
    console.log(`new-order.${this.store_slack}`);
  },
  methods: {
    mark_merge(event, id) {
      if (event.target.checked) this.merge_idx.push(id);
      else {
        let _index = this.merge_idx.findIndex((item) => item == id);
        this.merge_idx.splice(_index, 1);
      }
      console.log("data==> ", this.merge_idx);
    },
    init() {
      this.merge_idx = [];
    },
    merge_order(index, key) {
      var parent_order = "";
      var child_orders = [];
      this.merge_idx.forEach((item, key) => {
        if (key > 0) child_orders.push(item);
        else parent_order = item;
      });

      if (child_orders.length == 0) {
        return false;
      }

      this.processing = true;
      var formData = new FormData();

      formData.append("access_token", window.settings.access_token);
      formData.append("parent_slack", parent_order);
      formData.append("children", child_orders);

      axios
        .post(this.merge_order_api_link, formData)
        .then((response) => {
          if (response.data.status_code == 200) {
            this.show_response_message(response.data.msg, "SUCCESS");

            if (parseFloat(response.data.data.order_difference) != 0) {
              // this.order_difference = response.data.data.order_difference;
              // this.order_difference_type = response.data.data.order_difference_exists;
              console.log(response.data.data.order_difference);
              // this.show_merge_order_modal = false;
              // this.show_merge_response_modal = true;
            } else {
              setTimeout(function () {
                window.location.reload();
              }, 1000);
            }
          } else {
            this.processing = false;
            try {
              var error_json = JSON.parse(response.data.msg);
              // this.loop_api_errors(error_json);
              console.log(error_json);
            } catch (err) {
              // this.merge_order_server_errors = response.data.msg;
              console.log(response.data.msg);
            }
            // this.error_class = 'error';
          }
        })
        .catch((error) => {
          console.log(error);
        });

      // });

      // this.$on("close",function () {
      //     this.show_merge_order_modal = false;
      // });
    },
    load_kot_list() {
      this.processing = true;
      this.kot_list = [];
      this.kot_list_orin = [];

      var formData = new FormData();
      formData.append("access_token", window.settings.access_token);
      axios
        .post("/api/get_in_kitchen_order_list", formData)
        .then((response) => {
          if (response.data.status_code == 200) {
            this.init();
            console.log(response.data.data);
            for (let i = 0; i < response.data.data.length; i++) {
              if(response.data.data[i].status.constant == "CLOSED")
                continue;
              // response.data.data[i].products.sort((a, b) =>
              //   a.name > b.name ? 1 : -1
              // );

              for (let j = 0; j < response.data.data[i].products.length; j++) {
                if (
                  response.data.data[i].products[j].product.category.type ===
                  this.category
                ) {
                  this.kot_list_orin.push(response.data.data[i]);
                  break;
                }
              }
            }

            this.kot_list_orin.sort((a, b) => (a.table > b.table ? 1 : -1));
            this.update_duration_for_products();
            this.processing = false;
            this.list_populated = this.kot_list_orin.length > 0 ? true : false;
            this.total_orders = this.kot_list_orin.length;
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
    },

    update_kitchen_status(order_slack, kitchen_status) {
      let item_key = this.kot_list_orin.findIndex(
        (item) => item.slack == order_slack
      );
      this.$set(
        this.kot_list_orin[item_key],
        "kitchen_status_processing",
        true
      );
      console.log("item_key:", item_key);
      console.log("order_slack", order_slack);
      console.log("kitchen_status", kitchen_status);

      var formData = new FormData();
      formData.append("access_token", window.settings.access_token);
      formData.append("order_slack", order_slack);
      formData.append("kitchen_status", kitchen_status);

      axios
        .post("/api/update_kitchen_order_status", formData)
        .then((response) => {
          if (response.data.status_code == 200) {
            this.$set(
              this.kot_list_orin[item_key],
              "kitchen_status_processing",
              false
            );
            this.$set(
              this.kot_list_orin,
              item_key,
              response.data.data.order_data
            );
          } else {
            this.$set(
              this.kot_list_orin[item_key],
              "kitchen_status_processing",
              false
            );
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
      for (var i = 0; i < this.kot_list_orin.length; i++) {
        var duration = this.calculate_duration(
          this.kot_list_orin[i].create_at_utc
        );
        this.$set(this.kot_list_orin[i], "duration", duration);
      }
    },

    mark_prepared(slack, order_slack, item_index) {
      let index = this.kot_list_orin.findIndex(
        (item) => item.slack == order_slack
      );

      this.$set(
        this.kot_list_orin[index]["products"][item_index],
        "item_status_loading",
        true
      );

      var formData = new FormData();
      formData.append("access_token", window.settings.access_token);
      formData.append("item_slack", slack);

      axios
        .post("/api/update_kitchen_item_status", formData)
        .then((response) => {
          if (response.data.status_code == 200) {
            this.$set(this.kot_list_orin, index, response.data.data.order_data);
            this.$set(
              this.kot_list_orin[index]["products"][item_index],
              "item_status_loading",
              false
            );
            console.log(
              "Axios successfully",
              this.kot_list_orin[index]["products"][item_index]
            );
          } else {
            this.$set(
              this.kot_list_orin[index]["products"][item_index],
              "item_status_loading",
              false
            );
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
    },

    dismiss_order(slack) {
      this.$off("submit");
      this.$off("close");

      this.show_modal = true;
      this.$on("submit", function () {
        this.processing = true;
        var formData = new FormData();

        formData.append("access_token", window.settings.access_token);
        formData.append("order_slack", slack);
        formData.append("screen", "KITCHEN_SCREEN");

        axios
          .post(this.dismiss_order_api_link, formData)
          .then((response) => {
            if (response.data.status_code == 200) {
              this.show_response_message(response.data.msg, "SUCCESS");

              this.load_kot_list();

              this.show_modal = false;
              this.processing = false;
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

    tick_update_duration_for_products() {
      setInterval(() => {
        this.update_duration_for_products();
      }, 1000);
    },

    tick_update_kot_list() {
      setInterval(() => {
        if (this.auto_refresh == true) {
          this.load_kot_list();
        }
      }, 60000);
    },
  },
};
</script>
