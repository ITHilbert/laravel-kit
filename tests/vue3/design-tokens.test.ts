// Design-Tokens der IT-Hilbert GmbH: css/ith-tokens.css, css/ith-theme.css und die Umstellung der Themes (#2195).
import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'
import { describe, expect, test } from 'vitest'

const kit = resolve(__dirname, '../..')
const cssDir = resolve(kit, 'css')
const themen = resolve(kit, 'vue3/themes')

// Paketname als Variable: Vite löst ihn nicht vorab auf, ein fehlendes Paket meldet Node („Cannot find package“).
const laden = (name: string) => import(/* @vite-ignore */ name)

const lesen = (pfad: string) => readFileSync(pfad, 'utf8')
const norm = (wert: string) => wert.replace(/\s+/g, ' ').trim().toLowerCase()

const TOKENS: Record<string, string> = {
  '--ith-navy': '#073259',
  '--ith-blue': '#0062a1',
  '--ith-blue-dark': '#004e81',
  '--ith-sky': '#1075b7',
  '--ith-cyan': '#2ba8db',
  '--ith-ice': '#e8f4fc',
  '--ith-mist': '#bbe2f4',
  '--ith-text': '#212529',
  '--ith-text-muted': '#6c757d',
  '--ith-border': '#dee2e6',
  '--ith-surface': '#f7f7f7',
  '--ith-success': '#198754',
  '--ith-warning': '#9a5b00',
  '--ith-danger': '#b02a37',
  '--ith-radius': '0.25rem',
  '--ith-font': '"inter", "ubuntu", arial, sans-serif',
}

const ohneKommentare = (css: string) => css.replace(/\/\*[\s\S]*?\*\//g, '')

function deklarationen(block: string): Map<string, string> {
  const karte = new Map<string, string>()
  for (const teil of block.split(';')) {
    const m = (teil.split(/[{}]/).pop() as string).match(/^\s*(--[\w-]+)\s*:\s*([\s\S]+?)\s*$/)
    if (m) karte.set(m[1], norm(m[2]))
  }
  return karte
}

function rootBloecke(css: string): string[] {
  return [...ohneKommentare(css).matchAll(/(?:^|[}\s]):root\s*\{([^}]*)\}/g)].map((m) => m[1])
}

/** Ersetzt var(--ith-x) durch den Tokenwert, damit Verweise und Literale gleich behandelt werden. */
const aufloesen = (wert: string) => norm(wert).replace(/var\(\s*(--ith-[\w-]+)\s*\)/g, (_, n) => TOKENS[n] ?? `?${n}`)

const EXTERN = /https?:\/\/|(?<![:*])\/\/|googleapis|gstatic|fonts\.google/i
const externeAdressen = (css: string) => ohneKommentare(css).match(EXTERN)

const STANDARDFARBE = /(?<![\w-])(?:[a-z0-9]+[:-])*(?:blue|gray|red|green|yellow|slate)-\d{2,3}/

describe('Prüfhelfer erkennen ihre Treffer (Gegenproben)', () => {
  test('externe Adressen und Standardfarben werden gefunden', () => {
    for (const s of ['a{background:url(http://x)}', '@import "https://x";', 'a{background:url(//cdn/x.png)}', '@import url(fonts.googleapis.com/css)']) {
      expect(externeAdressen(s), s).not.toBeNull()
    }
    expect(externeAdressen('/* kommentar */ a{color:red}')).toBeNull()
    for (const s of ['@apply bg-blue-600', '@apply hover:bg-gray-50', '@apply focus-visible:ring-blue-500', 'text-red-600']) {
      expect(STANDARDFARBE.test(s), s).toBe(true)
    }
    expect(STANDARDFARBE.test('color: var(--ith-blue-dark); bg-white')).toBe(false)
  })
})

