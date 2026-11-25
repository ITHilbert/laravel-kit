# Spec design-tokens (#2195)

Risiko: normal
Quelle der Werte: https://wiki.lan/it-hilbert/corporate-design (Abschnitt „Design-Tokens“, Stand 2026-11-25). Freigabe: Auftrag Joerg (Telegram 2260), Spec selbst freigegeben im automode 2026-11-25.

## Ziel
Das Kit liefert die Design-Tokens der IT-Hilbert GmbH an einer Stelle aus. Apps binden sie ein, statt den Block zu kopieren. Die gepflegten Themes (Vue-3-Kern und Blade-Theme `kern`) setzen ihre Farben aus den Tokens.

## Akzeptanzkriterien
- **AK1** Gegeben das Paket, wenn man `css/ith-tokens.css` liest, dann enthält sie genau einen `:root`-Block mit den 16 Variablen `--ith-navy #073259`, `--ith-blue #0062a1`, `--ith-blue-dark #004e81`, `--ith-sky #1075b7`, `--ith-cyan #2ba8db`, `--ith-ice #e8f4fc`, `--ith-mist #bbe2f4`, `--ith-text #212529`, `--ith-text-muted #6c757d`, `--ith-border #dee2e6`, `--ith-surface #f7f7f7`, `--ith-success #198754`, `--ith-warning #9a5b00`, `--ith-danger #b02a37`, `--ith-radius 0.25rem`, `--ith-font "Inter", "Ubuntu", Arial, sans-serif` mit diesen Werten.
- **AK2** Gegeben `css/ith-theme.css`, wenn man sie mit Tailwind 4 nach `@import "tailwindcss"` übersetzt, dann enthält sie einen `@theme`-Block mit `--color-brand-navy`, `-blue`, `-blue-dark`, `-sky`, `-cyan`, `-ice`, `-mist` (Werte wie AK1), `--color-ink #212529`, `--color-ink-muted #6c757d`, `--color-line #dee2e6`, `--color-surface #f7f7f7`, `--color-success`, `--color-warning`, `--color-danger` (Werte wie AK1), `--font-sans` (Wert wie `--ith-font`) und `--radius-brand 0.25rem`; die Utilities `bg-brand-navy`, `text-brand-blue`, `rounded-brand` entstehen daraus.
- **AK3** Gegeben beide CSS-Dateien, wenn man sie nach Adressen durchsucht, dann kommt keine externe Adresse (`http://`, `https://`, `//`) und kein Google-Fonts-Bezug vor.
- **AK4** Gegeben die README, wenn man den Abschnitt „Design-Tokens“ liest, dann nennt er beide Dateien, je ein Einbindebeispiel für Tailwind/Vite, Sass/Bootstrap und Klartext-CSS, die Wiki-Seite als Quelle der Werte („Werte zuerst im Wiki ändern“), die Schrift Inter selbst gehostet über `@fontsource/inter` mit den Schnitten 400, 500, 600, 700 und das Verbot des Google-Fonts-CDN.
- **AK5** Gegeben `vue3/themes/tailwind.css`, wenn man sie liest, dann nutzt sie keine Tailwind-Standardfarben (`blue-`, `gray-`, `red-`, `green-`, `yellow-`, `slate-`) mehr, sondern die Tokens (`var(--ith-…)`): Primär-Fläche Navy mit weißer Schrift, Sekundär weiß mit Rahmen und Schrift Hilbert-Blau und Hover Eisblau, Links, aktiver Reiter und Fokusring Hilbert-Blau, Fehler `--ith-danger`, Erfolg `--ith-success`, Rahmen `--ith-border`, Text `--ith-text`, Nebentext `--ith-text-muted`, Ecken `--ith-radius`.
- **AK6** Gegeben `vue3/themes/bootstrap5.scss`, wenn man sie liest, dann stehen für Akzent-, Fehler- und Erfolgsfarbe der `kit-`-Klassen die Tokens (`var(--ith-blue)`, `var(--ith-danger)`, `var(--ith-success)`) statt `var(--bs-primary)`, `var(--bs-success)` und `$form-feedback-invalid-color`.
- **AK7** Gegeben beide Theme-Dateien, wenn sie mit Tailwind 4 bzw. Dart Sass übersetzt werden (wie `tests/vue3/themes-build.test.ts`), dann gelingt das, und die Ausgabe enthält `var(--ith-` und die Tokens selbst stehen in der Ausgabe (die Themes binden `css/ith-tokens.css` selbst ein, damit bestehende Einbindungen ohne Änderung Farbe behalten).
- **AK8** Gegeben das Paket, wenn die vorhandenen Tests laufen (PHP, Vitest, JS), dann bleiben sie grün; die Optik-Klassen und Namen der `kit-`-Klassen ändern sich nicht (Klassenregel `Vue3QuelltextTest`).
- **AK9** Gegeben `CHANGELOG.md`, wenn man den Abschnitt „Unveröffentlicht“ liest, dann nennt er die neuen Dateien und die Umstellung der beiden Themes.

## Randfälle
1. Ein Projekt bindet `ith-tokens.css` und eines der Themes: doppelte Variablen sind harmlos (gleiche Werte).
2. Ein Projekt ohne Tailwind bindet nur `ith-tokens.css`: `ith-theme.css` wird nicht gebraucht.

## Nicht-Ziele
- Die eingefrorenen Varianten (`vue/tailwind`, `vue/bootstrap4`, `vue/bootstrap5`, Blade-Themes `tailwind` und `bootstrap5`) bleiben unverändert (Entscheidung 0013, Punkt 6). Der Task nennt Buttons/DataTable; diese liegen in der eingefrorenen Variante. Nachfrage dazu im Protokoll.
- Dunkelmodus, Logo-Dateien, Schriftdateien im Paket (Inter wird als Abhängigkeit dokumentiert).
- Die Apps (dashboard, Gallerie, …) stellen selbst um.

## Entscheidungsspielraum
Zwei Dateien statt einer (Variablen für alle, `@theme` nur für Tailwind, weil `@theme` ohne Tailwind ungültig ist). Keine neuen Pakete. Version: nächste MINOR.
