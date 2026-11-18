# Prüfung: blade-kern — 2026-11-18
Kit `feature/blade-kern` 87deecd1 (Basis `main` 4019bd53), Host `feature/blade-kern` 9ceb44d (Basis `main`). Review-Platz `/home/joerg/projekte/.worktrees/packages_dev/reviewer`, Server 127.0.0.1:8104.

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „Wenn ein beliebiger der 34 Bausteine rendert, Dann kommt das Markup aus `src/Views/kit/kern/` ohne Exception“ | bestanden | `KernQuelltextTest::test_kern_hat_zu_jedem_bootstrap5_template_ein_gleichnamiges` (34 = 34, gleiche Namen); `KernFelderTest`, `KernLayoutTest`, `KernTabsComboboxTest` rendern jeden Baustein (AK1-Tests) grün; GET /ui-kit-blade-kern rendert alle 34 → 200 |
| AK1 „und `KitTheme::erlaubteWerte()` enthält `kern`“ | bestanden | `KernFelderTest::test_theme_kern_ist_erlaubt_…` grün; `src/Kit/KitTheme.php:14` `case Kern = 'kern'` |
| AK2 „gibt es `div.kit-input-text`, darin `label.kit-input-text__label` mit `for="vorname"` und `input.kit-input-text__input` mit `id="vorname"`, `name="vorname"`, `type="text"`“ | bestanden | `KernFelderTest::test_feld_hat_wurzel_label_und_eingabe_mit_kit_klassen` (Datensatz input-text) per XPath |
| AK2 „dasselbe gilt mit den Klassen aus der Tabelle für `input-email`, `input-password`, `input-date`, `input-number`, `input-euro`, `text-area` und `select`“ | bestanden | derselbe Test, je ein Datensatz (Wurzel `kit-textarea` für text-area, `kit-select` für select) |
| AK3 „Dann trägt die Wurzel `is-invalid`“ | bestanden | `KernFelderTest::test_fehler_setzt_is_invalid_aria_und_zeigt_meldung_mit_old_wert` |
| AK3 „das Eingabefeld hat `value="Max"`, `aria-invalid="true"` und `aria-describedby` mit der `id` von `span.kit-input-text__error`“ | bestanden | derselbe Test (old-Input über Session, id des Spans im aria-describedby) |
| AK3 „und der Span zeigt die Meldung“ | bestanden | derselbe Test: Text „Vorname fehlt.“ |
| AK3 „ohne Fehler gibt es weder `is-invalid` noch `__error`“ | bestanden | derselbe Test, Gegenprobe vor dem Fehleraufbau |
| AK4 „steht nach dem Eingabefeld ein `kit-input-euro__suffix` mit „€““ | bestanden | `KernFelderTest::test_input_euro_hat_suffix_nach_dem_eingabefeld` (following-sibling); Browser: „€“ rechts neben dem Feld |
| AK5 „`div.kit-checkbox` mit `input.kit-checkbox__input` (`type="checkbox"`, angehakt, `value="1"`)“ | bestanden | `KernFelderTest::test_checkbox_ist_angehakt_nur_mit_checked_…` |
| AK5 „und `label.kit-checkbox__label` „Aktiv“, das auf die `id` des Eingabefelds zeigt“ | bestanden | derselbe Test (`for` = id) |
| AK5 „ohne `checked` ist sie nicht angehakt“ | bestanden | derselbe Test (`$ohne`) |
| AK6 „`div.kit-input-file` mit `input.kit-input-file__input` (`type="file"`, `accept="image/png, image/jpeg"`)“ | bestanden | `KernFelderTest::test_input_file_img_hat_dateifeld_mit_bildtypen` |
| AK7 „trägt jeder die Klasse `kit-button` und `data-variant` laut Tabelle“ | bestanden | `KernLayoutTest` (7 Symbol-Buttons + button-submit + button mit/ohne `variant`); Sonde: button-edit als `type="button"` → `data-variant="edit"` |
| AK7 „behält `type`, `href` (Variante `type="link"` bzw. `route`), `title`“ | bestanden | `KernLayoutTest::test_symbol_button_als_link_…` (href, title je Variante), `…_button_form_behaelt_type_…` (type button/submit); Props je Button gleich bootstrap5 (Sonde Props-Vergleich) |
| AK7 „und die `data-kit-delete`-Bindung des Themes `bootstrap5`“ | bestanden | `KernLayoutTest` (`data-kit-delete="42"`); Sonde: delete als Link ohne `data-kit-delete` wie bootstrap5; `js/loeschen.js` bindet über `[data-kit-delete]` |
| AK7 „(ohne `data-toggle="tooltip"`)“ | bestanden | `KernLayoutTest` prüft `//*[@data-toggle]` nicht vorhanden |
| AK7 „ein Symbol steht als `svg.kit-button__icon` mit `aria-hidden="true"`“ | bestanden | `KernLayoutTest` je Symbol-Button; Browser: Symbole sichtbar, Breite 16 px |
| AK8 „gibt es `div.kit-card` mit `kit-card__header`, `kit-card__body` und `kit-card__footer`“ | bestanden | `KernLayoutTest::test_card_hat_kopf_rumpf_und_fuss_…` |
| AK8 „`classcard="extra"` hängt `extra` an `kit-card`“ | bestanden | derselbe Test, Gegenprobe ohne classcard |
| AK9 „gibt es `kit-tabs__list` (`role="tablist"`), zwei `button.kit-tabs__tab` (`role="tab"`, `aria-controls`, `aria-selected`), `kit-tabs__body` und zwei `kit-tabs__panel` (`role="tabpanel"`)“ | bestanden | `KernTabsComboboxTest::test_tabs_haben_rollen_aria_und_aktiv_zustand` (Zählung je 2) |
| AK9 „Reiter und Panel `a` tragen `is-active`“ | bestanden | derselbe Test |
| AK9 „Panel `b` trägt `hidden`“ | bestanden | derselbe Test; Browser: Panel B `display: none` beim Laden |
| AK10 „tragen Reiter und Panel `b` `is-active`“ | bestanden | `kernTabs.test.mjs` (führt `js/tabs.js` aus, Klick); eigener Chrome (CDP, echte Mausklicks) auf /ui-kit-blade-kern: Tab B → `kit-tabs__tab is-active`, Panel `pruef-b` `is-active` |
| AK10 „Panel `b` hat kein `hidden`, Panel `a` hat `hidden`“ | bestanden | ebd.; berechneter Stil: Panel B `display: block`, Panel A `display: none` |
| AK10 „und `aria-selected` stimmt“ | bestanden | ebd.: A `false`, B `true`; Zurückklicken auf A stellt den Ausgangszustand her |
| AK10 „die Ausgabe von `js/tabs.js` für die Themes `bootstrap5` und `tailwind` bleibt unverändert“ | bestanden | bestehende `tabs.test.mjs` grün; neuer Wächter in `kernTabs.test.mjs` (alte Klassen ohne `is-active`/`hidden`); Diff `js/tabs.js` ändert nur Zweige für `kit-tabs__tab`/`kit-tabs__panel` |
| AK11 „gibt es `div.kit-combobox`, `label.kit-combobox__label` und ein `select.kit-combobox__select` mit `name="land"`, `data-kit-combobox` und gewählter Option `at`“ | bestanden | `KernTabsComboboxTest::test_combobox_rendert_verstecktes_select_mit_gewaehlter_option` (Gegenprobe `de` nicht gewählt) |
| AK12 „ergänzt es in `div.kit-combobox` die Teile `kit-combobox__input` (`role="combobox"`, Wert „Österreich“), `kit-combobox__trigger` und `kit-combobox__list` (`role="listbox"`, versteckt)“ | bestanden | `kernCombobox.test.mjs` (Skript auf DOM, Wert „Österreich“); Browser: Teile als Geschwister des Selects in `div.kit-combobox`, Liste `hidden`, Select `display: none` |
| AK12 „geöffnet trägt `kit-combobox` `is-open`“ | bestanden | `kernCombobox.test.mjs`; Browser: Klick auf Trigger → `kit-combobox is-open`, Liste `display: block`, 105 px hoch, oberstes Element am ersten Eintrag ist der Eintrag selbst (`elementFromPoint`) |
| AK12 „jeder Eintrag ist `kit-combobox__item`“ | bestanden | ebd.: 3 × `kit-combobox__item` |
| AK12 „der markierte trägt `is-active`“ | bestanden | `kernCombobox.test.mjs` (ArrowDown → `is-active`, kein `active`) |
| AK12 „und eine Auswahl setzt den Wert des `<select>`“ | bestanden | `kernCombobox.test.mjs` (`ch`, change-Ereignis); Browser: Klick auf „Österreich“ → `select.value = at`, Liste zu, `is-open` entfernt |
| AK12 „Für `select.selectcb` der alten Themes bleibt die Ausgabe unverändert“ | bestanden | bestehende `combobox.test.mjs`, `comboboxAktualisieren.test.mjs` grün; Wächter in `kernCombobox.test.mjs` (span.kit-combobox, `kit-combobox-input`) |
| AK13 „`kit-show-euro` mit dem Text aus `KitAnzeige::euro`“ | bestanden | `KernLayoutTest::test_show_euro_show_date_und_breadcrumb_im_kit_markup`; Sonde: „1.234,50 €“ |
| AK13 „`kit-show-date` mit dem Text aus dem Theme `bootstrap5`“ | bestanden | derselbe Test vergleicht mit bootstrap5-Ausgabe; Sonde: beide „31.10.2026“ |
| AK13 „`nav.kit-breadcrumb` (`aria-label="breadcrumb"`) mit `ol.kit-breadcrumb__list`“ | bestanden | derselbe Test |
| AK14 „enthält kein `class`-Wert eine Klasse ohne Vorsilbe `kit-` außer den Zuständen“ | bestanden | `KernQuelltextTest::test_kern_templates_haben_nur_kit_klassen_…` (inkl. `partials/`), Prüfer mit Gegenprobe; zusätzlich gerendert: `fremdeKlassen()` in `KernFelderTest`/`KernLayoutTest`/`KernTabsComboboxTest` leer; Diff gelesen |
| AK14 „und es gibt keine Icon-Schrift (`fa-`, `bi-`)“ | bestanden | derselbe Test; `grep` in `src/Views/kit/kern/` ohne Treffer, Symbole als inline-SVG |
| AK15 „enthält jede eine Regel für `kit-button`, `kit-card`, `kit-card__header`, `kit-card__body`, `kit-card__footer`, `kit-tabs__body`, `kit-breadcrumb`, `kit-breadcrumb__list`, `kit-show-euro`, `kit-show-date`“ | bestanden | `KernQuelltextTest::test_themes_haben_regeln_…` (beide Dateien); Sonde Fremdformat: `bootstrap5.scss` über den Host-Build (Sass) übersetzt, `tailwind.css` mit echtem Tailwind 4.2.1 (`@tailwindcss/node` compile, `@apply` ohne Fehler), Regeln im Ergebnis vorhanden |
| AK15 „und für `kit-button` je `data-variant` aus AK7“ | bestanden | derselbe Test (8 Varianten je Datei); Browser: `data-variant=delete` rot (rgb 220,53,69) |
| AK15 „`bootstrap5.scss` nutzt nur Variablen aus Bootstrap 5.2“ | bestanden | `KernQuelltextTest::test_bootstrap5_theme_nutzt_nur_sass_variablen_…` gegen `node_modules/bootstrap` 5.2.3 (nicht übersprungen); Build ohne Sass-Fehler |
| AK16 „nennt die README im Abschnitt „Theme-Schalter“ den Wert `kern` mit den CSS-Dateien, die ihn stylen“ | bestanden | `KernQuelltextTest::test_readme_nennt_kern_…`; README gelesen: `vue3/kit.css` + `vue3/themes/bootstrap5.scss` bzw. `vue3/themes/tailwind.css` |
| AK16 „und sagt bei `bootstrap5` und `tailwind` „eingefroren, wird nicht mehr gepflegt (Entscheidung 0013)““ | bestanden | derselbe Test, je Zeile |
| AK16 „der CHANGELOG nennt das Theme unter „Unveröffentlicht““ | bestanden | `KernQuelltextTest::test_changelog_nennt_…` |
| AK17 „Wenn `npm run production` läuft, Dann baut er ohne Fehler“ | bestanden | eigener Lauf `mix --production` in einer Kopie des Platzes (Scratchpad, node_modules verlinkt): „Compiled successfully“; `public/css/kit-vue3.css` bytegleich mit dem eingecheckten Stand, `public/js/app.js` enthält dieselben Kern-Teile (Modul-IDs pfadbedingt anders) |
| AK17 „die neue Prüfseite `/ui-kit-blade-kern` zeigt alle 34 Bausteine im Theme `kern`“ | bestanden | `UiKitBladeKernTest` (2 Tests) grün; Bildschirmfoto im eigenen Chrome: alle Felder, Buttons, Karte, Tabs, Anzeige, Breadcrumb sichtbar |
| AK17 „mit dem Bootstrap-5-Theme“ | bestanden | Seite lädt `css/kit-vue3.css` (Bootstrap-Sass + `vue3/themes/bootstrap5.scss`); berechnete Stile: Button-Padding 6px 12px, Karte 1px solid, Fehlertext rot |
| AK17 „ohne Konsolenfehler“ | bestanden | `browser-lauf http://127.0.0.1:8104/ui-kit-blade-kern` → 0 Konsolenfehler, keine XHR/fetch; CDP: keine `console.error`/Exception |
| AK17 „Tabs schalten per Klick“ | bestanden | CDP-Mausklicks, siehe AK10 |
| AK17 „die Combobox öffnet eine sichtbare Liste“ | bestanden | `browser-lauf … --klick '.kit-combobox__trigger'` OK; CDP siehe AK12 (sichtbar, nicht verdeckt) |
| Randfall 1 „Theme `kern` und ein Feld ohne `label`: kein `__label`-Element“ | bestanden | `KernFelderTest::test_ohne_label_gibt_es_kein_label_element` (input-text, text-area, input-file, select, mit Gegenprobe); Sonde combobox ohne label: kein label |
| Randfall 2 „`select` bzw. `combobox` ohne `options`: kein Fehler, leeres `<select>`“ | bestanden | `KernFelderTest::test_select_ohne_options_…`, `KernTabsComboboxTest::test_combobox_ohne_options_…` |
| Randfall 3 „`tab-body` ohne `active` in einer Gruppe ohne aktiven Reiter: alle Panels `hidden`“ | bestanden | `KernTabsComboboxTest::test_tab_body_ohne_active_…`; `kernTabs.test.mjs` (Ausgangszustand) |
| Randfall 3 „kein Fehler im Skript“ | bestanden | `kernTabs.test.mjs`: `initTabs` wirft nicht, Klick zeigt danach das gewählte Panel |
| Randfall 4 „Ein Wert mit HTML (`<b>`) in `label`, `value` oder Option: escaped ausgegeben wie im Theme `bootstrap5`“ | bestanden | `KernFelderTest::test_html_in_label_value_und_option_wird_escaped` (input-text, select); Sonde: combobox-Label und -Option, text-area-value, input-file-Label escaped |
| Randfall 5 „wie im Theme `bootstrap5` kein `__error`-Span, kein `is-invalid`, kein `aria-invalid`“ | bestanden | `KernFelderTest::test_fehleranzeige_aus_…` mit Gegenprobe (Schalter an → `__error` da); bestehender `FehleranzeigeSchalterTest` grün |
| Randfall 5 „ein übergebenes `aria-describedby` bleibt unverändert“ | bestanden | derselbe Test: `aria-describedby="hilfe"` |
| Vertrag „Der Standardwert der Config bleibt `tailwind`“ | bestanden | `src/Config/laravelkit.php:15` |
| Vertrag „Props, Standardwerte und Verhalten … sind gleich wie im Theme `bootstrap5`“ | teilweise | Props-Vergleich je Template (Sonde): gleich bis auf `card-main`/`card-main-header`/`tab-header` (Klassen-Props ohne Standard, so verlangt), `input-text` (+`addClass`) und `input-euro` (ohne `invalid`, → sollte) |
| Vertrag „Klassen-Props … ohne Standardwert. Ein übergebener Wert hängt an die Wurzel“ | bestanden | Sonde: `add-class` → Wurzel `kit-input-text x`, `addclass` → `kit-checkbox zusatz`, `classheader` → `kit-card__header kopf`; Standard `''`/`null` |
| Nicht-Ziele (Standard-Theme, userauth, Passwort-Schalter, neue Bausteine, alte Themes entfernen) | eingehalten | Diff: keine Änderung an `src/Views/kit/bootstrap5/`, `src/Views/kit/tailwind/`, userauth, Config-Standard; kein radio/toggle/input-group unter `kern/`; Passwortfeld ohne Schalter |
| „Keine neuen npm- oder Composer-Pakete.“ | eingehalten | `package.json`, `composer.json` in Kit und Host unverändert |

