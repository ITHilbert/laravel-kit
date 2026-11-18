<script setup lang="ts">
// Auswahlfeld (natives <select>). Props: options, placeholder, modelValue (v-model), name, label, error, invalid, disabled.
import { useFeld, type FeldProps } from '../feld/feld'

defineOptions({ name: 'KitSelect', inheritAttrs: false })

export interface KitOption {
  value: string | number
  label: string
  disabled?: boolean
}

const props = defineProps<FeldProps & { options?: KitOption[]; placeholder?: string; modelValue?: string | number | null }>()
const emit = defineEmits<{ 'update:modelValue': [wert: string | number] }>()

const { inputId, errorId, hatFehlertext, ungueltig, eingabeAttribute } = useFeld(props)

function beiAuswahl(e: Event) {
  const roh = (e.target as HTMLSelectElement).value
  const treffer = (props.options ?? []).find((o) => String(o.value) === roh)
  emit('update:modelValue', treffer ? treffer.value : roh)
}
</script>

<template>
  <div class="kit-select" :class="{ 'is-invalid': ungueltig, disabled: props.disabled }">
    <label v-if="props.label" class="kit-select__label" :for="inputId">{{ props.label }}</label>
    <select class="kit-select__input" :value="props.modelValue ?? ''" v-bind="{ ...$attrs, ...eingabeAttribute }" @change="beiAuswahl">
      <option v-if="props.placeholder" value="">{{ props.placeholder }}</option>
      <option v-for="o in props.options ?? []" :key="o.value" :value="o.value" :disabled="o.disabled">{{ o.label }}</option>
    </select>
    <span v-if="hatFehlertext" :id="errorId" class="kit-select__error">{{ props.error }}</span>
  </div>
</template>
