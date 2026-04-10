<template>
    <a v-if="type === 'link'" :href="route" class="btn btn-back" :title="tooltip">
        <i class="fas fa-undo"></i> <slot />
    </a>
    <button v-else :type="type" class="btn btn-back" :title="tooltip">
        <i class="fas fa-undo"></i> <slot />
    </button>
</template>

<script setup lang="ts">
defineProps({
    route:   { type: String,  default: '#' },
    type:    { type: String,  default: 'link' },
    tooltip: { type: String,  default: 'Zurück' },
});
</script>