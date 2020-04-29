<template>
    <a v-if="type === 'link'" v-bind:href="route" class="btn btn-create" data-toggle="tooltip" :title="tooltip"><i class="fas fa-plus-circle"></i> <slot></slot></a>
    <button v-else v-bind:type="type" class="btn btn-create" data-toggle="tooltip" :title="tooltip"><i class="fas fa-plus-circle"></i> <slot></slot></button>
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
                default: 'Neuen Eintrag erstellen'
            }
        }
    }
</script>