describe('ith-tokens.css', () => {
  // AK1
  test('enthält genau einen :root-Block mit den 16 Variablen und Werten', () => {
    const css = lesen(resolve(cssDir, 'ith-tokens.css'))
    const bloecke = rootBloecke(css)
    expect(bloecke).toHaveLength(1)
    const karte = deklarationen(bloecke[0])
    expect(Object.fromEntries(karte)).toEqual(TOKENS)
  })

  // Randfall 2
  test('ist ohne Tailwind nutzbar: keine At-Regeln, ith-theme.css ist der Tailwind-Teil', () => {
    const tokens = ohneKommentare(lesen(resolve(cssDir, 'ith-tokens.css')))
    const theme = ohneKommentare(lesen(resolve(cssDir, 'ith-theme.css')))
    expect(theme).toContain('@theme')
    expect(tokens).not.toMatch(/@(theme|import|apply|tailwind|reference|source|plugin)/)
  })
})

describe('ith-theme.css mit Tailwind 4', () => {
  const kandidaten = [
    'bg-brand-navy', 'text-brand-blue', 'rounded-brand', 'bg-brand-blue-dark', 'bg-brand-sky', 'bg-brand-cyan',
    'bg-brand-ice', 'bg-brand-mist', 'text-ink', 'text-ink-muted', 'border-line', 'bg-surface', 'text-success',
    'text-warning', 'text-danger', 'font-sans',
  ]

  // AK2
  test('@theme liefert Farben, Schrift und Radius mit den Tokenwerten; die drei Utilities entstehen', async () => {
    const { compile } = await laden('@tailwindcss/node')
    const quelle = `@import "tailwindcss";\n${lesen(resolve(cssDir, 'ith-theme.css'))}`
    const kompiliert = await compile(quelle, { base: cssDir, onDependency: () => {} })
    const css: string = kompiliert.build(kandidaten)

    const erwartet: Record<string, string> = {
      '--color-brand-navy': TOKENS['--ith-navy'],
      '--color-brand-blue': TOKENS['--ith-blue'],
      '--color-brand-blue-dark': TOKENS['--ith-blue-dark'],
      '--color-brand-sky': TOKENS['--ith-sky'],
      '--color-brand-cyan': TOKENS['--ith-cyan'],
      '--color-brand-ice': TOKENS['--ith-ice'],
      '--color-brand-mist': TOKENS['--ith-mist'],
      '--color-ink': '#212529',
      '--color-ink-muted': '#6c757d',
      '--color-line': '#dee2e6',
      '--color-surface': '#f7f7f7',
      '--color-success': TOKENS['--ith-success'],
      '--color-warning': TOKENS['--ith-warning'],
      '--color-danger': TOKENS['--ith-danger'],
      '--font-sans': TOKENS['--ith-font'],
      '--radius-brand': '0.25rem',
    }
    const ist = deklarationen(css)
    for (const [name, wert] of Object.entries(erwartet)) {
      expect(ist.has(name), `${name} fehlt in der Tailwind-Ausgabe`).toBe(true)
      expect(aufloesen(ist.get(name) as string), name).toBe(wert)
    }
    expect(css).toMatch(/\.bg-brand-navy\s*\{[^}]*--color-brand-navy/)
    expect(css).toMatch(/\.text-brand-blue\s*\{[^}]*--color-brand-blue/)
    expect(css).toMatch(/\.rounded-brand\s*\{[^}]*--radius-brand/)
  })
})

describe('keine externen Adressen', () => {
  // AK3
  test('weder ith-tokens.css noch ith-theme.css enthalten externe Adressen oder Google Fonts', () => {
    for (const datei of ['ith-tokens.css', 'ith-theme.css']) {
      const css = lesen(resolve(cssDir, datei))
      expect(css.length, `${datei} ist leer`).toBeGreaterThan(100)
      expect(css, `${datei} ohne Tokens gelesen`).toContain('#073259')
      expect(externeAdressen(css), `${datei} enthält eine externe Adresse`).toBeNull()
    }
  })
})

