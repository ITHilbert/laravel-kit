<script setup lang="ts">
// Auswahlfeld (natives <select>). Props: options, placeholder, modelValue (v-model), name, label, error, invalid, disabled.
import { computed, watch } from 'vue'
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

// Gewählter Wert: passender modelValue, sonst Platzhalter, sonst die erste wählbare Option (wie ein natives <select>).
// Nie ein Wert ohne passende Option, sonst hat das <select> im Browser keine Auswahl.
const anzeigeWert = computed(() => {
  const optionen = props.options ?? []
  const roh = props.modelValue
  if (roh !== null && roh !== undefined && optionen.some((o) => String(o.value) === String(roh))) return String(roh)
  if (props.placeholder) return ''
  const erste = optionen.find((o) => !o.disabled) ?? optionen[0]
  return erste ? String(erste.value) : ''
})

// Ohne placeholder und ohne passenden Wert meldet die Komponente die sichtbare Startauswahl ans Modell (AK22).
// Nach dem Emit passt das Modell, ein weiterer Emit entfällt.
watch(
  () => [props.options, props.modelValue] as const,
  () => {
    if (props.placeholder) return
    const optionen = props.options ?? []
    const roh = props.modelValue
    if (roh !== null && roh !== undefined && optionen.some((o) => String(o.value) === String(roh))) return
    const erste = optionen.find((o) => !o.disabled)
    if (erste) emit('update:modelValue', erste.value)
  },
  { immediate: true },
)

function beiAuswahl(e: Event) {
  const roh = (e.target as HTMLSelectElement).value
  const treffer = (props.options ?? []).find((o) => String(o.value) === roh)
  emit('update:modelValue', treffer ? treffer.value : roh)
}
</script>

<template>
  <div class="kit-select" :class="{ 'is-invalid': ungueltig, disabled: props.disabled }">
    <label v-if="props.label" class="kit-select__label" :for="inputId">{{ props.label }}</label>
    <select class="kit-select__input" :value="anzeigeWert" v-bind="{ ...$attrs, ...eingabeAttribute }" @change="beiAuswahl">
      <option v-if="props.placeholder" value="">{{ props.placeholder }}</option>
      <option v-for="o in props.options ?? []" :key="o.value" :value="o.value" :disabled="o.disabled">{{ o.label }}</option>
    </select>
    <span v-if="hatFehlertext" :id="errorId" class="kit-select__error">{{ props.error }}</span>
  </div>
</template>
