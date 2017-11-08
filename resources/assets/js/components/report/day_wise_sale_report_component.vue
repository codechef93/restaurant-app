<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> {{ $t("Day Wise Sale Report") }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <date-picker type="month" :lang='date.lang' :format="date.format" v-model="month" @change="month_change" input-class="form-control bg-white"></date-picker>
                </div>
            </div>

            <div class="d-flex flex-wrap">
                <div class="col-sm-12 col-md-12 col-lg-12 col-xl-12 p-0">
                    <div class="">
                        <div class='chart_container'> 
                            <canvas id="day_wise_sale" class=""></canvas>
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

                month : new Date(moment().format("YYYY-MM")),
                month_formatted : new Date(moment().format("YYYY-MM")),

                chart_fill_color : 'rgba(104, 216, 155, 0.9)',
                chart_border_color : 'rgba(104, 216, 155)',

                chart_config : {
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
                                tension: 0.000001
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
                                    display: true,
                                    drawBorder: true,
                                    drawOnChartArea: true,
                                },
                            }],
                            yAxes: [{
                                ticks: {
                                    beginAtZero: true,
                                },
                                display: true,
                                scaleLabel: {
                                    display: true,
                                    labelString: 'Value'
                                },
                                gridLines: {
                                    display: true,
                                    drawBorder: true,
                                    drawOnChartArea: true,
                                },
                            }]
                        }
                    }
                }
            }
        },
        mounted() {
            console.log('Day wise sale report loaded');
            this.chart();
        },
        methods: {

            convert_date_format(date){
                return (date != '')?moment(date).format("YYYY-MM"):'';
            },

            month_change(){
                this.month_formatted = this.convert_date_format(this.month);
                this.chart();
            },

            set_form_data(){
                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);
                formData.append("date", this.convert_date_format(this.month_formatted));
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

            chart(){

                var formData = this.set_form_data();

                var chart = this.create_chart('day_wise_sale', this.chart_config);

                axios.post('/api/get_order_chart_stats', formData).then((response) => {
                    if(response.data.status_code == 200) {
                        
                        chart.data.datasets = [];
                        chart.data.labels = response.data.data.x_axis;
                        chart.data.datasets.push(
                            {
                                label: 'Order Count',
                                borderWidth: 1,
                                backgroundColor: this.chart_fill_color,
                                borderColor: this.chart_fill_color,
                                data: response.data.data.y_axis.order_count,
                                pointRadius: 5,
                                pointHoverRadius: 8,
                                pointBackgroundColor: '#FFF'
                            }
                        );
                        chart.update();

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