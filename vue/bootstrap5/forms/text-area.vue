<template v-slot="default">
    <textarea :id="name" :name="name" :class="['form-control text-area', $attrs.class, addClass]" :rows="rows" v-model="currentValue" v-bind="$attrs"><slot></slot></textarea>
</template>

<script>
    export default {
        props: {
            rows: {
                type: Number,
                default: '3'
            },
            value: {
                required: false,
                type: String,
                default: '',
            },
            addClass: {
                type: String,
                default: '',
            },
            name: {
                type: String,
                default: ''
            },
        },
        data() {
            return {
                currentValue: this.getDefaultValue(),
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
