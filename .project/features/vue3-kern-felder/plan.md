# Plan vue3-kern-felder
laravel-kit `feature/vue3-kern-felder` (von `feature/vue3-kern-logik` 77e3ba03 — baut auf Einstieg, `kit.css` und Themes von vue3-kern-logik auf, das im Review ist). Host `packages_dev` `feature/vue3-kern-felder` (von `feature/vue3-kern-logik` 3dbe4dd) für T6.
Spec: [spec.md](spec.md) (freigegeben 2026-11-18, Moderator in Vollmacht). Kein Dashboard-Task.

## Aufgaben
- [x] TW (test-writer aed6469519f935f21, rot: Vitest 100/100 neue Fälle rot — Komponenten und Exporte fehlen; PHP 4 rot/3 grün = Gegenproben; Grünbar entfällt) test-writer (AK1–AK21, Randfälle 1–5) — Tests:
  - `tests/vue3/felder-text.test.ts` (AK1–AK4, AK8, Randfall 1; AK2–AK4 je für alle Felder der Tabelle)
  - `tests/vue3/felder-zahl.test.ts` (AK6, AK7, Randfall 2)
  - `tests/vue3/felder-auswahl.test.ts` (AK5, AK9–AK13, Randfälle 3–5)
  - `tests/vue3/felder-rahmen.test.ts` (AK14–AK16, AK19)
  - `tests/Feature/Vue3/Vue3FelderQuelltextTest.php` (AK17, AK18, AK21)
- [ ] T1 (AK1–AK4, AK8) [pro] gemeinsame Feld-Logik (`vue3/components/feld/`), `kit-input-text`, `kit-input-email`, `kit-input-password`, `kit-input-date`, `kit-input-color`, `kit-textarea`, Einstieg — Tests: `tests/vue3/felder-text.test.ts`
- [ ] T2 (AK6, AK7) [pro] `kit-input-number`, `kit-input-int`, `kit-input-euro`, `kit-input-percent`, Einstieg — Tests: `tests/vue3/felder-zahl.test.ts`
- [ ] T3 (AK5, AK9–AK13) [pro] `kit-select`, `kit-checkbox`, `kit-radio`, `kit-toggle`, `kit-input-file`, Einstieg — Tests: `tests/vue3/felder-auswahl.test.ts`
- [ ] T4 (AK14–AK16, AK19) [pro] `kit-label`, `kit-form`, `kit-input-group`, `kit-input-group-text`, Einstieg vollständig — Tests: `tests/vue3/felder-rahmen.test.ts`
- [ ] T5 (AK17, AK18, AK21) [pro] Themes, README, CHANGELOG — Tests: `tests/Feature/Vue3/Vue3FelderQuelltextTest.php`
- [ ] T6 (AK20) [pro] Host: Prüfseite `/ui-kit-vue3` um alle Felder erweitern, Build — Tests: Host `tests/Feature/HostGetRoutenTest.php`, `tests/Feature/KitVue3CssTest.php`; Nachweis per Echtlauf (AK20 nicht automatisch prüfbar)

Bestehende Tests: `tests/vue3/*.test.ts` aus vue3-kern-grundlage und vue3-kern-logik, `tests/Feature/Vue3/Vue3QuelltextTest.php`, `tests/Feature/Vue3/Vue3LogikQuelltextTest.php`, `tests/js/mount-inseln.test.mjs`
Hinweis Aufträge: Kit-Tests (`*.test.ts`) nur mit `PROJEKT: …/packages/laravel-kit` in die `TESTS:`-Zeile (ohne `PROJEKT:` startet das GATE sie per `node --test`, HANDOFF in vue3-kern-logik F2).
Marketing: entfällt (Bausteine ohne neue öffentliche Seite).
Design: entfällt (Optik nur über die vorhandenen Theme-Dateien, Prüfseite im Host).
Offener Punkt aed6469519f935f21: AK-fremd (AK12 „Leertaste“ angenähert über `type=button` + Klick, happy-dom simuliert Leertaste→Klick nicht — Nachweis im Echtlauf T6; Hinweise an Coder: Datei je Tag `vue3/components/<name>/<name>.vue`, `kit-input-group-text` neben `input-group/`, README-Tabelle „Ersetzt“ mit Backticks, Theme-Regeln ohne `@each`/Mixin, `label`-Prop auch an `kit-checkbox`/`kit-radio` — steht im Vertrag „gemeinsame Props der Felder“)
Review: nötig (Querschnitt Kit und Host, > 50 Zeilen Produktivcode).

## Stand
Rote Tests committet. Nächster Schritt: T1 coder-pro.
