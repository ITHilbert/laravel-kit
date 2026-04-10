<template>
    <input type="number" :id="name" :name="name" class="input-int" :step="step" v-bind="$attrs">
</template>

<script setup lang="ts">
defineProps<{ name?: string; step?: number }>();
defineOptions({ inheritAttrs: false });
</script>
