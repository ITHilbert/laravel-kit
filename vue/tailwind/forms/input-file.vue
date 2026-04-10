<template>
    <input type="file" :id="name" :name="name" class="input-file px-3 py-2 block w-full rounded-md border border-gray-300 shadow-sm focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 dark:focus:ring-blue-500" v-bind="$attrs" />
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                default: '',
            },
        },
    }
</script>
