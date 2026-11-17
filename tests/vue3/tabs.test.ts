// Tabs (AK5, AK6, AK9 für kit-tab, Randfall 2).
import { flushPromises } from '@vue/test-utils'
import { afterEach, describe, expect, test, vi } from 'vitest'
import { alle, aufraeumen, eins, klick, montiere, sichtbar, taste } from './helfer'

afterEach(() => {
  aufraeumen()
  vi.restoreAllMocks()
})

const drei = (attr = '', defaultValue = 'default-value="a"') => `
  <kit-tabs ${defaultValue}>
    <div role="presentation">
      <kit-tab value="a">Tab A</kit-tab>
      <kit-tab value="b" ${attr}>Tab B</kit-tab>
      <kit-tab value="c">Tab C</kit-tab>
    </div>
    <kit-tab-panel value="a">Inhalt-A</kit-tab-panel>
    <kit-tab-panel value="b">Inhalt-B</kit-tab-panel>
    <kit-tab-panel value="c">Inhalt-C</kit-tab-panel>
  </kit-tabs>`

const sichtbarePanels = () => alle('.kit-tabs__panel').filter((p) => sichtbar(p))
const tab = (n: 'a' | 'b' | 'c') => alle('.kit-tabs__tab')[{ a: 0, b: 1, c: 2 }[n]]

describe('Tabs', () => {
  // AK5
  test('rendert Liste, drei Tabs und genau ein sichtbares Panel; aktiv ist nur „a“', async () => {
    montiere(drei())
    await flushPromises()

    expect(eins('.kit-tabs')).not.toBeNull()
    expect(eins('.kit-tabs__list')?.getAttribute('role')).toBe('tablist')
    const tabs = alle('.kit-tabs__tab')
    expect(tabs).toHaveLength(3)
    expect(tabs.map((t) => t.getAttribute('role'))).toEqual(['tab', 'tab', 'tab'])
    expect(tabs.map((t) => t.getAttribute('aria-selected'))).toEqual(['true', 'false', 'false'])
    expect(tabs.map((t) => t.classList.contains('is-active'))).toEqual([true, false, false])

    const panels = sichtbarePanels()
    expect(panels).toHaveLength(1)
    expect(panels[0].getAttribute('role')).toBe('tabpanel')
    expect(panels[0].textContent).toContain('Inhalt-A')
    expect(panels[0].classList.contains('is-active')).toBe(true)
  })

  // AK6
  test('Pfeil-rechts fokussiert Tab „b“; Klick auf „c“ zeigt Panel „c“ aktiv, „a“ weg', async () => {
    montiere(drei())
    await flushPromises()
    tab('a').focus()
    expect(document.activeElement).toBe(tab('a')) // Gegenprobe: Start auf „a“

    await taste(tab('a'), 'ArrowRight')
    await vi.waitFor(() => expect(document.activeElement).toBe(tab('b')))

    await klick(tab('c'))
    await vi.waitFor(() => {
      const panels = sichtbarePanels()
      expect(panels).toHaveLength(1)
      expect(panels[0].textContent).toContain('Inhalt-C')
      expect(panels[0].classList.contains('is-active')).toBe(true)
    })
    expect(sichtbarePanels().some((p) => p.textContent?.includes('Inhalt-A'))).toBe(false)
  })

  // AK9 (kit-tab)
  test('deaktivierter Tab trägt „disabled“ und lässt sich nicht anklicken', async () => {
    montiere(drei('disabled'))
    await flushPromises()
    expect(tab('b').classList.contains('disabled')).toBe(true)
    expect(tab('a').classList.contains('disabled')).toBe(false)

    await klick(tab('c')) // Gegenprobe: ein aktivierter Tab wechselt
    await vi.waitFor(() => expect(sichtbarePanels()[0]?.textContent).toContain('Inhalt-C'))

    await klick(tab('b'))
    await flushPromises()
    expect(sichtbarePanels()[0]?.textContent).toContain('Inhalt-C')
    expect(tab('b').getAttribute('aria-selected')).toBe('false')
  })

  // Randfall 2
  test('ohne default-value: kein Tab aktiv, kein Panel sichtbar, kein Fehler', async () => {
    const fehler = vi.spyOn(console, 'error').mockImplementation(() => {})
    montiere(drei('', ''))
    await flushPromises()

    expect(alle('.kit-tabs__tab')).toHaveLength(3) // Gegenprobe: gerendert
    expect(alle('.kit-tabs__tab.is-active')).toHaveLength(0)
    expect(sichtbarePanels()).toHaveLength(0)
    expect(fehler).not.toHaveBeenCalled()
  })
})
