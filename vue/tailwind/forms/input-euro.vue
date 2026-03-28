<template>
    <div class="flex rounded-md shadow-sm w-full">
        <input type="number" :id="name" :name="name" :class="['block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 input-euro', addClass]" :step="step" lang="de-DE" aria-label="Euro" v-bind="$attrs" style="-webkit-appearance: none; -moz-appearance: textfield;">
        <span class="inline-flex items-center px-6 border border-gray-200 dark:border-gray-700 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400"> &euro; </span>
    </div>
</template>



<script>
    export default {
        props: {
            step: {
                type: Number,
                default: 0.01,
            },
            invalid: {
                type: Boolean,
                default: false,
            },
            addClass: {
                type: String,
                default: '',
            },
            name: {
                type: String,
                default: '',
            },
        },
    }
</script>
