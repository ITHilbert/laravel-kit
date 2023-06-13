<template>
    <div :class="['card', classcard]" v-bind="$attrs">
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
    }
</script>
