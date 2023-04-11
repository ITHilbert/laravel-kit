<template >
    <input :type="type" class="form-control input-int">
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