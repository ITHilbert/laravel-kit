<template>
    <div class="show-txt" v-bind="$attrs">
        {{ value }}
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
withDefaults(defineProps<{
    value?: string | number;
}>(), {
    value: ''
});

defineOptions({ inheritAttrs: false });
</script>
