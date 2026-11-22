# Changelog

## Unveröffentlicht

## 1.11.0 – 2026-11-22
### Kontakt-Leiste
- Neu: `<x-kit::kontakt-leiste />` (themenunabhängig): feste Leiste mit Telefon, E-Mail und WhatsApp, Inline-CSS und -SVG, kein Script, keine externen Quellen. Mobil unten, ab 768 px rechts mit ausklappender Fahne. Config `laravelkit.kontakt_leiste` (Env `LARAVELKIT_KONTAKT_AKTIV`, `_TELEFON`, `_TELEFON_ANZEIGE`, `_EMAIL`, `_WHATSAPP`, `_WHATSAPP_TEXT`), Farben über CSS-Variablen `--kontakt-leiste-*`. Unter 768 px bekommt `body` unten Innenabstand in Leistenhöhe; im Druck ist die Leiste ausgeblendet.

## 1.10.0 – 2026-11-21
### DataTable (vue/tailwind)
- Neu: Prop `aktionsSpalte` (Datenfeld mit Aktions-HTML vom Server, z. B. Yajra `addColumn('action', …)`; ersetzt die Standard-Buttons) und Prop `zeilenLink` (URL-Vorlage mit `:id`, Klick auf eine Zeile öffnet sie) (#1794).

### HButton
- Fix: `HButton` (project, create, show, edit, delete, save, back) gibt Inline-SVG statt Font Awesome (`<i class="fas …">`) aus, Pfade wie die Vue-Buttons; bei leerem Text setzt es `aria-label` mit dem Tooltip, ein übergebenes `aria-label` gewinnt (#1795). Hinweis: Die Blade-Components `bootstrap5` und `Classes/Bootstrap4/Button` nutzen noch `fas`.

### db:backup-seeders
- Neu: Config `laravelkit.backup_seeders.only_tables` (Standard `[]`): nicht leer = nur diese Tabellen exportieren, `ignore_tables` gilt dann nicht; unbekannte Tabellen warnen, Exit-Code 0 (#1780).
- Neu: Der Befehl entfernt von iseed doppelt ergänzte `$this->call(X::class);`-Zeilen in `DatabaseSeeder.php`, wenn der Seeder dort schon vorkommt (z. B. in `call([...])`); vorhandene Zeilen und neue Aufrufe bleiben (#1780).

### Log-Dateien
- Neu: Das Kit stellt den Log-Kanal `single` auf `daily` um (eine Datei je Tag, `days` 30), ohne Änderung an `.env` oder `config/logging.php`. Config `laravelkit.logs.taeglich`, `.tage`, `.zeitplan` (Env `LARAVELKIT_LOGS_TAEGLICH`, `_TAGE`, `_ZEITPLAN`); Details in der README „Log-Dateien“ (#1716).
- Neu: `php artisan log:prune [--days=N] [--dry-run]` löscht `.log`-Dateien in `storage/logs`, die älter als N Tage sind; läuft täglich im Scheduler (abschaltbar).

### Deprecated
- Auslaufend, Entfernung mit einer neuen Major-Version; Ersatz siehe README „Auslaufend“ (#1715): `vue/bootstrap4`, `vue/bootstrap5`, `vue/tailwind` (Ersatz `vue3/`), `vue/vueapp_TW.js`, `vue/vueapp_B5.js`, `vue/vueapp_B4.js` (Ersatz `vue3/` mit `mountInseln`) und die Blade-Themes `bootstrap5` und `tailwind` (Ersatz Theme `kern`).
- Neu dazu: Bei aktivem Blade-Theme `bootstrap5` oder `tailwind` löst das Kit je Request einmal `E_USER_DEPRECATED` aus; die `vueapp_*.js` warnen beim Laden per `console.warn` (nicht mit `NODE_ENV=production`).

### Weitere Änderungen
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
