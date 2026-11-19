// Toggle mit name und disabled (Randfall 6): das versteckte Feld ist ebenfalls disabled.
import { flushPromises } from '@vue/test-utils'
import { afterEach, describe, expect, test } from 'vitest'
import { aufraeumen, eins, montiere } from './helfer'

afterEach(() => {
  aufraeumen()
})

const gesendet = () => new FormData(eins('#f') as HTMLFormElement)

describe('Toggle disabled im Formular', () => {
  // Randfall 6
  test('mit disabled sendet das Formular nichts unter name, ohne disabled den Wert', async () => {
    // Gegenprobe: ohne disabled wird der Wert gesendet
    montiere('<form id="f"><kit-toggle name="aktiv" :model-value="true" /></form>')
    await flushPromises()
    expect(gesendet().get('aktiv')).toBe('1')
    aufraeumen()

    montiere('<form id="f"><kit-toggle name="aktiv" :model-value="true" disabled /></form>')
    await flushPromises()
    const verstecktes = eins('input[type="hidden"][name="aktiv"]') as HTMLInputElement
    expect(verstecktes).not.toBeNull()
    expect(verstecktes.disabled).toBe(true)
    expect(gesendet().has('aktiv')).toBe(false)
  })
})
