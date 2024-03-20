<template>
    <div v-bind:class="defaultclasscard + classcard" v-bind="$attrs">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: {
          'classcard': {
              default: 'bg-light mb-3'
          },
        },
        data: function(){
            return {
                'defaultclasscard': 'card '
            }
        },
    }
</script>
