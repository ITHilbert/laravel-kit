<template>
    <div class="tabs" v-bind="$attrs"><slot></slot></div>
</template>

<script setup lang="ts">
import { provide, ref } from 'vue';

const props = defineProps<{
    default?: string;
}>();

defineOptions({ inheritAttrs: false });

const activeTab = ref<string>(props.default ?? '');

provide('tabsActive', activeTab);
provide('tabsSelect', (name: string) => {
    activeTab.value = name;
});
</script>
