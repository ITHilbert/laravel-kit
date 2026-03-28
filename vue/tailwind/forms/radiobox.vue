<template >
   <div class="flex items-center gap-2 mb-2">
        <input type="radio" :id="id" :class="['h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 radiobox', $attrs.class, addClass]" v-bind="$attrs" :checked="active">
        <label class="text-sm text-gray-900 dark:text-gray-300" :for="id"><slot></slot></label>
    </div>

</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            id: {
                type: String,
                default: ''
            },
            active: {
                type: Boolean,
                default: false
            },
            addClass: {
                type: String,
                default: '',
            },
        },
    }
</script>
