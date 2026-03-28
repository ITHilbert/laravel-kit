<template>
    <li class="block" role="presentation">
        <button :class="[paneClasses, addClass]" :id="navLinkId" data-bs-toggle="tab" :data-bs-target="navTabId" type="button" role="tab" :aria-controls="name" :aria-selected="active" v-bind="$attrs"><slot></slot></button>
    </li>
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            'name': {
                type: String,
                required: true
            },
            'active': {
                type: Boolean,
                default: false
            },
            'addClass':{
                type: String,
                default: ''
            }
        },
        computed: {
            navLinkId() {
                return `${this.name}-tab`
            },
            navTabId() {
                return `#${this.name}`
            },

            paneClasses() {
                return {
                    'tab-header': true,
                    'block px-6 py-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded transition-colors': true,
                    'active': this.active
                }
            }
        }
    }
</script>


<style>
    button.tab-header.nav-link.active{
        border-top: 1px #ddd solid !important;
        border-right: 1px #ddd solid !important;
        border-left: 1px #ddd solid !important;
    }
</style>
