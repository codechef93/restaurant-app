<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title" v-if="booking_slack == ''">{{ $t("Add Booking or Event") }}</span>
                        <span class="text-title" v-else>{{ $t("Edit Booking or Event") }}</span>
                    </div>
                    <div class="">
                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="event_type">{{ $t("Event Type") }}</label>
                        <select name="event_type" v-model="event_type" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option v-for="(event_type, index) in event_types" v-bind:value="index" v-bind:key="index">
                                {{ event_type }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('event_type') }">{{ errors.first('event_type') }}</span>  
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="start_date">{{ $t("Booking or Event Start Date") }}</label>
                        <date-picker :format="date.format" :lang='date.lang' v-model="start_date" v-validate="'required|date_format:yyyy-MM-dd'" input-class="form-control form-control-custom bg-white" ref="start_date" name="start_date" :placeholder="$t('Please enter booking/event start date')" autocomplete="off"></date-picker>
                        <span v-bind:class="{ 'error' : errors.has('start_date') }">{{ errors.first('start_date') }}</span>  
                    </div>
                    <div class="form-group col-md-3">
                        <label for="start_time">{{ $t("Booking or Event Start Time") }}</label>
                        <date-picker :format="time.format" :lang='time.lang' value-type="format" type="time" v-model="start_time" v-validate="'required|date_format:hh:mm a'" input-class="form-control form-control-custom bg-white" ref="start_time" name="start_time" :placeholder="$t('Please enter booking/event start time')" autocomplete="off"></date-picker>
                        <span v-bind:class="{ 'error' : errors.has('start_time') }">{{ errors.first('start_time') }}</span>  
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="end_date">{{ $t("Booking or Event End Date") }}</label>
                        <date-picker :format="date.format" :lang='date.lang' v-model="end_date" v-validate="'required|date_format:yyyy-MM-dd'" input-class="form-control form-control-custom bg-white" ref="end_date" name="end_date" :placeholder="$t('Please enter booking/event end date')" autocomplete="off"></date-picker>
                        <span v-bind:class="{ 'error' : errors.has('end_date') }">{{ errors.first('end_date') }}</span>  
                    </div>
                    <div class="form-group col-md-3">
                        <label for="end_time">{{ $t("Booking or Event End Time") }}</label>
                        <date-picker :format="time.format" :lang='time.lang' value-type="format" type="time" v-model="end_time" v-validate="'required|date_format:hh:mm a'" input-class="form-control form-control-custom bg-white" ref="end_time" name="end_time" :placeholder="$t('Please enter booking/event end time')" autocomplete="off"></date-picker>
                        <span v-bind:class="{ 'error' : errors.has('end_time') }">{{ errors.first('end_time') }}</span>  
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="name">{{ $t("Name") }}</label>
                        <input type="text" name='name' v-model="name" v-validate="`max:150`" class="form-control form-control-custom" :placeholder="$t('Please enter name')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('name') }">{{ errors.first('name') }}</span>  
                    </div>
                    <div class="form-group col-md-3">
                        <label for="email">{{ $t("Email") }}</label>
                        <input type="text" name='email' v-model="email" v-validate="`email|max:150`" class="form-control form-control-custom" :placeholder="$t('Please enter email')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('email') }">{{ errors.first('email') }}</span>  
                    </div>
                    <div class="form-group col-md-3">
                        <label for="phone">{{ $t("Contact No") }}</label>
                        <input type="text" name='phone' v-model="phone" v-validate="`min:10|max:15`" class="form-control form-control-custom" :placeholder="$t('Please enter phone')"  autocomplete="off">
                        <span v-bind:class="{ 'error' : errors.has('phone') }">{{ errors.first('phone') }}</span>  
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="no_of_persons">{{ $t("No of Persons") }}</label>
                        <input type="number" name='no_of_persons' v-model="no_of_persons" v-validate="`numeric`" class="form-control form-control-custom" :placeholder="$t('Please enter no of persons')"  autocomplete="off" step="1" min="0">
                        <span v-bind:class="{ 'error' : errors.has('no_of_persons') }">{{ errors.first('no_of_persons') }}</span>  
                    </div>
                </div>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="description">{{ $t("Description") }}</label>
                        <textarea name="description" v-model="description" v-validate="'max:65535'" class="form-control form-control-custom" rows="5" :placeholder="$t('Enter description')"></textarea>
                        <span v-bind:class="{ 'error' : errors.has('description') }">{{ errors.first('description') }}</span>
                    </div>
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
                <button type="button" class="btn btn-primary" @click="$emit('submit')" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> Continue</button>
            </template>
        </modalcomponent>
        
    </div>
