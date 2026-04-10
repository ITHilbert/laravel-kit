<template>
    <div class="accordion-item" v-bind="$attrs"><slot></slot></div>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
