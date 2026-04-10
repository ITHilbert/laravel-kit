<template>
    <div class="flex flex-col gap-4 mb-6" v-bind="$attrs"><slot></slot></div>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
