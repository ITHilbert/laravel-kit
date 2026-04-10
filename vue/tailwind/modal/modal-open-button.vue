<template>
    <button type="button" class="modal-button-open" data-bs-toggle="modal" :data-bs-target="modalTarget" v-bind="$attrs">
        <slot></slot>
    </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    modalId: string;
}>();

const modalTarget = computed(() => `#${props.modalId}`);

defineOptions({ inheritAttrs: false });
</script>
