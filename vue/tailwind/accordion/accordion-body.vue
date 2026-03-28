<template>
    <div :class="[paneClasses, $attrs.class]" :id="navLinkId" aria-labelledby="labelby" v-bind="$attrs">
        <div class="accordion-body pt-0">
            <slot></slot>
        </div>
    </div>
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
                return `${this.name}-accordion-body`
            },
            labelby(){
                return `${this.name}-accordion-header`
            },
            paneClasses() {
                return {
                    'accordion-collapse': true,
                    'collapse': true,
                    'show': this.active,
                }
            }
        },
    }
</script>
