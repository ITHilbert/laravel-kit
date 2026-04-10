<template>
    <a v-if="type === 'link'" :href="route" class="btn btn-cancel" :title="tooltip">
        <i class="fas fa-times"></i> <slot />
    </a>
    <button v-else :type="type" class="btn btn-cancel" :title="tooltip">
        <i class="fas fa-times"></i> <slot />
    </button>
</template>

<script setup lang="ts">
defineProps({
    route:   { type: String, default: '#' },
    type:    { type: String, default: 'button' },
    tooltip: { type: String, default: 'Abbrechen' },
});
</script>