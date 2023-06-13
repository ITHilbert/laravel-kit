<template v-slot="default">
    <textarea :class="[defaultclass, $attrs.class, addClass]" :rows="rows" v-model="currentValue" v-bind="$attrs"><slot></slot></textarea>
</template>

<script>
    export default {
        props: {
            'rows': {
                default: '3'
            },
            value: {
                required: false,
                type: String,
                default: '',
            },
            addClass: {
                default: '',
            },
        },
        data() {
            return {
                currentValue: this.getDefaultValue(),
                defaultclass: 'form-control text-area ',
            }
        },

        methods: {
            getDefaultValue() {
                if (this.$slots.default && this.$slots.default.length) {
                    return this.$slots.default[0].text
                }

                return this.value
            }
        },
    }
</script>