</template>

<script>
    'use strict';
    
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    import moment from "moment";

    export default {
        data(){
            return{
                date:{
                    lang : 'en',
                    format : "YYYY-MM-DD",
                },
                time:{
                    lang : 'en',
                    format : 'hh:mm A'
                },

                event_types : { 
                    'BOOKING' : 'Booking',
                    'EVENT' : 'Event'  
                },

                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                api_link        : (this.booking_data == null)?'/api/add_booking':'/api/update_booking/'+this.booking_data.slack,

                booking_slack  : (this.booking_data == null)?'':this.booking_data.slack,
                event_type     : (this.booking_data == null)?'BOOKING':this.booking_data.event_type,
                event_code     : (this.booking_data == null)?'':this.booking_data.event_code,
                start_date     : (this.booking_data == null)?'':new Date(this.booking_data.start_date),
                start_time     : (this.booking_data == null)?'':this.booking_data.start_time,
                end_date       : (this.booking_data == null)?'':new Date(this.booking_data.end_date),
                end_time       : (this.booking_data == null)?'':this.booking_data.end_time,
                name           : (this.booking_data == null)?'':this.booking_data.name,
                email          : (this.booking_data == null)?'':this.booking_data.email,
                phone          : (this.booking_data == null)?'':this.booking_data.phone,
                no_of_persons  : (this.booking_data == null)?'':this.booking_data.no_of_persons,
                description    : (this.booking_data == null)?'':this.booking_data.description,
            }
        },
        props: {
            booking_data: [Array, Object]
        },
        mounted() {
            console.log('Add booking page loaded');
        },
        methods: {

            convert_date_format(date){
                return (date != '')?moment(date).format("YYYY-MM-DD"):'';
            },

            submit_form(){

                this.$off("submit");
                this.$off("close");

                this.$validator.validateAll().then((result) => {
                    if (result) {
                        this.show_modal = true;
                        this.$on("submit",function () {
                            
                            this.processing = true;
                            var formData = new FormData();

                            formData.append("access_token", window.settings.access_token);
                            formData.append("event_type", (this.event_type == null)?'':this.event_type);
                            formData.append("event_code", (this.event_code == null)?'':this.event_code);
                            formData.append("start_date", (this.start_date == null)?'':this.convert_date_format(this.start_date));
                            formData.append("start_time", (this.start_time == null)?'':this.start_time);
                            formData.append("end_date", (this.end_date == null)?'':this.convert_date_format(this.end_date));
                            formData.append("end_time", (this.end_time == null)?'':this.end_time);
                            formData.append("name", (this.name == null)?'':this.name);
                            formData.append("email", (this.email == null)?'':this.email);
                            formData.append("phone", (this.phone == null)?'':this.phone);
                            formData.append("no_of_persons", (this.no_of_persons == null)?'':this.no_of_persons);
                            formData.append("description", (this.description == null)?'':this.description);

                            axios.post(this.api_link, formData).then((response) => {
                                if(response.data.status_code == 200) {
                                    this.show_response_message(response.data.msg, 'SUCCESS');
                                
                                    setTimeout(function(){
                                        location.reload();
                                    }, 1000);
                                }else{
                                    this.show_modal = false;
                                    this.processing = false;
                                    try{
                                        var error_json = JSON.parse(response.data.msg);
                                        this.loop_api_errors(error_json);
                                    }catch(err){
                                        this.server_errors = response.data.msg;
                                    }
                                    this.error_class = 'error';
                                }
                            })
                            .catch((error) => {
                                console.log(error);
                            });
                        });
                        
                        this.$on("close",function () {
                            this.show_modal = false;
                        });
                    }
                });
            }
        }
    }
</script>