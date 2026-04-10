<template>
    <input type="color" :id="name" :name="name" class="input-color" v-bind="$attrs">
</template>

<script setup lang="ts">
defineProps<{ name?: string }>();
defineOptions({ inheritAttrs: false });
</script>
