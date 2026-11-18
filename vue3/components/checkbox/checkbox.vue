<script setup lang="ts">
// Kontrollkästchen, Label rechts (Slot oder Prop label). Props: modelValue (Boolean), value, name, label, error, invalid, disabled.
import { useFeld, type FeldProps } from '../feld/feld'

defineOptions({ name: 'KitCheckbox', inheritAttrs: false })

const props = defineProps<FeldProps & { modelValue?: boolean; value?: string }>()
const emit = defineEmits<{ 'update:modelValue': [wert: boolean] }>()

const { inputId, errorId, hatFehlertext, ungueltig, eingabeAttribute } = useFeld(props)
</script>

<template>
  <div class="kit-checkbox" :class="{ 'is-invalid': ungueltig, disabled: props.disabled }">
    <input
      class="kit-checkbox__input"
      type="checkbox"
      :checked="!!props.modelValue"
      v-bind="{ ...(props.value !== undefined ? { value: props.value } : {}), ...$attrs, ...eingabeAttribute }"
      @change="emit('update:modelValue', ($event.target as HTMLInputElement).checked)"
    />
    <label v-if="$slots.default || props.label" class="kit-checkbox__label" :for="inputId"><slot>{{ props.label }}</slot></label>
    <span v-if="hatFehlertext" :id="errorId" class="kit-checkbox__error">{{ props.error }}</span>
  </div>
</template>
