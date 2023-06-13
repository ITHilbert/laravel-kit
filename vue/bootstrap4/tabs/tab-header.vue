<template>
    <li class="nav-item" role="presentation">
        <button :class="[paneClasses, addClass]" :id="navLinkId" data-toggle="tab" :data-target="navTabId" type="button" role="tab" :aria-controls="name" :aria-selected="active" v-bind="$attrs"><slot></slot></button>
    </li>
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            'name': {
                type: String,
                default: ''
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
                return `nav-${this.name}-tab`
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
