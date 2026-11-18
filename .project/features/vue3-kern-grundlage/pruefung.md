# Prüfung: vue3-kern-grundlage — 2026-11-18 (Nachprüfung seit ee4bd590)
Geprüft im Review-Platz: Host a7fb294 (Fix-Commits 8187d93, 9f67618), laravel-kit 3c94c8ca (Fix-Commits bd73b550, 203ccd9e, 3c94c8ca). Spec seit ee4bd590 unverändert. Der Fix ändert `vue3/themes/bootstrap5.scss` (Dialog ohne `@extend .modal-content`/`.modal-title`), die README (`:root`-Voraussetzung), im Host `resources/scss/kit-vue3.scss` (`bootstrap/scss/root`) und die gebaute `public/css/kit-vue3.css`; keine neuen Klassen, keine Migration, keine geteilten Komponenten → Nachprüfung, kein volles Review.
Browser = eigene Headless-Chrome-Instanz (Port 0, Profil im Scratchpad) per CDP mit echten Maus- und Tastenereignissen auf http://127.0.0.1:8104/ui-kit-vue3 (Server des Platzes, cwd `…/reviewer/public`).

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 (alle vier Aussagen) | bestanden (Prüfung ee4bd590) | Fix berührt nur die Optik; Browser erneut: nach Klick `class="kit-dialog__content is-open"`, Elternelement `BODY` |
| AK2 „Dann ist `.kit-dialog__content` nicht mehr im DOM“ (Escape) | bestanden | Browser: echte Escape-Taste → 0 Inhalte |
| AK2 „und der Fokus liegt wieder auf dem Trigger“ | bestanden | Browser: `activeElement` = `.kit-dialog__trigger` |
| AK2 „… oder `<kit-dialog-close>` angeklickt wird“ | bestanden (Prüfung ee4bd590) | |
| AK3, AK4 | bestanden (Prüfung ee4bd590) | Vitest `tests/vue3` 17/17 grün |
| AK5, AK6, AK7, AK8, AK9 (alle Aussagen) | bestanden (Prüfung ee4bd590) | Vitest 17/17 grün; Theme-Änderung betrifft Tabs/Accordion nicht (Diff nur `.kit-dialog__content`, `.kit-dialog__title`) |
| AK10 (alle drei Aussagen) | bestanden (Prüfung ee4bd590) | `Vue3QuelltextTest` grün; Fix ändert keine Komponente |
| AK11 „`vue3/themes/tailwind.css` … (Liste)“ | bestanden (Prüfung ee4bd590) | |
| AK11 „… `vue3/themes/bootstrap5.scss` … (Liste)“ | bestanden | `Vue3QuelltextTest` grün (10 Tests, 48 Assertions); Sonde Dart Sass: Theme mit vollem Bootstrap 5.2.3 und mit nur functions/variables/maps/mixins/modal/nav/accordion/buttons/close kompiliert ohne Fehler, `.kit-dialog__content` mit `background-color:#fff`, `border:1px solid var(--bs-border-color-translucent)`, `border-radius:.5rem`, `box-shadow` |
| AK11 „und die Zustände `is-open`, `is-active`, `disabled`“ | bestanden | Test grün |
| AK12 (beide Aussagen) | bestanden (Prüfung ee4bd590) | Vitest `inseln.test.ts` grün |
| AK13 „Wenn er `vue3/index.ts` einbindet und `npm run production` läuft, Dann baut er ohne Fehler“ | bestanden | `mix --production` in Kopie des Platzes (Scratchpad; laravel-kit kopiert, übrige Pakete und node_modules des Platzes): Exit 0, „Compiled Successfully“; `public/css/kit-vue3.css` byte-gleich mit dem Commit (`cmp`) |
| AK13 „und die Seite `/ui-kit-vue3` (neu, nur im Host) zeigt Dialog, Tabs und Accordion“ | bestanden | GET → 200; Browser: 0 unaufgelöste `kit-*`-Tags, Screenshot zeigt alle drei |
| AK13 „mit dem Bootstrap-5-Theme“ | bestanden | Browser `getComputedStyle(.kit-dialog__content)` nach echtem Klick: `background-color: rgb(255,255,255)`, `border: 1px solid rgba(0,0,0,0.176)`, `border-radius: 8px`, Schatten, `position: fixed`, `z-index: 1051`, Breite 500 px mittig, Mitte des Dialogs oben (`elementFromPoint`); Overlay `rgba(0,0,0,0.5)`; Titel `margin-top: 0`, `line-height` gesetzt. Screenshot: weiße Fläche mit Rahmen und Radius über abgedunkelter Seite. Accordion-Eintrag `border: 1px solid rgb(222,226,230)`, Tabs und Phoenix-Textfarbe (`rgb(49,55,74)`) unverändert. Theme setzt kein `position` mehr → Ladereihenfolge kit.css/Theme egal (Hinweis aus ee4bd590 erledigt) |
| AK13 „ohne Konsolenfehler“ | bestanden | Browser: Konsole nur Boost-Logger-Meldung, keine Fehler, keine Ausnahmen, auch nach Klick und Escape |
| AK14 „beschreibt die README den Ordner `vue3/`, die Klassenregel …“ | bestanden | README Abschnitt „Vue 3“ gelesen, unverändert bis auf den neuen Absatz; Test grün |
| AK14 „das Einbinden (`KitVue3`, `kitKomponenten` mit `mountInseln`, Theme-Datei, `npm install reka-ui`)“ | bestanden | README „Einbinden“ 1–4; neuer Absatz nennt die `:root`-Voraussetzung des Bootstrap-5-Themes und den Weg für Präfix-Projekte (Phoenix) — mit der Sass-Sonde stimmig (`$modal-content-border-color` = `var(--#{$prefix}border-color-translucent)`) |
| AK14 „und `vue/README.md` sagt …“ | bestanden (Prüfung ee4bd590) | |
| Randfall 1–3 | bestanden (Prüfung ee4bd590) | Vitest grün |
| Randfall 4 „Der Dialog rendert per Teleport an `body`“ | bestanden | Browser: Elternelement `BODY` |
| Randfall 4 „im Insel-Betrieb …“ | bestanden (Prüfung ee4bd590) | |
| Vertrag `js/mount-inseln.js` unverändert | bestanden | `git diff ee4bd590..3c94c8ca -- js/` leer; `node --test tests/js/*.test.mjs` 240/240 |
| Vertrag `reka-ui ~2.11` | bestanden (Prüfung ee4bd590) | |
| Nicht-Ziele | eingehalten | Fix-Diff nur Theme, README, Host-SCSS/CSS, Tests, plan.md; kein Tag |

