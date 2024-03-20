<template>
    <div :class="['card card-main', classcard]" v-bind="$attrs">
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