describe('Theme tailwind.css', () => {
  // AK5
  test('nutzt keine Tailwind-Standardfarben, sondern die Tokens', () => {
    const css = lesen(resolve(themen, 'tailwind.css'))
    const treffer = ohneKommentare(css).match(new RegExp(STANDARDFARBE.source, 'g'))
    expect(treffer, `Standardfarben: ${treffer?.slice(0, 5).join(', ')}`).toBeNull()

    for (const name of ['navy', 'blue', 'ice', 'danger', 'success', 'border', 'text', 'text-muted', 'radius']) {
      expect(css, `--ith-${name} fehlt`).toMatch(new RegExp(`--ith-${name}(?![\\w-])`))
    }
    const regel = (selektor: string) => css.match(new RegExp(`${selektor.replace(/[.]/g, '\\.')}\\s*\\{([^}]*)\\}`))?.[1] ?? ''
    expect(regel('.kit-dialog__trigger')).toMatch(/--ith-navy(?![\w-])/)
    expect(regel('.kit-dialog__close')).toMatch(/--ith-blue(?![\w-])/)
    expect(regel('.kit-dialog__close')).toMatch(/--ith-border(?![\w-])/)
  })
})

describe('Theme bootstrap5.scss', () => {
  // AK6
  test('Akzent-, Fehler- und Erfolgsfarbe kommen aus den Tokens', () => {
    const scss = lesen(resolve(themen, 'bootstrap5.scss'))
    expect(scss).toContain('var(--ith-blue)')
    expect(scss).toContain('var(--ith-danger)')
    expect(scss).toContain('var(--ith-success)')
    expect(scss).not.toContain('var(--bs-primary)')
    expect(scss).not.toContain('var(--bs-success)')
    expect(scss).not.toContain('$form-feedback-invalid-color')
  })
})

describe('Themes übersetzen mit den Tokens', () => {
  const tokenwerte = () => new Map(Object.entries(TOKENS))

  async function tailwindAusgabe(): Promise<string> {
    const { compile } = await laden('@tailwindcss/node')
    const quelle = lesen(resolve(themen, 'tailwind.css'))
    return (await compile(quelle, { base: themen, onDependency: () => {} })).build([])
  }

  async function sassAusgabe(): Promise<string> {
    const sass = await laden('sass')
    return sass.compileString('@import "bootstrap/scss/bootstrap";\n@import "bootstrap5";', {
      loadPaths: [resolve(kit, 'node_modules'), themen],
      silenceDeprecations: ['import', 'global-builtin', 'color-functions', 'mixed-decls', 'if-function'] as never,
      quietDeps: true,
      logger: sass.Logger.silent,
    }).css
  }

  function pruefeTokens(css: string, name: string) {
    expect(css, `${name}: keine Verwendung von var(--ith-`).toContain('var(--ith-')
    const definiert = new Map<string, string[]>()
    for (const m of css.matchAll(/(--ith-[\w-]+)\s*:\s*([^;}]+)/g)) {
      definiert.set(m[1], [...(definiert.get(m[1]) ?? []), norm(m[2])])
    }
    for (const [token, wert] of tokenwerte()) {
      expect(definiert.has(token), `${name}: ${token} nicht in der Ausgabe definiert`).toBe(true)
      // Randfall 1: doppelte Definitionen (Tokens + Theme) haben gleiche Werte
      for (const ist of definiert.get(token) as string[]) expect(ist, `${name}: ${token}`).toBe(wert)
    }
  }

  // AK7, Randfall 1
  test('tailwind.css übersetzt, nutzt var(--ith- und enthält die Tokens selbst', async () => {
    pruefeTokens(await tailwindAusgabe(), 'tailwind.css')
  })

  // AK7, Randfall 1
  test('bootstrap5.scss übersetzt, nutzt var(--ith- und enthält die Tokens selbst', async () => {
    pruefeTokens(await sassAusgabe(), 'bootstrap5.scss')
  })
})
