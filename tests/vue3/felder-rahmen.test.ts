// Label, Formular, Eingabegruppe und Einstieg (AK14, AK15, AK16, AK19).
import { flushPromises } from '@vue/test-utils'
import { createApp } from 'vue'
import { afterEach, describe, expect, test, vi } from 'vitest'
// @ts-expect-error JS-Modul ohne Typen
import { mountInseln } from '../../js/mount-inseln.js'
import * as kit from '../../vue3/index'
import { aufraeumen, eins, montiere } from './helfer'

afterEach(() => {
  aufraeumen()
  document.head.querySelectorAll('meta[name="csrf-token"]').forEach((m) => m.remove())
  vi.restoreAllMocks()
})

function csrf(inhalt: string) {
  const meta = document.createElement('meta')
  meta.name = 'csrf-token'
  meta.content = inhalt
  document.head.appendChild(meta)
}
const form = () => eins('form') as HTMLFormElement
const daten = () => new FormData(form())

describe('Label', () => {
  // AK14
  test('kit-label rendert ein label mit Klasse, for und Text', async () => {
    montiere('<kit-label for="x">Name</kit-label>')
    await flushPromises()
    const el = eins('label')!
    expect(el.className).toBe('kit-label')
    expect(el.getAttribute('for')).toBe('x')
    expect(el.textContent?.trim()).toBe('Name')
  })
})

describe('Formular', () => {
  // AK15
  test('post: Klasse, action, method und verstecktes _token aus dem Meta-Tag, kein _method', async () => {
    csrf('t0k')
    montiere('<kit-form action="/speichern" method="post" />')
    await flushPromises()
    expect(form().className).toBe('kit-form')
    expect(form().getAttribute('action')).toBe('/speichern')
    expect(form().getAttribute('method')).toBe('post')
    expect(daten().get('_token')).toBe('t0k')
    expect((eins('input[name="_token"]') as HTMLInputElement).type).toBe('hidden')
    expect(daten().get('_method')).toBeNull()
  })

  // AK15
  test.each([
    ['put', 'PUT'],
    ['delete', 'DELETE'],
  ])('method=%s: Formular sendet per post mit _method=%s und _token', async (methode, erwartet) => {
    csrf('t0k')
    montiere(`<kit-form action="/x" method="${methode}" />`)
    await flushPromises()
    expect(form().getAttribute('method')).toBe('post')
    expect(daten().get('_method')).toBe(erwartet)
    expect(daten().get('_token')).toBe('t0k')
  })

  // AK15
  test('method=get: weder _token noch _method', async () => {
    csrf('t0k')
    montiere('<kit-form action="/suche" method="get" />')
    await flushPromises()
    expect(form().getAttribute('method')).toBe('get')
    expect(daten().get('_token')).toBeNull()
    expect(daten().get('_method')).toBeNull()
  })

  // AK15
  test('fehlt das Meta-Tag: kein _token und kein Fehler (Gegenprobe: mit Meta-Tag ist es da)', async () => {
    const fehler = vi.spyOn(console, 'error').mockImplementation(() => {})
    const warn = vi.spyOn(console, 'warn').mockImplementation(() => {})
    csrf('t0k')
    montiere('<kit-form action="/x" method="post" />')
    await flushPromises()
    expect(daten().get('_token')).toBe('t0k')
    aufraeumen()
    document.head.querySelectorAll('meta[name="csrf-token"]').forEach((m) => m.remove())
    montiere('<kit-form action="/x" method="post" />')
    await flushPromises()
    expect(form()).not.toBeNull()
    expect(daten().get('_token')).toBeNull()
    expect(fehler).not.toHaveBeenCalled()
    expect(warn).not.toHaveBeenCalled()
  })
})

describe('Eingabegruppe', () => {
  // AK16
  test('Gruppe enthält Zusatztext und Feld in dieser Reihenfolge', async () => {
    montiere('<kit-input-group><kit-input-group-text>https://</kit-input-group-text><kit-input-text name="url" /></kit-input-group>')
    await flushPromises()
    const gruppe = eins('.kit-input-group')!
    expect(Array.from(gruppe.children).map((c) => c.className.split(' ')[0])).toEqual(['kit-input-group-text', 'kit-input-text'])
    expect(gruppe.children[0].textContent?.trim()).toBe('https://')
    expect(eins('.kit-input-group .kit-input-text__input')).not.toBeNull()
  })
})

describe('Einstieg', () => {
  const TAGS: Array<[string, string]> = [
    ['KitInputText', 'kit-input-text'],
    ['KitInputEmail', 'kit-input-email'],
    ['KitInputPassword', 'kit-input-password'],
    ['KitInputNumber', 'kit-input-number'],
    ['KitInputInt', 'kit-input-int'],
    ['KitInputEuro', 'kit-input-euro'],
    ['KitInputPercent', 'kit-input-percent'],
    ['KitInputDate', 'kit-input-date'],
    ['KitInputColor', 'kit-input-color'],
    ['KitInputFile', 'kit-input-file'],
    ['KitTextarea', 'kit-textarea'],
    ['KitSelect', 'kit-select'],
    ['KitCheckbox', 'kit-checkbox'],
    ['KitRadio', 'kit-radio'],
    ['KitToggle', 'kit-toggle'],
    ['KitLabel', 'kit-label'],
    ['KitForm', 'kit-form'],
    ['KitInputGroup', 'kit-input-group'],
    ['KitInputGroupText', 'kit-input-group-text'],
  ]

  // AK19
  test.each(TAGS)('%s: benannter Export, in kitKomponenten und im Plugin als %s', async (export_, tag) => {
    const komponente = (kit as Record<string, unknown>)[export_]
    expect(komponente).toBeDefined()
    expect(Object.keys(kit.kitKomponenten)).toContain(tag)
    expect(((await (kit.kitKomponenten as Record<string, () => Promise<{ default: unknown }>>)[tag]()).default)).toBe(komponente)
    const app = createApp({})
    app.use(kit.KitVue3)
    expect(app.component(tag)).toBe(komponente)
  })

  // AK19
  test('Insel kit-input-text mountet ohne Konsolenfehler, das Formular sendet den getippten Wert unter vorname', async () => {
    const warn = vi.spyOn(console, 'warn').mockImplementation(() => {})
    const fehler = vi.spyOn(console, 'error').mockImplementation(() => {})
    document.body.innerHTML = `<form id="f"><div id="i" data-vue="kit-input-text" data-props='{"name":"vorname","label":"Vorname"}'></div></form>`
    expect(eins('#i .kit-input-text')).toBeNull() // Gegenprobe: vorher leer

    await mountInseln(kit.kitKomponenten)
    await new Promise((r) => setTimeout(r, 0))
    const input = eins('#i .kit-input-text__input') as HTMLInputElement
    expect(input).not.toBeNull()
    expect(eins('#i .kit-input-text__label')!.textContent?.trim()).toBe('Vorname')

    input.value = 'Anna'
    input.dispatchEvent(new Event('input', { bubbles: true }))
    await flushPromises()
    expect(new FormData(eins('#f') as HTMLFormElement).get('vorname')).toBe('Anna')
    expect(fehler).not.toHaveBeenCalled()
    expect(warn).not.toHaveBeenCalled()
  })
})
