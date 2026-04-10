<template>
    <a v-if="type === 'link'" v-bind:href="route" class="inline-flex items-center justify-center px-4 py-2 font-medium text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors btn-save" data-toggle="tooltip" :title="tooltip"><i class="fas fa-save mr-2"></i><slot></slot></a>
    <button v-else v-bind:type="type" class="inline-flex items-center justify-center px-4 py-2 font-medium text-sm text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors btn-save" data-toggle="tooltip" :title="tooltip"><i class="fas fa-save mr-2"></i><slot></slot></button>
</template>


<script>
    export default {
        props: {
            'route': {
                default: '#'
            },
            'type': {
                default: 'submit'
            },
            'tooltip': {
                default: ''
            }
        }
    }
</script>