<template >
    <input type="number" :id="name" :name="name" :class="['form-control input-number', $attrs.class, addClass]" :step="step" >
</template>

<script>
export default {
    props: {
        step: {
            type: Number,
            default: 0.01,
        },
        addClass: {
            default: '',
        },
        name: {
            type: String,
            default: '',
        },
    },
};
</script>
