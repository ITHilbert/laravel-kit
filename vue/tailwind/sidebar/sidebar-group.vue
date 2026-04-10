<template>
    <li class="sidebar-group" v-bind="$attrs">
        <slot></slot>
    </li>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
