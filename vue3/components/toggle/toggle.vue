<script setup lang="ts">
// Schalter als Schaltfläche (role="switch"). Props: modelValue (Boolean), name (versteckt gesendet als 1 bzw. 0), label, error, invalid, disabled.
import { ref, watch } from 'vue'
import { useFeld, type FeldProps } from '../feld/feld'

defineOptions({ name: 'KitToggle', inheritAttrs: false })

const props = defineProps<FeldProps & { modelValue?: boolean }>()
const emit = defineEmits<{ 'update:modelValue': [wert: boolean] }>()

const { inputId, errorId, hatFehlertext, ungueltig, eingabeAttribute } = useFeld(props)

const aktiv = ref(!!props.modelValue)

watch(
  () => props.modelValue,
  (neu) => {
    aktiv.value = !!neu
  },
)

function umschalten() {
  if (props.disabled) return
  aktiv.value = !aktiv.value
  emit('update:modelValue', aktiv.value)
}
</script>

<template>
  <div class="kit-toggle" :class="{ 'is-invalid': ungueltig, 'is-active': aktiv, disabled: props.disabled }">
    <input v-if="props.name" type="hidden" :name="props.name" :value="aktiv ? 1 : 0" :disabled="props.disabled || undefined" />
    <button
      class="kit-toggle__input"
      type="button"
      role="switch"
      :aria-checked="aktiv ? 'true' : 'false'"
      v-bind="{ ...$attrs, ...eingabeAttribute, name: undefined }"
      @click="umschalten"
    >
      <span class="kit-toggle__thumb" aria-hidden="true" />
    </button>
    <label v-if="props.label" class="kit-toggle__label" :for="inputId">{{ props.label }}</label>
    <span v-if="hatFehlertext" :id="errorId" class="kit-toggle__error">{{ props.error }}</span>
  </div>
</template>
