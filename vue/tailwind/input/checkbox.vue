<template >
   <div class="flex items-center gap-2 mb-2">
        <input v-if="check() === true" type="checkbox" :value="value" :class="defaultclass + addclass" v-bind="$attrs" checked>
        <input v-else type="checkbox" :value="value" :class="defaultclass + addclass" v-bind="$attrs">
        <label class="text-sm text-gray-900 dark:text-gray-300" :for="id"><slot></slot></label>
    </div>
    
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            'id': {},
            'addclass': {default: ''},
            'checked': {default: false},
            'value': {default: 1},
        },
        data: function(){
            return {
                'defaultclass': 'h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600 ',

            }
        },
        methods: {
            check: function () {
                if(this.checked == 1 || this.checked == true){
                    return true;
                }else{
                    return false;
                }
            }
        }     
    }
</script>