## Sicherheit
- Ausgabe-Escaping: Alle Werte über `{{ }}` bzw. `ComponentAttributeBag`. Einzige rohe Ausgabe `{!! $symbol !!}` in `kern/partials/symbol-button.blade.php` gibt nur feste SVG-Pfade aus einem `match` aus, keine Eingaben. Sonde: `variant="a&quot; onclick=…"` landet escaped in `data-variant`.
- JS: `knopf.innerHTML = PFEIL` setzt eine feste Konstante; die Einträge baut `hervorheben()` aus Textknoten.
- Lösch-Bindung: `data-kit-delete` nur an `<button>`, wie bootstrap5; `js/loeschen.js` bindet weiter über das Attribut.
- Route `/ui-kit-blade-kern` ohne Login wie `/ui-kit` und `/ui-kit-vue3` (Entwicklungs-Host, keine Daten). `config(['laravelkit.theme' => 'kern'])` gilt nur für die Anfrage.
- Risiko `normal` passt: Der Diff berührt weder Zahlung, Login/Rechte, Geheimnisse, Löschlogik noch Kundendaten. Er ändert nur Markup und Optik.

## Regressionen
Gelaufen im Review-Platz (je einmal, alle grün):
- `php artisan test --compact packages/laravel-kit/tests/Feature/KitComponents tests/Feature/UiKitBladeKernTest.php` → 2130 passed (neue Tests: KernFelderTest, KernLayoutTest, KernTabsComboboxTest, KernQuelltextTest, dazu alle bestehenden aus plan.md: BausteineTest, ThemeTest, LabelForTest, FormularOldFehlerTest, FehleranzeigeSchalterTest, CheckboxCheckedTest, ComboboxTest, Bootstrap5LabelTest, FormularNeuTest, ReadmeTest)
- `packages/laravel-kit/tests/Feature/Vue3` → 24 passed (bestehend laut plan.md; Vue3*QuelltextTest lesen `tailwind.css`/`bootstrap5.scss`)
- `packages/laravel-kit/tests/Feature/LogMigrationOptionalTest.php` → 6 passed (nennt `laravelkit.php`)
- Host `tests/Feature/KitVue3CssTest.php` → 2 passed; `tests/Feature/HostGetRoutenTest.php` → 6 passed (`routes/web.php`, `app.js` geändert)
- `node --test packages/laravel-kit/tests/js/*.test.mjs` → 249/249 (u. a. tabs, combobox, comboboxAktualisieren, loeschen, kernTabs, kernCombobox)
- `npx vitest run` im Kit → 160/160 (`kit.css` ändert das Combobox-Raster auch für den Vue-Kern)
- Browser: `/ui-kit`, `/ui-kit-vue3`, `/` je 0 Konsolenfehler (Host-`app.js` startet `initTabs`/`initCombobox` jetzt auf jeder Seite); Vue-Combobox auf `/ui-kit-vue3` vermessen: Trigger rechts im Feld, Liste öffnet direkt unter dem Feld.
- Ganze Suite: nicht gelaufen (kein `SUITE: ja`).
- Umgebung, kein Fund des Features: `storage/logs/laravel.log` des Platzes loggt je Anfrage `telescope_entries doesn't exist` (seit 2026-11-17, vor diesem Review) → Task #1618.

