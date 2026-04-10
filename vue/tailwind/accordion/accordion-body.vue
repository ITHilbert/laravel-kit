<template>
    <div 
        :id="navLinkId" 
        :class="['accordion-collapse', { 'block': active, 'hidden': !active }, $attrs.class]" 
        :aria-labelledby="labelby" 
        v-bind="$attrs"
    >
        <div class="accordion-body">
            <slot></slot>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    name: string;
    active?: boolean;
}>();

defineOptions({ inheritAttrs: false });

const navLinkId = computed(() => `${props.name}-accordion-body`);
const labelby = computed(() => `${props.name}-accordion-header`);
</script>
