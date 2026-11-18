// Inseln (AK12, dazu Randfall 4): mountInseln(kitKomponenten) mountet die Kit-Komponenten.
import { afterEach, describe, expect, test, vi } from 'vitest'
// @ts-expect-error JS-Modul ohne Typen
import { mountInseln } from '../../js/mount-inseln.js'
import { kitKomponenten } from '../../vue3/index'
import { alle, eins } from './helfer'

afterEach(() => {
  document.body.innerHTML = ''
  vi.restoreAllMocks()
})

describe('Inseln', () => {
  // AK12
  test('Tabs-Insel mountet ohne Konsolenfehler und Warnung', async () => {
    const warn = vi.spyOn(console, 'warn').mockImplementation(() => {})
    const fehler = vi.spyOn(console, 'error').mockImplementation(() => {})
    document.body.innerHTML = `<div id="t" data-vue="kit-tabs" data-props='{"defaultValue":"a"}'></div>`
    expect(eins('#t .kit-tabs')).toBeNull() // Gegenprobe: vorher leer

    await mountInseln(kitKomponenten)
    await new Promise((r) => setTimeout(r, 0))

    expect(eins('#t .kit-tabs')).not.toBeNull()
    expect(warn).not.toHaveBeenCalled()
    expect(fehler).not.toHaveBeenCalled()
  })

  // AK12 und Randfall 4
  test('zwei Inseln (Dialog, Tabs) unabhängig; Dialog-Inhalt liegt außerhalb der Insel', async () => {
    vi.spyOn(console, 'warn').mockImplementation(() => {}) // Dialog ohne Titel warnt (Reka)
    const fehler = vi.spyOn(console, 'error').mockImplementation(() => {})
    document.body.innerHTML = `
      <div id="d" data-vue="kit-dialog" data-props='{"open":true}'></div>
      <div id="t" data-vue="kit-tabs" data-props='{"defaultValue":"a"}'></div>`

    await mountInseln(kitKomponenten)
    await new Promise((r) => setTimeout(r, 0))

    const inhalt = alle('.kit-dialog__content')
    expect(inhalt).toHaveLength(1)
    expect(eins('#t .kit-tabs')).not.toBeNull()
    expect(eins('#d')!.contains(inhalt[0])).toBe(false)
    expect(eins('#t')!.contains(inhalt[0])).toBe(false)
    expect(document.body.contains(inhalt[0])).toBe(true)
    expect(fehler).not.toHaveBeenCalled()
  })
})
