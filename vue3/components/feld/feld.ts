// Gemeinsame Feld-Logik des Vue-3-Kerns: Props, erzeugte ids, Fehlerzustand.
// Wiederverwendet von den Textfeldern (feld/text-feld.vue) und den späteren Feldern (Zahlen, Auswahl, Haken …).
import { computed, useAttrs, useId } from 'vue'

export interface FeldProps {
  name?: string
  label?: string
  error?: string
  invalid?: boolean
  disabled?: boolean
}

export function useFeld(props: FeldProps) {
  const attrs = useAttrs()
  const erzeugt = useId()
  /** Eigene `id` aus den Attributen, sonst eine je Instanz erzeugte. */
  const inputId = computed(() => (typeof attrs.id === 'string' && attrs.id !== '' ? attrs.id : `kit-feld-${erzeugt}`))
  const errorId = computed(() => `${inputId.value}-error`)
  const hatFehlertext = computed(() => !!props.error)
  const ungueltig = computed(() => !!props.invalid || hatFehlertext.value)
  /** Attribute fürs Eingabefeld (ohne die eigene id; die setzt der Aufrufer aus inputId). */
  const eingabeAttribute = computed(() => ({
    id: inputId.value,
    name: props.name,
    disabled: props.disabled || undefined,
    'aria-invalid': ungueltig.value ? 'true' : undefined,
    'aria-describedby': hatFehlertext.value ? errorId.value : undefined,
  }))
  return { inputId, errorId, hatFehlertext, ungueltig, eingabeAttribute }
}
