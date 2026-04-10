<template>
  <p class="card-text" v-bind="$attrs"><slot></slot></p>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
