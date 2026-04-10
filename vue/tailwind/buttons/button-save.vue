<template>
    <a v-if="type === 'link'" :href="route" class="btn btn-save" :title="tooltip">
        <i class="fas fa-save"></i> <slot />
    </a>
    <button v-else :type="type" class="btn btn-save" :title="tooltip">
        <i class="fas fa-save"></i> <slot />
    </button>
</template>

<script setup lang="ts">
defineProps({
    route:   { type: String, default: '#' },
    type:    { type: String, default: 'submit' },
    tooltip: { type: String, default: 'Speichern' },
});
</script>