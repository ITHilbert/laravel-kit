<template >
   <div class="form-check">
        <input v-if="check() === true" type="checkbox" :value="value" :class="['form-check-input', addclass]" :name="name" :id="name" v-bind="$attrs" checked>
        <input v-else type="checkbox" :value="value" :class="['form-check-input', addclass]" :name="name" :id="name" v-bind="$attrs">
        <label class="form-check-label" :for="name"><slot></slot></label>
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
