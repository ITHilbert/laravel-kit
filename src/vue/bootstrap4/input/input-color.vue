<template >
    <input :type="type" class="form-control input-color">
</template>

<script>
    export default {
        props: {
            'type': {
                default: 'color'
            },
        }
    }
</script>