## Funde: 0 muss, 1 sollte, 2 kann
- sollte: `src/Views/kit/kern/input-euro.blade.php:2` ohne Prop `invalid` (bootstrap5 hat `'invalid' => false`). Ein übergebenes `invalid="true"` landet im Theme kern als HTML-Attribut am `input.kit-input-euro__input`. Das verletzt den Vertrag „Props, Standardwerte … gleich wie im Theme bootstrap5“, kein AK (→ Folge-Task #1617).
- kann: `kern/input-text` hat zusätzlich den Prop `addClass` (bootstrap5 nicht). Das passt zu „Klassen-Props“, weicht aber von „Props gleich“ ab. Dazu ist der Spec-Satz „hängt an die Wurzel wie das Attribut `class`“ mehrdeutig: In den Feldern geht `class` ans Eingabefeld (wie Vue `inheritAttrs: false`), die Klassen-Props gehen an die Wurzel. Die Spec sollte das klarstellen.
- kann: `kernTabs.test.mjs`/`kernCombobox.test.mjs` bauen das Markup von Hand nach, statt die gerenderte Blade-Ausgabe zu nutzen. Die Kopplung Template ↔ Skript sichern nur die Attributnamen in `KernTabsComboboxTest`. Im Browser ist sie bestätigt.

## Neue Tasks: #1617 laravel-kit Theme kern – Nacharbeit aus Review blade-kern; #1618 Host packages_dev/Telescope – Migration telescope_entries fehlt, frische DB loggt bei jeder Anfrage SQL-Fehler

## Fehlerprotokoll
- {"schwere":"sollte","ak":"","fund":"kern/input-euro ohne Prop invalid; übergebenes invalid erscheint als HTML-Attribut am Eingabefeld, Vertrag verlangt gleiche Props wie bootstrap5","ursache":"Template neu geschrieben statt die @props-Zeile des Vorbilds zu übernehmen; kein Test vergleicht die Props-Listen beider Themes","anweisung":"Beim Nachbau eines Templates für ein neues Theme die @props-Zeile des Vorbild-Themes übernehmen und jede Abweichung als Annahme melden; bei der Vertragsaussage 'Props gleich' einen Vergleichstest der @props je Template verlangen"}

## Fazit: freigegeben
