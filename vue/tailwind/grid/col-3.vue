<template>
    <div class="col-3" v-bind="$attrs"><slot></slot></div>
</template>
<script setup lang="ts">
defineOptions({ inheritAttrs: false });
</script>
