// Gemeinsame Helfer der Vue-3-Tests (kein Test, wird von den *.test.ts eingebunden).
import { flushPromises, mount, type VueWrapper } from '@vue/test-utils'
import { defineComponent } from 'vue'
import { KitVue3 } from '../../vue3/index'

let aktiv: VueWrapper | null = null

/** Mountet ein Template mit allen Kit-Komponenten (Plugin KitVue3) am body, wie ein Projekt es einbindet. */
export function montiere(template: string, setup?: () => Record<string, unknown>) {
  const komponente = defineComponent({ template, setup })
  aktiv = mount(komponente, { attachTo: document.body, global: { plugins: [KitVue3] } })
  return aktiv
}

export function aufraeumen() {
  aktiv?.unmount()
  aktiv = null
  document.body.innerHTML = ''
}

/** Klick wie im Browser: mousedown, mouseup, click (Reka-Tabs reagieren auf mousedown). */
export async function klick(el: Element) {
  const opt = { bubbles: true, cancelable: true, button: 0 }
  el.dispatchEvent(new MouseEvent('mousedown', opt))
  el.dispatchEvent(new MouseEvent('mouseup', opt))
  el.dispatchEvent(new MouseEvent('click', opt))
  await flushPromises()
}

export async function taste(el: Element, key: string) {
  el.dispatchEvent(new KeyboardEvent('keydown', { key, bubbles: true, cancelable: true }))
  await flushPromises()
}

/** „Sichtbar“: im DOM, ohne hidden-Attribut und nicht per display:none ausgeblendet. */
export function sichtbar(el: Element | null | undefined): boolean {
  if (!el || !el.isConnected) return false
  for (let n: Element | null = el; n; n = n.parentElement) {
    if ((n as HTMLElement).hidden) return false
    if (getComputedStyle(n).display === 'none') return false
  }
  return true
}

export const alle = (sel: string): HTMLElement[] => Array.from(document.querySelectorAll<HTMLElement>(sel))
export const eins = (sel: string): HTMLElement | null => document.querySelector<HTMLElement>(sel)
