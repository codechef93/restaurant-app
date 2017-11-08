<template>
    <div class="row">
        <div class="col-md-12">

            <div class="d-flex flex-wrap mb-4">
                <div class="mr-auto">
                   <div class="d-flex">
                        <div>
                            <span class="text-title"> <span class='text-muted'>{{ event_type }}</span> {{ booking.event_code }} </span>
                        </div>
                    </div>
                </div>
                <div class="">
                    <button type="submit" class="btn btn-danger mr-1" v-if="delete_booking_access == true" v-on:click="delete_booking()" v-bind:disabled="delete_processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="delete_processing == true"></i> {{ $t("Delete") }}</button>
                </div>
            </div>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Customer Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="name">{{ $t("Name") }}</label>
                    <p>{{ (booking.name)?booking.name:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="email">{{ $t("Email") }}</label>
                    <p>{{ (booking.email)?booking.email:'-' }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="phone">{{ $t("Contact No") }}</label>
                    <p>{{ (booking.phone)?booking.phone:'-' }}</p>
                </div>
            </div>

            <hr>

            <div class="mb-2">
                <span class="text-subhead">{{ $t("Time Information") }}</span>
            </div>
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="start_time">{{ $t("Booking or Event Start Time") }}</label>
                    <p>{{ booking.start_date_raw }}</p>
                </div>
                <div class="form-group col-md-3">
                    <label for="end_time">{{ $t("Booking or Event End Time") }}</label>
                    <p>{{ booking.end_date_raw }}</p>
                </div>
            </div>
            <hr>

            <div class="form-row mb-2">
                <div class="form-group col-md-6">
                    <label for="description">{{ $t("Description") }}</label>
                    <p>{{ (booking.description)?booking.description:'-' }}</p>
                </div>
            </div>

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
    
    export default {
        data(){
            return{
                show_modal: false,
                delete_processing: false,
                processing: false,
                delete_booking_api_link : '/api/delete_booking/'+this.booking_data.slack,

                booking : this.booking_data,
                event_type: this.booking_data.event_type
            }
        },
        props: {
            booking_data: [Array, Object],
            delete_booking_access: Boolean,
        },
        mounted() {
            console.log('Booking detail page loaded');
        },
        methods: {
           delete_booking(){

                this.$off("submit");
                this.$off("close");
                this.show_modal = true;

                this.$on("submit",function () {       
                    this.processing = true;
                    this.delete_processing = true;

                    var formData = new FormData();
                    formData.append("access_token", window.settings.access_token);

                    axios.post(this.delete_booking_api_link, formData).then((response) => {

                        if(response.data.status_code == 200) {
                            if(response.data.link != ""){
                                window.location.href = response.data.link;
                            }else{
                                location.reload();
                            }
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
                        this.delete_processing = false;
                    })
                    .catch((error) => {
                        console.log(error);
                    });
                });

                this.$on("close",function () {
                    this.show_modal = false;
                });
            }
        }
    }
</script>