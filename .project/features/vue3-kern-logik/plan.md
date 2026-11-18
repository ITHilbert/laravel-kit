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
- [x] T3 (coder-pro ac9071cbbebb3cdee, GATE GRÜN: 20 passed; Sass-Probe fehlerfrei) (AK15, AK16, AK19) [pro] Themes `vue3/themes/tailwind.css`, `vue3/themes/bootstrap5.scss`, README, CHANGELOG — Tests: `tests/Feature/Vue3/Vue3LogikQuelltextTest.php`
- [x] T4 (coder-pro ab2c532e48c553d8e, GATE GRÜN: 8 passed; Build ok) (AK18) [pro] Host: Prüfseite `/ui-kit-vue3` um die vier Komponenten erweitern, Build — Tests: Host `tests/Feature/HostGetRoutenTest.php`, `tests/Feature/KitVue3CssTest.php`; Nachweis per Echtlauf (AK18 nicht automatisch prüfbar)

Bestehende Tests: `tests/vue3/dialog.test.ts`, `tests/vue3/tabs.test.ts`, `tests/vue3/accordion.test.ts`, `tests/vue3/inseln.test.ts`, `tests/Feature/Vue3/Vue3QuelltextTest.php`, `tests/js/mount-inseln.test.mjs`
Marketing: entfällt (Bausteine ohne neue öffentliche Seite).
Design: entfällt (Optik nur über die vorhandenen Theme-Dateien, Prüfseite im Host).
Offener Punkt a4dffde1e914e5013: AK-fremd (Klassen-Wächter in `Vue3LogikQuelltextTest` doppelt zu `Vue3QuelltextTest` — Refactoring zu Trait später; AK16 verlangt Wurzelregeln einzeln, Hinweis an Coder T3)
Abweichung a9b15a6df26b79af3: Fehlalarm: keine gemeldet
Offener Punkt a9b15a6df26b79af3: AK-fremd (Leertext „Keine Treffer“ fest und Trigger `aria-label="Liste öffnen"` — Entscheidungsspielraum, Spec nennt keinen Wortlaut; ganzes Verzeichnis rot nur wegen T2-Tests)
Abweichung a25421fd988245892: Fehlalarm: keine gemeldet
Offener Punkt a25421fd988245892: AK-fremd (lokaler Wert folgt `modelValue` per watch — Umsetzungsdetail, AK3/AK8 verlangen nur Verhalten; Leeren meldet `update:modelValue` "" — passt zu AK8; Liste meldet Strings — Freitext der Datalist, AK12 nennt keinen Typ; vue-tsc nicht lauffähig wegen veralteter `moduleResolution` — wie Review vue3-kern-grundlage, Folge-Task #1556)
Abweichung ac9071cbbebb3cdee: Fehlalarm: keine gemeldet
Offener Punkt ac9071cbbebb3cdee: AK-fremd (Tailwind-Theme nicht kompiliert — AK16 verlangt nur die Regeln; kein Tailwind im Host, Kompilier-Probe wie in vue3-kern-grundlage durch den Reviewer)
Abweichung ab2c532e48c553d8e: Fehlalarm: keine gemeldet
Offener Punkt ab2c532e48c553d8e: zurückgebaut (Fix F1: Kit-Theme liest fünf Variablen, die es erst in Bootstrap 5.3 gibt — der Host definierte sie nach; das bräche jedes Projekt mit Bootstrap 5.2. Ursache im Kit (T3), Host-Ergänzung kommt wieder heraus)
Befund Moderator (T4): `vue3/themes/bootstrap5.scss` nutzt `--bs-secondary-color`, `--bs-secondary-bg`, `--bs-tertiary-bg`, `--bs-primary-border-subtle`, `--bs-form-invalid-border-color` (nur Bootstrap 5.3). Host-Bootstrap 5.2.3. → Fix F1 coder-pro: Theme nutzt nur Sass-Variablen bzw. `--bs-*`, die Bootstrap 5.2 an `:root` setzt; Host-`:root`-Block entfernen. Wächter: Host `tests/Feature/KitVue3CssTest.php` (rot ohne Host-Block).
Abweichung abe52f5c615fac7ad: Fehlalarm: keine gemeldet
Offener Punkt abe52f5c615fac7ad: AK-fremd (Optik nicht pixelgleich zu Bootstrap 5.3 — Werte aus 5.2-Sass, gewollt)
Fix F1: coder-pro abe52f5c615fac7ad (GATE GRÜN: 25 passed): Theme mit Sass-Variablen statt 5.3-Variablen, Host-`:root`-Block entfernt.
Echtlauf F1: browser: /ui-kit-vue3 — 0 Konsolenfehler, keine XHR/fetch, Klick: .kit-combobox__trigger ausgelöst, .kit-search__input: 2 sichtbar, .kit-combobox__list: 1 vorhanden → OK. CDP-Sonde (echter Mausklick, eigener Server :8225): Liste offen, bg weiß, Rahmen 1px, z-index 1000, letztes Item liegt oben (elementFromPoint), Auswahl „Österreich“ → FormData `land=at`; Suche/Token/Liste mit Rahmen, `is-invalid` rot an allen vier Wurzeln.
Befund Moderator (Screenshot): Combobox-Trigger bricht unter das Eingabefeld um, Eingabefeld nur ~200 px breit (Suche, Token, Liste sind volle Breite). → Fix F2 (Optik, kurzer Weg): coder-junior, Wurzel als Zeile, Eingabefeld füllt die Breite. (Host-Prüfseite), Kit hat keine eigene Seite.
Review: nötig (Querschnitt Kit und Host, > 50 Zeilen Produktivcode).

Fix F2: coder-junior a85254cdf69f4f3b0 — HANDOFF nur wegen GATE: `TESTS:` nannte `packages/laravel-kit/tests/vue3/combobox.test.ts` ohne `PROJEKT:`, das GATE startete die `.ts`-Datei per `node --test` (Moderator-Fehler im Auftrag, kein Code-Fehler). Umsetzung fertig: Ursache war die Global-Regel `.kit-combobox { display:inline-block; margin-right:1.8em }` der alten Varianten (`vue/bootstrap4/input/search.vue`, `vue/bootstrap5/forms/search.vue`, `vue/bootstrap5/input/search.vue`; dazu `js/combobox.js` mit derselben Wurzelklasse), die im Host nach dem Kit-CSS geladen wird. `vue3/kit.css`: `.kit-combobox.kit-combobox` als Zeile, Eingabefeld füllt die Breite.
Abweichung a85254cdf69f4f3b0: Fehlalarm: keine gemeldet
Offener Punkt a85254cdf69f4f3b0: AK-fremd (Namensgleichheit `.kit-combobox` mit den alten Varianten — Übergangslösung Doppelklasse, Rückbau mit #1546; bootstrap4-Variante nicht geprüft, eingefroren)
Annahme Moderator (F2): Doppelklasse statt Umbenennen in den eingefrorenen alten Varianten — Umbenennen träfe Projekte auf `dev-main`, die `.kit-combobox` aus `js/combobox.js` stylen. Umkehrbar: alte Klasse umbenennen, Doppelklasse entfernen.
Rückbau: #1546 (alte Varianten entfernt → `.kit-combobox.kit-combobox` in `vue3/kit.css` auf einfache Klasse zurück)
NACHWEIS F2 (Moderator selbst): Vitest 52/52, `node --test tests/js/*.test.mjs` 240/240, Host `KitVue3CssTest` + `HostGetRoutenTest` 8 passed, Kit `Vue3QuelltextTest|Vue3LogikQuelltextTest` 17 passed.
Echtlauf: browser: /ui-kit-vue3 — 0 Konsolenfehler, keine XHR/fetch, Klick: .kit-combobox__trigger ausgelöst, .kit-combobox__list: 1 vorhanden → OK; CDP-Sonde nach F2 (echter Mausklick, :8225): Eingabefeld volle Breite, Trigger in derselben Zeile, Liste 915 px breit über dem Folgeinhalt (letztes Item oben), Auswahl → FormData `land=at`; Screenshot geprüft.

## Stand
NACHWEIS grün (Fix F1, F2). Nächster Schritt: Review im Review-Platz.
