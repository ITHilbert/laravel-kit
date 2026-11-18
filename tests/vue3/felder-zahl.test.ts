// Zahlenfelder (AK6, AK7, Randfall 2).
import { flushPromises } from '@vue/test-utils'
import { ref } from 'vue'
import { afterEach, describe, expect, test, vi } from 'vitest'
import { aufraeumen, eins, montiere } from './helfer'

afterEach(() => {
  aufraeumen()
  vi.restoreAllMocks()
})

const eingabe = (f: string) => eins(`.kit-${f}__input`) as HTMLInputElement

async function tippe(f: string, text: string) {
  const el = eingabe(f)
  el.value = text
  el.dispatchEvent(new Event('input', { bubbles: true }))
  await flushPromises()
}

const FELDER: Array<[string, string, string]> = [
  ['input-number', ':step="0.5" :min="0"', '12.5'],
  ['input-int', '', '12'],
  ['input-euro', '', '12.5'],
  ['input-percent', '', '12.5'],
]

describe('Zahlenfelder', () => {
  // AK6
  test.each(FELDER)('%s: Eingabe wird zur Zahl, leeres Feld zu null, Wert von außen erscheint im Feld', async (f, attrs, text) => {
    const wert = ref<number | null>(null)
    montiere(`<kit-${f} name="z" v-model="wert" ${attrs} />`, () => ({ wert }))
    await flushPromises()
    await tippe(f, text)
    expect(wert.value).toBe(Number(text))
    expect(typeof wert.value).toBe('number')

    await tippe(f, '')
    expect(wert.value).toBeNull()

    wert.value = 7
    await flushPromises()
    expect(eingabe(f).value).toBe('7')
  })

  // AK6
  test('Attribute: number nimmt step, min und max an; euro, int und percent haben step und inputmode', async () => {
    montiere('<div><kit-input-number name="a" :step="0.5" :min="0" :max="9" /><kit-input-int name="b" /><kit-input-euro name="c" /><kit-input-percent name="d" /></div>')
    await flushPromises()
    for (const f of ['input-number', 'input-int', 'input-euro', 'input-percent']) expect(eingabe(f).type).toBe('number')
    expect(eingabe('input-number').getAttribute('step')).toBe('0.5')
    expect(eingabe('input-number').getAttribute('min')).toBe('0')
    expect(eingabe('input-number').getAttribute('max')).toBe('9')
    expect(eingabe('input-int').getAttribute('step')).toBe('1')
    expect(eingabe('input-int').getAttribute('inputmode')).toBe('numeric')
    expect(eingabe('input-euro').getAttribute('step')).toBe('0.01')
    expect(eingabe('input-euro').getAttribute('inputmode')).toBe('decimal')
    expect(eingabe('input-percent').getAttribute('inputmode')).toBe('decimal')
  })

  // Randfall 2
  test('Text, den der Browser nicht als Zahl liest (valueAsNumber NaN): Wert null, kein Fehler', async () => {
    const fehler = vi.spyOn(console, 'error').mockImplementation(() => {})
    const warn = vi.spyOn(console, 'warn').mockImplementation(() => {})
    const wert = ref<number | null>(null)
    montiere('<kit-input-number name="z" v-model="wert" />', () => ({ wert }))
    await flushPromises()
    await tippe('input-number', '5')
    expect(wert.value).toBe(5) // Gegenprobe: gültige Eingabe wird übernommen

    Object.defineProperty(eingabe('input-number'), 'valueAsNumber', { value: Number.NaN, configurable: true })
    await tippe('input-number', 'abc')
    expect(wert.value).toBeNull()
    expect(fehler).not.toHaveBeenCalled()
    expect(warn).not.toHaveBeenCalled()
  })
})

describe('Zusatz und Beschriftung', () => {
  const FALL: Array<[string, string, string, string]> = [
    ['input-euro', '€', 'Betrag in Euro', 'kit-input-euro__suffix'],
    ['input-percent', '%', 'Prozent', 'kit-input-percent__suffix'],
  ]

  // AK7
  test.each(FALL)('%s: Zusatz nach dem Feld, aria-hidden, Standard-aria-label nur ohne label und aria-label', async (f, zeichen, aria, klasse) => {
    montiere(`<kit-${f} name="z" />`)
    await flushPromises()
    const suffix = eins(`.${klasse}`)!
    expect(suffix.textContent?.trim()).toBe(zeichen)
    expect(suffix.getAttribute('aria-hidden')).toBe('true')
    expect(eingabe(f).compareDocumentPosition(suffix) & Node.DOCUMENT_POSITION_FOLLOWING).toBeTruthy()
    expect(eingabe(f).getAttribute('aria-label')).toBe(aria)

    aufraeumen()
    montiere(`<kit-${f} name="z" label="Preis" />`)
    await flushPromises()
    expect(eingabe(f).getAttribute('aria-label')).toBeNull()

    aufraeumen()
    montiere(`<kit-${f} name="z" aria-label="Eigener" />`)
    await flushPromises()
    expect(eingabe(f).getAttribute('aria-label')).toBe('Eigener')
  })
})
