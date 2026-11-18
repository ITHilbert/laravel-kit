<script setup lang="ts">
// Suche. Props: modelValue (v-model), name, disabled, invalid, verzoegerung (ms, Standard 300).
// Ereignisse: update:modelValue (sofort), suche (entprellt; beim Leeren sofort). Übrige Attribute gehen ans Eingabefeld.
import { onBeforeUnmount, ref, watch } from 'vue'

defineOptions({ name: 'KitSearch', inheritAttrs: false })

const props = withDefaults(
  defineProps<{
    modelValue?: string
    name?: string
    disabled?: boolean
    invalid?: boolean
    verzoegerung?: number
  }>(),
  { modelValue: '', verzoegerung: 300 },
)
const emit = defineEmits<{
  'update:modelValue': [wert: string]
  suche: [wert: string]
}>()

const wert = ref(props.modelValue ?? '')
const eingabe = ref<HTMLInputElement | null>(null)
let timer: ReturnType<typeof setTimeout> | undefined

watch(
  () => props.modelValue,
  (neu) => {
    wert.value = neu ?? ''
  },
)

const stoppe = () => {
  if (timer !== undefined) clearTimeout(timer)
  timer = undefined
}

function beiEingabe(e: Event) {
  wert.value = (e.target as HTMLInputElement).value
  emit('update:modelValue', wert.value)
  stoppe()
  const v = wert.value
  timer = setTimeout(() => {
    timer = undefined
    emit('suche', v)
  }, props.verzoegerung)
}

function leeren() {
  if (props.disabled) return
  stoppe()
  wert.value = ''
  emit('update:modelValue', '')
  emit('suche', '')
  eingabe.value?.focus()
}

onBeforeUnmount(stoppe)
</script>

<template>
  <div class="kit-search" :class="{ 'is-invalid': props.invalid, disabled: props.disabled }">
    <svg class="kit-search__icon" viewBox="0 0 16 16" width="1em" height="1em" aria-hidden="true" focusable="false">
      <circle cx="7" cy="7" r="4.5" fill="none" stroke="currentColor" stroke-width="1.5" />
      <path d="M10.5 10.5L14 14" fill="none" stroke="currentColor" stroke-width="1.5" />
    </svg>
    <input
      ref="eingabe"
      class="kit-search__input"
      type="search"
      :name="props.name"
      :value="wert"
      :disabled="props.disabled"
      :aria-invalid="props.invalid ? 'true' : undefined"
      v-bind="$attrs"
      @input="beiEingabe"
      @keydown.esc="leeren"
    />
    <button
      v-if="wert !== ''"
      class="kit-search__clear"
      type="button"
      aria-label="Suche leeren"
      :disabled="props.disabled"
      @click="leeren"
    >
      <svg viewBox="0 0 16 16" width="1em" height="1em" aria-hidden="true" focusable="false">
        <path d="M4 4l8 8M12 4l-8 8" fill="none" stroke="currentColor" stroke-width="1.5" />
      </svg>
    </button>
  </div>
</template>
