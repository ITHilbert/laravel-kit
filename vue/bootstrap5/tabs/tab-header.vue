<template>
    <li class="nav-item" role="presentation">
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
                    'nav-link': true,
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
