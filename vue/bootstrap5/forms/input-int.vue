<template >
    <input type="number" :id="name" :name="name" :class="['form-control input-int', $attrs.class, addClass]" :step="step" v-bind="$attrs">
</template>

<script>
    export default {
        props: {
            step: {
                type: Number,
                default: 1,
            },
            addClass: {
                default: '',
            },
            name: {
                type: String,
                default: '',
            },
        },
    }
</script>

