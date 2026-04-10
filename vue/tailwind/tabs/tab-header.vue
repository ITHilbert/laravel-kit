<template>
    <li class="block" role="presentation">
        <button 
            type="button" 
            :class="['tab-header', { 'active': active }, addClass]" 
            :id="navLinkId" 
            data-bs-toggle="tab" 
            :data-bs-target="navTabId" 
            role="tab" 
            :aria-controls="name" 
            :aria-selected="active" 
            v-bind="$attrs"
        >
            <slot></slot>
        </button>
    </li>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    name: string;
    active?: boolean;
    addClass?: string;
}>();

defineOptions({ inheritAttrs: false });

const navLinkId = computed(() => `${props.name}-tab`);
const navTabId = computed(() => `#${props.name}`);
</script>
