// Inseln-IDs (AK12): jede Insel ist eine eigene Vue-App, Datalist-ids dürfen sich nicht überschneiden.
import { afterEach, describe, expect, test } from 'vitest'
// @ts-expect-error JS-Modul ohne Typen
import { mountInseln } from '../../js/mount-inseln.js'
import { kitKomponenten } from '../../vue3/index'
import { alle } from './helfer'

afterEach(() => {
  document.body.innerHTML = ''
})

describe('Inseln: eindeutige IDs', () => {
  // AK12
  test('zwei kit-input-list-Inseln mit gleichem name haben verschiedene Datalist-ids', async () => {
    const props = (optionen: string[]) => JSON.stringify({ name: 'stadt', options: optionen })
    document.body.innerHTML =
      `<div data-vue="kit-input-list" data-props='${props(['Berlin', 'Bonn'])}'></div>` +
      `<div data-vue="kit-input-list" data-props='${props(['Hamburg', 'Hannover'])}'></div>`

    await mountInseln(kitKomponenten)
    await new Promise((r) => setTimeout(r, 0))

    const listen = alle('.kit-input-list__datalist')
    const eingaben = alle('.kit-input-list__input')
    // Gegenprobe: Aufbau greift, zwei Listen mit nicht leeren ids
    expect(listen).toHaveLength(2)
    expect(eingaben).toHaveLength(2)
    const ids = listen.map((l) => l.id)
    expect(ids.every((id) => id !== '')).toBe(true)

    expect(ids[0]).not.toBe(ids[1])
    eingaben.forEach((eingabe, i) => expect(eingabe.getAttribute('list')).toBe(ids[i]))
  })
})
