<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Store") }}</span> {{ store.name }} ({{ store.store_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="store.status.color">{{ store.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="store_code">{{ $t("Store Code") }}</label>
                    <p>{{ store.store_code }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="name">{{ $t("Name") }}</label>
                    <p>{{ store.name }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="tax_number">{{ $t("Tax Number or GST number") }}</label>
                    <p>{{ store.tax_number }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (store.created_by == null)?'-':store.created_by['fullname']+' ('+store.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (store.updated_by == null)?'-':store.updated_by['fullname']+' ('+store.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ store.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ store.updated_at_label }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Contact Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="primary_contact">{{ $t("Primary Contact No.") }}</label>
                    <p>{{ (store.primary_contact != null)?store.primary_contact:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="secondary_contact">{{ $t("Secondary Contact No.") }}</label>
                    <p>{{  (store.secondary_contact != null)?store.secondary_contact:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="primary_email">{{ $t("Primary Email") }}</label>
                    <p>{{  (store.secondary_contact != null)?store.primary_email:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="secondary_email">{{ $t("Secondary Email") }}</label>
                    <p>{{  (store.secondary_contact != null)?store.secondary_email:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="address">{{ $t("Address") }}</label>
                    <p>{{ store.address }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="pincode">{{ $t("Pincode") }}</label>
                    <p>{{ (store.pincode != null)?store.pincode:'-' }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Restaurant Mode Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="restaurant_mode">{{ $t("Enable Restaurant Mode") }}</label>
                    <p>{{ restaurant_mode }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="restaurant_billing_type">{{ $t("Default Billing Type") }}</label>
                    <p>{{ (store.restaurant_billing_type != null)?store.restaurant_billing_type.label:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="restaurant_waiter_role">{{ $t("Role for Waiter") }}</label>
                    <p>{{ (store.restaurant_waiter_role != null)?store.restaurant_waiter_role.role_code+' - '+store.restaurant_waiter_role.label:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="restaurant_chef_role">{{ $t("Role for Chef") }}</label>
                    <p>{{ (store.restaurant_chef_role != null)?store.restaurant_chef_role.role_code+' - '+store.restaurant_chef_role.label:'-' }}</p>
                </div>
            </div>
            <div class="mb-2">
                <span class="text-subhead">{{ $t("Digital Menu Settings") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="restaurant_mode">{{ $t("Enable Digital QR Menu") }}</label>
                    <p>{{ digital_menu_enabled }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="restaurant_billing_type">{{ $t("Menu Timing") }}</label>
                    <p>{{ (store.menu_open_time != null)?store.menu_open_time+' - '+store.menu_close_time:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="restaurant_waiter_role">{{ $t("Digital Menu OTP Verification") }}</label>
                    <p>{{ enable_digital_menu_otp_verification }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="restaurant_chef_role">{{ $t("Send Digital Menu Orders To Kitchen") }}</label>
                    <p>{{ digital_menu_send_order_to_kitchen }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="restaurant_chef_role">{{ $t("Digital Menu Language") }}</label>
                    <p>{{ (store.menu_language != null)?store.menu_language.language:'-' }}</p>
                </div>
            </div>
            <hr>

            <div class="mb-3">
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Currency Information") }}</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="enable_detail_popup">{{ $t("Currency") }}</label>
                        <p>{{ (store.currency_code != null)?store.currency_code+ ' - ' +store.currency_name:'-' }}</p>
                    </div>
                </div>
            </div>
            <hr>

            <div class="mb-3">
                
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Tax Information") }}</span>
                </div>
                <div class="form-row mb-2" v-if="store.tax_code != null">
                    <div class="form-group col-md-3">
                        <label for="tax_code">{{ $t("Tax Code") }}</label>
                        <p>{{ store.tax_code.tax_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_percentage">{{ $t("Tax Percentage") }}</label>
                        <p>{{ store.tax_code.tax_percentage }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="tax_code_label">{{ $t("Tax Name") }}</label>
                        <p>{{ store.tax_code.label }}</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="row">
                        <div class="table-responsive" v-if="store.tax_code != null">
                            <table class="table display nowrap text-nowrap w-100">
                                <thead>
                                    <tr>
                                    <th scope="col">{{ $t("Tax Type") }}</th>
                                    <th scope="col">{{ $t("Tax Percentage") }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(tax_component, key, index) in store.tax_code.tax_components" v-bind:key="index">
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
                <div class="form-row mb-2" v-if="store.discount_code != null">
                    <div class="form-group col-md-3">
                        <label for="discount_code">{{ $t("Discount Code") }}</label>
                        <p>{{ store.discount_code.discount_code }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_percentage">{{ $t("Discount Percentage") }}</label>
                        <p>{{ store.discount_code.discount_percentage }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code_label">{{ $t("Discount Name") }}</label>
                        <p>{{ store.discount_code.label }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="discount_code_description">{{ $t("Discount Description") }}</label>
                        <p>{{ store.discount_code.description }}</p>
                    </div>
                </div>
                <div class="mb-3" v-else>No Discount Information</div>
            </div>
            <hr>

            <div class="mb-3">
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("POS Screen Setting") }}</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="enable_detail_popup">{{ $t("Enable Customer Detail Popup") }}</label>
                        <p>{{ enable_customer_popup }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="enable_variant_popup">{{ $t("Enable Variant Selection Popup") }}</label>
                        <p>{{ enable_variants_popup }}</p>
                    </div>
                </div>
            </div>
            <hr>
            
            <div class="mb-3">
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Invoice Settings") }}</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="invoice_type">{{ $t("Invoice Print Type") }}</label>
                        <p>{{ store.invoice_type.print_type_label }}</p>
                    </div>
                </div>
            </div>
            <hr>

            <div class="mb-3">
                <div class="mb-2">
                    <span class="text-subhead">{{ $t("Print Setting (PrintNode)") }}</span>
                </div>
                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="invoice_type">{{ $t("Enable PrintNode Printing") }}</label>
                        <p>{{ printnode_enabled }}</p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="invoice_type">{{ $t("POS Invoice Printer") }}</label>
                        <p>{{ (store.invoice_printer != null)?store.invoice_printer.printer_code+' - '+store.invoice_printer.printer_name:'-' }}
                            <span v-if="store.invoice_printer != null"><br>{{ $t("Printer ID") }}: {{ store.invoice_printer.printer_id }}</span>
                        </p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="invoice_type">{{ $t("KOT Printer") }}</label>
                        <p>{{ (store.kot_printer != null)?store.kot_printer.printer_code+' - '+store.kot_printer.printer_name:'-' }}
                            <span v-if="store.kot_printer != null"><br>{{ $t("Printer ID") }}: {{ store.kot_printer.printer_id }}</span>
                        </p>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="invoice_type">{{ $t("Other Printer") }}</label>
                        <p>{{ (store.other_printer != null)?store.other_printer.printer_code+' - '+store.other_printer.printer_name:'-' }}
                            <span v-if="store.other_printer != null"><br>{{ $t("Printer ID") }}: {{ store.other_printer.printer_id }}</span>
                        </p>
                    </div>
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
                store : this.store_data,
                restaurant_mode : (this.store_data.restaurant_mode == 1)?'Yes':'No',
                enable_customer_popup: (this.store_data.enable_customer_popup == 1)?'Yes':'No',
                enable_variants_popup: (this.store_data.enable_variants_popup == 1)?'Yes':'No',
                printnode_enabled: (this.store_data.printnode_enabled == 1)?'Yes':'No',
                digital_menu_enabled: (this.store_data.digital_menu_enabled == 1)?'Yes':'No',
                enable_digital_menu_otp_verification: (this.store_data.enable_digital_menu_otp_verification == 1)?'Yes':'No',
                digital_menu_send_order_to_kitchen: (this.store_data.digital_menu_send_order_to_kitchen == 1)?'Yes':'No',
            }
        },
        props: {
            store_data: [Array, Object]
        },
        mounted() {
            console.log('Store detail page loaded');
        },
        methods: {
           
        }
    }
</script>