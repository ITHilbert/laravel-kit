<template >
    <input :type="type" class="form-control input-password">
</template>

<script>
    export default {
        props: {
            'type': {
                default: 'password'
            },
        }    
    }
</script>