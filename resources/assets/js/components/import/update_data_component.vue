<template>
    <div class="row">
        <div class="col-md-12">
            
            <form @submit.prevent="submit_form" class="mb-3">

                <div class="d-flex flex-wrap mb-4">
                    <div class="mr-auto">
                        <span class="text-title">{{ $t("Upload & Update Data") }}</span>
                    </div>
                    <div class="d-flex">
                        
                        <button class="btn btn-outline-primary mr-1" type="button" v-on:click="download_reference_sheet()" v-bind:disabled="reference_processing == true"> 
                            <i class='fa fa-circle-notch fa-spin'  v-if="reference_processing == true"></i>
                            {{ $t("Download Reference Sheet") }}
                        </button>
                        
                        <div class="dropdown mr-1" v-if="templates.length != 0">
                            <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ $t("Download Templates") }}
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown">
                                <a :href="template.template_link" class="dropdown-item" v-for="(template, index) in templates" :key="index" >{{ template.template_label }} Template</a>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" v-bind:disabled="processing == true"> <i class='fa fa-circle-notch fa-spin'  v-if="processing == true"></i> {{ $t("Upload & Save") }}</button>
                    </div>
                </div>
                    
                <p v-html="server_errors" v-bind:class="[error_class]"></p>

                <div class="form-row mb-2">
                    <div class="form-group col-md-3">
                        <label for="upload_type">{{ $t("Type of Upload") }}</label>
                         <select name="upload_type" v-model="upload_type" v-validate="'required'" class="form-control form-control-custom custom-select">
                            <option value="">Choose Type of Upload..</option>
                            <option v-for="(upload_option, index) in upload_options" v-bind:value="upload_option.key" v-bind:key="index">
                                {{ upload_option.value }}
                            </option>
                        </select>
                        <span v-bind:class="{ 'error' : errors.has('upload_type') }">{{ errors.first('upload_type') }}</span> 
                    </div>
                    <div class="form-group col-md-3">
                        <label for="upload_file">{{ $t("Upload File") }}</label>
                        <input type="file" name="upload_file" ref="upload_file" v-on:change="on_file_select" class="form-control-file" v-validate="'required|ext:xls,xlsx'">
                        <span v-bind:class="{ 'error' : errors.has('upload_file') }">{{ errors.first('upload_file') }}</span> 
                    </div>
                </div>

            </form>

            <div v-if="import_errors.length!=0">
                <p class="error">There are some errors in the file. Please correct the following errors and upload the file again.</p>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th scope="col">Row Number</th>
                            <th scope="col">Errors</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(import_error, index) in import_errors" :key="index">
                            <th scope="row">{{ index }}</th>
                            <td class="table-danger">
                                <div class="" v-for="(error, index) in import_error" :key="index">{{ error }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                server_errors   : '',
                error_class     : '',
                processing      : false,
                modal           : false,
                show_modal      : false,
                reference_processing : false,
                api_link        : '/api/update_data',
                reference_sheet_api_link : 'api/download_reference_sheet',

                upload_type     : '',
                upload_file     : '',

                import_errors   : []
            }
        },
        props: {
            upload_options: Array,
            templates: Array
        },
        mounted() {
            console.log('Upload data page loaded');
        },
        methods: {
            on_file_select(){
                this.upload_file = this.$refs.upload_file.files[0];
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
                            formData.append("upload_type", this.upload_type);
                            formData.append("upload_file", this.upload_file);

                            axios.post(this.api_link, formData).then((response) => {
                                if(response.data.status_code == 200) {
                                    if(response.data.data.update_status){
                                        this.show_response_message(response.data.msg, 'SUCCESS');
                                
                                        setTimeout(function(){
                                            location.reload();
                                        }, 1000);
                                    }else{
                                        this.import_errors = response.data.data.errors;
                                        this.show_modal = false;
                                        this.processing = false;
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
            },

            download_reference_sheet(){

                this.reference_processing = true; 

                var formData = new FormData();
                formData.append("access_token", window.settings.access_token);

                axios.post(this.reference_sheet_api_link, formData).then((response) => {

                    if(response.data.status_code == 200) {
                        if(typeof response.data.link != 'undefined' && response.data.link != ""){
                            window.open(response.data.link, '_blank');
                        }else{
                            location.reload();
                        }
                    }else{
                        try{
                            var error_json = JSON.parse(response.data.msg);
                            this.loop_api_errors(error_json);
                        }catch(err){
                            this.server_errors = response.data.msg;
                        }
                        this.error_class = 'error';
                    }
                    this.reference_processing = false;
                })
                .catch((error) => {
                    console.log(error);
                });
            }
        }
    }
</script>