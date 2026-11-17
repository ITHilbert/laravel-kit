# Plan vue3-kern-grundlage
laravel-kit `feature/vue3-kern-grundlage` (von `main`, 1.7.0). Host `packages_dev` für T5 (Prüfseite) auf eigenem Branch.
Spec: [spec.md](spec.md) (freigegeben 2026-11-17). Kein Dashboard-Task.

## Aufgaben
- [x] T0 (coder-pro a28c39823b6330313, GATE GRÜN: PHP 3 passed, JS 24 pass) [pro] Abhängigkeiten und Testwerkzeug im Kit: `reka-ui ~2.11` (peerDependency + devDependency), `vue ^3.5`, `vitest`, `@vue/test-utils`, `happy-dom`, `@vitejs/plugin-vue`, `typescript`; `vitest.config.ts` (Umgebung happy-dom, nur `tests/vue3/**`); Skript `test:js` — kein AK — Tests: `tests/js/mount-inseln.test.mjs` (bestehend)
- [x] TW (test-writer a9a5ea46fcf028c80, rot: 4 Vitest-Dateien „vue3/index nicht gefunden“, PHP 4 rot/3 grün = Gegenproben; Grünbar entfällt, Risiko normal) test-writer (AK1–AK12, Randfälle 1–4) — Tests:
  - `tests/vue3/dialog.test.ts` (AK1–AK4, Randfall 1, 3)
  - `tests/vue3/tabs.test.ts` (AK5, AK6, AK9, Randfall 2)
  - `tests/vue3/accordion.test.ts` (AK7, AK8, AK9)
  - `tests/vue3/inseln.test.ts` (AK12, Randfall 4)
  - `tests/vue3/helfer.ts` (gemeinsamer Helfer, kein Test)
  - `tests/Feature/Vue3/Vue3QuelltextTest.php` (AK10, AK11, AK14)
- [x] T1 (coder-senior af1a00dbb0a666a55, GATE GRÜN: Vitest 7 pass, JS 24 pass) (AK1–AK4) [senior] Grundgerüst `vue3/` (Einstieg `index.ts` mit `KitVue3` und `kitKomponenten`, `kit.css`) und Dialog — Tests: `tests/vue3/dialog.test.ts`
- [x] T2 (coder-pro a2b8369ec9835be9f, GATE GRÜN: Vitest 16 pass, JS 24 pass) (AK5–AK9, AK12) [pro] Tabs und Accordion, Inseln — Tests: `tests/vue3/tabs.test.ts`, `tests/vue3/accordion.test.ts`, `tests/vue3/inseln.test.ts`
- [x] T3 (coder-pro aca7b80f0ab8360ce, GATE GRÜN: PHP 13 passed; Nachtrag Moderator-Befund: Accordion-Zustände im Theme hingen an Trigger/Inhalt statt am Eintrag → korrigiert) (AK10, AK11, AK14) [pro] Themes `vue3/themes/tailwind.css`, `vue3/themes/bootstrap5.scss`, README, `vue/README.md`, CHANGELOG — Tests: `tests/Feature/Vue3/Vue3QuelltextTest.php`
- [x] T4 (coder-pro a925db3336e28a680, GATE GRÜN: 8 passed; Host-Branch `feature/vue3-kern-grundlage`) (AK13) [pro] Host: Prüfseite `/ui-kit-vue3`, Einbindung `vue3/index.ts` und Bootstrap-5-Theme, `npm install reka-ui` — Tests: Host `tests/Feature/HostGetRoutenTest.php`; Nachweis per Echtlauf (AK13 nicht automatisch prüfbar)

Bestehende Tests: `tests/js/mount-inseln.test.mjs`, `tests/Feature/KitComponents/ReadmeTest.php`
Marketing/Design: entfällt (Bausteine ohne neue öffentliche Seite; `/ui-kit-vue3` ist eine Prüfseite im Host).
Review: nötig (neue Route im Host, Querschnitt über Kit und Host, > 50 Zeilen Produktivcode).

Offener Punkt a28c39823b6330313: AK-fremd (npm audit: 5 high in der picomatch-Kette — picomatch 4.0.7 ist aktuell, kein Patch; nur Entwicklungsabhängigkeiten von vitest/vite, verarbeiten nur eigene Glob-Muster; Laufzeit-Audit 0 Funde. Annahme Moderator: hinnehmen, bei nächstem Vitest-Update erneut prüfen)
Abweichung a28c39823b6330313: Fehlalarm: keine gemeldet

