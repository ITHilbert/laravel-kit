<template >
   <div class="form-check">
        <input type="checkbox" :value="value" :class="['form-check-input', addclass]" :name="name" :id="name" v-bind="$attrs" :checked="angehakt()">
        <label class="form-check-label" :for="name"><slot></slot></label>
    </div>

</template>

<script>
    import { istAngehakt } from '../../shared/checkboxChecked.mjs';

    export default {
        inheritAttrs: false,
        props: {
            'name': {
                type: String,
                default: ''
            },
            'addclass': {default: ''},
            'value': {default: 1},
            'checked': {},
        },
        methods: {
            angehakt: function () {
                return istAngehakt(this.checked, this.value);
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
