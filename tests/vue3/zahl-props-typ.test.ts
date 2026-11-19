// Zahlenfelder: step, min, max als Zahl oder Text (Randfall 7, #1611).
import { flushPromises } from '@vue/test-utils'
import { defineComponent, h } from 'vue'
import { mount } from '@vue/test-utils'
import { afterEach, describe, expect, test, vi } from 'vitest'
import { aufraeumen, eins, montiere } from './helfer'

afterEach(() => {
  aufraeumen()
  vi.restoreAllMocks()
})

const FELDER = ['input-number', 'input-int', 'input-euro', 'input-percent']
const attribute = (f: string) => {
  const el = eins(`.kit-${f}__input`) as HTMLInputElement
  return { step: el.getAttribute('step'), min: el.getAttribute('min'), max: el.getAttribute('max') }
}

describe('Zahlenfelder: Typ von step, min, max', () => {
  // Randfall 7
  test('der Spion erkennt eine Vue-Warnung (Gegenprobe)', () => {
    const warn = vi.spyOn(console, 'warn').mockImplementation(() => {})
    const Prueffeld = defineComponent({ props: { x: String }, render: () => h('i') })
    mount(Prueffeld, { props: { x: 5 as unknown as string } })
    expect(warn).toHaveBeenCalled()
  })

  // Randfall 7
  test.each(FELDER)('%s: Zahl und Text ohne Warnung, Attribute am Eingabefeld gleich', async (f) => {
    const warn = vi.spyOn(console, 'warn').mockImplementation(() => {})

    montiere(`<kit-${f} name="z" :step="0.5" :min="1" :max="9" />`)
    await flushPromises()
    const alsZahl = attribute(f)
    aufraeumen()

    montiere(`<kit-${f} name="z" step="0.5" min="1" max="9" />`)
    await flushPromises()
    const alsText = attribute(f)

    expect(warn).not.toHaveBeenCalled()
    expect(alsZahl).toEqual({ step: '0.5', min: '1', max: '9' })
    expect(alsText).toEqual(alsZahl)
  })
})
