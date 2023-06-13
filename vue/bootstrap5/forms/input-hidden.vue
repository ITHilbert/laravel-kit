<template >
    <input type="hidden" :class="[$attrs.class, addClass]" v-bind="$attrs">
</template>

<script>
    export default {
        props: {
            addClass: {
              default: '',
            },
        }
    }
</script>
