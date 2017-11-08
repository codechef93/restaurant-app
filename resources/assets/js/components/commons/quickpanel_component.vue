<template>
    <transition name="modal">
        <div>
            <div class="p-0 bg-white hidden quickpanel" v-bind:class="[ panel_classes ]">
                <div class="">
                    
                    <div class="border-bottom p-0">
                        <div class="d-flex justify-content-between p-3">
                            <div class="mr-auto text-subtitle">
                                <slot name="quick-panel-header">
                                    Quick Panel
                                </slot>
                            </div>
                            
                            <button type="button" @click="$emit('close-quick-panel')" class="close panel-close bg-light ml-auto" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-0">
                        <div class="d-flex flex-column p-3 mb-1">
                            <slot name="quick-panel-body">
                                Quick Panel body
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
                panel_classes  : this.panel_class,
            }
        },
        props: {
            panel_class: String
        }
    }
</script>
