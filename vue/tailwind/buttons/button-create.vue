<template>
    <a v-if="type === 'link'" :href="route" class="btn btn-create" :title="tooltip">
        <i class="fas fa-plus"></i> <slot />
    </a>
    <button v-else :type="type" class="btn btn-create" :title="tooltip">
        <i class="fas fa-plus"></i> <slot />
    </button>
</template>

<script setup lang="ts">
defineProps({
    route:   { type: String, default: '#' },
    type:    { type: String, default: 'link' },
    tooltip: { type: String, default: 'Neu erstellen' },
});
</script>
