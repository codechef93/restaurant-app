<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> {{ $t("Dashboard") }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <date-picker type="month" :lang='date.lang' :format="date.format" v-model="dashboard_month" @change="dashboard_month_change" input-class="form-control bg-white"></date-picker>
                </div>
            </div>
            
            <div class="d-flex flex-wrap mb-4">
                <div class="col-md-12">
                    <div class="row">
                        
                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-6 box">
                            <div class="col-12 p-3 bg-light rounded custom-border-light">

                                <div class="d-flex flex-wrap box-content">
                                    <div class="d-flex col-sm-12 col-md-12 col-lg-8 col-xl-8 p-0">
                                       <div class='stat_chart_container'> 
                                            <canvas id="today_sales_count_chart" class=""></canvas>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-end flex-column col-sm-12 col-md-12 col-lg-4 col-xl-4 p-0">
                                        <div class="p-2">{{ $t("Todays Sales") }}</div>

                                        <div class="mt-auto p-2">
                                            <span class="text-headline">
                                                <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                                <span v-else>{{ todays_order_count.formatted }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-6 box">
                            <div class="col-12 p-3 bg-light rounded custom-border-light">
                                
                                <div class="d-flex flex-wrap box-content">
                                    <div class="d-flex col-sm-12 col-md-12 col-lg-8 col-xl-8 p-0">
                                       <div class='stat_chart_container'> 
                                            <canvas id="today_sales_value_chart" class=""></canvas>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-end flex-column col-sm-12 col-md-12 col-lg-4 col-xl-4 p-0">
                                        <div class="p-2">{{ $t("Todays POS Sale Value") }} ({{ store_currency }})</div>

                                        <div class="mt-auto p-2">
                                            <span class="text-headline">
                                                <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                                <span v-else>{{ todays_order_value.formatted }}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4">
                <div class="col-md-12">
                    <div class="row">
                        
                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box" :title="order_count.raw">
                            <div class="col-12 p-3 bg-light rounded custom-border-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">{{ $t("Total Sales") }}</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                            <span v-else>{{ order_count.formatted }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box" :title="order_value.raw">
                            <div class="col-12 p-3 bg-light rounded custom-border-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">{{ $t("Total POS Sale Value") }} ({{ store_currency }})</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                            <span v-else>{{ order_value.formatted }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box" :title="revenue_value.raw">
                            <div class="col-12 p-3 bg-light rounded custom-border-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">{{ $t("Total Revenue") }} ({{ store_currency }})</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                            <span v-else>{{ revenue_value.formatted }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box" :title="invoices_count.raw">
                            <div class="col-12 p-3 bg-light rounded custom-border-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">{{ $t("Total Invoices") }}</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                            <span v-else>{{ invoices_count.formatted }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box" :title="expense.raw">
                            <div class="col-12 p-3 bg-light rounded custom-border-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">{{ $t("Total Expense") }} ({{ store_currency }})</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                            <span v-else>{{ expense.formatted }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box" :title="net_profit_value.raw">
                            <div class="col-12 p-3 bg-light rounded custom-border-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">{{ $t("Net Profit") }} ({{ store_currency }})</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                            <span v-else>{{ net_profit_value.formatted }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box" :title="customer_count.raw">
                            <div class="col-12 p-3 bg-light rounded custom-border-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">{{ $t("Total Customers") }}</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                            <span v-else>{{ customer_count.formatted }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3 box" :title="purchase_order_count.raw">
                            <div class="col-12 p-3 bg-light rounded custom-border-light">
                                <div class="d-flex align-items-end flex-column box-content">
                                    <div class="p-2">{{ $t("Total Purchase Orders") }}</div>

                                    <div class="mt-auto p-2">
                                        <span class="text-headline">
                                            <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                            <span v-else>{{ purchase_order_count.formatted }}</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            

            <div class="d-flex flex-wrap">
                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 p-0">
                    <div class="mb-2">
                        <span class="text-subhead"> {{ $t("POS Order Count Day Wise") }}</span>
                    </div>
                    <div class="">
                        <div class='chart_container'> 
                            <canvas id="pos_sales_count_activity_chart" class=""></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6 p-0">
                    <div class="mb-2">
                        <span class="text-subhead"> {{ $t("POS Order Value Day Wise") }}</span>
                    </div>
                    <div class="">
                        <div class='chart_container'> 
                            <canvas id="pos_sales_value_activity_chart" class=""></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap">
                <div class="mb-2">
                    <span class="text-subhead"> {{ $t("Targets") }}</span>
                </div>
            </div>
            <div class="d-flex flex-wrap mb-4">
                <div class="col-md-12">
                    <div class="row">

                    <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3">
                        <div class="col-12 p-3 bg-light rounded custom-border-light">
                            <div class="d-flex align-items-end flex-column box-content">
                                <div class="p-2">{{ $t("Income") }} ({{ store_currency }})</div>

                                <div class="mt-auto p-2">
                                    <span class="dashboard-target-label">
                                        <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                        <span v-else>{{ revenue_value.raw }} / {{ target.income }}</span>
                                    </span>
                                </div>

                                <div class="progress mt-2 w-100 progress-height">
                                    <div class="progress-bar" role="progressbar" v-bind:style="{'width':target.income_width}" v-bind:aria-valuenow="revenue_value.raw" aria-valuemin="0" v-bind:aria-valuemax="target.income"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3">
                        <div class="col-12 p-3 bg-light rounded custom-border-light">
                            <div class="d-flex align-items-end flex-column box-content">
                                <div class="p-2">{{ $t("Expense") }} ({{ store_currency }})</div>

                                <div class="mt-auto p-2">
                                    <span class="dashboard-target-label">
                                        <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                        <span v-else>{{ expense.raw }} / {{ target.expense }}</span>
                                    </span>
                                </div>

                                <div class="progress mt-2 w-100 progress-height">
                                    <div class="progress-bar" role="progressbar" v-bind:style="{'width':target.expense_width}" v-bind:aria-valuenow="expense.raw" aria-valuemin="0" v-bind:aria-valuemax="target.expense"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3">
                        <div class="col-12 p-3 bg-light rounded custom-border-light">
                            <div class="d-flex align-items-end flex-column box-content">
                                <div class="p-2">{{ $t("POS Sales") }} ({{ store_currency }})</div>

                                <div class="mt-auto p-2">
                                    <span class="dashboard-target-label">
                                        <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                        <span v-else>{{ order_value.raw }} / {{ target.sales }}</span>
                                    </span>
                                </div>

                                <div class="progress mt-2 w-100 progress-height">
                                    <div class="progress-bar" role="progressbar" v-bind:style="{'width':target.sales_width}" v-bind:aria-valuenow="order_value.raw" aria-valuemin="0" v-bind:aria-valuemax="target.sales"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start flex-column p-1 mb-1 col-md-3">
                        <div class="col-12 p-3 bg-light rounded custom-border-light">
                            <div class="d-flex align-items-end flex-column box-content">
                                <div class="p-2">{{ $t("Net Profit") }} ({{ store_currency }})</div>

                                <div class="mt-auto p-2">
                                    <span class="dashboard-target-label">
                                        <i class='fa fa-circle-notch fa-spin' v-if="stats_processing == true"></i>
                                        <span v-else>{{ net_profit_value.raw }} / {{ target.net_profit }}</span>
                                    </span>
                                </div>

                                <div class="progress mt-2 w-100 progress-height">
                                    <div class="progress-bar" role="progressbar" v-bind:style="{'width':target.net_profit_width}" v-bind:aria-valuenow="net_profit_value.raw" aria-valuemin="0" v-bind:aria-valuemax="target.net_profit"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>
                </div>
            </div>

            <div class="d-flex flex-wrap mb-4">

                <div class="col-sm-12 col-md-4 col-lg-4 col-xl-4 p-0 mb-4">
                    <div class="mb-2">
                        <span class="text-subhead"> {{ $t("Income vs Expense") }}</span>
                    </div>
                    <div class="bg-light rounded custom-border-light">
                        <div class='chart_container'> 
                            <canvas id="income_expense_chart" class=""></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-8 col-lg-8 col-xl-8 pl-sm-0 pl-md-3 pr-4">
                    <div class="mb-2">
                        <span class="text-subhead"> {{ $t("Recent Transactions") }}</span>
                    </div>
                    <div class="">
                        <div class='table-container'> 
                            <div class="table-responsive mb-2" v-if="transactions.length>0">
                                <table class="table table-striped display nowrap text-nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">{{ $t("Transaction Code") }}</th>
                                            <th scope="col">{{ $t("Transaction Date") }}</th>
                                            <th scope="col">{{ $t("Transaction Type") }}</th>
                                            <th scope="col">{{ $t("Payment Method") }}</th>
                                            <th scope="col" class="text-right">{{ $t("Amount") }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(transaction, key, index) in transactions" v-bind:value="transactions.slack" v-bind:key="index">
                                            <th scope="col">{{ key+1 }}</th>
                                            <td>{{ transaction.transaction_code }}</td>
                                            <td>{{ transaction.transaction_date }}</td>
                                            <td>{{ transaction.transaction_type_data.label }}</td>
                                            <td>{{ transaction.payment_method }}</td>
                                            <td class="text-right">{{ transaction.amount }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div v-else>Transactions not found</div>
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
                date:{
                    lang : 'en',
                    format : "YYYY-MM",
                },

                dashboard_month : new Date(moment().format("YYYY-MM")),
                dashboard_month_formatted : new Date(moment().format("YYYY-MM")),
                store_currency: this.store.currency_code,

                stats_processing: false,
                todays_order_count: {
                    raw: '-',
                    formatted: '-',
                },
                todays_order_value: {
                    raw: '-',
                    formatted: '-'
                },
                order_count: {
                    raw: '-',
                    formatted: '-',
                },
                order_value: {
                    raw: '-',
                    formatted: '-'
                },
                revenue_value: {
                    raw: '-',
                    formatted: '-'
                },
                customer_count: {
                    raw: '-',
                    formatted: '-'
                },
                expense: {
                    raw: '-',
                    formatted: '-'
                },
                net_profit_value: {
                    raw: '-',
                    formatted: '-'
                },
                purchase_order_count: {
                    raw: '-',
                    formatted: '-'
                },
                invoices_count: {
                    raw: '-',
                    formatted: '-'
                },

                target: {
                    income: '-',
                    income_width : 0,

                    expense: '-',
                    expense_width : 0,

                    sales: '-',
                    sales_width : 0,

                    net_profit: '-',
                    net_profit_width : 0,
                },

                transactions : [],

                todays_sales_count_chart_color: '#41C6AC',
                todays_sales_count_chart_fill_color: '#b3e8de',

                todays_sales_value_chart_color: '#3185fc',
                todays_sales_value_chart_fill_color: '#adcefe',

                pos_sales_count_activity_chart_fill_color : '#3185fc',
                pos_sales_value_activity_chart_fill_color : '#2E3CB4',
                income_chart_fill_color: '#059BFF',
                expense_chart_fill_color: '#FFC233',

                todays_sales_count_chart_config : {
                    type: 'line',
                    data: {
                        datasets: [],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        title: {
                            display: false,
                            text: ''
                        },
                        elements: {
                            line: {
                                tension: 0.4
                            }
                        },
                        layout: {
                            padding: {
                                top: 10
                            }
                        },
                        legend: {
                            display: false
                        },
                        scales: {
                            xAxes: [{
                                ticks: {
                                    fontSize: 10
                                },
                                display: true,
                                scaleLabel: {
                                    display: false,
                                    labelString: 'Day of the Month'
                                },
                                gridLines: {
                                    display: false,
                                    drawBorder: true,
                                    drawOnChartArea: false,
                                },
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                },
                                display: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value'
                                },
                                gridLines: {
                                    display: false,
                                    drawBorder: false,
                                    drawOnChartArea: false,
                                },
                            }]
                        }
                    }
                },

                todays_sales_value_chart_config : {
                    type: 'line',
                    data: {
                        datasets: [],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        title: {
                            display: false,
                            text: ''
                        },
                        elements: {
                            line: {
                                tension: 0.4
                            }
                        },
                        layout: {
                            padding: {
                                top: 10
                            }
                        },
                        legend: {
                            display: false
                        },
                        scales: {
                            xAxes: [{
                                ticks: {
                                    fontSize: 10
                                },
                                display: true,
                                scaleLabel: {
                                    display: false,
                                    labelString: 'Day of the Month'
                                },
                                gridLines: {
                                    display: false,
                                    drawBorder: true,
                                    drawOnChartArea: false,
                                },
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                },
                                display: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value'
                                },
                                gridLines: {
                                    display: false,
                                    drawBorder: false,
                                    drawOnChartArea: false,
                                },
                            }]
                        }
                    }
                },

                pos_sales_count_activity_chart_config : {
                    type: 'line',
                    data: {
                        datasets: [],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        title: {
                            display: true,
                            text: ''
                        },
                        elements: {
                            line: {
                                tension: 0.4
                            }
                        },
                        layout: {
                            padding: {
                                top: 10,
                                bottom: 10
                            }
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Day of the Month'
                                },
                                gridLines: {
                                    display: false,
                                    drawBorder: true,
                                    drawOnChartArea: false,
                                },
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                },
                                display: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value'
                                },
                                gridLines: {
                                    display: false,
                                    drawBorder: false,
                                    drawOnChartArea: false,
                                },
                            }]
                        }
                    }
                },

                pos_sales_value_activity_chart_config : {
                    type: 'line',
                    data: {
                        datasets: [],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        spanGaps: false,
                        title: {
                            display: true,
                            text: ''
                        },
                        elements: {
                            line: {
                                tension: 0.4
                            }
                        },
                        layout: {
                            padding: {
                                top: 10,
                                bottom: 10
                            }
                        },
                        scales: {
                            xAxes: [{
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Day of the Month'
                                },
                                gridLines: {
                                    display: false,
                                    drawBorder: true,
                                    drawOnChartArea: false,
                                },
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                },
                                display: false,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value'
                                },
                                gridLines: {
                                    display: false,
                                    drawBorder: false,
                                    drawOnChartArea: false,
                                },
                            }]
                        }
                    }
                },

                income_expense_chart : {
                    type: 'doughnut',
                    data: {
                        datasets: [],
                        labels: [
                            'Income',
                            'Expense'
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        legend: {
                            position: 'top',
                        },
                        title: {
                            display: false,
                            text: ''
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
                }

            }
        },
        props: {
            store: [Array, Object]
        },
        mounted() {
            console.log('Dashboard loaded');
            this.fire_requests();
        },
        methods: {

            convert_date_format(date){
                return (date != '')?moment(date).format("YYYY-MM"):'';
            },

            dashboard_month_change(){
                this.dashboard_month_formatted = this.convert_date_format(this.dashboard_month);
                this.fire_requests();
            },

            set_form_data(){
                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("date", this.convert_date_format(this.dashboard_month_formatted));
                return formData;
            },

            fire_requests(){
                
                this.get_dashboard_stats();

                this.order_count_activity_chart();

                this.get_recent_trasactions();

                //this.trending_products();
            },

            get_dashboard_stats(){
                var formData = this.set_form_data();

                this.stats_processing = true;

                axios.post('/api/get_dashboard_stats', formData).then((response) => {
                    this.stats_processing = false;
                    if(response.data.status_code == 200) {

                        this.todays_order_count.raw = response.data.data.todays_order_count.count_raw;
                        this.todays_order_count.formatted = response.data.data.todays_order_count.count_formatted;

                        this.todays_order_value.raw = response.data.data.todays_order_value.count_raw;
                        this.todays_order_value.formatted = response.data.data.todays_order_value.count_formatted;

                        this.order_count.raw = response.data.data.order_count.count_raw;
                        this.order_count.formatted = response.data.data.order_count.count_formatted;

                        this.order_value.raw = response.data.data.order_value.count_raw;
                        this.order_value.formatted = response.data.data.order_value.count_formatted;

                        this.order_count.raw = response.data.data.order_count.count_raw;
                        this.order_count.formatted = response.data.data.order_count.count_formatted;

                        this.revenue_value.raw = response.data.data.revenue_value.count_raw;
                        this.revenue_value.formatted = response.data.data.revenue_value.count_formatted;

                        this.customer_count.raw = response.data.data.customer_count.count_raw;
                        this.customer_count.formatted = response.data.data.customer_count.count_formatted;

                        this.expense.raw = response.data.data.expense.count_raw;
                        this.expense.formatted = response.data.data.expense.count_formatted;

                        this.net_profit_value.raw = response.data.data.net_profit_value.count_raw;
                        this.net_profit_value.formatted = response.data.data.net_profit_value.count_formatted;

                        this.purchase_order_count.raw = response.data.data.purchase_order_count.count_raw;
                        this.purchase_order_count.formatted = response.data.data.purchase_order_count.count_formatted;

                        this.invoices_count.raw = response.data.data.invoices_count.count_raw;
                        this.invoices_count.formatted = response.data.data.invoices_count.count_formatted;

                        //stats
                        this.target.income = response.data.data.targets.income;
                        this.target.income_width = ((response.data.data.revenue_value.count_raw/response.data.data.targets.income)*100)+'%';

                        this.target.expense = response.data.data.targets.expense;
                        this.target.expense_width = ((response.data.data.expense.count_raw/response.data.data.targets.expense)*100)+'%';

                        this.target.sales = response.data.data.targets.sales;
                        this.target.sales_width = ((response.data.data.order_value.count_raw/response.data.data.targets.sales)*100)+'%';

                        this.target.net_profit = response.data.data.targets.net_profit;
                        this.target.net_profit_width = ((response.data.data.net_profit_value.count_raw/response.data.data.targets.net_profit)*100)+'%';

                        var today_sales_count_chart = this.create_chart('today_sales_count_chart', this.todays_sales_count_chart_config);
                        today_sales_count_chart.data.datasets = [];
                        today_sales_count_chart.data.labels = response.data.data.todays_order_count.chart.x_axis;
                        today_sales_count_chart.data.datasets.push(
                            {
                                borderWidth: 2,
                                borderColor: this.todays_sales_count_chart_color,
                                backgroundColor: this.todays_sales_count_chart_fill_color,
                                pointRadius: 1,
                                data: response.data.data.todays_order_count.chart.y_axis,
                            }
                        );
                        today_sales_count_chart.update();

                        var today_sales_value_chart = this.create_chart('today_sales_value_chart', this.todays_sales_value_chart_config);
                        today_sales_value_chart.data.datasets = [];
                        today_sales_value_chart.data.labels = response.data.data.todays_order_value.chart.x_axis;
                        today_sales_value_chart.data.datasets.push(
                            {
                                borderWidth: 2,
                                borderColor: this.todays_sales_value_chart_color,
                                backgroundColor: this.todays_sales_value_chart_fill_color,
                                pointRadius: 1,
                                data: response.data.data.todays_order_value.chart.y_axis,
                            }
                        );
                        today_sales_value_chart.update();

                        var income_expense_chart = this.create_chart('income_expense_chart', this.income_expense_chart);
                        income_expense_chart.data.datasets = [];
                        income_expense_chart.data.datasets.push(
                            {
                                backgroundColor: [this.income_chart_fill_color, this.expense_chart_fill_color],
                                borderColor: '#FFF',
                                data: [this.revenue_value.raw, this.expense.raw],
                            }
                        );
                        income_expense_chart.update();    

                    }else{

                        this.todays_order_count.raw = '-';
                        this.todays_order_count.formatted = '-';

                        this.todays_order_value.raw = '-';
                        this.todays_order_value.formatted = '-';

                        this.order_count.raw = '-';
                        this.order_count.formatted = '-';

                        this.order_value.raw = '-';
                        this.order_value.formatted = '-';

                        this.order_count.raw = '-';
                        this.order_count.formatted = '-';

                        this.revenue_value.raw = '-';
                        this.revenue_value.formatted = '-';

                        this.customer_count.raw = '-';
                        this.customer_count.formatted = '-';

                        this.expense.raw = '-';
                        this.expense.formatted = '-';

                        this.net_profit_value.raw = '-';
                        this.net_profit_value.formatted = '-';

                        this.purchase_order_count.raw = '-';
                        this.purchase_order_count.formatted = '-';

                        this.invoices_count.raw = '-';
                        this.invoices_count.formatted = '-';
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
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

            order_count_activity_chart(){

                var formData = this.set_form_data();

                var pos_sales_count_activity_chart = this.create_chart('pos_sales_count_activity_chart', this.pos_sales_count_activity_chart_config);
                var pos_sales_value_activity_chart = this.create_chart('pos_sales_value_activity_chart', this.pos_sales_value_activity_chart_config);

                axios.post('/api/get_order_chart_stats', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        
                        pos_sales_count_activity_chart.data.datasets = [];
                        pos_sales_count_activity_chart.data.labels = response.data.data.x_axis;
                        pos_sales_count_activity_chart.data.datasets.push(
                            {
                                label: 'Order Count',
                                borderWidth: 1,
                                backgroundColor: this.pos_sales_count_activity_chart_fill_color,
                                borderColor: this.pos_sales_count_activity_chart_fill_color,
                                data: response.data.data.y_axis.order_count,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#FFF'
                            }
                        );
                        pos_sales_count_activity_chart.update();

                        pos_sales_value_activity_chart.data.datasets = [];
                        pos_sales_value_activity_chart.data.labels = response.data.data.x_axis;
                        pos_sales_value_activity_chart.data.datasets.push(
                            {
                                label: 'Order Value ('+ this.store_currency+ ')',
                                borderWidth: 1,
                                backgroundColor: this.pos_sales_value_activity_chart_fill_color,
                                borderColor: this.pos_sales_value_activity_chart_fill_color,
                                data: response.data.data.y_axis.order_value,
                                pointRadius: 3,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#FFF'
                            }
                        );
                        pos_sales_value_activity_chart.update();

                    }else{
                        
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            },

            get_recent_trasactions(){
                
                var formData = this.set_form_data();

                axios.post('/api/get_recent_trasactions', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        this.transactions = response.data.data;
                    }else{
                        
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>