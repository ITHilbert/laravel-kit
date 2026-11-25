# Plan design-tokens (#2195)
laravel-kit `feature/design-tokens` von `main` 0f393fce. Spec: [spec.md](spec.md).

## Aufgaben
- [x] TW [pro] test-writer (AK1–AK9) — Tests: `tests/vue3/design-tokens.test.ts` (AK1–AK3, AK5–AK7), `tests/Feature/DesignTokensReadmeTest.php` (AK4, AK9)
- [x] T1 [pro] `css/ith-tokens.css`, `css/ith-theme.css`, Themes `tailwind.css` und `bootstrap5.scss` umstellen, README-Abschnitt, CHANGELOG — AK1–AK9

Bestehende Tests: `tests/vue3/*.test.ts`, `tests/Feature/Vue3/*.php`, `tests/Feature/KitComponents/*.php`, `tests/js/*.mjs`
Marketing: entfällt (keine neue Seite).
Design: entfällt (Werte vorgegeben durch das Corporate Design).
Review: entfällt (reine CSS-Werte, kein Verhalten, keine Route, keine Migration, < 50 Zeilen Produktivlogik; Review des Stapels im Host).
Stand: Spec geschrieben, Test-Writer folgt.

TW erledigt (test-writer a6f66f2a0174df4cb): rot JS 8/9 (Meldungen: ENOENT css/ith-*.css, Standardfarben gefunden, var(--ith- fehlt), grün 1 = Gegenprobe), PHP 2/3 (Abschnitt „Design-Tokens“ fehlt, CHANGELOG nennt Dateien nicht); Grünbar per Probe-Kopie im Scratchpad JS 9/9, PHP 3/3.
Abweichung a6f66f2a0174df4cb: Fehlalarm: Testauslegung (AK2 über übersetzte Ausgabe, AK9 „Vue-3-Theme und Blade-Theme kern“, README-Überschrift `## Design-Tokens`) deckt sich mit der Spec

T1 erledigt (coder-pro aa9a70c81e3f3b51e, GATE GRÜN: 99 PHP, Vitest 11, Pint ok). NACHWEIS Moderator: Vitest ganze Kit-Suite 186/186, PHP Feature/Vue3 + KitComponents + DesignTokensReadmeTest 2201 passed.
Echtlauf: entfällt (nur CSS-Werte in Theme-Dateien; Übersetzung mit Tailwind 4 und Dart Sass im Test belegt)
Layout: entfällt (Farbwerte, keine Layoutänderung)
Abweichung aa9a70c81e3f3b51e: Fehlalarm: keine gemeldet
Offener Punkt aa9a70c81e3f3b51e: AK-fremd (Dialog-Schließen: Rahmen `--ith-border`, Schrift `--ith-blue` — vom Test so verlangt; Primär-Hover Hilbert-Blau, Variante edit weiße Schrift auf `--ith-warning` wegen Kontrast, Graustufen `$gray-*` in bootstrap5.scss bleiben — Annahmen, Spec nennt dazu nichts; README-Beispielpfade nicht in einer App geprüft)
Stand: umgesetzt, Review entfällt.
