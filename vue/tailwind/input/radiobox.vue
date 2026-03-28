<template >
   <div class="flex items-center gap-2 mb-2">
        <input type="radio" :id="id" :class="[defaultclass, $attrs.class, addClass]" v-bind="$attrs">
        <label class="text-sm text-gray-900 dark:text-gray-300" :for="id"><slot></slot></label>
    </div>

</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            'id': {},
                addClass: {
                default: '',
            },
        },
        data: function(){
            return {
                'defaultclass': 'h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 radiobox ',
            }
        }
    }
</script>
