// Typprüfung des Kits (AK17): `npm run typecheck` (vue-tsc --noEmit über vue3/).
import { spawnSync } from 'node:child_process'
import { readFileSync } from 'node:fs'
import { resolve } from 'node:path'
import { describe, expect, test } from 'vitest'

const kit = resolve(__dirname, '../..')

describe('Typprüfung', () => {
  // AK17
  test('package.json hat das Skript typecheck mit vue-tsc --noEmit', () => {
    const skripte = JSON.parse(readFileSync(resolve(kit, 'package.json'), 'utf8')).scripts ?? {}
    expect(skripte.typecheck).toBeDefined()
    expect(skripte.typecheck).toContain('vue-tsc')
    expect(skripte.typecheck).toContain('--noEmit')
  })

  // AK17
  test('npm run typecheck endet ohne Typfehler (Exit 0)', () => {
    const lauf = spawnSync('npm', ['run', '--silent', 'typecheck'], { cwd: kit, encoding: 'utf8' })
    expect(lauf.status, `${lauf.stdout}\n${lauf.stderr}`).toBe(0)
  }, 600_000)
})
