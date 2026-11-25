# Plan design-tokens (#2195)
laravel-kit `feature/design-tokens` von `main` 0f393fce. Spec: [spec.md](spec.md).

## Aufgaben
- [ ] TW [pro] test-writer (AK1–AK9) — Tests: `tests/vue3/design-tokens.test.ts` (AK1–AK3, AK5–AK7), `tests/Feature/DesignTokensReadmeTest.php` (AK4, AK9)
- [ ] T1 [pro] `css/ith-tokens.css`, `css/ith-theme.css`, Themes `tailwind.css` und `bootstrap5.scss` umstellen, README-Abschnitt, CHANGELOG — AK1–AK9

Bestehende Tests: `tests/vue3/*.test.ts`, `tests/Feature/Vue3/*.php`, `tests/Feature/KitComponents/*.php`, `tests/js/*.mjs`
Marketing: entfällt (keine neue Seite).
Design: entfällt (Werte vorgegeben durch das Corporate Design).
Review: entfällt (reine CSS-Werte, kein Verhalten, keine Route, keine Migration, < 50 Zeilen Produktivlogik; Review des Stapels im Host).
Stand: Spec geschrieben, Test-Writer folgt.
