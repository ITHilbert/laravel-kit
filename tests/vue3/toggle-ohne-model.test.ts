// Toggle ohne v-model (AK12): wie in Blade-Seiten und Inseln, wo kein Eltern-Zustand existiert.
import { flushPromises } from '@vue/test-utils'
import { afterEach, describe, expect, test } from 'vitest'
// @ts-expect-error JS-Modul ohne Typen
import { mountInseln } from '../../js/mount-inseln.js'
import { kitKomponenten } from '../../vue3/index'
import { aufraeumen, eins, klick, montiere } from './helfer'

afterEach(() => {
  aufraeumen()
})

const knopf = () => eins('.kit-toggle__input') as HTMLButtonElement
const wert = (sel: string) => new FormData(eins(sel) as HTMLFormElement).get('aktiv')

describe('Toggle ohne v-model', () => {
  // AK12
  test('Klick schaltet aria-checked, is-active und das Formularfeld, zweiter Klick schaltet zurück', async () => {
    montiere('<form id="f"><kit-toggle name="aktiv" label="Aktiv" /></form>')
    await flushPromises()
    // Gegenprobe: Ausgangszustand
    expect(knopf().getAttribute('aria-checked')).toBe('false')
    expect(eins('.kit-toggle')!.classList.contains('is-active')).toBe(false)
    expect(wert('#f')).toBe('0')

    await klick(knopf())
    expect(knopf().getAttribute('aria-checked')).toBe('true')
    expect(eins('.kit-toggle')!.classList.contains('is-active')).toBe(true)
    expect(wert('#f')).toBe('1')

    await klick(knopf())
    expect(knopf().getAttribute('aria-checked')).toBe('false')
    expect(eins('.kit-toggle')!.classList.contains('is-active')).toBe(false)
    expect(wert('#f')).toBe('0')
  })

  // AK12
  test('als Insel ohne Eltern-Zustand schaltet der Klick und das Formular sendet aktiv=1', async () => {
    document.body.innerHTML = `<form id="f"><div id="i" data-vue="kit-toggle" data-props='{"name":"aktiv"}'></div></form>`
    expect(eins('#i .kit-toggle')).toBeNull() // Gegenprobe: vorher leer

    await mountInseln(kitKomponenten)
    await new Promise((r) => setTimeout(r, 0))
    expect(knopf().getAttribute('aria-checked')).toBe('false')
    expect(wert('#f')).toBe('0')

    await klick(knopf())
    expect(knopf().getAttribute('aria-checked')).toBe('true')
    expect(eins('.kit-toggle')!.classList.contains('is-active')).toBe(true)
    expect(wert('#f')).toBe('1')
  })
})
