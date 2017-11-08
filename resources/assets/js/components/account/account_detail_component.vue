<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ $t("Account") }}</span> {{ account.label }} ({{ account.account_code }}) </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <span v-bind:class="account.status.color">{{ account.status.label }}</span>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Basic Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="account_name">{{ $t("Account Code") }}</label>
                    <p>{{ account.account_code  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="account_name">{{ $t("Account Name") }}</label>
                    <p>{{ account.label  }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">{{ $t("Account Type") }}</label>
                    <p>{{ account.account_type_data.label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">{{ $t("Initial Balance") }}</label>
                    <p>{{ account.initial_balance }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="label">{{ $t("POS Default Account") }}</label>
                    <p>{{ pos_default }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_by">{{ $t("Created By") }}</label>
                    <p>{{ (account.created_by == null)?'-':account.created_by['fullname']+' ('+account.created_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_by">{{ $t("Updated By") }}</label>
                    <p>{{ (account.updated_by == null)?'-':account.updated_by['fullname']+' ('+account.updated_by['user_code']+')' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="created_on">{{ $t("Created On") }}</label>
                    <p>{{ account.created_at_label }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="updated_on">{{ $t("Updated On") }}</label>
                    <p>{{ account.updated_at_label }}</p>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="form-group col-md-6">
                    <label for="description">{{ $t("Description") }}</label>
                    <p>{{ (account.description)?account.description:'-' }}</p>
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
                account : this.account_data,
                pos_default : (this.account_data.pos_default == 1)?'Yes':'No'
            }
        },
        props: {
            account_data: [Array, Object]
        },
        mounted() {
            console.log('Account detail page loaded');
        },
        methods: {
           
        }
    }
</script>