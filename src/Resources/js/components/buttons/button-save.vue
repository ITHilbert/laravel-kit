<template>
    <a v-if="type === 'link'" v-bind:href="route" class="btn btn-save" data-toggle="tooltip" :title="tooltip"><i class="fas fa-save"></i> <slot></slot></a>
    <button v-else v-bind:type="type" class="btn btn-save" data-toggle="tooltip" :title="tooltip"><i class="fas fa-save"></i> <slot></slot></button>
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