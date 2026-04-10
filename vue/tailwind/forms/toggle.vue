<template>
    <button 
        type="button" 
        @click="toggle" 
        :class="['toggle', { 'is-on': isActive }]" 
        :value="isActive"
        :aria-pressed="isActive"
    >
        <span aria-hidden="true"></span>
    </button>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';

const props = defineProps<{
    modelValue?: boolean;
}>();

const emit = defineEmits(['update:modelValue', 'toggle']);

const isActive = ref(props.modelValue || false);

watch(() => props.modelValue, (newVal) => {
    isActive.value = !!newVal;
});

function toggle() {
    isActive.value = !isActive.value;
    emit('update:modelValue', isActive.value);
    emit('toggle', isActive.value);
}
</script>
