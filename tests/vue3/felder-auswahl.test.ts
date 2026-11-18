// Auswahlfelder, Schalter, Datei (AK5, AK9–AK13, Randfälle 3–5).
import { flushPromises } from '@vue/test-utils'
import { ref } from 'vue'
import { afterEach, describe, expect, test } from 'vitest'
import { alle, aufraeumen, eins, klick, montiere } from './helfer'

afterEach(aufraeumen)

const OPT = [
  { value: 'de', label: 'Deutschland' },
  { value: 'at', label: 'Österreich', disabled: true },
]
const select = () => eins('.kit-select__input') as HTMLSelectElement
const daten = (f: string) => new FormData(eins(f) as HTMLFormElement)

describe('disabled', () => {
  const FELDER: Array<[string, string]> = [
    ['input-text', ''],
    ['input-email', ''],
    ['input-password', ''],
    ['input-number', ''],
    ['input-int', ''],
    ['input-euro', ''],
    ['input-percent', ''],
    ['input-date', ''],
    ['input-color', ''],
    ['input-file', ''],
    ['textarea', ''],
    ['select', ':options="[{ value: \'a\', label: \'A\' }]"'],
    ['checkbox', 'value="1"'],
    ['radio', 'value="a"'],
    ['toggle', ''],
  ]

  // AK5
  test.each(FELDER)('%s: Wurzel trägt disabled, Eingabefeld ist disabled', async (f, zusatz) => {
    montiere(`<kit-${f} name="f" disabled ${zusatz} />`)
    await flushPromises()
    expect(eins(`.kit-${f}`)!.classList.contains('disabled')).toBe(true)
    expect((eins(`.kit-${f}__input`) as HTMLInputElement).disabled).toBe(true)
    aufraeumen()
    montiere(`<kit-${f} name="f" ${zusatz} />`)
    await flushPromises()
    expect(eins(`.kit-${f}`)!.classList.contains('disabled')).toBe(false) // Gegenprobe
    expect((eins(`.kit-${f}__input`) as HTMLInputElement).disabled).toBe(false)
  })

  // AK5
  test('Klick auf Toggle, Checkbox und Passwort-Schalter ändert bei disabled nichts, ohne disabled schon', async () => {
    const an = ref(false)
    const ok = ref(false)
    montiere(
      `<div>
        <kit-toggle v-model="an" disabled />
        <kit-checkbox v-model="ok" disabled>Ja</kit-checkbox>
        <kit-input-password name="pw" disabled />
        <kit-toggle class="frei" v-model="an2" />
        <kit-input-password class="frei2" name="pw2" />
      </div>`,
      () => ({ an, ok, an2: ref(false) }),
    )
    await flushPromises()
    await klick(alle('.kit-toggle__input')[0])
    await klick(eins('.kit-checkbox__input')!)
    await klick(eins('.kit-checkbox__label')!)
    await klick(alle('.kit-input-password__toggle')[0])
    expect(an.value).toBe(false)
    expect(ok.value).toBe(false)
    expect((alle('.kit-input-password__input')[0] as HTMLInputElement).type).toBe('password')
    expect(alle('.kit-input-password__toggle')[0].getAttribute('aria-pressed')).toBe('false')

    // Gegenprobe: die freien Felder reagieren
    await klick(alle('.kit-toggle__input')[1])
    expect(alle('.kit-toggle')[1].classList.contains('is-active')).toBe(true)
    await klick(alle('.kit-input-password__toggle')[1])
    expect((alle('.kit-input-password__input')[1] as HTMLInputElement).type).toBe('text')
  })
})

