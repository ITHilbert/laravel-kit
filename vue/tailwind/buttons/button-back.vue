<template>
    <a v-if="type === 'link'" :href="route" class="btn btn-back" :title="tooltip">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
        <slot />
    </a>
    <button v-else :type="type" class="btn btn-back" :title="tooltip">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <polyline points="15 18 9 12 15 6"/>
        </svg>
        <slot />
    </button>
</template>

<script setup lang="ts">
defineProps({
    route:   { type: String,  default: '#' },
    type:    { type: String,  default: 'link' },
    tooltip: { type: String,  default: 'Zurück' },
});
</script>
