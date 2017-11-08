<template>
    <div class="side-nav pl-sm-0 pl-lg-4 pl-xl-4 active">
        <ul class="list-unstyled modules">
            <li class="module" v-for="(menu, menu_key_id) in menu_sorted" v-bind:key="menu.sort_order" :class="{ '': menu.menu_key == menu_key}">
                
                <a :href="(typeof menu.sub_menu == 'undefined' || menu.sub_menu == '')?menu.route:'#menu'+menu_key_id" :data-toggle="(typeof menu.sub_menu != 'undefined')?'collapse':''" aria-expanded="false" :class="{ 'dropdown-toggle': typeof menu.sub_menu != 'undefined', 'selected-menu': menu.menu_key == menu_key }" class="module-parent">
                    <span class="module-parent-text text-truncate"><i :class="(menu.icon != '')?menu.icon:''" class="main-menu-icon"></i> {{ $t(menu.label) }}</span>
                </a>

                <ul v-if="typeof menu.sub_menu != 'undefined'" class="collapse list-unstyled" :class="(menu.sub_menu != '' && menu.menu_key == menu_key)?'show in':''" :id="'menu'+menu_key_id" aria-expanded="false">
                     
                    <li v-for="sub_menu in menu.sub_menu" v-bind:key="sub_menu.sub_menu_id" :class="((sub_menu.menu_key != '' && sub_menu_key != '')?((sub_menu.menu_key == sub_menu_key)?'selected-menu':''):'')" class="module-child">
                        <a :href="(sub_menu.route && sub_menu.route!='')?sub_menu.route:'#'" :class="((sub_menu.menu_key != '' && sub_menu_key != '')?((sub_menu.menu_key == sub_menu_key)?'':''):'')" class="module-child-text text-truncate">{{ $t(sub_menu.label) }}</a>
                    </li>

                </ul>

            </li>
        </ul>
    </div>
</template>

<script>
    'use strict';
    
    export default {
        data(){
            return{

            }
        },
        props: {
            menus: [Array, Object],
            menu_key: String,
            sub_menu_key: String,
        },
        computed: {
            menu_sorted() {
                return _.orderBy(this.menus, 'sort_order', 'asc'); 
            },
        },
        mounted() {
            console.log('Side Navigation loaded');
        },
        methods: {
           
        }
    }
</script>