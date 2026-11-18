<script setup lang="ts">
// Combobox (Einfachauswahl) auf Reka UI. Props: modelValue (v-model), name, disabled, invalid, options.
// Das Eingabefeld zeigt das Label der Auswahl, Tippen filtert die Liste (Teilstring im Label, ohne
// Groß-/Kleinschreibung). Übrige Attribute (id, required, placeholder, aria-*) gehen an das Eingabefeld.
import { computed, ref, watch } from 'vue'
import {
  ComboboxAnchor,
  ComboboxContent,
  ComboboxInput,
  ComboboxItem,
  ComboboxRoot,
  ComboboxTrigger,
  ComboboxViewport,
} from 'reka-ui'

defineOptions({ name: 'KitCombobox', inheritAttrs: false })

type Option = { value: string | number; label: string; disabled?: boolean }

const props = defineProps<{
  name?: string
  disabled?: boolean
  invalid?: boolean
  options?: Option[]
}>()

const modell = defineModel<string | number | undefined>({ default: undefined })

const offen = ref(false)
const suche = ref('')

const liste = computed(() => props.options ?? [])

const gefiltert = computed(() => {
  const s = suche.value.trim().toLowerCase()
  return s === '' ? liste.value : liste.value.filter((o) => o.label.toLowerCase().includes(s))
})

const labelZu = (wert: unknown): string => liste.value.find((o) => o.value === wert)?.label ?? ''

// Beim Öffnen und nach der Auswahl zeigt die Liste wieder alle Optionen.
watch(offen, () => {
  suche.value = ''
})
</script>

<template>
  <ComboboxRoot
    v-model="modell"
    v-model:open="offen"
    class="kit-combobox"
    :class="{ 'is-open': offen, 'is-invalid': props.invalid, disabled: props.disabled }"
    :name="props.name"
    :disabled="props.disabled"
    ignore-filter
  >
    <ComboboxAnchor as="template">
      <ComboboxInput
        class="kit-combobox__input"
        :display-value="labelZu"
        :aria-invalid="props.invalid ? 'true' : undefined"
        v-bind="$attrs"
        @update:model-value="(v: string) => (suche = v)"
      />
    </ComboboxAnchor>
    <ComboboxTrigger class="kit-combobox__trigger" aria-label="Liste öffnen" :disabled="props.disabled">
      <svg viewBox="0 0 16 16" width="1em" height="1em" aria-hidden="true" focusable="false">
        <path d="M3 6l5 5 5-5" fill="none" stroke="currentColor" stroke-width="1.5" />
      </svg>
    </ComboboxTrigger>
    <ComboboxContent class="kit-combobox__list">
      <ComboboxViewport as="template">
        <div v-if="gefiltert.length === 0" class="kit-combobox__empty">Keine Treffer</div>
        <ComboboxItem
          v-for="o in gefiltert"
          :key="o.value"
          class="kit-combobox__item"
          :class="{ 'is-active': o.value === modell, disabled: o.disabled }"
          :value="o.value"
          :disabled="o.disabled"
          :text-value="o.label"
        >
          {{ o.label }}
        </ComboboxItem>
      </ComboboxViewport>
    </ComboboxContent>
  </ComboboxRoot>
</template>
