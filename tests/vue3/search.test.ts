// Suche (AK6–AK8, AK13, AK14, Randfall 5).
import { flushPromises } from '@vue/test-utils'
import { ref } from 'vue'
import { afterEach, beforeEach, describe, expect, test, vi } from 'vitest'
import { aufraeumen, eins, klick, montiere, taste } from './helfer'

beforeEach(() => {
  vi.useFakeTimers({ toFake: ['setTimeout', 'clearTimeout'] })
})
afterEach(() => {
  vi.useRealTimers()
  aufraeumen()
  vi.restoreAllMocks()
})

const eingabe = () => eins('.kit-search__input') as HTMLInputElement
const leeren = () => eins('.kit-search__clear')

async function tippe(text: string) {
  eingabe().value = text
  eingabe().dispatchEvent(new Event('input', { bubbles: true }))
  await flushPromises()
}

function suche(attrs = ':verzoegerung="300"', start = '') {
  const suchen: string[] = []
  const modell: string[] = []
  const w = montiere(
    `<div><kit-search v-if="an" name="q" ${attrs} ${start} @suche="s" @update:model-value="m" /></div>`,
    () => ({ an: ref(true), s: (v: string) => suchen.push(v), m: (v: string) => modell.push(v) }),
  )
  return { w, suchen, modell }
}

describe('Suche', () => {
  // AK6
  test('rendert Wurzel, Symbol, Eingabefeld type=search mit name; kein Leeren-Knopf', async () => {
    suche()
    await flushPromises()
    expect(eins('.kit-search')).not.toBeNull()
    expect(eins('.kit-search .kit-search__icon')?.getAttribute('aria-hidden')).toBe('true')
    expect(eingabe().type).toBe('search')
    expect(eingabe().name).toBe('q')
    expect(leeren()).toBeNull()
  })

  // AK7
  test('Tippen meldet update:modelValue sofort und „suche“ einmal 300 ms nach dem letzten Tastendruck', async () => {
    const { suchen, modell } = suche()
    await flushPromises()
    await tippe('abc')
    expect(modell).toContain('abc')
    expect(suchen).toEqual([])
    expect(leeren()).not.toBeNull()
    expect(leeren()!.getAttribute('aria-label')).toBe('Suche leeren')

    vi.advanceTimersByTime(299)
    expect(suchen).toEqual([])
    vi.advanceTimersByTime(1)
    expect(suchen).toEqual(['abc'])
    vi.advanceTimersByTime(2000)
    expect(suchen).toEqual(['abc'])
  })

  // AK8
  test.each([
    ['Klick auf den Leeren-Knopf', async () => klick(leeren()!)],
    ['Escape im Eingabefeld', async () => taste(eingabe(), 'Escape')],
  ])('%s: Wert leer, „suche“ sofort mit „“, Fokus im Feld, Knopf weg', async (_name, aktion) => {
    const { suchen } = suche(':verzoegerung="300"', 'model-value="abc"')
    await flushPromises()
    expect(eingabe().value).toBe('abc') // Gegenprobe: Aufbau greift
    expect(leeren()).not.toBeNull()

    await aktion()
    expect(eingabe().value).toBe('')
    expect(suchen).toEqual([''])
    expect(document.activeElement).toBe(eingabe())
    expect(leeren()).toBeNull()
  })

  // Randfall 5
  test('neuer Tastendruck innerhalb der Verzögerung verschiebt „suche“', async () => {
    const { suchen } = suche()
    await flushPromises()
    await tippe('a')
    vi.advanceTimersByTime(200)
    await tippe('ab')
    vi.advanceTimersByTime(200)
    expect(suchen).toEqual([]) // 400 ms nach dem ersten, 200 nach dem letzten Druck
    vi.advanceTimersByTime(100)
    expect(suchen).toEqual(['ab'])
  })

  // Randfall 5
  test('nach dem Aushängen kommt kein verspätetes „suche“', async () => {
    const { w, suchen } = suche()
    await flushPromises()
    await tippe('abc')
    expect(eins('.kit-search')).not.toBeNull() // Gegenprobe: Aufbau greift
    ;(w.vm as unknown as { an: boolean }).an = false
    await flushPromises()
    expect(eins('.kit-search')).toBeNull()
    vi.advanceTimersByTime(2000)
    expect(suchen).toEqual([])
  })

  // AK13
  test('invalid: is-invalid an der Wurzel, aria-invalid am Feld', async () => {
    suche(':verzoegerung="300" invalid')
    await flushPromises()
    expect(eins('.kit-search')!.classList.contains('is-invalid')).toBe(true)
    expect(eingabe().getAttribute('aria-invalid')).toBe('true')
    aufraeumen()
    suche() // Gegenprobe
    await flushPromises()
    expect(eins('.kit-search')!.classList.contains('is-invalid')).toBe(false)
  })

  // AK13
  test('disabled: Wurzel disabled, Feld disabled, Leeren ändert nichts', async () => {
    const { suchen } = suche(':verzoegerung="300" disabled', 'model-value="abc"')
    await flushPromises()
    expect(eins('.kit-search')!.classList.contains('disabled')).toBe(true)
    expect(eingabe().disabled).toBe(true)
    if (leeren()) await klick(leeren()!)
    expect(eingabe().value).toBe('abc')
    expect(suchen).toEqual([])
  })

  // AK14
  test('id und required stehen am Eingabefeld, nicht an der Wurzel', async () => {
    suche(':verzoegerung="300" id="feld" required')
    await flushPromises()
    expect(eingabe().id).toBe('feld')
    expect(eingabe().hasAttribute('required')).toBe(true)
    expect(eins('.kit-search')!.hasAttribute('id')).toBe(false)
    expect(eins('.kit-search')!.hasAttribute('required')).toBe(false)
  })
})
