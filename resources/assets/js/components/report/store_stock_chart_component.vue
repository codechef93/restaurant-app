<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> {{ $t("Store Stock Chart") }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    
                </div>
            </div>
            <div class="table-responsive mb-2">
                <table class="table table-striped display nowrap text-nowrap w-100">
                    <thead>
                        <tr>
                        <th scope="col" class="text-right">{{ $t("Total Quantity") }}</th>
                        <th scope="col" class="text-right">{{ $t("Total Purchase Cost") }}  ({{ store.currency_code }})</th>
                        <th scope="col" class="text-right">{{ $t("Total Sale Price") }}  ({{ store.currency_code }})</th>
                        <th scope="col" class="text-right">{{ $t("Estimated Profit") }}  ({{ store.currency_code }})</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-right">{{ total_quantity }}</td>
                            <td class="text-right">{{ total_purchase_cost }}</td>
                            <td class="text-right">{{ total_sale_value }}</td>
                            <td class="text-right">{{ total_profit_estimate }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="d-flex flex-wrap">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 p-0">
                    <div class="">
                        <div class='chart_container'> 
                            <canvas id="store_stock_chart" class=""></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    'use strict';
    
    import DatePicker from 'vue2-datepicker';
    import Chart from 'chart.js';
    import moment from "moment";
    
    export default {
        data(){
            return{

                profit_fill_color: '#a0eec0',
                cost_fill_color: '#d33f49',
                sale_price_fill_color: '#dda448',

                chart_config : {
                    type: 'doughnut',
                    data: {
                        datasets: [],
                        labels: [
                            'Total Purchase Cost',
                            'Total Sale Price',
                            'Profit Estimate',
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'bottom',
                        },
                        title: {
                            display: false,
                            text: ''
                        },
                        tooltips: {
                            enabled: true
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        },
                        layout: {
                            padding: {
                                top: 40,
                                bottom: 40,
                                left: 40,
                                right: 40
                            }
                        },
                    }
                },

                total_quantity: 0,
                total_purchase_cost: 0,
                total_sale_value: 0,
                total_profit_estimate: 0,

            }
        },
        props: {
            store: [Array, Object],
        },
        mounted() {
            console.log('Store Stock Chart loaded');
            this.store_stock_chart();
        },
        methods: {

            set_form_data(){
                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                return formData;
            },

            create_chart(canvas_id, chart_data) {
                var ctx = document.getElementById(canvas_id);
                var chart = new Chart(ctx, {
                    type: chart_data.type,
                    data: chart_data.data,
                    options: chart_data.options,
                });
                return chart;
            },

            store_stock_chart(){

                var formData = this.set_form_data();

                var store_stock_chart = this.create_chart('store_stock_chart', this.chart_config);

                axios.post('/api/store_stock_chart', formData).then((response) => {
                    if(response.data.status_code == 200) {

                        this.total_quantity = response.data.data.total_quantity;
                        this.total_purchase_cost = response.data.data.total_purchase_cost;
                        this.total_sale_value = response.data.data.total_sale_price;
                        this.total_profit_estimate = response.data.data.profit_estimate;
                        
                        store_stock_chart.data.datasets = [];
                        store_stock_chart.data.datasets.push(
                            {
                                backgroundColor: [this.cost_fill_color, this.sale_price_fill_color, this.profit_fill_color],
                                borderColor: '#FFF',
                                data: [response.data.data.total_purchase_cost, response.data.data.total_sale_price, response.data.data.profit_estimate],
                            }
                        );
                        store_stock_chart.update();

                    }else{
                        
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },
        }
    }
</script>