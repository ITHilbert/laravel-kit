<template >
    <input type="color" :class="['input-color px-3 py-2 block w-full rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500', $attrs.class, addClass]" :id="name" :name="name" v-bind="$attrs">
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
