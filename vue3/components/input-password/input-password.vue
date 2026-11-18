<script setup lang="ts">
// Passwortfeld mit Schalter „anzeigen/verbergen“.
// Props: modelValue (v-model), name, label, error, invalid, disabled. Übrige Attribute gehen ans Eingabefeld.
import { ref } from 'vue'
import type { FeldProps } from '../feld/feld'
import TextFeld from '../feld/text-feld.vue'

defineOptions({ name: 'KitInputPassword', inheritAttrs: false })

const props = defineProps<FeldProps & { modelValue?: string }>()
const emit = defineEmits<{ 'update:modelValue': [wert: string] }>()

const sichtbar = ref(false)

function umschalten() {
  if (props.disabled) return
  sichtbar.value = !sichtbar.value
}
</script>

<template>
  <TextFeld
    basis="input-password"
    :art="sichtbar ? 'text' : 'password'"
    v-bind="{ ...$attrs, ...props }"
    @update:model-value="emit('update:modelValue', $event)"
  >
    <button
      class="kit-input-password__toggle"
      :class="{ 'is-active': sichtbar }"
      type="button"
      :aria-pressed="sichtbar ? 'true' : 'false'"
      :aria-label="sichtbar ? 'Passwort verbergen' : 'Passwort anzeigen'"
      :disabled="props.disabled"
      @click="umschalten"
    >
      <svg viewBox="0 0 16 16" width="1em" height="1em" aria-hidden="true" focusable="false">
        <path d="M1 8s2.5-4.5 7-4.5S15 8 15 8s-2.5 4.5-7 4.5S1 8 1 8z" fill="none" stroke="currentColor" stroke-width="1.5" />
        <circle cx="8" cy="8" r="2" fill="none" stroke="currentColor" stroke-width="1.5" />
      </svg>
    </button>
  </TextFeld>
</template>
