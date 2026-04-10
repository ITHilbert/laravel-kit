<template>
    <ul class="nav-group" v-bind="$attrs"><slot></slot></ul>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