Spec präzisiert (Moderator, nach TW, kein AK-Satz geändert): `kitKomponenten` = Tag-Name → Loader, wie `mountInseln` ihn erwartet (Meldung test-writer a9a5ea46fcf028c80, Punkt 1). Tests unberührt.
Abweichung af1a00dbb0a666a55: Fehlalarm: keine gemeldet (Abschnitt leer)
Offener Punkt af1a00dbb0a666a55: AK-fremd (1. `kitKomponenten` lädt per `Promise.resolve`, kein echtes Nachladen je Insel — AK12 verlangt nur Mounten ohne Konsolenfehler, kein Nachladen. 2. Host braucht TS in Mix → gehört zu T4/AK13. 3. kein `vue-tsc` → Typprüfung der SFCs über Vitest und Host-Build)
Annahmen af1a00dbb0a666a55 (Entscheidungsspielraum): `kit-dialog` teleportiert alle direkten Kinder außer `<kit-dialog-trigger>` in den Inhalt (Trigger in einer eigenen Wrapper-Komponente wird nicht erkannt); `aria-describedby` aus; `kit.css` nicht aus `index.ts` importiert → README (T3) und Host (T4) binden es ein; Scroll-Sperre über Reka.
Abweichung a2b8369ec9835be9f: Fehlalarm: keine gemeldet
Offener Punkt a2b8369ec9835be9f: AK-fremd (keine gemeldet)
Annahmen a2b8369ec9835be9f (Entscheidungsspielraum): `kit-tabs` sammelt `kit-tab` rekursiv aus dem Slot in die eigene Liste; `is-active`/`is-open` aus `injectTabsRootContext`/`injectAccordionRootContext` (in reka-ui 2.11 öffentlich exportiert, Version auf ~2.11 festgelegt).
Abweichung aca7b80f0ab8360ce: Fehlalarm: keine gemeldet
Offener Punkt aca7b80f0ab8360ce: AK-fremd (Theme-Builds mit Tailwind v4 `@reference` bzw. Bootstrap-Sass ungeprüft → Nachweis in T4/AK13)
Abweichung a925db3336e28a680: Fehlalarm: keine gemeldet
Offener Punkt a925db3336e28a680: AK-fremd (1. Pint formatierte bestehenden Code in `routes/web.php` um, ohne Verhaltensänderung. 2. `tsconfig.json` im Host neu, reine Konfiguration; Host-TypeScript 4.9 kennt `moduleResolution: bundler` nicht. 3. Bootstrap-Selektoren btn/modal/nav/accordion stehen auf `/ui-kit-vue3` doppelt (Phoenix + `kit-vue3.css`). 4. „3 tabpanel im DOM“ → Befund Moderator unten. 5. Aussage „Typprüfung macht das Kit (vue-tsc)“ trifft nicht zu, das Kit hat kein vue-tsc; Typprüfung der SFCs nur indirekt über Vitest und Build)
Echtlauf: browser: /ui-kit-vue3 — 0 Konsolenfehler, 3 Tabs sichtbar, 1 Panel sichtbar; Klick `.kit-dialog__trigger` öffnet `.kit-dialog__content.is-open` (Prüf-Treffer „Overlay deckt die Seitenmitte“ = der gewollt geöffnete modale Dialog, ausgelöst durch den Klick); Klick auf Accordion-Trigger ohne Fehler. Build `npm run production` ohne Warnungen.
Befund Moderator (Echtlauf): `.kit-tabs__panel.is-active` = 3 im DOM. Reka lässt inaktive Panels mit `hidden` gemountet, `panel.vue` setzt `is-active` fest. Das verletzt den Vertrag „Zustände stehen als Klassen am Teil“ und AK5 („Panel a trägt is-active“). → Fix F1: test-writer ergänzt `tests/vue3/tabs.test.ts` (inaktive Panels ohne `is-active`), dann coder-pro.
Ablauf: T2 lief vor dem Commit von T1 (Moderator-Fehler: Commit gesperrt, T2 schon gestartet) → T1 und T2 in einem Commit.
AK13: nicht prüfbar automatisch → Echtlauf im Host (T4).
GATE und Vitest: Moderator entschieden (Joerg: „entscheide selbst“, 2026-11-17) — `.claude/hooks/coder-gate.sh` lokal angepasst: `*.test.ts` laufen über Vitest im Ziel, wenn dessen package.json vitest nennt; Template-Task #1547 (Projekt claude, Diff im Anhang).

## Stand
TW fertig, rote Tests committet. Nächster Schritt: T1 [senior].