describe('Select', () => {
  // AK9
  test('Platzhalter und Optionen, Auswahl und Wert von außen', async () => {
    const land = ref('')
    montiere('<kit-select name="land" placeholder="Bitte wählen" :options="o" v-model="land" />', () => ({ o: OPT, land }))
    await flushPromises()
    expect(select().tagName).toBe('SELECT')
    const opts = Array.from(select().querySelectorAll('option'))
    expect(opts).toHaveLength(3)
    expect(opts[0].textContent?.trim()).toBe('Bitte wählen')
    expect(opts[0].value).toBe('')
    expect(opts[1].value).toBe('de')
    expect(opts[2].textContent?.trim()).toBe('Österreich')
    expect(opts[2].disabled).toBe(true)
    expect(opts[1].disabled).toBe(false)

    select().value = 'de'
    select().dispatchEvent(new Event('change', { bubbles: true }))
    await flushPromises()
    expect(land.value).toBe('de')

    land.value = 'at'
    await flushPromises()
    expect(select().value).toBe('at')
    expect(select().querySelectorAll('option')[2].selected).toBe(true)
  })

  // AK9
  test('Zahlen-Werte kommen als Zahl zurück', async () => {
    const n = ref<number | string>('')
    montiere('<kit-select name="n" :options="o" v-model="n" />', () => ({ o: [{ value: 1, label: 'Eins' }, { value: 2, label: 'Zwei' }], n }))
    await flushPromises()
    select().value = '2'
    select().dispatchEvent(new Event('change', { bubbles: true }))
    await flushPromises()
    expect(n.value).toBe(2)
    expect(typeof n.value).toBe('number')
  })

  // Randfall 3
  test('ohne placeholder keine leere erste Option; mit placeholder schon', async () => {
    montiere('<kit-select name="land" :options="o" />', () => ({ o: OPT }))
    await flushPromises()
    const opts = Array.from(select().querySelectorAll('option'))
    expect(opts).toHaveLength(2)
    expect(opts.some((o) => o.value === '')).toBe(false)
    aufraeumen()
    montiere('<kit-select name="land" placeholder="Wählen" :options="o" />', () => ({ o: OPT }))
    await flushPromises()
    expect(select().querySelectorAll('option')[0].value).toBe('')
  })
})

describe('Checkbox und Radio', () => {
  // AK10
  test('Eingabefeld vor Label mit Slot-Text, Klick auf das Label schaltet, Wert von außen hakt an', async () => {
    const ok = ref(false)
    montiere('<kit-checkbox name="agb" value="1" v-model="ok">Ich stimme zu</kit-checkbox>', () => ({ ok }))
    await flushPromises()
    const input = eins('.kit-checkbox__input') as HTMLInputElement
    const label = eins('.kit-checkbox__label') as HTMLLabelElement
    expect(input.type).toBe('checkbox')
    expect(input.name).toBe('agb')
    expect(input.value).toBe('1')
    expect(input.compareDocumentPosition(label) & Node.DOCUMENT_POSITION_FOLLOWING).toBeTruthy()
    expect(label.textContent?.trim()).toBe('Ich stimme zu')
    expect(input.id).not.toBe('')
    expect(label.getAttribute('for')).toBe(input.id)

    await klick(label)
    expect(ok.value).toBe(true)
    await klick(label)
    expect(ok.value).toBe(false)

    ok.value = true
    await flushPromises()
    expect((eins('.kit-checkbox__input') as HTMLInputElement).checked).toBe(true)
  })

  // Randfall 4
  test('Checkbox ohne value sendet im Formular on, abgewählt nichts', async () => {
    montiere('<form id="f"><kit-checkbox name="agb" /></form>')
    await flushPromises()
    expect(daten('#f').get('agb')).toBeNull()
    await klick(eins('.kit-checkbox__input')!)
    expect(daten('#f').get('agb')).toBe('on')
  })

  // AK11
  test('Radio: Klick auf b setzt art und hakt nur b an; Wert von außen hakt nur a an', async () => {
    const art = ref('')
    montiere('<div><kit-radio name="art" value="a" v-model="art">A</kit-radio><kit-radio name="art" value="b" v-model="art">B</kit-radio></div>', () => ({ art }))
    await flushPromises()
    const radios = () => alle('.kit-radio__input') as HTMLInputElement[]
    expect(radios().map((r) => r.type)).toEqual(['radio', 'radio'])
    expect(radios().map((r) => r.checked)).toEqual([false, false])

    await klick(radios()[1])
    expect(art.value).toBe('b')
    expect(radios().map((r) => r.checked)).toEqual([false, true])

    art.value = 'a'
    await flushPromises()
    expect(radios().map((r) => r.checked)).toEqual([true, false])
  })
})

