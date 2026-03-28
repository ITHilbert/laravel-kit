<template >
   <div class="flex items-center gap-2 mb-2">
        <input v-if="check() === true" type="checkbox" :value="value" :class="['h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600', addclass]" :name="name" :id="name" v-bind="$attrs" checked>
        <input v-else type="checkbox" :value="value" :class="['h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600', addclass]" :name="name" :id="name" v-bind="$attrs">
        <label class="text-sm text-gray-900 dark:text-gray-300" :for="name"><slot></slot></label>
    </div>

</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            'name': {
                type: String,
                default: ''
            },
            'addclass': {default: ''},
            'value': {default: 1},
        },
        methods: {
            check: function () {
                if(this.value == 1 || this.value == true){
                    return true;
                }else{
                    return false;
                }
            }
        }
    }
</script>

<style>
.form-check-label{
    font-size: inherit;
    font-weight: 700;
    line-height: 1.49;
    color: var(--phoenix-gray-700);
}
</style>
