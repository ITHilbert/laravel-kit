# Spec vue3-kern-grundlage
Risiko: normal
Art: Feature — erster Teil des Vue-3-Kerns aus Entscheidung 0013 (Host `packages_dev`, `.project/doku/decisions/0013-laravel-kit-vue3-kern-ohne-framework-optik.md`, Schritt 3). Kein Dashboard-Task (Vorgabe Joerg, 2026-11-17).
Status: freigegeben (Joerg, Chat 2026-11-17: Spec, Vitest als Testwerkzeug, Reka UI ~2.11).
Rückbau: entfällt (kein Übergangsstand; der Rückbau der alten Varianten ist #1546)

## Ziel
Im Kit entsteht der Ordner `vue3/` mit Einstieg, Basis-CSS, zwei Theme-Dateien und den ersten drei Komponenten mit Logik: Dialog, Tabs, Accordion. Die Komponenten bauen auf Reka UI 2.x auf und tragen nur `kit-`-Klassen.

## Vertrag
- **Ordner:**
  - `vue3/components/<name>/` je Komponente, Dateien `<teil>.vue` mit `<script setup lang="ts">`
  - `vue3/kit.css` (Basis-CSS für die Funktion)
  - `vue3/themes/tailwind.css`, `vue3/themes/bootstrap5.scss`
  - `vue3/index.ts` (Einstieg)
- **Einstieg `vue3/index.ts`:**
  - exportiert jede Komponente einzeln
  - exportiert `kitKomponenten` für `mountInseln` (`js/mount-inseln.js`): Objekt Tag-Name → Loader in der Form, die `mountInseln` erwartet (`() => Promise<{ default: Komponente }>`); `js/mount-inseln.js` bleibt unverändert
  - exportiert ein Vue-Plugin `KitVue3` (`app.use(KitVue3)` registriert alle Komponenten)
- **Tag-Namen:** `kit-dialog`, `kit-dialog-trigger`, `kit-dialog-title`, `kit-dialog-close`, `kit-tabs`, `kit-tab`, `kit-tab-panel`, `kit-accordion`, `kit-accordion-item`.
- **Klassen:** Jedes gerenderte Teil trägt genau eine Hauptklasse `kit-<komponente>` bzw. `kit-<komponente>__<teil>` (Liste je Komponente in den AKs). Zustände stehen als Klassen `is-open`, `is-active`, `disabled` am Teil. Die `data-*`-Attribute von Reka bleiben zusätzlich erhalten.
- **Keine Optik im Kern:** Keine Datei unter `vue3/components/` und `vue3/kit.css` enthält Bootstrap- oder Tailwind-Klassen. `vue3/kit.css` enthält nur Regeln für die Funktion (Overlay-Position, Ausblenden, Scroll-Sperre).
- **Abhängigkeit:** `reka-ui` `~2.11` in `package.json` des Kits (`peerDependencies` bzw. Hinweis in der README: Projekte installieren `reka-ui` und `vue` ≥ 3.4 selbst).

## Akzeptanzkriterien
- **AK1** Gegeben `<kit-dialog>` mit `<kit-dialog-trigger>Öffnen</kit-dialog-trigger>`, `<kit-dialog-title>Titel</kit-dialog-title>` und Inhalt, Wenn der Trigger angeklickt wird, Dann ist der Dialog offen: Ein Element `.kit-dialog__overlay` und ein Element `.kit-dialog__content` mit `role="dialog"` stehen im DOM, `.kit-dialog__content` trägt die Klasse `is-open`, und `aria-labelledby` zeigt auf das Element `.kit-dialog__title`.
- **AK2** Gegeben ein offener Dialog, Wenn Escape gedrückt oder `<kit-dialog-close>` (Klasse `kit-dialog__close`) angeklickt wird, Dann ist `.kit-dialog__content` nicht mehr im DOM und der Fokus liegt wieder auf dem Trigger.
- **AK3** Gegeben ein offener Dialog mit zwei Schaltflächen im Inhalt, Wenn Tab auf der letzten fokussierbaren Schaltfläche gedrückt wird, Dann springt der Fokus zur ersten fokussierbaren Stelle im Dialog (Fokusfalle).
- **AK4** Gegeben `<kit-dialog v-model:open="offen">`, Wenn `offen` von außen auf `true` gesetzt wird, Dann öffnet sich der Dialog, und wenn er per Escape schließt, ist `offen` wieder `false`.
- **AK5** Gegeben `<kit-tabs default-value="a">` mit drei `<kit-tab value="a|b|c">` und drei `<kit-tab-panel value="a|b|c">`, Wenn die Komponente rendert, Dann gibt es `.kit-tabs`, `.kit-tabs__list` (`role="tablist"`), drei `.kit-tabs__tab` (`role="tab"`) und genau ein sichtbares `.kit-tabs__panel` (`role="tabpanel"`) für „a“; Tab „a“ und Panel „a“ tragen `is-active`, `aria-selected="true"` steht nur an Tab „a“.
- **AK6** Gegeben die Tabs aus AK5 mit Fokus auf Tab „a“, Wenn Pfeil-rechts gedrückt wird, Dann liegt der Fokus auf Tab „b“; wenn Tab „c“ angeklickt wird, Dann ist Panel „c“ sichtbar und trägt `is-active`, Panel „a“ ist nicht sichtbar.
- **AK7** Gegeben `<kit-accordion type="single" collapsible>` mit zwei `<kit-accordion-item value="x|y" title="…">`, Wenn die Komponente rendert, Dann gibt es `.kit-accordion`, je Eintrag `.kit-accordion__item`, `.kit-accordion__trigger` (Schaltfläche mit `aria-expanded="false"`) und ein nicht sichtbares `.kit-accordion__content`.
- **AK8** Gegeben das Accordion aus AK7, Wenn der Trigger von „x“ angeklickt wird, Dann ist der Inhalt von „x“ sichtbar, `.kit-accordion__item` von „x“ trägt `is-open` und der Trigger `aria-expanded="true"`; wenn danach der Trigger von „y“ angeklickt wird, ist „x“ geschlossen und „y“ offen; ein zweiter Klick auf „y“ schließt „y“.
- **AK9** Gegeben ein Teil einer Kit-Komponente mit dem Attribut `disabled` (`kit-tab`, `kit-accordion-item`), Wenn es rendert, Dann trägt es die Klasse `disabled` und lässt sich nicht per Klick aktivieren.
- **AK10** Gegeben alle Dateien unter `vue3/components/` und `vue3/kit.css`, Wenn ihr Quelltext gelesen wird, Dann enthalten sie keine Bootstrap-Klassen (`btn`, `modal`, `nav-`, `card`, `form-`, `col-`, `row`) und keine Tailwind-Utilities (Muster wie `px-`, `py-`, `text-`, `bg-`, `flex`, `grid`, `rounded`, `@apply`) und keine Klasse ohne Vorsilbe `kit-` außer den Zustandsklassen `is-open`, `is-active`, `is-invalid`, `disabled`.
- **AK11** Gegeben `vue3/themes/tailwind.css` und `vue3/themes/bootstrap5.scss`, Wenn sie gelesen werden, Dann enthält jede eine Regel für jede Hauptklasse und jeden Teil aus AK1–AK8 (`.kit-dialog__overlay`, `.kit-dialog__content`, `.kit-dialog__title`, `.kit-dialog__close`, `.kit-dialog__trigger`, `.kit-tabs__list`, `.kit-tabs__tab`, `.kit-tabs__panel`, `.kit-accordion__item`, `.kit-accordion__trigger`, `.kit-accordion__content`) und die Zustände `is-open`, `is-active`, `disabled`.
- **AK12** Gegeben `vue3/index.ts`, Wenn ein Projekt `mountInseln(kitKomponenten)` auf einer Seite mit `<div data-vue="kit-tabs" data-props='{"defaultValue":"a"}'>` aufruft, Dann mountet die Insel die Tabs ohne Konsolenfehler; zwei Inseln auf einer Seite (Dialog und Tabs) arbeiten unabhängig.
- **AK13** Gegeben der Host `packages_dev` (Vue 3, Laravel Mix 6), Wenn er `vue3/index.ts` einbindet und `npm run production` läuft, Dann baut er ohne Fehler, und die Seite `/ui-kit-vue3` (neu, nur im Host) zeigt Dialog, Tabs und Accordion mit dem Bootstrap-5-Theme ohne Konsolenfehler.
- **AK14** Gegeben README und CHANGELOG des Kits, Wenn gelesen wird, Dann beschreibt die README den Ordner `vue3/`, die Klassenregel (`kit-`, `__`, Zustände), das Einbinden (`KitVue3`, `kitKomponenten` mit `mountInseln`, Theme-Datei, `npm install reka-ui`), und `vue/README.md` sagt, dass nur `vue/tailwind/` Vue 3 ist und die Varianten unter `vue/` nicht mehr gepflegt werden.

- **AK15** (ergänzt 2026-11-19, Spec-Lücke, Moderator in Vollmacht; Joerg, Chat 2026-11-19: „stelle das laravel-kit nun fertig ohne weitere Rückfragen“) (#679) Gegeben `vue3/themes/tailwind.css`, Wenn sie gelesen wird, Dann stehen alle Regeln für `kit-`-Klassen in `@layer components`, sodass Tailwind-Utilities aus `@layer utilities` (z. B. `hidden`, `mt-4`) an einem Kit-Element die Theme-Regel überschreiben.
- **AK16** (ergänzt 2026-11-19, Spec-Lücke, Moderator in Vollmacht; Joerg, Chat 2026-11-19: „stelle das laravel-kit nun fertig ohne weitere Rückfragen“) (#1556) Gegeben beide Theme-Dateien, Wenn der Test sie mit den echten Werkzeugen übersetzt (`vue3/themes/bootstrap5.scss` mit Dart Sass und dem Bootstrap-5.2-Sass, `vue3/themes/tailwind.css` mit Tailwind 4), Dann übersetzen beide ohne Fehler.
- **AK17** (ergänzt 2026-11-19, Spec-Lücke, Moderator in Vollmacht; Joerg, Chat 2026-11-19: „stelle das laravel-kit nun fertig ohne weitere Rückfragen“) (#1556) Gegeben das Kit, Wenn `npm run typecheck` läuft (`vue-tsc --noEmit` über `vue3/`), Dann endet es ohne Typfehler.
- **AK18** (ergänzt 2026-11-19, Spec-Lücke, Moderator in Vollmacht; Joerg, Chat 2026-11-19: „stelle das laravel-kit nun fertig ohne weitere Rückfragen“) (#1556) Gegeben die README, Abschnitt „Vue 3“, Einbinden über `mountInseln`, Wenn gelesen wird, Dann steht dort, dass eine Insel nur ihre `data-props` bekommt und HTML-Inhalt im Insel-Element verworfen wird; Komponenten mit Slots (Dialog, Tabs, Accordion) bindet man über das Plugin `KitVue3` in einer Vue-App ein.

## Randfälle
1. Ein Dialog ohne `<kit-dialog-title>`: kein Fehler, Reka-Warnung in der Konsole ist erlaubt.
2. Tabs ohne `default-value`: kein Tab aktiv, kein Fehler.
3. Zwei Dialoge auf einer Seite: Es ist immer nur der zuletzt geöffnete fokussiert; Escape schließt nur ihn.
4. Der Dialog rendert per Teleport an `body`; im Insel-Betrieb liegt der Inhalt trotzdem außerhalb der Insel.
5. Konfiguration der Typprüfung (AK17): Das Kit legt sie nicht als `tsconfig.json` ab, sondern unter eigenem Namen (z. B. `tsconfig.typecheck.json`), und `typecheck` nennt sie per `-p`. Grund: ts-loader, Vite und esbuild eines einbindenden Projekts lesen die nächstgelegene `tsconfig.json`; der Host `packages_dev` (TypeScript 4.9) brach am `moduleResolution: Bundler` des Kits ab. Ein Projekt baut die Kit-Dateien mit seiner eigenen Konfiguration. (ergänzt 2026-11-19, Spec-Lücke, Moderator in Vollmacht)

## Nicht-Ziele
- Combobox, Token-Eingabe, Suche, Liste/DataTable und die einfachen Felder (eigene Features danach).
- Blade-Components (Entscheidung 0013, Punkt 5, später).
- Umstellen anderer Projekte, Release-Version.
- Eigene Optik über die Theme-Dateien hinaus; das Aussehen der Themes ist schlicht und an Bootstrap 5 bzw. Tailwind-Standardwerte angelehnt.

## Entscheidungsspielraum
Interne Struktur der Komponenten, Namen von Props über die genannten hinaus, Aufbau des Basis-CSS. Neue npm-Pakete nur: `reka-ui` (Laufzeit) und die Testwerkzeuge Vitest, `@vue/test-utils`, `happy-dom` (nur Entwicklung). Ergänzt 2026-11-19 (#1556, Moderator in Vollmacht): als Entwicklungs-Pakete außerdem `vue`, `@vitejs/plugin-vue`, `typescript` (Tests), `vue-tsc` (AK17), `sass`, `bootstrap` 5.2, `tailwindcss` und `@tailwindcss/node` (AK16).
