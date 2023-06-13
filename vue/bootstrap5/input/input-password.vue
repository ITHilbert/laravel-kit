<template >
    <div class="input-group">
        <input :type="type" :class="[defaultclass, $attrs.class, addClass]" v-bind="$attrs">
        <span class="input-group-text"><span data-feather="key"></span></span>
    </div>
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
