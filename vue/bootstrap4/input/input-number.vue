<template >
    <input type="number" :class="[defaultclass, $attrs.class, addClass]" :step="step" >
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
    },
    data: function () {
        return {
            defaultclass: 'form-control input-number ',
        };
    },
};
</script>
