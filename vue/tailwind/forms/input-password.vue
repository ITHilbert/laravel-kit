<template >
    <div class="flex rounded-md shadow-sm w-full">
        <input type="password" :id="name" :name="name" :class="['block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 input-password', $attrs.class, addClass]" v-bind="$attrs">
        <span class="inline-flex items-center px-6 border border-gray-200 dark:border-gray-700 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400"><span data-feather="key"></span></span>
    </div>
</template>

<script>
    export default {
        props: {
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
