"use strict";

export var mixin = {
    methods: {
        loop_api_errors(error_json) {
            this.server_errors = '';
            var error_string = '';
            $.each(error_json, (key, value) => {
                error_string += value[0] + '<br>';
            });
            this.server_errors = error_string;
            return this.server_errors;
        },

        show_response_message(message, title = '', duration = 2000){
            
            Vue.notify({
                group: 'notification_bar',
                title: title,
                text: message,
                duration: duration,
                closeOnClick: true
            });
            
        },

        play_beep(){
            if(typeof this.pos_order != 'undefined' && this.pos_order == true){
                var audio = new Audio('/audio/beep.mp3');
                audio.play();
            }
        },

        play_notification(){
            var audio = new Audio('/audio/notification.mp3');
            audio.play();
        },

        scroll_to(item){
            var element = this.$els[item];
            element.scrollIntoView();
        }
    }
}