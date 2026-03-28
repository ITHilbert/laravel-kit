<template>
    <div :class="[paneClasses, $attrs.class]" :id="name" role="tabpanel" :aria-labelledby="navLinkId" v-bind="$attrs"><slot></slot></div>
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
            }
        },
        computed: {
            navLinkId() {
                return `${this.name}-tab`
            },
            paneClasses() {
                return {
                    'tab-body': true,
                    'tab-pane': true,
                    'fade': true,
                    'show': this.active,
                    'active': this.active
                }
            }
        },
    }
</script>
