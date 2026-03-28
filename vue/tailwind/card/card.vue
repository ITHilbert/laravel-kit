<template>
    <div :class="['bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700 flex flex-col', classcard]" v-bind="$attrs">
        <slot></slot>
    </div>
</template>

<script>
    export default {
        props: {
          'classcard': {
              default: 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-white mb-6'
          },
        },
    }
</script>
