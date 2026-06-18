<template>
    <div
        :class="['tab-body', { active, show: active }]"
        :id="name"
        role="tabpanel"
        :aria-labelledby="`${name}-tab`"
        v-bind="$attrs"
    >
        <slot></slot>
    </div>
</template>

<script setup lang="ts">
import { computed, inject, ref } from 'vue';
import type { Ref } from 'vue';

const props = defineProps<{
    name: string;
}>();

defineOptions({ inheritAttrs: false });

const activeTab = inject<Ref<string>>('tabsActive', ref(''));

const active = computed(() => activeTab.value === props.name);
</script>
