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
- [x] T1 (coder-pro a9b15a6df26b79af3, GATE GRÜN: Vitest 30 pass, JS 24 pass) (AK1–AK5, AK13, AK14, AK17) [pro] Combobox auf Reka UI, Einstieg `vue3/index.ts` — Tests: `tests/vue3/combobox.test.ts`, `tests/vue3/inseln-logik.test.ts`
- [x] T2 (coder-pro a25421fd988245892, GATE GRÜN: Vitest 52 pass, JS 24 pass) (AK6–AK14) [pro] Suche, Token-Eingabe, Liste, Einstieg `vue3/index.ts` — Tests: `tests/vue3/search.test.ts`, `tests/vue3/input-token.test.ts`, `tests/vue3/input-list.test.ts`
- [ ] T3 (AK15, AK16, AK19) [pro] Themes `vue3/themes/tailwind.css`, `vue3/themes/bootstrap5.scss`, README, CHANGELOG — Tests: `tests/Feature/Vue3/Vue3LogikQuelltextTest.php`
- [ ] T4 (AK18) [pro] Host: Prüfseite `/ui-kit-vue3` um die vier Komponenten erweitern, Build — Tests: Host `tests/Feature/HostGetRoutenTest.php`, `tests/Feature/KitVue3CssTest.php`; Nachweis per Echtlauf (AK18 nicht automatisch prüfbar)

Bestehende Tests: `tests/vue3/dialog.test.ts`, `tests/vue3/tabs.test.ts`, `tests/vue3/accordion.test.ts`, `tests/vue3/inseln.test.ts`, `tests/Feature/Vue3/Vue3QuelltextTest.php`, `tests/js/mount-inseln.test.mjs`
Marketing: entfällt (Bausteine ohne neue öffentliche Seite).
Design: entfällt (Optik nur über die vorhandenen Theme-Dateien, Prüfseite im Host).
Offener Punkt a4dffde1e914e5013: AK-fremd (Klassen-Wächter in `Vue3LogikQuelltextTest` doppelt zu `Vue3QuelltextTest` — Refactoring zu Trait später; AK16 verlangt Wurzelregeln einzeln, Hinweis an Coder T3)
Abweichung a9b15a6df26b79af3: Fehlalarm: keine gemeldet
Offener Punkt a9b15a6df26b79af3: AK-fremd (Leertext „Keine Treffer“ fest und Trigger `aria-label="Liste öffnen"` — Entscheidungsspielraum, Spec nennt keinen Wortlaut; ganzes Verzeichnis rot nur wegen T2-Tests)
Abweichung a25421fd988245892: Fehlalarm: keine gemeldet
Offener Punkt a25421fd988245892: AK-fremd (lokaler Wert folgt `modelValue` per watch — Umsetzungsdetail, AK3/AK8 verlangen nur Verhalten; Leeren meldet `update:modelValue` "" — passt zu AK8; Liste meldet Strings — Freitext der Datalist, AK12 nennt keinen Typ; vue-tsc nicht lauffähig wegen veralteter `moduleResolution` — wie Review vue3-kern-grundlage, Folge-Task #1556)
Echtlauf: folgt in T4 (Host-Prüfseite), Kit hat keine eigene Seite.
Review: nötig (Querschnitt Kit und Host, > 50 Zeilen Produktivcode).

## Stand
T2 fertig. Nächster Schritt: T3 coder-pro (Themes, README, CHANGELOG).
