<template>
    <button :class="paneClasses" :id="navLinkId" data-bs-toggle="tab" :data-bs-target="navTabId" type="button" role="tab" :aria-controls="navTabId" aria-selected="true" v-bind="$attrs"><slot></slot></button>
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
            }
        },
        computed: {
            navLinkId() {
                return `nav-${this.name}-tab`
            },
            navTabId() {
                return `nav-${this.name}`
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
