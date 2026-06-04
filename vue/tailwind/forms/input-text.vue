<template>
    <div class="w-full">
        <label v-if="label" :for="id || name" class="hlabel">{{ label }}</label>
        <input type="text" :id="id || name" :name="name" class="input-text" v-bind="$attrs">
    </div>
</template>

<script setup lang="ts">
defineProps<{ name?: string; id?: string; label?: string }>();
defineOptions({ inheritAttrs: false });
</script>
