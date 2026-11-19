# Changelog

## Unveröffentlicht
- Fix: `kit-toggle` mit `name` und `disabled` sendet nichts mehr; das versteckte Feld ist ebenfalls `disabled`.
- Fix: Zahlenfelder (`kit-input-number`, `-int`, `-euro`, `-percent`) nehmen `step` als Zahl oder Text an, ohne Vue-Warnung (#1611).
- Fix: Alle `kit-`-Regeln in `vue3/themes/tailwind.css` stehen in `@layer components`, Tailwind-Utilities können sie überschreiben.
- Fix: Der Zusatz „€“/„%“ von `kit-input-euro` und `kit-input-percent` schließt im Theme `tailwind` bündig rechts ans Feld an, wie im Theme `bootstrap5` (#1611).
- Doku: README „Vue 3“ nennt die Grenzen der Inseln (nur `data-props`, HTML-Inhalt verworfen, Slots über `KitVue3`); die alten Tailwind-CSS-Dateien unter `vue/tailwind/` werden mit `layer(components)` importiert (#679); `kit-form` ohne `method` sendet per `post`.
- Neu: Theme `kern` für die Blade-Components (`LARAVELKIT_THEME=kern`, `KitTheme::Kern`, Templates unter `src/Views/kit/kern/`): gleiches Markup und gleiche `kit-`-Klassen wie der Vue-3-Kern, Symbole als inline-SVG. `js/tabs.js` und `js/combobox.js` binden die Kern-Templates über `data-`-Attribute (Ausgabe für die alten Themes unverändert); Regeln für Buttons, Cards, Tabs-Inhalt, Breadcrumb und Anzeigen in `vue3/themes/bootstrap5.scss` und `vue3/themes/tailwind.css`. Die Themes `bootstrap5` und `tailwind` sind eingefroren (Entscheidung 0013).
- Neu: Ordner `vue3/` mit Vue-3-Kern aus Dialog, Tabs und Accordion (Reka UI, Klassen `kit-…`, Plugin `KitVue3`, `kitKomponenten` für `mountInseln`), `vue3/kit.css` und Themes `vue3/themes/tailwind.css` und `vue3/themes/bootstrap5.scss`.
- Neu: Vue-3-Komponenten `kit-combobox` (Reka UI), `kit-search` (mit `verzoegerung`, Ereignis `suche`), `kit-input-token` (anzeigen, kopieren) und `kit-input-list` (Datalist), dazu Regeln in `vue3/kit.css` und beiden Themes.
- Neu: Vue-3-Felder `kit-input-text`, `kit-input-email`, `kit-input-password`, `kit-input-number`, `kit-input-int`, `kit-input-euro`, `kit-input-percent`, `kit-input-date`, `kit-input-color`, `kit-input-file`, `kit-textarea`, `kit-select`, `kit-checkbox`, `kit-radio`, `kit-toggle`, `kit-label`, `kit-form`, `kit-input-group`, `kit-input-group-text` (Teile `__label`, `__input`, `__error`, Zustände `is-invalid`, `is-active`, `disabled`), dazu Regeln in `vue3/kit.css` und beiden Themes. Sie ersetzen die Felder aus `vue/tailwind/forms/` (Tabelle „Ersetzt“ in der README). Damit ist der Vue-3-Kern vollständig.
- Neu: `kit-select` ohne `placeholder` hat keine leere Option. Angezeigt wird die erste wählbare Option; sie wird auch per `update:modelValue` an `v-model` gemeldet, wenn das Modell zu keiner Option passt (#1612).
- Neue Abhängigkeit: `reka-ui` (`~2.11`, peerDependency; dazu `vue` ab 3.5).
- Neu: `mountInseln` setzt je Insel ein eigenes `idPrefix` (`kit1`, `kit2`, …), dadurch sind die ids von `useId()` über alle Inseln einer Seite eindeutig (z. B. Datalist von `kit-input-list`).
- Hinweis: Die Wurzel `kit-combobox` trägt denselben Klassennamen wie `span.kit-combobox` aus `js/combobox.js`. Mit geladener `vue3/kit.css` wird dieses Element zur Zeile ohne Außenabstand.

## 1.7.0 – 2026-11-16
- Änderung: Die Vue-Komponente `search` (Bootstrap 4/5, Klasse `selectcb`) nutzt statt des jQuery-UI-Widgets `ui.combobox` das Vanilla-Skript `js/combobox.js` (`initCombobox(root = document)`). Combobox ohne jQuery UI: Suche ohne Groß-/Kleinschreibung mit Hervorhebung, Knopf öffnet alle Einträge, Tastatur (Pfeile, Enter, Escape), Auswahl löst `change` am Select aus.
- Änderung: `install:mix` installiert `jquery-ui` nicht mehr (`jquery` bleibt für DataTables). Host-Projekte können `jquery-ui` aus ihrer `package.json` entfernen.
- Änderung: Die CSS-Klassen `ui-combobox-*` heißen jetzt `kit-combobox-*`.
- Fix: `search` behält die Nutzerauswahl beim Neu-Rendern (Auswahl in `data.gewaehlt` statt Bindung an die Prop `value`), meldet die Auswahl per `input` (`v-model`) und gleicht die Combobox bei Änderung von `value`/`options` an. Neuer Export `aktualisiereCombobox(select)` in `js/combobox.js` (#1366).
- Entfallen: `window.onload` für Select-Elemente außerhalb der Komponente — dort `initCombobox()` selbst aufrufen.

## 1.6.1 – 2026-11-16
- Fix: `DataTable_DE.json` wieder im Paket (`src/Publish/Public/laravelkit/`), neuer Publish-Tag `laravelkit-assets` (`php artisan vendor:publish --tag=laravelkit-assets`).

## 1.6.0 – 2026-11-10
- Änderung: Die Migration der Tabelle `log` lässt sich über die Config `laravelkit.log_tabelle` (Env `LARAVELKIT_LOG_TABELLE=false`) abschalten; Standard bleibt an.

## 1.5.0 – 2026-11-10
- `db:backup-seeders`: ein `iseed`-Aufruf je Tabelle mit `--orderby` und `--skip-fk-checks`, `migrations` nicht mehr exportiert, Sperre von `db:seed` außerhalb `local`/`testing` im `DatabaseSeeder`, Exit-Code 1 bei Fehlern.
- `install:iSeed`: `composer require --dev orangehill/iseed`.
