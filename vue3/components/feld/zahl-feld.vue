<script setup lang="ts">
// Basis der Zahlenfelder: <input type="number">, Wert als Zahl oder null, optionaler Zusatz (Suffix) nach dem Feld.
// Props: basis (Tag-Name ohne `kit-`), step, inputmode, suffix, standardAria (aria-label, wenn weder label noch aria-label gesetzt sind).
import { ref, useAttrs, watch } from 'vue'
import { useFeld, type FeldProps } from './feld'

defineOptions({ name: 'KitZahlFeld', inheritAttrs: false })

const props = defineProps<
  FeldProps & {
    basis: string
    step?: string
    inputmode?: string
    suffix?: string
    standardAria?: string
    modelValue?: number | null
  }
>()
const emit = defineEmits<{ 'update:modelValue': [wert: number | null] }>()

const attrs = useAttrs()
const { inputId, errorId, hatFehlertext, ungueltig, eingabeAttribute } = useFeld(props)
const text = ref(props.modelValue == null ? '' : String(props.modelValue))

watch(
  () => props.modelValue,
  (neu) => {
    const aktuell = text.value === '' ? null : Number(text.value)
    if (neu !== aktuell) text.value = neu == null ? '' : String(neu)
  },
)

function zuZahl(el: HTMLInputElement): number | null {
  if (el.value === '') return null
  const zahl = el.valueAsNumber
  return Number.isNaN(zahl) ? null : zahl
}

function beiEingabe(e: Event) {
  const el = e.target as HTMLInputElement
  text.value = el.value
  emit('update:modelValue', zuZahl(el))
}

function ariaLabel(): string | undefined {
  return !props.label && !attrs['aria-label'] ? props.standardAria : undefined
}
</script>

<template>
  <div :class="[`kit-${props.basis}`, { 'is-invalid': ungueltig, disabled: props.disabled }]">
    <label v-if="props.label" :class="`kit-${props.basis}__label`" :for="inputId">{{ props.label }}</label>
    <input
      :class="`kit-${props.basis}__input`"
      type="number"
      :step="props.step"
      :inputmode="props.inputmode"
      :aria-label="ariaLabel()"
      :value="text"
      v-bind="{ ...$attrs, ...eingabeAttribute }"
      @input="beiEingabe"
    />
    <span v-if="props.suffix" :class="`kit-${props.basis}__suffix`" aria-hidden="true">{{ props.suffix }}</span>
    <span v-if="hatFehlertext" :id="errorId" :class="`kit-${props.basis}__error`">{{ props.error }}</span>
  </div>
</template>
