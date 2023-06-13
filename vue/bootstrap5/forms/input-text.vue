<template >
    <input type="text" :id="name" :name="name" :class="['form-control input-text']" v-bind="$attrs">
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                default: '',
            },
        },
    }
</script>
