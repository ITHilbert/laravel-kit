<template>
    <ul class="tabs-header" role="tablist" v-bind="$attrs"><slot></slot></ul>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
