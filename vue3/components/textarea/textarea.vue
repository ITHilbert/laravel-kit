<script setup lang="ts">
// Mehrzeiliges Textfeld (Prop rows als Attribut). Props: modelValue (v-model), name, label, error, invalid, disabled. Übrige Attribute gehen ans Eingabefeld.
import type { FeldProps } from '../feld/feld'
import TextFeld from '../feld/text-feld.vue'

defineOptions({ name: 'KitTextarea', inheritAttrs: false })

const props = defineProps<FeldProps & { modelValue?: string }>()
const emit = defineEmits<{ 'update:modelValue': [wert: string] }>()
</script>

<template>
  <TextFeld
    basis="textarea"
    art="textarea"
    v-bind="{ ...$attrs, ...props }"
    @update:model-value="emit('update:modelValue', $event)"
  />
</template>
