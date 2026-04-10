<template>
    <input type="email" :id="name" :name="name" class="input-email" v-bind="$attrs">
</template>

<script setup lang="ts">
defineProps<{ name?: string }>();
defineOptions({ inheritAttrs: false });
</script>
