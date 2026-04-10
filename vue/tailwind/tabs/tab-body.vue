<template>
    <div 
        :class="['tab-body', { 'active': active, 'show': active }, $attrs.class]" 
        :id="name" 
        role="tabpanel" 
        :aria-labelledby="navLinkId" 
        v-bind="$attrs"
    >
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    name: string;
    active?: boolean;
}>();

defineOptions({ inheritAttrs: false });

const navLinkId = computed(() => `${props.name}-tab`);
</script>
