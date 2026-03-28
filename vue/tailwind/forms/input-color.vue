<template >
    <input type="color" :class="['block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 input-color', $attrs.class, addClass]" :id="name" :name="name" v-bind="$attrs">
</template>

<script>
    export default {
        props: {
            addClass: {
                type: String,
                default: '',
            },
            name: {
                type: String,
                default: '',
            }
        },
    }
</script>
