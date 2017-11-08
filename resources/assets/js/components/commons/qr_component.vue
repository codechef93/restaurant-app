<template>
    <div class="row">
        <div class="col-md-12">
                
            <div class="form-row mb-2">
                <div class="form-group col-md-3">
                    <label for="size">{{ $t("Size") }}</label>
                    <input type="number" name="size" v-model="size" v-validate="'required|numeric'" class="form-control form-control-custom" :placeholder="$t('Please enter QR Code size')"  autocomplete="off">
                    <span v-bind:class="{ 'error' : errors.has('size') }">{{ errors.first('size') }}</span> 
                </div>
                <div class="form-group col-md-3">
                    <label for="foreground">{{ $t("Foreground Color") }}</label>
                    <input type="text" name="foreground" v-model="foreground" v-validate="'required'" class="form-control form-control-custom" :placeholder="$t('Please enter foreground color')"  autocomplete="off">
                    <span v-bind:class="{ 'error' : errors.has('foreground') }">{{ errors.first('foreground') }}</span> 
                </div>
                <div class="form-group col-md-3">
                    <label for="background">{{ $t("Background Color") }}</label>
                    <input type="text" name="background" v-model="background" v-validate="'required'" class="form-control form-control-custom" :placeholder="$t('Please enter background color')"  autocomplete="off">
                    <span v-bind:class="{ 'error' : errors.has('background') }">{{ errors.first('background') }}</span> 
                </div>
            </div>

            <p class="mb-6 col-6 p-0">QR Code menus are digital versions of physical menu cards at restaurants. You can right click on the QR code and choose 'Save Image As'. The QR code will be saved as image. You can take the print out and place it on tables.</p>

            <p class="mb-6 col-6 p-0">
                <a :href="menu_route">Browse Restaurant Menu</a>
            </p>

            <qrcode-vue :value="menu_route" :size="size" level="H" :renderAs="render_as" :foreground="foreground" :background="background"></qrcode-vue>

        </div>
        
    </div>
</template>

<script>
    'use strict';

    import QrcodeVue from 'qrcode.vue'
    
    export default {
        data(){
            return{
                menu_route  : this.menu_link,
                size        : 225,
                render_as   : 'canvas',
                foreground  : '#1D1E2C',
                background  : '#F7EBEC',

                render_as_array : ['canvas', 'svg']
            }
        },
        components: {
            QrcodeVue,
        },
        props: {
            menu_link: String
        },
        mounted() {
            console.log('QR menu page loaded');
        },
        methods: {
            
        }
    }
</script>