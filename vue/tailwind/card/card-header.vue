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
                'defaultclasscard': 'bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 flex flex-col ',
                'defaultclassheader': 'px-6 py-6 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-t-lg font-bold text-gray-900 dark:text-white ',
            }
        },
    }
</script>
