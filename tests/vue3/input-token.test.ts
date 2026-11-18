// Token-Eingabe (AK9–AK11, AK13, AK14, Randfall 4).
import { flushPromises } from '@vue/test-utils'
import { afterEach, beforeEach, describe, expect, test, vi } from 'vitest'
import { aufraeumen, eins, klick, montiere } from './helfer'

const urspruenglich = Object.getOwnPropertyDescriptor(navigator, 'clipboard')
const urspruenglichExec = Object.getOwnPropertyDescriptor(document, 'execCommand')

beforeEach(() => {
  vi.useFakeTimers({ toFake: ['setTimeout', 'clearTimeout'] })
})
afterEach(() => {
  vi.useRealTimers()
  aufraeumen()
  vi.restoreAllMocks()
  if (urspruenglich) Object.defineProperty(navigator, 'clipboard', urspruenglich)
  else delete (navigator as unknown as Record<string, unknown>).clipboard
  if (urspruenglichExec) Object.defineProperty(document, 'execCommand', urspruenglichExec)
  else delete (document as unknown as Record<string, unknown>).execCommand
})

const eingabe = () => eins('.kit-input-token__input') as HTMLInputElement
const schalter = () => eins('.kit-input-token__toggle') as HTMLButtonElement
const kopieren = () => eins('.kit-input-token__copy') as HTMLButtonElement
const status = () => eins('.kit-input-token__status')

function clipboard(writeText = vi.fn().mockResolvedValue(undefined)) {
  Object.defineProperty(navigator, 'clipboard', { value: { writeText }, configurable: true })
  return writeText
}
function ohneClipboard(execCommand: (() => boolean) | null) {
  Object.defineProperty(navigator, 'clipboard', { value: undefined, configurable: true })
  const exec = vi.fn(execCommand ?? (() => false))
  Object.defineProperty(document, 'execCommand', { value: exec, configurable: true, writable: true })
  return exec
}
const token = (extra = '') => montiere(`<kit-input-token name="api_token" model-value="geheim123" ${extra} />`)

describe('Token-Eingabe', () => {
  // AK9
  test('rendert Passwortfeld mit Wert, Schalter und Kopieren-Knopf', async () => {
    token()
    await flushPromises()
    expect(eins('.kit-input-token')).not.toBeNull()
    expect(eingabe().type).toBe('password')
    expect(eingabe().name).toBe('api_token')
    expect(eingabe().value).toBe('geheim123')
    expect(schalter().tagName).toBe('BUTTON')
    expect(schalter().getAttribute('aria-pressed')).toBe('false')
    expect(schalter().getAttribute('aria-label')).toBe('Token anzeigen')
    expect(kopieren().tagName).toBe('BUTTON')
    expect(kopieren().getAttribute('aria-label')).toBe('Token kopieren')
  })

  // AK10
  test('Schalter zeigt den Token, zweiter Klick verbirgt ihn wieder', async () => {
    token()
    await flushPromises()
    await klick(schalter())
    expect(eingabe().type).toBe('text')
    expect(schalter().getAttribute('aria-pressed')).toBe('true')
    expect(schalter().getAttribute('aria-label')).toBe('Token verbergen')
    expect(schalter().classList.contains('is-active')).toBe(true)

    await klick(schalter())
    expect(eingabe().type).toBe('password')
    expect(schalter().getAttribute('aria-pressed')).toBe('false')
    expect(schalter().getAttribute('aria-label')).toBe('Token anzeigen')
    expect(schalter().classList.contains('is-active')).toBe(false)
  })

  // AK11
  test('Kopieren schreibt in die Zwischenablage, zeigt „Kopiert“ und nach 2000 ms nicht mehr', async () => {
    const writeText = clipboard()
    token()
    await flushPromises()
    expect(status()?.textContent?.trim() ?? '').toBe('') // Gegenprobe: vorher leer
    expect(status()?.getAttribute('role')).toBe('status')
    expect(kopieren().classList.contains('is-active')).toBe(false)

    await klick(kopieren())
    expect(writeText).toHaveBeenCalledWith('geheim123')
    expect(kopieren().classList.contains('is-active')).toBe(true)
    expect(status()?.textContent).toContain('Kopiert')

    vi.advanceTimersByTime(1999)
    await flushPromises()
    expect(kopieren().classList.contains('is-active')).toBe(true)
    vi.advanceTimersByTime(1)
    await flushPromises()
    expect(kopieren().classList.contains('is-active')).toBe(false)
    expect(status()?.textContent?.trim() ?? '').toBe('')
  })

  // Randfall 4
  test('ohne navigator.clipboard kopiert execCommand(„copy“) über ein unsichtbares textarea', async () => {
    const exec = ohneClipboard(() => true)
    token()
    await flushPromises()
    await klick(kopieren())
    expect(exec).toHaveBeenCalledWith('copy')
    expect(status()?.textContent).toContain('Kopiert')
    expect(kopieren().classList.contains('is-active')).toBe(true)
  })

  // Randfall 4
  test('schlagen beide Wege fehl: „Kopieren fehlgeschlagen“ und kein is-active', async () => {
    const exec = ohneClipboard(() => false)
    token()
    await flushPromises()
    await klick(kopieren())
    expect(exec).toHaveBeenCalled() // Gegenprobe: der Ersatzweg lief
    expect(status()?.textContent).toContain('Kopieren fehlgeschlagen')
    expect(kopieren().classList.contains('is-active')).toBe(false)
  })

  // AK13
  test('invalid: is-invalid an der Wurzel, aria-invalid am Feld', async () => {
    token('invalid')
    await flushPromises()
    expect(eins('.kit-input-token')!.classList.contains('is-invalid')).toBe(true)
    expect(eingabe().getAttribute('aria-invalid')).toBe('true')
    aufraeumen()
    token() // Gegenprobe
    await flushPromises()
    expect(eins('.kit-input-token')!.classList.contains('is-invalid')).toBe(false)
  })

  // AK13
  test('disabled: Wurzel und Feld disabled, Schalter und Kopieren ändern nichts', async () => {
    const writeText = clipboard()
    token('disabled')
    await flushPromises()
    expect(eins('.kit-input-token')!.classList.contains('disabled')).toBe(true)
    expect(eingabe().disabled).toBe(true)
    await klick(schalter())
    await klick(kopieren())
    expect(eingabe().type).toBe('password')
    expect(writeText).not.toHaveBeenCalled()
    expect(kopieren().classList.contains('is-active')).toBe(false)
  })

  // AK14
  test('id und required stehen am Eingabefeld, nicht an der Wurzel', async () => {
    token('id="feld" required')
    await flushPromises()
    expect(eingabe().id).toBe('feld')
    expect(eingabe().hasAttribute('required')).toBe(true)
    expect(eins('.kit-input-token')!.hasAttribute('id')).toBe(false)
    expect(eins('.kit-input-token')!.hasAttribute('required')).toBe(false)
  })
})
