<template>
    <h2 class="accordion-header" :id="navLinkId">
        <button 
            type="button" 
            :class="['accordion-button', { 'collapsed': !active }, $attrs.class]" 
            :aria-expanded="active" 
            :aria-controls="controls"
            @click="toggle"
        >
            <slot></slot>
        </button>
    </h2>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    name: string;
    active?: boolean;
}>();

const emit = defineEmits(['toggle']);

defineOptions({ inheritAttrs: false });

const navLinkId = computed(() => `${props.name}-accordion-header`);
const controls = computed(() => `${props.name}-accordion-body`);

function toggle() {
    emit('toggle');
}
</script>
