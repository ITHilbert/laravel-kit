// Inseln mit Logik-Komponenten (AK17): Combobox als Insel in einem Formular.
import { flushPromises } from '@vue/test-utils'
import { afterEach, describe, expect, test, vi } from 'vitest'
// @ts-expect-error JS-Modul ohne Typen
import { mountInseln } from '../../js/mount-inseln.js'
import { kitKomponenten } from '../../vue3/index'
import { alle, eins, klick } from './helfer'

afterEach(() => {
  document.body.innerHTML = ''
  vi.restoreAllMocks()
})

describe('Inseln mit Logik', () => {
  // AK17
  test('Combobox-Insel mountet ohne Konsolenfehler; Auswahl landet im Formular unter „land“', async () => {
    const warn = vi.spyOn(console, 'warn').mockImplementation(() => {})
    const fehler = vi.spyOn(console, 'error').mockImplementation(() => {})
    const optionen = [
      { value: 'de', label: 'Deutschland' },
      { value: 'ch', label: 'Schweiz' },
    ]
    document.body.innerHTML = `<form id="f"><div id="i" data-vue="kit-combobox" data-props='${JSON.stringify({ name: 'land', options: optionen })}'></div></form>`
    expect(eins('#i .kit-combobox')).toBeNull() // Gegenprobe: vorher leer

    await mountInseln(kitKomponenten)
    await new Promise((r) => setTimeout(r, 0))
    expect(eins('#i .kit-combobox')).not.toBeNull()

    await klick(eins('.kit-combobox__trigger')!)
    await vi.waitFor(() => expect(alle('.kit-combobox__item')).toHaveLength(2))
    await klick(alle('.kit-combobox__item').find((i) => i.textContent?.includes('Schweiz'))!)
    await flushPromises()

    expect(new FormData(eins('#f') as HTMLFormElement).get('land')).toBe('ch')
    expect(fehler).not.toHaveBeenCalled()
    expect(warn).not.toHaveBeenCalled()
  })
})
