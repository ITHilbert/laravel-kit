<template>
    <div class="input-group">
        <input type="number" :id="name" :name="name" :class="['form-control input-euro', addClass]" :step="step" lang="de-DE" aria-label="Euro" v-bind="$attrs" style="-webkit-appearance: none; -moz-appearance: textfield;">
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
                type: String,
                default: '',
            },
            name: {
                type: String,
                default: '',
            },
        },
    }
</script>
