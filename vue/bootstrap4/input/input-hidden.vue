<template >
    <input :type="type" :class="[$attrs.class, addClass]" v-bind="$attrs">
</template>

<script>
    export default {
        props: {
            'type': {
                default: 'hidden'
            },
            addClass: {
              default: '',
            },
        }
    }
</script>
