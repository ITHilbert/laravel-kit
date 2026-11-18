// AK22 (#1612): kit-select ohne placeholder gleicht das Modell mit der sichtbaren Startauswahl ab
// (update:modelValue mit dem Wert der ersten wählbaren Option); Randfall 3.
import { flushPromises } from '@vue/test-utils'
import { afterEach, describe, expect, test } from 'vitest'
import { ref } from 'vue'
import { aufraeumen, montiere } from './helfer'

afterEach(aufraeumen)

const OPT = [
  { value: 'de', label: 'Deutschland' },
  { value: 'at', label: 'Österreich' },
]
const OPT_ERSTE_GESPERRT = [{ value: 'xx', label: 'Gesperrt', disabled: true }, ...OPT]
const OPT_ZAHL = [
  { value: 7, label: 'Sieben' },
  { value: 8, label: 'Acht' },
]

/** Mit v-model: liefert die Modell-Ref und alle gesendeten Werte. */
async function mitModell(start: unknown, optionen: unknown[] = OPT, extra = '') {
  const m = ref<unknown>(start)
  const o = ref<unknown[]>(optionen)
  const gesendet: unknown[] = []
  montiere(`<kit-select name="land" :options="o" v-model="m" @update:model-value="g" ${extra} />`, () => ({
    m,
    o,
    g: (v: unknown) => gesendet.push(v),
  }))
  await flushPromises()
  return { m, o, gesendet }
}

/** Ohne Modell-Bindung, nur Beobachtung der gesendeten Werte. */
async function beobachtet(modelValue: string | null, optionen: unknown[], extra = '') {
  const gesendet: unknown[] = []
  montiere(`<kit-select name="land" :options="o" :model-value="mv" @update:model-value="g" ${extra} />`, () => ({
    o: optionen,
    mv: modelValue,
    g: (v: unknown) => gesendet.push(v),
  }))
  await flushPromises()
  return gesendet
}

describe('kit-select ohne placeholder: Modell folgt der Startauswahl (AK22)', () => {
  const STARTS: Array<[string, unknown]> = [
    ['null', null],
    ['undefined', undefined],
    ['ohne passende Option (zz)', 'zz'],
  ]

  // AK22 / Randfall 3
  test.each(STARTS)('Modell %s: nach Mount ist das Modell die erste Option', async (_n, start) => {
    const { m, gesendet } = await mitModell(start)
    expect(m.value).toBe('de')
    expect(gesendet).toEqual(['de'])
  })

  // AK22 / Randfall 3
  test('erste Option gesperrt: Modell wird die erste wählbare', async () => {
    const { m } = await mitModell(null, OPT_ERSTE_GESPERRT)
    expect(m.value).toBe('de')
  })

  // AK22
  test('Zahlen-Werte kommen als Zahl zurück', async () => {
    const { m } = await mitModell(null, OPT_ZAHL)
    expect(m.value).toBe(7)
  })

  // AK22
  test('Optionen ändern sich, Modell passt nicht mehr: Modell wird die erste wählbare der neuen Optionen', async () => {
    const { m, o } = await mitModell('at')
    expect(m.value).toBe('at') // Gegenprobe: passendes Modell bleibt beim Start unverändert
    o.value = [
      { value: 'ch', label: 'Schweiz', disabled: true },
      { value: 'fr', label: 'Frankreich' },
    ]
    await flushPromises()
    expect(m.value).toBe('fr')
  })

  // AK22 (Gegenproben: sendet beim Start nichts)
  test('sendet beim Start nichts: passender Wert, placeholder, alle gesperrt, keine Optionen', async () => {
    // Der Aufbau (Beobachtung über @update:model-value) greift: dieselbe Ereignisform liefert in den Tests oben 'de'.
    expect(await beobachtet('at', OPT)).toEqual([])
    aufraeumen()
    expect(await beobachtet(null, OPT, 'placeholder="Bitte wählen"')).toEqual([])
    aufraeumen()
    expect(await beobachtet(null, OPT.map((x) => ({ ...x, disabled: true })))).toEqual([])
    aufraeumen()
    expect(await beobachtet(null, [])).toEqual([])
  })
})
