<script setup lang="ts">
// Dateifeld. Props: modelValue (FileList, nur Ausgabe), name, label, error, invalid, disabled. accept und multiple gehen als Attribute ans Eingabefeld.
import { useFeld, type FeldProps } from '../feld/feld'

defineOptions({ name: 'KitInputFile', inheritAttrs: false })

const props = defineProps<FeldProps & { modelValue?: FileList | null }>()
const emit = defineEmits<{ 'update:modelValue': [wert: FileList | null] }>()

const { inputId, errorId, hatFehlertext, ungueltig, eingabeAttribute } = useFeld(props)
</script>

<template>
  <div class="kit-input-file" :class="{ 'is-invalid': ungueltig, disabled: props.disabled }">
    <label v-if="props.label" class="kit-input-file__label" :for="inputId">{{ props.label }}</label>
    <input
      class="kit-input-file__input"
      type="file"
      v-bind="{ ...$attrs, ...eingabeAttribute }"
      @change="emit('update:modelValue', ($event.target as HTMLInputElement).files)"
    />
    <span v-if="hatFehlertext" :id="errorId" class="kit-input-file__error">{{ props.error }}</span>
  </div>
</template>
