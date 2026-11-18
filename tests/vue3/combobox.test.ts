// Combobox (AK1–AK5, AK13, AK14, Randfall 1–3).
import { flushPromises } from '@vue/test-utils'
import { ref } from 'vue'
import { afterEach, describe, expect, test, vi } from 'vitest'
import { alle, aufraeumen, eins, klick, montiere, sichtbar, taste } from './helfer'

afterEach(() => {
  aufraeumen()
  vi.restoreAllMocks()
})

const OPT = [
  { value: 'de', label: 'Deutschland' },
  { value: 'at', label: 'Österreich' },
  { value: 'ch', label: 'Schweiz' },
]

const eingabe = () => eins('.kit-combobox__input') as HTMLInputElement
const items = () => alle('.kit-combobox__item')
const offen = () => sichtbar(eins('.kit-combobox__list'))

async function tippe(text: string) {
  eingabe().value = text
  eingabe().dispatchEvent(new Event('input', { bubbles: true }))
  await flushPromises()
}

/** Combobox im Formular mit Protokoll der update:modelValue-Ereignisse. */
function formular(optionen: unknown = OPT, extra = '') {
  const gemeldet: unknown[] = []
  const w = montiere(
    `<form id="f"><kit-combobox name="land" :options="o" ${extra} @update:model-value="log" /></form>`,
    () => ({ o: optionen, log: (v: unknown) => gemeldet.push(v) }),
  )
  return { w, gemeldet }
}
const land = () => new FormData(eins('#f') as HTMLFormElement).get('land')

