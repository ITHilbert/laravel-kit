<template>
    <div class="input-group">
        <input type="number" :class="[defaultclass, addClass]" :step="step" aria-label="Euro" v-bind="$attrs">
        <div class="input-group-append">
            <span class="input-group-text"> &euro; </span>
        </div>
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
        },
    }
</script>
