<template>
    <p class="sidebar-header-label" v-bind="$attrs">
        <slot></slot>
    </p>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
