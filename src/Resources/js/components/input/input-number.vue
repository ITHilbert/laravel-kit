<template >
    <input :type="type" class="form-control input-number">
</template>

<script>
    export default {
        props: {
            'type': {
                default: 'text'
            },
        }    
    }
</script>