<template >
    <input :type="type" :class="[defaultclass, $attrs.class, addClass]" :step="step" v-bind="$attrs">
</template>

<script>
    export default {
        props: {
            'type': {
                default: 'number'
            },
            step: {
                type: Number,
                default: 1,
            },
            addClass: {
                default: '',
            },
        },
        data: function () {
            return {
                defaultclass: 'form-control input-int ',
            };
        },
    }
</script>

