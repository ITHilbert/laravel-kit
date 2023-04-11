<template>
    <div v-bind:class="defaultclassheader + classheader" v-bind="$attrs">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: {
          'classheader': {
              default: 'font-weight-bold'
          },
        },
        data: function(){
            return {
                'defaultclasscard': 'card ',
                'defaultclassheader': 'card-header ',
            }
        },
    }
</script>
