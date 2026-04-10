<template>
    <input type="date" :id="name" :name="name" class="input-date" v-bind="$attrs">
</template>

<script setup lang="ts">
defineProps<{ name?: string }>();
defineOptions({ inheritAttrs: false });
</script>
