<template >
    <input :type="type" :class="[defaultclass, $attrs.class, addClass]" v-bind="$attrs">
</template>

<script>
    export default {
        props: {
            'type': {
                default: 'color'
            },
            addClass: {
                default: '',
            },
        },
        data: function () {
            return {
                defaultclass: 'form-control input-color ',
            };
        },
    }
</script>