describe('Toggle', () => {
  // AK12
  test('Aufbau, Klick schaltet, Formular sendet aktiv=0 bzw. aktiv=1', async () => {
    const an = ref(false)
    montiere('<form id="f"><kit-toggle v-model="an" label="Aktiv" name="aktiv" /></form>', () => ({ an }))
    await flushPromises()
    const knopf = () => eins('.kit-toggle__input') as HTMLButtonElement
    expect(knopf().tagName).toBe('BUTTON')
    expect(knopf().type).toBe('button')
    expect(knopf().getAttribute('role')).toBe('switch')
    expect(knopf().getAttribute('aria-checked')).toBe('false')
    expect(eins('.kit-toggle__thumb')!.getAttribute('aria-hidden')).toBe('true')
    expect(eins('.kit-toggle__label')!.textContent?.trim()).toBe('Aktiv')
    expect(eins('.kit-toggle')!.classList.contains('is-active')).toBe(false)
    expect(daten('#f').get('aktiv')).toBe('0')

    await klick(knopf())
    expect(an.value).toBe(true)
    expect(knopf().getAttribute('aria-checked')).toBe('true')
    expect(eins('.kit-toggle')!.classList.contains('is-active')).toBe(true)
    expect(daten('#f').get('aktiv')).toBe('1')
  })

  // AK12 angenähert: Leertaste ist Browser-Standard einer Schaltfläche (type=button), im DOM-Test nicht simuliert
  test('Toggle_angenaehert_Leertaste: Schaltfläche ist type=button und folgt dem Klick, den die Leertaste im Browser auslöst', async () => {
    const an = ref(false)
    montiere('<kit-toggle v-model="an" name="aktiv" />', () => ({ an }))
    await flushPromises()
    const knopf = eins('.kit-toggle__input') as HTMLButtonElement
    expect(knopf.type).toBe('button')
    knopf.click()
    await flushPromises()
    expect(an.value).toBe(true)
  })

  // Randfall 5
  test('ohne name kein verstecktes Feld; mit name eins', async () => {
    montiere('<form id="f"><kit-toggle name="aktiv" /></form>')
    await flushPromises()
    expect(eins('.kit-toggle input[type="hidden"]')).not.toBeNull()
    aufraeumen()
    montiere('<form id="f"><kit-toggle /></form>')
    await flushPromises()
    expect(eins('.kit-toggle')).not.toBeNull()
    expect(eins('.kit-toggle input[type="hidden"]')).toBeNull()
    expect(Array.from(daten('#f').keys())).toEqual([])
  })
})

describe('Datei', () => {
  // AK13
  test('Eingabefeld hat accept und multiple, gewählte Dateien kommen als FileList an', async () => {
    const gemeldet: unknown[] = []
    montiere('<kit-input-file name="bild" accept="image/*" multiple @update:model-value="g" />', () => ({ g: (v: unknown) => gemeldet.push(v) }))
    await flushPromises()
    const input = eins('.kit-input-file__input') as HTMLInputElement
    expect(input.type).toBe('file')
    expect(input.getAttribute('accept')).toBe('image/*')
    expect(input.multiple).toBe(true)
    expect(gemeldet).toHaveLength(0)

    const dateien = Object.assign([new File(['x'], 'a.png', { type: 'image/png' })], { item: (i: number) => dateien[i] })
    Object.defineProperty(input, 'files', { value: dateien, configurable: true })
    input.dispatchEvent(new Event('change', { bubbles: true }))
    await flushPromises()
    expect(gemeldet).toHaveLength(1)
    expect(gemeldet[0]).toBe(dateien)
  })
})
