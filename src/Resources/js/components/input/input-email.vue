<template >
    <input :type="type" class="form-control input-text">
</template>

<script>
    export default {
        props: {
            'type': {
                default: 'email'
            },
        }    
    }
</script>