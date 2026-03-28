<template>
    <select :id="name" :name="name" :value="value" :class="['block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:border-blue-500 combobox', $attrs.class, addClass]"><slot></slot></select>
</template>


<script>
export default{
    props: {
        'name': {
            required: true,
            type: String,
        },
        'value': {
            required: true,
        },
        addClass: {
            default: '',
        },
    }
}
</script>
