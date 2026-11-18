<script setup lang="ts">
// Schalter als Schaltfläche (role="switch"). Props: modelValue (Boolean), name (versteckt gesendet als 1 bzw. 0), label, error, invalid, disabled.
import { useFeld, type FeldProps } from '../feld/feld'

defineOptions({ name: 'KitToggle', inheritAttrs: false })

const props = defineProps<FeldProps & { modelValue?: boolean }>()
const emit = defineEmits<{ 'update:modelValue': [wert: boolean] }>()

const { inputId, errorId, hatFehlertext, ungueltig, eingabeAttribute } = useFeld(props)

function umschalten() {
  if (props.disabled) return
  emit('update:modelValue', !props.modelValue)
}
</script>

<template>
  <div class="kit-toggle" :class="{ 'is-invalid': ungueltig, 'is-active': !!props.modelValue, disabled: props.disabled }">
    <input v-if="props.name" type="hidden" :name="props.name" :value="props.modelValue ? 1 : 0" />
    <button
      class="kit-toggle__input"
      type="button"
      role="switch"
      :aria-checked="props.modelValue ? 'true' : 'false'"
      v-bind="{ ...$attrs, ...eingabeAttribute, name: undefined }"
      @click="umschalten"
    >
      <span class="kit-toggle__thumb" aria-hidden="true" />
    </button>
    <label v-if="props.label" class="kit-toggle__label" :for="inputId">{{ props.label }}</label>
    <span v-if="hatFehlertext" :id="errorId" class="kit-toggle__error">{{ props.error }}</span>
  </div>
</template>
