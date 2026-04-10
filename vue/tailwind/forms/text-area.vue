<template>
    <textarea 
        :id="name" 
        :name="name" 
        class="text-area" 
        :rows="rows" 
        :value="modelValue || defaultContent"
        @input="$emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
        v-bind="$attrs"
    ></textarea>
</template>

<script setup lang="ts">
import { useSlots, computed } from 'vue';

const props = defineProps<{
    name?: string;
    rows?: number | string;
    modelValue?: string;
}>();

const emit = defineEmits(['update:modelValue']);
defineOptions({ inheritAttrs: false });

const slots = useSlots();

const defaultContent = computed(() => {
    if (slots.default) {
        const defaultSlot = slots.default();
        if (defaultSlot && defaultSlot.length > 0) {
            return defaultSlot[0].children || '';
        }
    }
    return '';
});
</script>
