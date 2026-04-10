<template>
    <input type="text" :id="name" :name="name" class="input-text" v-bind="$attrs">
</template>

<script setup lang="ts">
defineProps<{ name?: string }>();
defineOptions({ inheritAttrs: false });
</script>
