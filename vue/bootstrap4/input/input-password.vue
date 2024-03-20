<template >
    <input :type="type" :class="[defaultclass, $attrs.class, addClass]" v-bind="$attrs">
</template>

<script>
    export default {
        props: {
            'type': {
                default: 'password'
            },
            addClass: {
                default: '',
            },
        },
        data: function () {
            return {
                defaultclass: 'form-control input-password ',
            };
        },
    }
</script>
