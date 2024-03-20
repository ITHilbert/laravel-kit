<template>
    <a v-if="type === 'link'" v-bind:href="route" class="btn btn-delete" data-toggle="tooltip" :title="tooltip"><i class="fas fa-minus-circle"></i> <slot></slot></a>
    <button v-else v-on:click="OpenDeleteForm()" class="btn btn-delete" data-toggle="tooltip" :title="tooltip"><i class="fas fa-minus-circle"></i> <slot></slot></button>
</template>



<script>
    export default {
        props: {
            'route': {
                default: '#'
            },
            'type': {
                default: 'button'
            },
            'value': {
                default: 0
            },
            'tooltip': {
                default: 'Löschen'
            }
        },
        /* methods: {
            OpenDeleteForm: function () {
                deleteID = this.value;
                $('#myModalDelete').modal('show');
            }
        } */
    }
</script>
