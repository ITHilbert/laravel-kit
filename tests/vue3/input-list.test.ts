// Liste mit Vorschlägen (AK12, AK13, AK14).
import { flushPromises } from '@vue/test-utils'
import { afterEach, describe, expect, test, vi } from 'vitest'
import { alle, aufraeumen, eins, montiere } from './helfer'

afterEach(() => {
  aufraeumen()
  vi.restoreAllMocks()
})

const OPT = [
  { value: 'Berlin', label: 'Berlin' },
  { value: 'Bonn', label: 'Bonn' },
]
const eingabe = () => eins('.kit-input-list__input') as HTMLInputElement
const liste = (extra = '') => montiere(`<kit-input-list name="stadt" :options="o" ${extra} />`, () => ({ o: OPT }))

describe('Liste', () => {
  // AK12
  test('Eingabefeld zeigt per list auf die Datalist mit zwei Optionen', async () => {
    liste()
    await flushPromises()
    expect(eins('.kit-input-list')).not.toBeNull()
    expect(eingabe().name).toBe('stadt')
    const id = eingabe().getAttribute('list')
    expect(id).toBeTruthy()
    const datalist = eins('.kit-input-list__datalist')!
    expect(datalist.tagName).toBe('DATALIST')
    expect(datalist.id).toBe(id)
    expect(datalist.querySelectorAll('option')).toHaveLength(2)
  })

  // AK12
  test('zwei Listen mit gleichem name haben verschiedene Datalist-ids', async () => {
    montiere(`<div><kit-input-list name="stadt" :options="o" /><kit-input-list name="stadt" :options="o" /></div>`, () => ({ o: OPT }))
    await flushPromises()
    const ids = alle('.kit-input-list__datalist').map((d) => d.id)
    expect(ids).toHaveLength(2)
    expect(ids[0]).toBeTruthy()
    expect(ids[0]).not.toBe(ids[1])
    expect(alle('.kit-input-list__input').map((i) => i.getAttribute('list'))).toEqual(ids)
  })

  // AK13
  test('invalid: is-invalid an der Wurzel, aria-invalid am Feld', async () => {
    liste('invalid')
    await flushPromises()
    expect(eins('.kit-input-list')!.classList.contains('is-invalid')).toBe(true)
    expect(eingabe().getAttribute('aria-invalid')).toBe('true')
    aufraeumen()
    liste() // Gegenprobe
    await flushPromises()
    expect(eins('.kit-input-list')!.classList.contains('is-invalid')).toBe(false)
  })

  // AK13
  test('disabled: Wurzel disabled und Feld disabled', async () => {
    liste('disabled')
    await flushPromises()
    expect(eins('.kit-input-list')!.classList.contains('disabled')).toBe(true)
    expect(eingabe().disabled).toBe(true)
  })

  // AK14
  test('id und required stehen am Eingabefeld, nicht an der Wurzel', async () => {
    liste('id="feld" required')
    await flushPromises()
    expect(eingabe().id).toBe('feld')
    expect(eingabe().hasAttribute('required')).toBe(true)
    expect(eins('.kit-input-list')!.hasAttribute('id')).toBe(false)
    expect(eins('.kit-input-list')!.hasAttribute('required')).toBe(false)
  })
})
