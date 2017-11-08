<template>
    <div class="row">
        <div class="col-md-12">

            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="table_slack == ''">{{ $t("Add Table") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Table") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i
                                class='fa fa-circle-notch fa-spin' v-if="processing == true"></i> {{ $t("Save")
                                }}</button>
                    </div>
                </div>

                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="table_number">{{ $t("Table Name or Number") }}</label>
                        <input type="text" name="table_number" v-model="table_number" v-validate="'required|max:250'"
                            class="form-control form-control-custom" :placeholder="$t('Please enter table name')"
                            autocomplete="off">
                        <span v-bind:class="{ 'error': errors.has('table_number') }">{{ errors.first('table_number')
                        }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="no_of_occupants">{{ $t("No. of Occupants") }}</label>
                        <input type="number" name='no_of_occupants' v-model="no_of_occupants"
                            v-validate="'required|numeric'" class="form-control form-control-custom"
                            :placeholder="$t('Please enter no of occupants')" autocomplete="off" step="1" min="0">
                        <span v-bind:class="{ 'error': errors.has('no_of_occupants') }">{{
                                errors.first('no_of_occupants')
                        }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="status">{{ $t("Status") }}</label>
                        <select name="status" v-model="status" v-validate="'required|numeric'"
                            class="form-control form-control-custom custom-select">
                            <option value="">Choose Status..</option>
                            <option v-for="(status, index) in statuses" v-bind:value="status.value" v-bind:key="index">
                                {{ status.label }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error': errors.has('status') }">{{ errors.first('status') }}</span>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="area">Area</label>
                        <select name="area" v-model="restoarea_id" v-validate="'required|numeric'"
                            class="form-control form-control-custom custom-select">
                            <option value="0">Choose Area..</option>
                            <option v-for="(restoarea, index) in restoareas" v-bind:value="restoarea.id"
                                v-bind:key="index" :selected="restoarea.id == restoarea_id">
                                {{ restoarea.name }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error': errors.has('status') }">{{ errors.first('status') }}</span>
                    </div>
                </div>

                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Default Waiter For Table") }}</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="waiter">{{ $t("Waiter") }}</label>
                        <select name="waiter" v-model="waiter" v-validate="''"
                            class="form-control form-control-custom custom-select">
                            <option value="">Choose Default Waiter..</option>
                            <option v-for="(waiter_item, index) in waiter_list" v-bind:value="waiter_item.slack"
                                v-bind:key="index">
                                {{ waiter_item.user_code }} - {{ waiter_item.fullname }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error': errors.has('waiter') }">{{ errors.first('waiter') }}</span>
                    </div>
                </div>

                <div v-if="table_slack != ''">
                    <div class="mb-2">
                        <span class="text-subhead">{{ $t("QR Menu With Table Information") }}</span>
                    </div>
                    <qrcomponent :menu_link="menu_link"></qrcomponent>
                </div>

            </form>

        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                {{ $t("Are you sure you want to proceed?") }}
            </template>
            <template v-slot:modal-footer>
                <button type="button" class="btn btn-light" @click="$emit('close')">Cancel</button>
                <button type="button" class="btn btn-primary" @click="$emit('submit')"
                    v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'
                        v-if="processing == true"></i> Continue</button>
            </template>
        </modalcomponent>

    </div>
</template>

<script>
'use strict';

export default {
    data() {
        return {
            server_errors: '',
            error_class: '',
            processing: false,
            modal: false,
            show_modal: false,
            api_link: (this.table_data == null) ? '/api/add_table' : '/api/update_table/' + this.table_data.slack,

            table_slack: (this.table_data == null) ? '' : this.table_data.slack,
            table_number: (this.table_data == null) ? '' : this.table_data.table_number,
            restoarea_id: (this.table_data == null) ? 0 : this.table_data.restoarea_id,
            no_of_occupants: (this.table_data == null) ? '' : this.table_data.no_of_occupants,
            status: (this.table_data == null) ? '' : (this.table_data.status == null) ? '' : this.table_data.status.value,
            waiter: (this.table_data == null) ? '' : (this.table_data.waiter == null) ? '' : this.table_data.waiter.slack,
        }
    },
    props: {
        statuses: Array,
        table_data: [Array, Object],
        waiter_list: [Array, Object],
        menu_link: String,
        restoareas: [Array, Object]
    },
    mounted() {
        console.log('Add table page loaded');
    },
    methods: {
        submit_form() {

            this.$off("submit");
            this.$off("close");

            this.$validator.validateAll().then((result) => {
                if (result) {
                    this.show_modal = true;
                    this.$on("submit", function () {

                        this.processing = true;
                        var formData = new FormData();

                        formData.append("access_token", window.settings.access_token);
                        formData.append("table_number", (this.table_number == null) ? '' : this.table_number);
                        formData.append("no_of_occupants", (this.no_of_occupants == null) ? '' : this.no_of_occupants);
                        formData.append("waiter", (this.waiter == null) ? '' : this.waiter);
                        formData.append("status", (this.status == null) ? '' : this.status);
                        formData.append("restoarea_id", this.restoarea_id);

                        axios.post(this.api_link, formData).then((response) => {
                            if (response.data.status_code == 200) {
                                this.show_response_message(response.data.msg, 'SUCCESS');

                                setTimeout(function () {
                                    location.reload();
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
                                this.error_class = 'error';
                            }
                        })
                            .catch((error) => {
                                console.log(error);
                            });
                    });

                    this.$on("close", function () {
                        this.show_modal = false;
                    });
                }
            });
        }
    }
}
</script>