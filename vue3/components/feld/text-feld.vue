<script setup lang="ts">
// Basis der Textfelder: Wurzel, Label, Eingabefeld (input oder textarea), Zusatz-Slot, Fehlertext.
// Props: basis (Tag-Name ohne `kit-`, Klassenpräfix), art (input-type, `textarea` = <textarea>), dazu FeldProps und modelValue.
// Übrige Attribute gehen ans Eingabefeld, nicht an die Wurzel.
import { ref, watch } from 'vue'
import { useFeld, type FeldProps } from './feld'

defineOptions({ name: 'KitTextFeld', inheritAttrs: false })

const props = defineProps<
  FeldProps & {
    basis: string
    art?: string
    modelValue?: string
  }
>()
const emit = defineEmits<{ 'update:modelValue': [wert: string] }>()

const { inputId, errorId, hatFehlertext, ungueltig, eingabeAttribute } = useFeld(props)
const wert = ref(props.modelValue ?? '')

watch(
  () => props.modelValue,
  (neu) => {
    wert.value = neu ?? ''
  },
)

function beiEingabe(e: Event) {
  wert.value = (e.target as HTMLInputElement | HTMLTextAreaElement).value
  emit('update:modelValue', wert.value)
}
</script>

<template>
  <div :class="[`kit-${props.basis}`, { 'is-invalid': ungueltig, disabled: props.disabled }]">
    <label v-if="props.label" :class="`kit-${props.basis}__label`" :for="inputId">{{ props.label }}</label>
    <textarea
      v-if="props.art === 'textarea'"
      :class="`kit-${props.basis}__input`"
      :value="wert"
      v-bind="{ ...$attrs, ...eingabeAttribute }"
      @input="beiEingabe"
    />
    <input
      v-else
      :class="`kit-${props.basis}__input`"
      :type="props.art ?? 'text'"
      :value="wert"
      v-bind="{ ...$attrs, ...eingabeAttribute }"
      @input="beiEingabe"
    />
    <slot />
    <span v-if="hatFehlertext" :id="errorId" :class="`kit-${props.basis}__error`">{{ props.error }}</span>
  </div>
</template>