describe('Combobox', () => {
  // AK1
  test('rendert Wurzel, Eingabefeld mit Rolle combobox, Trigger; Liste nicht sichtbar', async () => {
    formular()
    await flushPromises()
    expect(eins('.kit-combobox')).not.toBeNull()
    expect(eins('.kit-combobox .kit-combobox__input')?.getAttribute('role')).toBe('combobox')
    expect(eingabe().getAttribute('aria-expanded')).toBe('false')
    expect(eins('.kit-combobox .kit-combobox__trigger')).not.toBeNull()
    expect(offen()).toBe(false)
  })

  // AK2
  test('Klick auf den Trigger öffnet die Liste mit drei Optionen', async () => {
    formular()
    await flushPromises()
    await klick(eins('.kit-combobox__trigger')!)
    await vi.waitFor(() => expect(offen()).toBe(true))
    expect(eins('.kit-combobox__list')?.getAttribute('role')).toBe('listbox')
    expect(eins('.kit-combobox')!.classList.contains('is-open')).toBe(true)
    expect(eingabe().getAttribute('aria-expanded')).toBe('true')
    expect(items().map((i) => i.textContent?.trim())).toEqual(['Deutschland', 'Österreich', 'Schweiz'])
    expect(items().map((i) => i.getAttribute('role'))).toEqual(['option', 'option', 'option'])
  })

  // AK3
  test('Tippen filtert ohne Groß-/Kleinschreibung auf das Label; ohne Treffer erscheint „empty“', async () => {
    formular()
    await flushPromises()
    await klick(eins('.kit-combobox__trigger')!)
    await vi.waitFor(() => expect(items()).toHaveLength(3))
    expect(eins('.kit-combobox__empty')).toBeNull() // Gegenprobe

    await tippe('st')
    await vi.waitFor(() => expect(items()).toHaveLength(1))
    expect(items()[0].textContent).toContain('Österreich')

    await tippe('xyz')
    await vi.waitFor(() => expect(items()).toHaveLength(0))
    expect(sichtbar(eins('.kit-combobox__empty'))).toBe(true)
  })

  // AK4
  test('Klick auf „Schweiz“ schließt, zeigt das Label, meldet den value, Formular sendet ihn, is-active beim Öffnen', async () => {
    const { gemeldet } = formular()
    await flushPromises()
    expect(land()).toBeFalsy() // Gegenprobe: vorher nichts gesendet
    await klick(eins('.kit-combobox__trigger')!)
    await vi.waitFor(() => expect(items()).toHaveLength(3))
    await klick(items().find((i) => i.textContent?.includes('Schweiz'))!)

    await vi.waitFor(() => expect(offen()).toBe(false))
    expect(eingabe().value).toBe('Schweiz')
    expect(gemeldet).toContain('ch')
    expect(land()).toBe('ch')

    await klick(eins('.kit-combobox__trigger')!)
    await vi.waitFor(() => expect(items()).toHaveLength(3))
    const aktiv = items().filter((i) => i.classList.contains('is-active'))
    expect(aktiv.map((i) => i.textContent?.trim())).toEqual(['Schweiz'])
  })

  // AK4
  test('Pfeil-runter und Enter wählen eine Option: Liste zu, Label im Feld, value gemeldet und gesendet', async () => {
    const { gemeldet } = formular()
    await flushPromises()
    await klick(eins('.kit-combobox__trigger')!)
    await vi.waitFor(() => expect(items()).toHaveLength(3))
    await taste(eingabe(), 'ArrowDown')
    await taste(eingabe(), 'Enter')

    await vi.waitFor(() => expect(offen()).toBe(false))
    expect(gemeldet).toHaveLength(1)
    const gewaehlt = OPT.find((o) => o.value === gemeldet[0])!
    expect(gewaehlt).toBeDefined()
    expect(eingabe().value).toBe(gewaehlt.label)
    expect(land()).toBe(gewaehlt.value)
  })

  // AK5
  test('v-model zeigt das Label zum Wert und folgt einer Änderung von außen', async () => {
    const w = montiere(`<kit-combobox v-model="land" :options="o" />`, () => ({ land: ref('at'), o: OPT }))
    await flushPromises()
    expect(eingabe().value).toBe('Österreich')
    ;(w.vm as unknown as { land: string }).land = 'ch'
    await flushPromises()
    await vi.waitFor(() => expect(eingabe().value).toBe('Schweiz'))
  })

  // Randfall 1
  test('ohne options und mit leerer Liste: kein Fehler, geöffnet „empty“', async () => {
    const fehler = vi.spyOn(console, 'error').mockImplementation(() => {})
    for (const extra of ['', ':options="[]"']) {
      montiere(`<kit-combobox name="x" ${extra} />`)
      await flushPromises()
      await klick(eins('.kit-combobox__trigger')!)
      await vi.waitFor(() => expect(sichtbar(eins('.kit-combobox__empty'))).toBe(true))
      expect(items()).toHaveLength(0)
      aufraeumen()
    }
    expect(fehler).not.toHaveBeenCalled()
  })

  // Randfall 2
  test('modelValue ohne passende Option: Eingabefeld leer, kein Fehler', async () => {
    const fehler = vi.spyOn(console, 'error').mockImplementation(() => {})
    montiere(`<kit-combobox model-value="xx" :options="o" />`, () => ({ o: OPT }))
    await flushPromises()
    expect(eins('.kit-combobox')).not.toBeNull() // Gegenprobe: gerendert
    expect(eingabe().value).toBe('')
    expect(fehler).not.toHaveBeenCalled()
  })

  // Randfall 3
  test('deaktivierte Option trägt „disabled“ und lässt sich weder anklicken noch per Enter wählen', async () => {
    const einzeln = [{ value: 'de', label: 'Deutschland', disabled: true }]
    const { gemeldet } = formular([...einzeln, { value: 'at', label: 'Österreich' }])
    await flushPromises()
    await klick(eins('.kit-combobox__trigger')!)
    await vi.waitFor(() => expect(items()).toHaveLength(2))
    expect(items().map((i) => i.classList.contains('disabled'))).toEqual([true, false])

    await klick(items()[0])
    expect(gemeldet).toEqual([])
    await klick(items()[1]) // Gegenprobe: aktivierte Option wählt
    await vi.waitFor(() => expect(gemeldet).toEqual(['at']))

    aufraeumen()
    const nur = formular(einzeln)
    await flushPromises()
    await klick(eins('.kit-combobox__trigger')!)
    await vi.waitFor(() => expect(items()).toHaveLength(1))
    await taste(eingabe(), 'ArrowDown')
    await taste(eingabe(), 'Enter')
    expect(nur.gemeldet).toEqual([])
  })

  // AK13
  test('invalid: Wurzel is-invalid und aria-invalid am Eingabefeld', async () => {
    formular(OPT, 'invalid')
    await flushPromises()
    expect(eins('.kit-combobox')!.classList.contains('is-invalid')).toBe(true)
    expect(eingabe().getAttribute('aria-invalid')).toBe('true')
    aufraeumen()
    formular() // Gegenprobe
    await flushPromises()
    expect(eins('.kit-combobox')!.classList.contains('is-invalid')).toBe(false)
  })

  // AK13
  test('disabled: Wurzel disabled, Eingabefeld disabled, Klick auf den Trigger öffnet nicht', async () => {
    formular(OPT, 'disabled')
    await flushPromises()
    expect(eins('.kit-combobox')!.classList.contains('disabled')).toBe(true)
    expect(eingabe().disabled).toBe(true)
    await klick(eins('.kit-combobox__trigger')!)
    await flushPromises()
    expect(offen()).toBe(false)
    expect(eins('.kit-combobox')!.classList.contains('is-open')).toBe(false)
  })

  // AK14
  test('id und required stehen am Eingabefeld, nicht an der Wurzel', async () => {
    formular(OPT, 'id="feld" required')
    await flushPromises()
    expect(eingabe().id).toBe('feld')
    expect(eingabe().hasAttribute('required')).toBe(true)
    const wurzel = eins('.kit-combobox')!
    expect(wurzel.hasAttribute('id')).toBe(false)
    expect(wurzel.hasAttribute('required')).toBe(false)
  })
})
