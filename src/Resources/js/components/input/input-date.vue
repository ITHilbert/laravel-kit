<template >
    <input :type="type" class="form-control input-date">
</template>

<script>
    export default {
        props: {
            'type': {
                default: 'date'
            },
        }    
    }
</script>