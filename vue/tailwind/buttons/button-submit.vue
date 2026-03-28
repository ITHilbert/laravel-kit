<template>
    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 font-medium text-sm rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors btn-submit" data-toggle="tooltip" :title="tooltip"><slot></slot></button>
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