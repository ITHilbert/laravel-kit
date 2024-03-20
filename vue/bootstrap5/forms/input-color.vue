<template >
    <input type="color" :class="['form-control input-color', $attrs.class, addClass]" :id="name" :name="name" v-bind="$attrs">
</template>

<script>
    export default {
        props: {
            addClass: {
                type: String,
                default: '',
            },
            name: {
                type: String,
                default: '',
            }
        },
    }
</script>
