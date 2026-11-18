// Dialog (AK1–AK4, Randfall 1, Randfall 3). Der Dialog rendert per Teleport an body.
import { flushPromises } from '@vue/test-utils'
import { afterEach, describe, expect, test, vi } from 'vitest'
import { ref } from 'vue'
import { alle, aufraeumen, eins, klick, montiere, taste } from './helfer'

afterEach(() => {
  aufraeumen()
  vi.restoreAllMocks()
})

const dialog = `
  <kit-dialog>
    <kit-dialog-trigger>Öffnen</kit-dialog-trigger>
    <kit-dialog-title>Titel</kit-dialog-title>
    <p>Inhalt</p>
    <kit-dialog-close>Schließen</kit-dialog-close>
  </kit-dialog>`

describe('Dialog', () => {
  // AK1
  test('Klick auf den Trigger öffnet den Dialog mit Overlay, role=dialog, is-open und beschriftetem Titel', async () => {
    montiere(dialog)
    expect(eins('.kit-dialog__content')).toBeNull() // Gegenprobe: vorher geschlossen

    await klick(eins('.kit-dialog__trigger')!)

    const content = eins('.kit-dialog__content')!
    expect(eins('.kit-dialog__overlay')).not.toBeNull()
    expect(content.getAttribute('role')).toBe('dialog')
    expect(content.classList.contains('is-open')).toBe(true)
    const titel = eins('.kit-dialog__title')!
    expect(titel).not.toBeNull()
    expect(titel.id).not.toBe('')
    expect(content.getAttribute('aria-labelledby')).toBe(titel.id)
  })

  // AK2
  test.each([
    ['Escape', async () => taste(eins('.kit-dialog__content')!, 'Escape')],
    ['Schließen-Knopf', async () => klick(eins('.kit-dialog__close')!)],
  ])('schließt per %s: Inhalt weg, Fokus auf dem Trigger', async (_weg, schliessen) => {
    montiere(dialog)
    const trigger = eins('.kit-dialog__trigger')!
    trigger.focus()
    await klick(trigger)
    expect(eins('.kit-dialog__content')).not.toBeNull() // Gegenprobe: war offen

    await schliessen()

    await vi.waitFor(() => expect(eins('.kit-dialog__content')).toBeNull())
    await vi.waitFor(() => expect(document.activeElement).toBe(trigger))
  })

  // AK3
  test('Tab auf der letzten Schaltfläche springt zur ersten (Fokusfalle)', async () => {
    const offen = ref(true)
    montiere(`
      <kit-dialog v-model:open="offen">
        <kit-dialog-title>Titel</kit-dialog-title>
        <button id="erste" type="button">Eins</button>
        <button id="letzte" type="button">Zwei</button>
      </kit-dialog>`, () => ({ offen }))
    await flushPromises()
    const erste = eins('#erste')!
    const letzte = eins('#letzte')!
    expect(erste).not.toBeNull()

    letzte.focus()
    expect(document.activeElement).toBe(letzte) // Gegenprobe: Fokus steht wirklich auf der letzten
    await taste(letzte, 'Tab')

    await vi.waitFor(() => expect(document.activeElement).toBe(erste))
  })

  // AK4
  test('v-model:open: von außen öffnen, per Escape schließt und setzt offen auf false', async () => {
    const offen = ref(false)
    montiere(
      `<kit-dialog v-model:open="offen"><kit-dialog-title>T</kit-dialog-title><p>x</p></kit-dialog>`,
      () => ({ offen }),
    )
    expect(eins('.kit-dialog__content')).toBeNull()

    offen.value = true
    await flushPromises()
    expect(eins('.kit-dialog__content')).not.toBeNull()

    await taste(eins('.kit-dialog__content')!, 'Escape')
    await vi.waitFor(() => expect(offen.value).toBe(false))
    await vi.waitFor(() => expect(eins('.kit-dialog__content')).toBeNull())
  })

  // Randfall 1
  test('ohne Titel: kein Fehler, Dialog öffnet trotzdem', async () => {
    const warn = vi.spyOn(console, 'warn').mockImplementation(() => {}) // Reka-Warnung erlaubt
    const fehler = vi.spyOn(console, 'error').mockImplementation(() => {})
    montiere(`<kit-dialog><kit-dialog-trigger>Auf</kit-dialog-trigger><p>x</p></kit-dialog>`)

    await klick(eins('.kit-dialog__trigger')!)

    expect(eins('.kit-dialog__content')).not.toBeNull()
    expect(fehler).not.toHaveBeenCalled()
    expect(warn).toBeDefined()
  })

  // Randfall 3
  test('zwei Dialoge: nur der zuletzt geöffnete hat den Fokus, Escape schließt nur ihn', async () => {
    const a = ref(false)
    const b = ref(false)
    montiere(
      `<div>
        <kit-dialog v-model:open="a"><kit-dialog-title>A</kit-dialog-title><button id="knopf-a" type="button">a</button></kit-dialog>
        <kit-dialog v-model:open="b"><kit-dialog-title>B</kit-dialog-title><button id="knopf-b" type="button">b</button></kit-dialog>
      </div>`,
      () => ({ a, b }),
    )

    a.value = true
    await flushPromises()
    await vi.waitFor(() => expect(document.activeElement?.id).toBe('knopf-a')) // Gegenprobe: A hatte den Fokus
    b.value = true
    await flushPromises()

    await vi.waitFor(() => expect(document.activeElement?.id).toBe('knopf-b'))
    await taste(document.activeElement!, 'Escape')

    await vi.waitFor(() => expect(b.value).toBe(false))
    expect(a.value).toBe(true)
    expect(alle('.kit-dialog__content')).toHaveLength(1)
    expect(eins('#knopf-a')).not.toBeNull()
    expect(eins('#knopf-b')).toBeNull()
  })
})
