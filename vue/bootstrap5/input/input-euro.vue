<template>
    <div class="input-group">
        <input type="number" :class="[defaultclass, addClass]" :step="step" lang="de-DE" aria-label="Euro" v-bind="$attrs" style="-webkit-appearance: none; -moz-appearance: textfield;">
        <span class="input-group-text"> &euro; </span>
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
                default: '',
            },
        },
        data: function () {
            return {
                defaultclass: 'form-control input-euro ',
            };
        }
    }
</script>
