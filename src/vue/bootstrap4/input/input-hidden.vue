<template >
    <input :type="type">
</template>

<script>
    export default {
        props: {
            'type': {
                default: 'hidden'
            },
        }    
    }
</script>