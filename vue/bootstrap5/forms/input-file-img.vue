<template>
    <input type="file" :id="name" :name="name" class="form-control" accept="image/png, image/jpeg" v-bind="$attrs" />
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
