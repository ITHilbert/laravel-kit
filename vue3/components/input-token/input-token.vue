<script setup lang="ts">
// Token-Eingabe: Passwortfeld mit Schalter (anzeigen/verbergen) und Kopieren-Knopf.
// Props: modelValue (v-model), name, disabled, invalid. Übrige Attribute gehen ans Eingabefeld.
import { onBeforeUnmount, ref, watch } from 'vue'

defineOptions({ name: 'KitInputToken', inheritAttrs: false })

const props = defineProps<{
  modelValue?: string
  name?: string
  disabled?: boolean
  invalid?: boolean
}>()
const emit = defineEmits<{ 'update:modelValue': [wert: string] }>()

const wert = ref(props.modelValue ?? '')
const sichtbar = ref(false)
const meldung = ref('')
const kopiert = ref(false)
let timer: ReturnType<typeof setTimeout> | undefined

watch(
  () => props.modelValue,
  (neu) => {
    wert.value = neu ?? ''
  },
)

function beiEingabe(e: Event) {
  wert.value = (e.target as HTMLInputElement).value
  emit('update:modelValue', wert.value)
}

function umschalten() {
  if (props.disabled) return
  sichtbar.value = !sichtbar.value
}

function ersatzKopie(text: string): boolean {
  const feld = document.createElement('textarea')
  feld.value = text
  feld.setAttribute('readonly', '')
  feld.setAttribute('aria-hidden', 'true')
  feld.style.position = 'fixed'
  feld.style.opacity = '0'
  feld.style.pointerEvents = 'none'
  document.body.appendChild(feld)
  try {
    feld.select()
    return typeof document.execCommand === 'function' && document.execCommand('copy')
  } catch {
    return false
  } finally {
    feld.remove()
  }
}

async function kopieren() {
  if (props.disabled) return
  const text = wert.value
  let ok = false
  if (navigator.clipboard?.writeText) {
    try {
      await navigator.clipboard.writeText(text)
      ok = true
    } catch {
      ok = ersatzKopie(text)
    }
  } else {
    ok = ersatzKopie(text)
  }
  kopiert.value = ok
  meldung.value = ok ? 'Kopiert' : 'Kopieren fehlgeschlagen'
  if (timer !== undefined) clearTimeout(timer)
  timer = setTimeout(() => {
    timer = undefined
    kopiert.value = false
    meldung.value = ''
  }, 2000)
}

onBeforeUnmount(() => {
  if (timer !== undefined) clearTimeout(timer)
})
</script>

<template>
  <div class="kit-input-token" :class="{ 'is-invalid': props.invalid, disabled: props.disabled }">
    <input
      class="kit-input-token__input"
      :type="sichtbar ? 'text' : 'password'"
      :name="props.name"
      :value="wert"
      :disabled="props.disabled"
      :aria-invalid="props.invalid ? 'true' : undefined"
      v-bind="$attrs"
      @input="beiEingabe"
    />
    <button
      class="kit-input-token__toggle"
      :class="{ 'is-active': sichtbar }"
      type="button"
      :aria-pressed="sichtbar ? 'true' : 'false'"
      :aria-label="sichtbar ? 'Token verbergen' : 'Token anzeigen'"
      :disabled="props.disabled"
      @click="umschalten"
    >
      <svg viewBox="0 0 16 16" width="1em" height="1em" aria-hidden="true" focusable="false">
        <path d="M1 8s2.5-4.5 7-4.5S15 8 15 8s-2.5 4.5-7 4.5S1 8 1 8z" fill="none" stroke="currentColor" stroke-width="1.5" />
        <circle cx="8" cy="8" r="2" fill="none" stroke="currentColor" stroke-width="1.5" />
      </svg>
    </button>
    <button
      class="kit-input-token__copy"
      :class="{ 'is-active': kopiert }"
      type="button"
      aria-label="Token kopieren"
      :disabled="props.disabled"
      @click="kopieren"
    >
      <svg viewBox="0 0 16 16" width="1em" height="1em" aria-hidden="true" focusable="false">
        <rect x="5" y="5" width="9" height="9" rx="1" fill="none" stroke="currentColor" stroke-width="1.5" />
        <path d="M11 5V3a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1v7a1 1 0 0 0 1 1h2" fill="none" stroke="currentColor" stroke-width="1.5" />
      </svg>
    </button>
    <span class="kit-input-token__status" role="status">{{ meldung }}</span>
  </div>
</template>
