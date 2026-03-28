<template >
    <input type="number" :id="name" :name="name" :class="['block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 input-int', $attrs.class, addClass]" :step="step" v-bind="$attrs">
</template>

<script>
    export default {
        props: {
            step: {
                type: Number,
                default: 1,
            },
            addClass: {
                default: '',
            },
            name: {
                type: String,
                default: '',
            },
        },
    }
</script>

