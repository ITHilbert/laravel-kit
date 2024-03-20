<template >
    <input type="date" :id="name" :name="name" :class="['form-control input-date', $attrs.class, addClass]" v-bind="$attrs">
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
            }
        },
    }
</script>
