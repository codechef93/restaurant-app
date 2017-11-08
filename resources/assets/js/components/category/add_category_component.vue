<template>
    <div class="row">
        <div class="col-md-12">

            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="category_slack == ''">{{ $t("Add Category") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Category") }}</span>
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
                        <label for="category_name">{{ $t("Category Name") }}</label>
                        <input type="text" name="category_name" v-model="category_name" v-validate="'required|max:250'"
                            class="form-control form-control-custom" :placeholder="$t('Please enter category name')"
                            autocomplete="off">
                        <span v-bind:class="{ 'error': errors.has('category_name') }">{{ errors.first('category_name')
                        }}</span>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="display_on_pos_screen">{{ $t("Show on POS Screen") }}</label>
                        <select name="display_on_pos_screen" v-model="display_on_pos_screen"
                            v-validate="'required|numeric'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Show on POS Screen..</option>
                            <option v-for="(display_on_pos_screen_option, index) in display_on_pos_screen_options"
                                v-bind:value="index" v-bind:key="index">
                                {{ display_on_pos_screen_option }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error': errors.has('display_on_pos_screen') }">{{
                                errors.first('display_on_pos_screen')
                        }}</span>
                    </div>
                    <div class="form-group col-md-2">
                        <label for="display_on_qr_menu">{{ $t("Show on QR Menu") }}</label>
                        <select name="display_on_qr_menu" v-model="display_on_qr_menu" v-validate="'required|numeric'"
                            class="form-control form-control-custom custom-select">
                            <option value="">Choose Show on QR Menu..</option>
                            <option v-for="(display_on_qr_menu_option, index) in display_on_qr_menu_options"
                                v-bind:value="index" v-bind:key="index">
                                {{ display_on_qr_menu_option }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error': errors.has('display_on_qr_menu') }">{{
                                errors.first('display_on_qr_menu')
                        }}</span>
                    </div>
                    <div class="form-group col-md-2">
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
                    <div class="form-group col-md-2">
                        <label for="type">{{ $t("Type") }}</label>
                        <select name="type" v-model="type" v-validate="'required'"
                            class="form-control form-control-custom custom-select">
                            <!-- <option value="">Choose Type..</option> -->
                            <option v-for="(type, index) in types" v-bind:value="type" v-bind:key="index">
                                {{ type }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error': errors.has('status') }">{{ errors.first('status') }}</span>
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="description">{{ $t("Description") }}</label>
                        <textarea name="description" v-model="description" v-validate="'max:65535'"
                            class="form-control form-control-custom" rows="5"
                            :placeholder="$t('Enter description')"></textarea>
                        <span v-bind:class="{ 'error': errors.has('description') }">{{ errors.first('description')
                        }}</span>
                    </div>
                </div>

            </form>

        </div>

        <modalcomponent v-if="show_modal" v-on:close="show_modal = false">
            <template v-slot:modal-header>
                {{ $t("Confirm") }}
            </template>
            <template v-slot:modal-body>
                <p v-if="status == 0">If category is inactive all the products under this catgeory will get affected.
                </p>
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
            api_link: (this.category_data == null) ? '/api/add_category' : '/api/update_category/' + this.category_data.slack,

            display_on_pos_screen_options: {
                '1': 'Yes',
                '0': 'No'
            },

            display_on_qr_menu_options: {
                '1': 'Yes',
                '0': 'No'
            },

            category_slack: (this.category_data == null) ? '' : this.category_data.slack,
            category_name: (this.category_data == null) ? '' : this.category_data.label,
            description: (this.category_data == null) ? '' : this.category_data.description,
            display_on_pos_screen: (this.category_data == null) ? '' : (this.category_data.display_on_pos_screen == null) ? '' : this.category_data.display_on_pos_screen,
            display_on_qr_menu: (this.category_data == null) ? '' : (this.category_data.display_on_qr_menu == null) ? '' : this.category_data.display_on_qr_menu,
            status: (this.category_data == null) ? '' : (this.category_data.status == null) ? '' : this.category_data.status.value,
            type: (this.category_data == null)?'' : this.category_data.type,
            types: [
                'Food', 'Drink', 'Kitchen1', 'Kitchen2', 'Kitchen3'
            ],
        }
    },
    props: {
        statuses: Array,
        category_data: [Array, Object]
    },
    mounted() {
        console.log('Add category page loaded');
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
                        formData.append("category_name", (this.category_name == null) ? '' : this.category_name);
                        formData.append("description", (this.description == null) ? '' : this.description);
                        formData.append("display_on_qr_menu", (this.display_on_qr_menu == null) ? '' : this.display_on_qr_menu);
                        formData.append("display_on_pos_screen", (this.display_on_pos_screen == null) ? '' : this.display_on_pos_screen);
                        formData.append("status", (this.status == null) ? '' : this.status);
                        formData.append("type", this.type);

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