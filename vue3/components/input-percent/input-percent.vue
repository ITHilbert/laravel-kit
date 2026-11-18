<script setup lang="ts">
// Zahlenfeld. Props: modelValue (v-model, Zahl oder null), name, label, error, invalid, disabled. Übrige Attribute (step, min, max …) gehen ans Eingabefeld.
import type { FeldProps } from '../feld/feld'
import ZahlFeld from '../feld/zahl-feld.vue'

defineOptions({ name: 'KitInputPercent', inheritAttrs: false })

const props = defineProps<FeldProps & { modelValue?: number | null }>()
const emit = defineEmits<{ 'update:modelValue': [wert: number | null] }>()
</script>

<template>
  <ZahlFeld
    basis="input-percent"
    inputmode="decimal"
    suffix="%"
    standard-aria="Prozent"
    v-bind="{ ...$attrs, ...props }"
    @update:model-value="emit('update:modelValue', $event)"
  />
</template>
