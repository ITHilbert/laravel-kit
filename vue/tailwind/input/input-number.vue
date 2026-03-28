<template >
    <input type="number" :class="[defaultclass, $attrs.class, addClass]" :step="step" >
</template>

<script>
export default {
    props: {
        step: {
            type: Number,
            default: 0.01,
        },
        addClass: {
            default: '',
        },
    },
    data: function () {
        return {
            defaultclass: 'block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 input-number ',
        };
    },
};
</script>
