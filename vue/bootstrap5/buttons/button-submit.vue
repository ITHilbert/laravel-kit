<template>
    <button type="submit" class="btn btn-submit" data-toggle="tooltip" :title="tooltip"><slot></slot></button>
</template>


<script>
    export default {
        props: {
            'tooltip': {
                default: ''
            }
        }
    }
</script>