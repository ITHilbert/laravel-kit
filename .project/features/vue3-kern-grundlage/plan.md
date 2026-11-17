# Plan vue3-kern-grundlage
laravel-kit `feature/vue3-kern-grundlage` (von `main`, 1.7.0). Host `packages_dev` für T5 (Prüfseite) auf eigenem Branch.
Spec: [spec.md](spec.md) (freigegeben 2026-11-17). Kein Dashboard-Task.

## Aufgaben
- [x] T0 (coder-pro a28c39823b6330313, GATE GRÜN: PHP 3 passed, JS 24 pass) [pro] Abhängigkeiten und Testwerkzeug im Kit: `reka-ui ~2.11` (peerDependency + devDependency), `vue ^3.5`, `vitest`, `@vue/test-utils`, `happy-dom`, `@vitejs/plugin-vue`, `typescript`; `vitest.config.ts` (Umgebung happy-dom, nur `tests/vue3/**`); Skript `test:js` — kein AK — Tests: `tests/js/mount-inseln.test.mjs` (bestehend)
- [ ] TW test-writer (AK1–AK12, Randfälle 1–3) — Tests:
  - `tests/vue3/dialog.test.ts` (AK1–AK4, Randfall 1, 3)
  - `tests/vue3/tabs.test.ts` (AK5, AK6, AK9, Randfall 2)
  - `tests/vue3/accordion.test.ts` (AK7, AK8, AK9)
  - `tests/vue3/inseln.test.ts` (AK12)
  - `tests/Feature/Vue3/Vue3QuelltextTest.php` (AK10, AK11, AK14)
- [ ] T1 (AK1–AK4, AK12) [senior] Grundgerüst `vue3/` (Einstieg `index.ts`, `kit.css`) und Dialog — Tests: `tests/vue3/dialog.test.ts`, `tests/vue3/inseln.test.ts`
- [ ] T2 (AK5–AK9) [pro] Tabs und Accordion — Tests: `tests/vue3/tabs.test.ts`, `tests/vue3/accordion.test.ts`
- [ ] T3 (AK10, AK11, AK14) [pro] Themes `vue3/themes/tailwind.css`, `vue3/themes/bootstrap5.scss`, README, `vue/README.md`, CHANGELOG — Tests: `tests/Feature/Vue3/Vue3QuelltextTest.php`
- [ ] T4 (AK13) [pro] Host: Prüfseite `/ui-kit-vue3`, Einbindung `vue3/index.ts` und Bootstrap-5-Theme, `npm install reka-ui` — Tests: Host `tests/Feature/HostGetRoutenTest.php`; Nachweis per Echtlauf (AK13 nicht automatisch prüfbar)

Bestehende Tests: `tests/js/mount-inseln.test.mjs`, `tests/Feature/KitComponents/ReadmeTest.php`
Marketing/Design: entfällt (Bausteine ohne neue öffentliche Seite; `/ui-kit-vue3` ist eine Prüfseite im Host).
Review: nötig (neue Route im Host, Querschnitt über Kit und Host, > 50 Zeilen Produktivcode).

Offener Punkt a28c39823b6330313: AK-fremd (npm audit: 5 high in der picomatch-Kette — picomatch 4.0.7 ist aktuell, kein Patch; nur Entwicklungsabhängigkeiten von vitest/vite, verarbeiten nur eigene Glob-Muster; Laufzeit-Audit 0 Funde. Annahme Moderator: hinnehmen, bei nächstem Vitest-Update erneut prüfen)
Abweichung a28c39823b6330313: Fehlalarm: keine gemeldet

## Offen
- GATE kann Vitest-Dateien noch nicht ausführen (`.claude/hooks/coder-gate.sh` schickt `*.test.ts` an `node --test`). Klärung mit Joerg vor TW.

## Stand
T0 fertig (vitest 5.1.0, reka-ui 2.11.0, vue 3.5.45). TW wartet auf die Klärung zum GATE.
