// Accordion (AK7, AK8, AK9 für kit-accordion-item).
import { flushPromises } from '@vue/test-utils'
import { afterEach, describe, expect, test, vi } from 'vitest'
import { alle, aufraeumen, eins, klick, montiere, sichtbar } from './helfer'

afterEach(aufraeumen)

const accordion = (attrY = '') => `
  <kit-accordion type="single" collapsible>
    <kit-accordion-item value="x" title="Eintrag X"><p>Inhalt-X</p></kit-accordion-item>
    <kit-accordion-item value="y" title="Eintrag Y" ${attrY}><p>Inhalt-Y</p></kit-accordion-item>
  </kit-accordion>`

const item = (n: 0 | 1) => alle('.kit-accordion__item')[n]
const trigger = (n: 0 | 1) => item(n).querySelector<HTMLElement>('.kit-accordion__trigger')!
const inhaltSichtbar = (n: 0 | 1) => sichtbar(item(n).querySelector('.kit-accordion__content'))
const offen = (n: 0 | 1) => item(n).classList.contains('is-open') && trigger(n).getAttribute('aria-expanded') === 'true'

describe('Accordion', () => {
  // AK7
  test('rendert Einträge mit geschlossenem Trigger und unsichtbarem Inhalt', async () => {
    montiere(accordion())
    await flushPromises()

    expect(eins('.kit-accordion')).not.toBeNull()
    expect(alle('.kit-accordion__item')).toHaveLength(2)
    const triggers = alle('.kit-accordion__trigger')
    expect(triggers).toHaveLength(2)
    triggers.forEach((t) => {
      expect(t.tagName).toBe('BUTTON')
      expect(t.getAttribute('aria-expanded')).toBe('false')
    })
    expect(inhaltSichtbar(0)).toBe(false)
    expect(inhaltSichtbar(1)).toBe(false)
  })

  // AK8
  test('single/collapsible: x öffnen, y öffnen schließt x, zweiter Klick auf y schließt y', async () => {
    montiere(accordion())
    await flushPromises()
    expect(inhaltSichtbar(0)).toBe(false) // Gegenprobe: vorher zu

    await klick(trigger(0))
    await vi.waitFor(() => expect(inhaltSichtbar(0)).toBe(true))
    expect(item(0).classList.contains('is-open')).toBe(true)
    expect(trigger(0).getAttribute('aria-expanded')).toBe('true')

    await klick(trigger(1))
    await vi.waitFor(() => expect(offen(1) && inhaltSichtbar(1)).toBe(true))
    await vi.waitFor(() => expect(inhaltSichtbar(0)).toBe(false))
    expect(offen(0)).toBe(false)

    await klick(trigger(1))
    await vi.waitFor(() => expect(inhaltSichtbar(1)).toBe(false))
    expect(offen(1)).toBe(false)
  })

  // AK9 (kit-accordion-item)
  test('deaktivierter Eintrag trägt „disabled“ und lässt sich nicht öffnen', async () => {
    montiere(accordion('disabled'))
    await flushPromises()
    expect(item(1).classList.contains('disabled')).toBe(true)
    expect(item(0).classList.contains('disabled')).toBe(false)

    await klick(trigger(0)) // Gegenprobe: aktivierter Eintrag öffnet
    await vi.waitFor(() => expect(offen(0)).toBe(true))

    await klick(trigger(1))
    await flushPromises()
    expect(offen(1)).toBe(false)
    expect(inhaltSichtbar(1)).toBe(false)
  })
})
