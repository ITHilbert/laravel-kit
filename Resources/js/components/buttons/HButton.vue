<template >
    <a v-if="type === 'link'" v-bind:href="route" class="btn" data-toggle="tooltip" :title="tooltip"><slot></slot></a>
    <button v-else v-bind:type="type" class="btn" data-toggle="tooltip" :title="tooltip"><slot></slot></button>
</template>

<script>
    export default {
        props: {
            'type': {
                default: 'link'
            },
            'route': {
                default: '#'
            },
            'tooltip': {
                default: ''
            }
          
        }    
    }
</script>