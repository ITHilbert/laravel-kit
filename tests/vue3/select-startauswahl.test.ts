// Randfall 3 (Regression aus dem Review): kit-select ohne passenden Wert startet wie ein natives <select> mit der
// ersten wählbaren Option. happy-dom wählt nach einer Zuweisung ohne passende Option (anders als Chrome, selectedIndex -1)
// nachträglich wieder die erste Option. Darum wird zusätzlich mitgeschrieben, welche Werte die Komponente an
// select.value zuweist: ein Wert ohne passende Option ergibt im Browser „keine Auswahl“ und fehlt in FormData.
import { flushPromises } from '@vue/test-utils'
import { afterEach, beforeEach, describe, expect, test } from 'vitest'
import { aufraeumen, eins, montiere } from './helfer'

const proto = HTMLSelectElement.prototype
const original = Object.getOwnPropertyDescriptor(proto, 'value')!
let zugewiesen: Array<{ wert: string; passt: boolean }> = []

beforeEach(() => {
  zugewiesen = []
  Object.defineProperty(proto, 'value', {
    ...original,
    set(this: HTMLSelectElement, v: string) {
      zugewiesen.push({ wert: String(v), passt: Array.from(this.options).some((o) => o.value === String(v)) })
      original.set!.call(this, v)
    },
  })
})
afterEach(() => {
  Object.defineProperty(proto, 'value', original)
  aufraeumen()
})

const OPT = [
  { value: 'de', label: 'Deutschland' },
  { value: 'at', label: 'Österreich' },
]
const OPT_ERSTE_GESPERRT = [{ value: 'xx', label: 'Gesperrt', disabled: true }, ...OPT]

async function start(tag: string, optionen = OPT) {
  montiere(`<form id="f">${tag}</form>`, () => ({ o: optionen }))
  await flushPromises()
  const s = eins('.kit-select__input') as HTMLSelectElement
  return { s, form: new FormData(eins('#f') as HTMLFormElement) }
}

describe('Select ohne placeholder: Startauswahl', () => {
  const FAELLE: Array<[string, string]> = [
    ['ohne v-model', '<kit-select name="land" :options="o" />'],
    ['modelValue null', '<kit-select name="land" :options="o" :model-value="null" />'],
    ['modelValue ohne passende Option', '<kit-select name="land" :options="o" model-value="zz" />'],
  ]

  // Randfall 3
  test.each(FAELLE)('%s: erste Option gewählt, FormData sendet ihren Wert, kein Wert ohne Option zugewiesen', async (_n, tag) => {
    const { s, form } = await start(tag)
    expect(s.selectedIndex).toBe(0)
    expect(s.value).toBe('de')
    expect(form.get('land')).toBe('de')
    expect(zugewiesen.filter((z) => !z.passt).map((z) => z.wert)).toEqual([])
  })

  // Randfall 3
  test('erste Option disabled: die erste wählbare ist gewählt und wird gesendet', async () => {
    const { s, form } = await start('<kit-select name="land" :options="o" />', OPT_ERSTE_GESPERRT)
    expect(s.selectedIndex).toBe(1)
    expect(form.get('land')).toBe('de')
    expect(zugewiesen.filter((z) => !z.passt).map((z) => z.wert)).toEqual([])
  })

  // Randfall 3 (Gegenprobe: passender modelValue wählt seine Option)
  test('passender modelValue wählt genau diese Option', async () => {
    const { s, form } = await start('<kit-select name="land" :options="o" model-value="at" />')
    expect(zugewiesen.map((z) => z.wert)).toContain('at')
    expect(s.selectedIndex).toBe(1)
    expect(form.get('land')).toBe('at')
  })
})
