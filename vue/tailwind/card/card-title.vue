<template>
  <h5 class="card-title" v-bind="$attrs"><slot></slot></h5>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
