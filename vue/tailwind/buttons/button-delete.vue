<template>
    <a v-if="type === 'link'" :href="route" class="btn btn-delete" :title="tooltip">
        <i class="fas fa-minus-circle"></i> <slot />
    </a>
    <button v-else :type="type" class="btn btn-delete" :title="tooltip">
        <i class="fas fa-minus-circle"></i> <slot />
    </button>
</template>

<script setup lang="ts">
defineProps({
    route:   { type: String, default: '#' },
    type:    { type: String, default: 'button' },
    tooltip: { type: String, default: 'Löschen' },
});
</script>
