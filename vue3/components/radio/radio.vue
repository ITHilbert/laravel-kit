<script setup lang="ts">
// Auswahlknopf, Label rechts (Slot oder Prop label). Props: modelValue (value des gewählten Knopfs), value, name, label, error, invalid, disabled.
import { useFeld, type FeldProps } from '../feld/feld'

defineOptions({ name: 'KitRadio', inheritAttrs: false })

const props = defineProps<FeldProps & { modelValue?: string | number | null; value?: string | number }>()
const emit = defineEmits<{ 'update:modelValue': [wert: string | number | undefined] }>()

const { inputId, errorId, hatFehlertext, ungueltig, eingabeAttribute } = useFeld(props)
</script>

<template>
  <div class="kit-radio" :class="{ 'is-invalid': ungueltig, disabled: props.disabled }">
    <input
      class="kit-radio__input"
      type="radio"
      :checked="props.value !== undefined && props.modelValue === props.value"
      v-bind="{ ...(props.value !== undefined ? { value: props.value } : {}), ...$attrs, ...eingabeAttribute }"
      @change="emit('update:modelValue', props.value)"
    />
    <label v-if="$slots.default || props.label" class="kit-radio__label" :for="inputId"><slot>{{ props.label }}</slot></label>
    <span v-if="hatFehlertext" :id="errorId" class="kit-radio__error">{{ props.error }}</span>
  </div>
</template>
