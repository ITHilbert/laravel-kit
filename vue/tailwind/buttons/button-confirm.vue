<template>
    <a v-if="type === 'link'" :href="route" class="btn btn-confirm" :title="tooltip">
        <i class="fas fa-check"></i> <slot />
    </a>
    <button v-else :type="type" class="btn btn-confirm" :title="tooltip">
        <i class="fas fa-check"></i> <slot />
    </button>
</template>

<script setup lang="ts">
defineProps({
    route:   { type: String, default: '#' },
    type:    { type: String, default: 'button' },
    tooltip: { type: String, default: 'Bestätigen' },
});
</script>
