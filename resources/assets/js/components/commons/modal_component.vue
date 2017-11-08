<template>
    <transition name="modal">
        <div>
            <div class="modal-wrapper modal">
                <div class="modal-dialog-centered">
                    <div class="modal-container" v-bind:class="[ modal_w ]">

                        <div class="modal-header">
                            <span class="modal-title">
                                <slot name="modal-header">
                                    {{ $t("Confirm") }}
                                </slot>
                            </span>
                            
                            <button type="button" @click="$emit('close')" class="close" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div v-bind:class="['modal-body']">
                            <slot name="modal-body">
                            {{ $t("Are you sure you want to proceed?") }}
                            </slot>
                        </div>

                        <div class="modal-footer">
                            <slot name="modal-footer">
                                <button type="button" class="btn btn-light" @click="$emit('close')">Cancel</button>
                            </slot>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-mask modal"></div>
        </div>
    </transition>
</template>

<script>
    'use strict';
    
    export default {
        data(){
            return{
                modal_w : this.modal_width,
            }
        },
        props: {
            modal_width: String,
        }
    }
</script>
