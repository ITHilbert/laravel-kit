# Plan vue3-kern-logik
laravel-kit `feature/vue3-kern-logik` (von `main` e855b3cc). Host `packages_dev` `feature/vue3-kern-logik` (von `main` 4a7d7db) für T4.
Spec: [spec.md](spec.md) (freigegeben 2026-11-18, Moderator in Vollmacht). Kein Dashboard-Task.

## Aufgaben
- [x] TW (test-writer a4dffde1e914e5013, rot: Vitest 35/35 neue Tests rot — Tags nicht registriert, `.kit-*` fehlt; PHP 4 rot/3 grün = Gegenproben; Grünbar entfällt, Risiko normal) test-writer (AK1–AK19, Randfälle 1–5) — Tests:
  - `tests/vue3/combobox.test.ts` (AK1–AK5, AK13 und AK14 für die Combobox, Randfall 1–3)
  - `tests/vue3/search.test.ts` (AK6–AK8, AK13 und AK14 für die Suche, Randfall 5)
  - `tests/vue3/input-token.test.ts` (AK9–AK11, AK13 und AK14 für die Token-Eingabe, Randfall 4)
  - `tests/vue3/input-list.test.ts` (AK12, AK13 und AK14 für die Liste)
  - `tests/vue3/inseln-logik.test.ts` (AK17)
  - `tests/Feature/Vue3/Vue3LogikQuelltextTest.php` (AK15, AK16, AK19)
- [ ] T1 (AK1–AK5, AK13, AK14, AK17) [pro] Combobox auf Reka UI, Einstieg `vue3/index.ts` — Tests: `tests/vue3/combobox.test.ts`, `tests/vue3/inseln-logik.test.ts`
- [ ] T2 (AK6–AK14) [pro] Suche, Token-Eingabe, Liste, Einstieg `vue3/index.ts` — Tests: `tests/vue3/search.test.ts`, `tests/vue3/input-token.test.ts`, `tests/vue3/input-list.test.ts`
- [ ] T3 (AK15, AK16, AK19) [pro] Themes `vue3/themes/tailwind.css`, `vue3/themes/bootstrap5.scss`, README, CHANGELOG — Tests: `tests/Feature/Vue3/Vue3LogikQuelltextTest.php`
- [ ] T4 (AK18) [pro] Host: Prüfseite `/ui-kit-vue3` um die vier Komponenten erweitern, Build — Tests: Host `tests/Feature/HostGetRoutenTest.php`, `tests/Feature/KitVue3CssTest.php`; Nachweis per Echtlauf (AK18 nicht automatisch prüfbar)

Bestehende Tests: `tests/vue3/dialog.test.ts`, `tests/vue3/tabs.test.ts`, `tests/vue3/accordion.test.ts`, `tests/vue3/inseln.test.ts`, `tests/Feature/Vue3/Vue3QuelltextTest.php`, `tests/js/mount-inseln.test.mjs`
Marketing: entfällt (Bausteine ohne neue öffentliche Seite).
Design: entfällt (Optik nur über die vorhandenen Theme-Dateien, Prüfseite im Host).
Offener Punkt a4dffde1e914e5013: AK-fremd (Klassen-Wächter in `Vue3LogikQuelltextTest` doppelt zu `Vue3QuelltextTest` — Refactoring zu Trait später; AK16 verlangt Wurzelregeln einzeln, Hinweis an Coder T3)
Review: nötig (Querschnitt Kit und Host, > 50 Zeilen Produktivcode).

## Stand
Rote Tests committet. Nächster Schritt: T1 coder-pro (Combobox).
