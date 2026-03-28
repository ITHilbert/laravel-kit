<template >
    <div class="flex rounded-md shadow-sm w-full">
        <input :type="type" :class="[defaultclass, $attrs.class, addClass]" aria-label="Prozent" :step="step" v-bind="$attrs">
        <span class="inline-flex items-center px-6 border border-gray-200 dark:border-gray-700 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-gray-400">%</span>
    </div>
</template>


<script>
    export default {
        inheritAttrs: false,
        props: {
            'type': {
                default: 'number'
            },
            step: {
                type: Number,
                default: 0.01,
            },
            'addclass': {
                default: '',
            },
        },
        data: function(){
            return {
                'defaultclass': 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 input-percent ',
            }
        },
    }
</script>
