<template>
    <div class="row">
        <div class="col-md-12">
            
            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> {{ $t("Calendar") }}</span>
                        </div>
                    </div>
                </div>
                <div class="">
                    
                </div>
            </div>

            <FullCalendar :options="calendarOptions" />
        </div>

    </div>
</template>  

<script>
    'use strict';

    import { Calendar } from '@fullcalendar/core';
    import FullCalendar from '@fullcalendar/vue';
    import dayGridPlugin from '@fullcalendar/daygrid';
    import timeGridPlugin from '@fullcalendar/timegrid';
    import listPlugin from '@fullcalendar/list';
    import interactionPlugin from '@fullcalendar/interaction';

    import tippy from 'tippy.js';
    import 'tippy.js/dist/tippy.css';
    import moment from "moment";
    
    export default {
        components: {
            FullCalendar
        },
        data(){
            return{
                processing: false,
                api_link: '/api/load_events',
                server_errors: '',
                error_class: '',

                start_date: '',
                end_date: '',

                calendarOptions: {
                    themeSystem: 'standard',
                    plugins: [ dayGridPlugin, timeGridPlugin, listPlugin, interactionPlugin ],
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev,next today',
                        center: 'title',
                        right: 'dayGridMonth,timeGridWeek,timeGridDay'
                    },
                    handleWindowResize: true,
                    editable: false,
                    selectable: true,
                    selectMirror: true,
                    dayMaxEvents: true,
                    weekends: true,
                    timezone: this.timezone,
                    events: this.load_bookings,
                    eventDidMount: this.mount,
                    locale: window.settings.language
                },
            }
        },
        props: {
            timezone: String
        },
        mounted() {
            console.log('Calendar & Booking page loaded');
        },
        methods: {

            mount(info){
                tippy(info.el, {
                    content:`<div class="d-flex flex-column p-2">

                            <div class="border-bottom mb-1"><b>${info.event.title}</b></div>
                            <div class="">Start Date : ${moment(info.event.startStr).format('DD/MM/YYYY, h:mm a')}</div>
                            <div class="border-bottom mb-1">End Date : ${moment(info.event.endStr).format('DD/MM/YYYY, h:mm a')}</div>
                        
                            <div class="">Name : ${(info.event.extendedProps.name != null)?info.event.extendedProps.name:'-'}</div>
                            <div class="">Email : ${(info.event.extendedProps.email != null)?info.event.extendedProps.email:'-'}</div>
                            <div class="border-bottom mb-1">Phone : ${(info.event.extendedProps.phone != null)?info.event.extendedProps.phone:'-'}</div>

                            <div class="">No of Persons : ${(info.event.extendedProps.no_of_persons != null)?info.event.extendedProps.no_of_persons:'-'}</div>
                            
                            <div class="">Description : ${(info.event.extendedProps.description != null)?info.event.extendedProps.description:'-'}</div>
                        </div>
                    `,
                    allowHTML: true
                });
            },
            load_bookings(select_info) {

                this.start_date = select_info.startStr;
                this.end_date = select_info.endStr;

                var formData = new FormData();

                formData.append("access_token", window.settings.access_token);
                formData.append("start_date", (this.start_date == null)?'':this.start_date);
                formData.append("end_date", (this.end_date == null)?'':this.end_date);

                let events = axios.post(this.api_link, formData).then((response) => response.data.data);
                
                return events;
            },
        }
    }
</script>