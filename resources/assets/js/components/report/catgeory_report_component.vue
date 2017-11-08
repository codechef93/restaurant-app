<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> {{ $t("Category Report") }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <date-picker type="month" :lang='date.lang' :format="date.format" v-model="month" @change="month_change" input-class="form-control bg-white"></date-picker>
                </div>
            </div>

            <div class="table-responsive">
                <table id="listing-table" class="table display nowrap w-100">
                    <thead>
                        <tr>
                            <th>{{ $t("Category Name") }}</th>
                            <th>{{ $t("Category Code") }}</th>
                            <th>{{ $t("Sold Quantity") }}</th>
                            <th>{{ $t("Purchase Amount Total (Sold)") }} ({{ store.currency_code }})</th>
                            <th>{{ $t("Sold Amount Total") }} ({{ store.currency_code }})</th>
                            <th>{{ $t("Profit/Loss") }} ({{ store.currency_code }})</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
<script>
    'use strict';
    
    import DatePicker from 'vue2-datepicker';
    import moment from "moment";
    import {event_bus} from '../../event_bus.js';
    
    export default {
        data(){
            return{
                date:{
                    lang : 'en',
                    format : "YYYY-MM",
                },

                month : new Date(moment().format("YYYY-MM")),
                month_formatted : new Date(moment().format("YYYY-MM")),
            }
        },
        props: {
            store: [Array, Object],
        },
        mounted() {
            console.log('Category report page loaded');
            this.fire_request();
        },
        methods: {

            convert_date_format(date){
                return (date != '')?moment(date).format("YYYY-MM"):'';
            },

            month_change(){
                this.month_formatted = this.convert_date_format(this.month);
                this.fire_request();
            },

            fire_request(){
                event = new CustomEvent("month", { "detail": this.month_formatted });
                document.dispatchEvent(event);
            },
        }
    }
</script>