// Textfelder und gemeinsame Feld-Logik (AK1–AK4, AK8, Randfall 1).
import { flushPromises } from '@vue/test-utils'
import { ref } from 'vue'
import { afterEach, describe, expect, test } from 'vitest'
import { alle, aufraeumen, eins, klick, montiere } from './helfer'

afterEach(aufraeumen)

/** Felder mit __label, __input und __error (alle außer kit-label, kit-form, kit-input-group*), mit dem, was sie zum Rendern brauchen. */
const FELDER: Array<[string, string]> = [
  ['input-text', ''],
  ['input-email', ''],
  ['input-password', ''],
  ['input-number', ''],
  ['input-int', ''],
  ['input-euro', ''],
  ['input-percent', ''],
  ['input-date', ''],
  ['input-color', ''],
  ['input-file', ''],
  ['textarea', ''],
  ['select', ':options="[{ value: \'a\', label: \'A\' }]"'],
  ['checkbox', 'value="1"'],
  ['radio', 'value="a"'],
  ['toggle', ''],
]

const tag = (feld: string, attrs = '', inhalt = '') => `<kit-${feld} name="f" ${attrs} ${inhalt === '' ? '/>' : `>${inhalt}</kit-${feld}>`}`
const feld = (f: string, extra: string, zusatz: string) => montiere(tag(f, `${zusatz} ${extra}`))
const wurzel = (f: string) => eins(`.kit-${f}`) as HTMLElement
const eingabe = (f: string) => eins(`.kit-${f}__input`) as HTMLElement

describe('Beschriftung und id', () => {
  // AK1
  test('kit-input-text: Wurzel, Label mit Text, Eingabefeld mit type und name, for zeigt auf erzeugte id', async () => {
    montiere('<kit-input-text name="vorname" label="Vorname" />')
    await flushPromises()
    const label = eins('.kit-input-text .kit-input-text__label') as HTMLLabelElement
    const input = eins('.kit-input-text .kit-input-text__input') as HTMLInputElement
    expect(label.tagName).toBe('LABEL')
    expect(label.textContent?.trim()).toBe('Vorname')
    expect(input.type).toBe('text')
    expect(input.name).toBe('vorname')
    expect(input.id).not.toBe('')
    expect(label.getAttribute('for')).toBe(input.id)
  })

  // AK2
  test.each(FELDER)('%s: eigene id am Eingabefeld, Label zeigt darauf; ohne label kein __label', async (f, zusatz) => {
    feld(f, 'label="Beschriftung" id="eigen"', zusatz)
    await flushPromises()
    expect(eingabe(f).id).toBe('eigen')
    expect(eins(`.kit-${f}__label`)!.getAttribute('for')).toBe('eigen')
    expect(wurzel(f).id).toBe('') // Gegenprobe: id landet nicht an der Wurzel
    aufraeumen()
    feld(f, '', zusatz)
    await flushPromises()
    expect(eins(`.kit-${f}__label`)).toBeNull()
  })

  // Randfall 1
  test('zwei Felder mit gleichem name ohne id: verschiedene ids, jedes Label zeigt auf sein Feld', async () => {
    montiere('<div><kit-input-text name="x" label="A" /><kit-input-text name="x" label="B" /></div>')
    await flushPromises()
    const inputs = alle('.kit-input-text__input')
    const labels = alle('.kit-input-text__label')
    expect(inputs).toHaveLength(2)
    expect(inputs[0].id).not.toBe('')
    expect(inputs[0].id).not.toBe(inputs[1].id)
    expect(labels.map((l) => l.getAttribute('for'))).toEqual(inputs.map((i) => i.id))
  })
})

describe('Fehler und Zustand', () => {
  // AK4
  test.each(FELDER)('%s: error setzt is-invalid, aria-invalid, aria-describedby und Text; invalid allein ohne __error; sonst nichts', async (f, zusatz) => {
    feld(f, 'error="Pflichtfeld"', zusatz)
    await flushPromises()
    const fehler = eins(`.kit-${f}__error`)!
    expect(fehler.textContent?.trim()).toBe('Pflichtfeld')
    expect(fehler.id).not.toBe('')
    expect(wurzel(f).classList.contains('is-invalid')).toBe(true)
    expect(eingabe(f).getAttribute('aria-invalid')).toBe('true')
    expect(eingabe(f).getAttribute('aria-describedby')).toBe(fehler.id)

    aufraeumen()
    feld(f, 'invalid', zusatz)
    await flushPromises()
    expect(wurzel(f).classList.contains('is-invalid')).toBe(true)
    expect(eingabe(f).getAttribute('aria-invalid')).toBe('true')
    expect(eins(`.kit-${f}__error`)).toBeNull()

    aufraeumen()
    feld(f, '', zusatz)
    await flushPromises()
    expect(wurzel(f)).not.toBeNull() // Gegenprobe: rendert, nur ohne Fehlerzustand
    expect(wurzel(f).classList.contains('is-invalid')).toBe(false)
    expect(eingabe(f).getAttribute('aria-invalid')).toBeNull()
    expect(eingabe(f).getAttribute('aria-describedby')).toBeNull()
    expect(eins(`.kit-${f}__error`)).toBeNull()
  })
})

describe('v-model der Textfelder', () => {
  const FALL: Array<[string, string, string]> = [
    ['input-text', 'abc', 'xyz'],
    ['input-email', 'a@b.de', 'c@d.de'],
    ['input-password', 'abc', 'xyz'],
    ['input-date', '2026-11-18', '2027-01-02'],
    ['input-color', '#ff0000', '#00ff00'],
    ['textarea', 'abc', 'xyz'],
  ]

  // AK3
  test.each(FALL)('%s: Eingabe setzt den Wert, Wert von außen erscheint im Feld', async (f, tippen, extern) => {
    const wert = ref('')
    montiere(`<kit-${f} name="f" v-model="wert" />`, () => ({ wert }))
    await flushPromises()
    const el = eingabe(f) as HTMLInputElement
    el.value = tippen
    el.dispatchEvent(new Event('input', { bubbles: true }))
    await flushPromises()
    expect(wert.value).toBe(tippen)

    wert.value = extern
    await flushPromises()
    expect(eingabe(f).value).toBe(extern)
  })
})

describe('Passwort', () => {
  const schalter = () => eins('.kit-input-password__toggle') as HTMLButtonElement
  const feldEl = () => eingabe('input-password') as HTMLInputElement

  // AK8
  test('Schalter zeigt das Passwort, zweiter Klick stellt den Ausgangszustand her', async () => {
    montiere('<kit-input-password name="pw" />')
    await flushPromises()
    expect(schalter().tagName).toBe('BUTTON')
    expect(feldEl().type).toBe('password')
    expect(schalter().getAttribute('aria-pressed')).toBe('false')
    expect(schalter().getAttribute('aria-label')).toBe('Passwort anzeigen')
    expect(schalter().classList.contains('is-active')).toBe(false)

    await klick(schalter())
    expect(feldEl().type).toBe('text')
    expect(schalter().getAttribute('aria-pressed')).toBe('true')
    expect(schalter().getAttribute('aria-label')).toBe('Passwort verbergen')
    expect(schalter().classList.contains('is-active')).toBe(true)

    await klick(schalter())
    expect(feldEl().type).toBe('password')
    expect(schalter().getAttribute('aria-pressed')).toBe('false')
    expect(schalter().getAttribute('aria-label')).toBe('Passwort anzeigen')
    expect(schalter().classList.contains('is-active')).toBe(false)
  })
})
