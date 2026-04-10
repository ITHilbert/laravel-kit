<template>
    <button type="submit" class="btn btn-submit" :title="tooltip">
        <slot />
    </button>
</template>

<script setup lang="ts">
defineProps({
    tooltip: { type: String, default: '' },
});
</script>