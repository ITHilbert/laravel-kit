<script setup lang="ts">
// Eingabefeld mit Vorschlagsliste (<datalist>). Props: modelValue (v-model), name, disabled, invalid, options.
// Freie Eingabe bleibt möglich. Übrige Attribute gehen ans Eingabefeld.
import { ref, useId, watch } from 'vue'

defineOptions({ name: 'KitInputList', inheritAttrs: false })

type Option = { value: string | number; label: string; disabled?: boolean }

const props = defineProps<{
  modelValue?: string | number
  name?: string
  disabled?: boolean
  invalid?: boolean
  options?: Option[]
}>()
const emit = defineEmits<{ 'update:modelValue': [wert: string] }>()

const listenId = `kit-input-list-${useId()}`
const wert = ref(props.modelValue ?? '')

watch(
  () => props.modelValue,
  (neu) => {
    wert.value = neu ?? ''
  },
)

function beiEingabe(e: Event) {
  wert.value = (e.target as HTMLInputElement).value
  emit('update:modelValue', wert.value as string)
}
</script>

<template>
  <div class="kit-input-list" :class="{ 'is-invalid': props.invalid, disabled: props.disabled }">
    <input
      class="kit-input-list__input"
      type="text"
      :name="props.name"
      :value="wert"
      :list="listenId"
      :disabled="props.disabled"
      :aria-invalid="props.invalid ? 'true' : undefined"
      v-bind="$attrs"
      @input="beiEingabe"
    />
    <datalist :id="listenId" class="kit-input-list__datalist">
      <option v-for="o in props.options ?? []" :key="o.value" :value="o.value" :disabled="o.disabled">{{ o.label }}</option>
    </datalist>
  </div>
</template>
