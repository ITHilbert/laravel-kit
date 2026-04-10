<template>
    <a v-if="type === 'link'" :href="route" class="btn btn-show" :title="tooltip">
        <i class="fas fa-file-contract"></i> <slot />
    </a>
    <button v-else :type="type" class="btn btn-show" :title="tooltip">
        <i class="fas fa-file-contract"></i> <slot />
    </button>
</template>

<script setup lang="ts">
defineProps({
    route:   { type: String, default: '#' },
    type:    { type: String, default: 'link' },
    tooltip: { type: String, default: 'Anzeigen' },
});
</script>