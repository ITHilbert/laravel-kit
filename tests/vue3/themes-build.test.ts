// Beide Theme-Dateien übersetzen mit den echten Werkzeugen (AK16).
import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'
import { describe, expect, test } from 'vitest'

const kit = resolve(__dirname, '../..')
const themen = resolve(kit, 'vue3/themes')

// Paketname als Variable: Vite löst ihn nicht vorab auf, ein fehlendes Paket meldet Node („Cannot find package“).
const laden = (name: string) => import(/* @vite-ignore */ name)

describe('Theme-Dateien übersetzen', () => {
  // AK16
  test('bootstrap5.scss übersetzt mit Dart Sass und dem Bootstrap-5.2-Sass', async () => {
    const sass = await laden('sass')
    const bootstrapVersion = JSON.parse(readFileSync(resolve(kit, 'node_modules/bootstrap/package.json'), 'utf8')).version
    expect(bootstrapVersion).toMatch(/^5\.2\./)

    const ergebnis = sass.compileString('@import "bootstrap/scss/bootstrap";\n@import "bootstrap5";', {
      loadPaths: [resolve(kit, 'node_modules'), themen],
      silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'mixed-decls', 'if-function'] as never,
      quietDeps: true,
      logger: sass.Logger.silent,
    })
    expect(ergebnis.css).toContain('.kit-dialog__trigger')
  })

  // AK16
  test('tailwind.css übersetzt mit Tailwind 4', async () => {
    const { compile } = await laden('@tailwindcss/node')
    const quelle = readFileSync(resolve(themen, 'tailwind.css'), 'utf8')
    const kompiliert = await compile(quelle, { base: themen, onDependency: () => {} })
    const css = kompiliert.build([])
    expect(css).toContain('.kit-dialog__trigger')
  })
})
