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
                defaultclass: 'px-3 py-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 text-area ',
            }
        },

        methods: {
            getDefaultValue() {
                if (this.$slots.default) {
                    let defaultSlot = typeof this.$slots.default === 'function' ? this.$slots.default() : this.$slots.default;
                    if (defaultSlot && defaultSlot.length > 0) {
                        return defaultSlot[0].children || defaultSlot[0].text || '';
                    }
                }
                return this.value;
            }
        },
    }
</script>
