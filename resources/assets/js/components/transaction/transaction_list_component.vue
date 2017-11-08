<template>
    <div>
        <div class="mb-2">
            <span class="text-subhead">{{ $t("Transactions") }}</span>
        </div>
        <div class="table-responsive mb-2" v-if="transactions.length>0">
            <table class="table table-striped display nowrap text-nowrap w-100">
                 <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ $t("Transaction Code") }}</th>
                        <th scope="col">{{ $t("Transaction Date") }}</th>
                        <th scope="col">{{ $t("Transaction Type") }}</th>
                        <th scope="col">{{ $t("Account") }}</th>
                        <th scope="col">{{ $t("Payment Method") }}</th>
                        <th scope="col" class="text-right">{{ $t("Amount") }}</th>
                        <th scope="col">{{ $t("Created On") }}</th>
                        <th scope="col">{{ $t("Created By") }}</th>
                        <th scope="col">{{ $t("Action") }}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(transaction, key, index) in transactions" v-bind:value="transactions.slack" v-bind:key="index">
                         <th scope="col">{{ key+1 }}</th>
                         <td>{{ transaction.transaction_code }}</td>
                        <td>{{ transaction.transaction_date }}</td>
                        <td>{{ transaction.transaction_type_data.label }}</td>
                        <td>{{ transaction.account.label }}</td>
                        <td>{{ transaction.payment_method }}</td>
                        <td class="text-right">{{ transaction.amount }}</td>
                        <td>{{ transaction.created_at_label }}</td>
                        <td>{{ (transaction.created_by != null)?transaction.created_by.fullname:'-' }}</td>
                        <td>
                            <div class="dropdown" v-show="transaction.detail_link != ''">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle actions-dropdown-btn" type="button" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-h actions-dropdown"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown">
                                    <a v-bind:href="transaction.detail_link" class="dropdown-item">{{ $t("View") }}</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p>No transactions found</p>
        </div>
    </div>
</template>  

<script>
    'use strict';
    
    export default {
        data(){
            return{
                transactions : this.transaction_list,
            }
        },
        props: {
            transaction_list: [Array, Object]
        },
        mounted() {
            console.log('Transaction listing component loaded');
        },
        methods: {
           
        }
    }
</script>