## Sicherheit
- Fix-Diff ohne PHP-Produktivcode, ohne Route, ohne Eingaben: nur SCSS, gebaute CSS und Doku. Keine Befunde.

## Regressionen
- Volle Suite nicht gelaufen (Nachprüfung: Fix ändert keinen PHP-Code; die Host-SCSS wirkt nur auf `/ui-kit-vue3`, kein anderer View bindet `kit-vue3.css` ein — grep `resources/views`).
- `php artisan test packages/laravel-kit/tests/Feature/Vue3` → 10 grün (48 Assertions, inkl. drei neuer Dialog-Tests); `tests/Feature/KitVue3CssTest.php` → 2 grün.
- Kit: `npx vitest run tests/vue3` → 17/17; `node --test tests/js/*.test.mjs` → 240/240.
- `laravel.log` seit dem Echtlauf: nur `telescope_entries doesn't exist` (Review-DB des Platzes, schon in ee4bd590 vermerkt).

## Funde: 0 muss, 0 sollte, 3 kann
- kann: `bootstrap5.scss` setzt `background-color: $modal-content-bg` als festen Sass-Wert (Bootstrap 5.2: `#fff`); mit Bootstrap 5.3 und `data-bs-theme="dark"` folgt der Dialog dem Farbmodus nur, wenn das Projekt mit 5.3-Sass kompiliert (dort ist der Wert `var(--bs-body-bg)`).
- kann: `Vue3QuelltextTest::test_bootstrap5_tabs_und_accordion_erben_die_elternklasse_mit_den_variablen` — letzte Assertion `assertNull(regelRumpf(…, '.kit-x'))` prüft nur den Helfer, nicht das Theme.
- kann: `KitVue3CssTest` prüft die eingecheckte `public/css/kit-vue3.css`, nicht, dass sie zum aktuellen SCSS passt (im Review per Build und `cmp` bestätigt).
- Die sollte-Funde aus ee4bd590 stehen weiter in Folge-Task #1556; die kann-Funde aus ee4bd590 gelten weiter, außer dem Hinweis zu `position: relative` (erledigt).

## Neue Tasks: keine

## Fehlerprotokoll
- keine

## Fazit: freigegeben
Der muss-Fund aus ee4bd590 (AK13 „mit dem Bootstrap-5-Theme“, Dialog ohne Fläche) ist behoben und im Browser an den berechneten Stilen und im Screenshot nachgewiesen; Build reproduzierbar, Tests des Features grün.
