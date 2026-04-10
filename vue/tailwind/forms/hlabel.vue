<template>
    <label class="hlabel" v-bind="$attrs"><slot></slot></label>
</template>

<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
