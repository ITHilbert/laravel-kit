<template>
    <li class="block" role="presentation">
        <button
            type="button"
            :class="['tab-header', { active }]"
            :id="`${name}-tab`"
            role="tab"
            :aria-selected="active"
            @click="select(name)"
            v-bind="$attrs"
        >
            <slot></slot>
        </button>
    </li>
</template>

<script setup lang="ts">
import { computed, inject, onMounted, ref } from 'vue';
import type { Ref } from 'vue';

const props = defineProps<{
    name: string;
}>();

defineOptions({ inheritAttrs: false });

const activeTab = inject<Ref<string>>('tabsActive', ref(''));
const select = inject<(name: string) => void>('tabsSelect', () => {});
const register = inject<(name: string) => void>('tabsRegister', () => {});

const active = computed(() => activeTab.value === props.name);

// Beim Mount registrieren, damit ohne `default` der erste Tab aktiv wird.
onMounted(() => register(props.name));
</script>
