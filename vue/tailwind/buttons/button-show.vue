<template>
    <a v-if="type === 'link'" v-bind:href="route" class="inline-flex items-center justify-center px-4 py-2 font-medium text-sm rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors btn-show" data-toggle="tooltip" :title="tooltip"><i class="fas fa-file-contract"></i> <slot></slot></a>
    <button v-else v-bind:type="type" class="inline-flex items-center justify-center px-4 py-2 font-medium text-sm rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors btn-show" data-toggle="tooltip" :title="tooltip"><i class="fas fa-file-contract"></i> <slot></slot></button>
</template>


<script>
    export default {
        props: {
            'route': {
                default: '#'
            },
            'type': {
                default: 'link'
            },
            'tooltip': {
                default: 'Anzeigen'
            }
        }
    }
</script>