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

/**
 * Tab-Header registrieren sich beim Mount. Ist noch kein Tab aktiv (kein
 * `default` gesetzt), wird der zuerst registrierte Tab automatisch aktiviert —
 * so ist immer genau ein Tab sichtbar, ohne dass der Aufrufer `default` setzen muss.
 */
const register = (name: string): void => {
    if (activeTab.value === '') {
        activeTab.value = name;
    }
};

provide('tabsActive', activeTab);
provide('tabsSelect', (name: string): void => {
    activeTab.value = name;
});
provide('tabsRegister', register);
</script>
