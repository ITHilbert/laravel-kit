<template >
    <input type="email" :id="name" :name="name" :class="['form-control input-email', $attrs.class, addClass]" v-bind="$attrs">
</template>

<script>
    export default {
        props: {
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
