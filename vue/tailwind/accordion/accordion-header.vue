<template>
    <h2 class="accordion-header" id="navLinkId">
        <button :class="[paneClasses, $attrs.class]" type="button" data-bs-toggle="collapse" :data-bs-target="target" :aria-expanded="active" :aria-controls="controls">
            <slot></slot>
        </button>
        </h2>
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
                return `${this.name}-accordion-header`
            },
            target(){
                return `#${this.name}-accordion-body`
            },
            controls(){
                return `${this.name}-accordion-body`
            },
            paneClasses() {
                return {
                    'accordion-button': true,
                    'collapsed': !this.active,
                }
            }
        },
    }
</script>
