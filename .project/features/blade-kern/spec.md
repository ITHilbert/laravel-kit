# Spec blade-kern
Risiko: normal
Art: Feature — letzter Schritt aus Entscheidung 0013 (Host `packages_dev`, `.project/doku/decisions/0013-laravel-kit-vue3-kern-ohne-framework-optik.md`, Punkt 5 und 6): Blade-Components ohne Framework-Optik. Kein Dashboard-Task.
Status: freigegeben (Moderator in Vollmacht; Joerg, Chat 2026-11-18: „die Komponenten zu Ende umbauen ohne weitere Rückfragen“). AK18–AK21, Randfälle 6–7: Spec-Lücke #1715 (Joerg, Chat 2026-11-19: alte Komponenten klar als auslaufend kennzeichnen, Laufzeit-Hinweis „ja“) — freigegeben (Joerg, Chat 2026-11-19: „2. ja“).
Rückbau: entfällt (kein Übergangsstand; der Rückbau der alten Themes ist #1546)

## Ziel
Die Blade-Components `<x-kit::…>` bekommen ein drittes Theme `kern`. Es erzeugt dasselbe Markup und dieselben `kit-`-Klassen wie der Vue-3-Kern. Die Optik kommt aus denselben Dateien `vue3/kit.css` und `vue3/themes/*` wie beim Vue-Kern. Die Themes `bootstrap5` und `tailwind` der Blade-Components werden eingefroren.

## Vertrag
- **Theme-Wert:** `KitTheme` bekommt den Fall `Kern = 'kern'`. `laravelkit.theme = kern` wählt die Templates unter `src/Views/kit/kern/`. Der Standardwert der Config bleibt `tailwind`.
- **Templates:** Für jedes der 34 Templates aus `src/Views/kit/bootstrap5/` gibt es eine gleichnamige Datei unter `src/Views/kit/kern/`. Props, Standardwerte und Verhalten (Name, `id`, `old()`, Fehleranzeige, `aria-*`, gewählte Option, `checked`) sind gleich wie im Theme `bootstrap5`. Nur Markup und Klassen ändern sich.
- **Klassen** (Regel aus 0013): Jedes gerenderte Element trägt höchstens eine Klasse `kit-<name>` bzw. `kit-<name>__<teil>`, dazu nur die Zustände `is-invalid`, `is-active`, `is-open`, `disabled`. Keine Bootstrap-Klasse, keine Tailwind-Utility, keine Icon-Schrift (`fa-…`). Symbole als inline-SVG mit `aria-hidden="true"`.
- **Zuordnung Baustein → Klassen:**
  | Baustein(e) | Wurzel | Teile |
  |---|---|---|
  | `input-text`, `input-email`, `input-password`, `input-date`, `input-number` | `kit-<baustein>` | `__label`, `__input`, `__error` |
  | `input-euro` | `kit-input-euro` | `__label`, `__input`, `__suffix` („€“), `__error` |
  | `input-file`, `input-file-img` | `kit-input-file` | `__label`, `__input`, `__error` |
  | `text-area` | `kit-textarea` | `__label`, `__input`, `__error` |
  | `select` | `kit-select` | `__label`, `__input`, `__error` |
  | `checkbox` | `kit-checkbox` | `__input`, `__label`, `__error` |
  | `combobox` | `kit-combobox` | `__label`, `__select` (verstecktes `<select>`), `__error`; das Skript ergänzt `__input`, `__trigger`, `__list`, `__item` |
  | `label` | `kit-label` | — |
  | `input-hidden` | keine Klasse | — |
  | `button`, `button-back`, `button-cancel`, `button-create`, `button-delete`, `button-edit`, `button-save`, `button-show`, `button-submit` | `kit-button` mit `data-variant="<variant>"` (`button`: Prop `variant`, sonst der Name ohne `button-`) | `__icon` (SVG) |
  | `card-main`, `card-main-header`, `card-body`, `card-footer` | `kit-card` | `kit-card__header`, `kit-card__body`, `kit-card__footer` (je eigenes Element) |
  | `tabs-header`, `tab-header`, `tabs-body`, `tab-body` | — | `kit-tabs__list`, `kit-tabs__tab`, `kit-tabs__body`, `kit-tabs__panel` |
  | `show-euro`, `show-date` | `kit-show-euro`, `kit-show-date` | — |
  | `breadcrumb` | `kit-breadcrumb` | `__list` |
- **Markup der Felder:** wie die Vue-Kern-Felder (`vue3/components/feld/text-feld.vue`): Wurzel `<div class="kit-<feld>">`, darin `<label class="kit-<feld>__label" for="<id>">`, das Eingabefeld `kit-<feld>__input` und `<span class="kit-<feld>__error" id="…">`. Ein Fehler setzt `is-invalid` an die Wurzel und `aria-invalid="true"` sowie `aria-describedby` an das Eingabefeld.
- **Klassen-Props** (`addClass`, `addclass`, `classcard`, `classheader`): ohne Standardwert. Ein übergebener Wert hängt an die Wurzel wie das Attribut `class`.
- **Skripte:** `js/tabs.js` und `js/combobox.js` binden die Kern-Templates über `data-`-Attribute. Für die alten Themes ändert sich ihre Ausgabe nicht.
- **Optik:** Neue Regeln für `kit-button`, `kit-card`, `kit-tabs__body`, `kit-breadcrumb`, `kit-show-euro`, `kit-show-date` stehen in `vue3/themes/bootstrap5.scss` und `vue3/themes/tailwind.css`. Funktion (z. B. ausgeblendetes Panel) steht in `vue3/kit.css`.
- **Keine neuen npm- oder Composer-Pakete.**

## Akzeptanzkriterien
- **AK1** Gegeben `laravelkit.theme = kern`, Wenn ein beliebiger der 34 Bausteine rendert, Dann kommt das Markup aus `src/Views/kit/kern/` ohne Exception, und `KitTheme::erlaubteWerte()` enthält `kern`.
- **AK2** Gegeben Theme `kern` und `<x-kit::input-text name="vorname" label="Vorname" />`, Wenn er rendert, Dann gibt es `div.kit-input-text`, darin `label.kit-input-text__label` mit `for="vorname"` und `input.kit-input-text__input` mit `id="vorname"`, `name="vorname"`, `type="text"`; dasselbe gilt mit den Klassen aus der Tabelle für `input-email`, `input-password`, `input-date`, `input-number`, `input-euro`, `text-area` und `select`.
- **AK3** Gegeben Theme `kern`, `laravelkit.fehleranzeige = true`, ein Validierungsfehler für `vorname` in der Session und `old('vorname') = "Max"`, Wenn `<x-kit::input-text name="vorname" />` rendert, Dann trägt die Wurzel `is-invalid`, das Eingabefeld hat `value="Max"`, `aria-invalid="true"` und `aria-describedby` mit der `id` von `span.kit-input-text__error`, und der Span zeigt die Meldung; ohne Fehler gibt es weder `is-invalid` noch `__error`.
- **AK4** Gegeben Theme `kern`, Wenn `<x-kit::input-euro name="preis" />` rendert, Dann steht nach dem Eingabefeld ein `kit-input-euro__suffix` mit „€“.
- **AK5** Gegeben Theme `kern`, Wenn `<x-kit::checkbox name="aktiv" checked>Aktiv</x-kit::checkbox>` rendert, Dann gibt es `div.kit-checkbox` mit `input.kit-checkbox__input` (`type="checkbox"`, angehakt, `value="1"`) und `label.kit-checkbox__label` „Aktiv“, das auf die `id` des Eingabefelds zeigt; ohne `checked` ist sie nicht angehakt.
- **AK6** Gegeben Theme `kern`, Wenn `<x-kit::input-file-img name="bild" />` rendert, Dann gibt es `div.kit-input-file` mit `input.kit-input-file__input` (`type="file"`, `accept="image/png, image/jpeg"`).
- **AK7** Gegeben Theme `kern`, Wenn `button`, `button-back`, `button-cancel`, `button-create`, `button-delete`, `button-edit`, `button-save`, `button-show` und `button-submit` rendern, Dann trägt jeder die Klasse `kit-button` und `data-variant` laut Tabelle, behält `type`, `href` (Variante `type="link"` bzw. `route`), `title` und die `data-kit-delete`-Bindung des Themes `bootstrap5` (ohne `data-toggle="tooltip"`), und ein Symbol steht als `svg.kit-button__icon` mit `aria-hidden="true"`.
- **AK8** Gegeben Theme `kern`, Wenn `card-main` mit `card-main-header`, `card-body` und `card-footer` rendert, Dann gibt es `div.kit-card` mit `kit-card__header`, `kit-card__body` und `kit-card__footer`; `classcard="extra"` hängt `extra` an `kit-card`.
- **AK9** Gegeben Theme `kern` und `tabs-header` mit zwei `tab-header` (`name="a" active`, `name="b"`) sowie `tabs-body` mit zwei `tab-body` (`a` aktiv, `b`), Wenn sie rendern, Dann gibt es `kit-tabs__list` (`role="tablist"`), zwei `button.kit-tabs__tab` (`role="tab"`, `aria-controls`, `aria-selected`), `kit-tabs__body` und zwei `kit-tabs__panel` (`role="tabpanel"`); Reiter und Panel `a` tragen `is-active`, Panel `b` trägt `hidden`.
- **AK10** Gegeben die Tabs aus AK9 und `js/tabs.js`, Wenn Reiter `b` angeklickt wird, Dann tragen Reiter und Panel `b` `is-active`, Panel `b` hat kein `hidden`, Panel `a` hat `hidden`, und `aria-selected` stimmt; die Ausgabe von `js/tabs.js` für die Themes `bootstrap5` und `tailwind` bleibt unverändert.
- **AK11** Gegeben Theme `kern` und `<x-kit::combobox name="land" label="Land" :options="['de' => 'Deutschland', 'at' => 'Österreich']" value="at" />`, Wenn sie rendert, Dann gibt es `div.kit-combobox`, `label.kit-combobox__label` und ein `select.kit-combobox__select` mit `name="land"`, `data-kit-combobox` und gewählter Option `at`.
- **AK12** Gegeben die Combobox aus AK11 und `js/combobox.js` (`initCombobox`), Wenn das Skript bindet, Dann ergänzt es in `div.kit-combobox` die Teile `kit-combobox__input` (`role="combobox"`, Wert „Österreich“), `kit-combobox__trigger` und `kit-combobox__list` (`role="listbox"`, versteckt); geöffnet trägt `kit-combobox` `is-open`, jeder Eintrag ist `kit-combobox__item`, der markierte trägt `is-active`, und eine Auswahl setzt den Wert des `<select>`. Für `select.selectcb` der alten Themes bleibt die Ausgabe unverändert.
- **AK13** Gegeben Theme `kern`, Wenn `show-euro value="1234.5"`, `show-date value="2026-10-31"` und `breadcrumb` rendern, Dann gibt es `kit-show-euro` mit dem Text aus `KitAnzeige::euro`, `kit-show-date` mit dem Text aus dem Theme `bootstrap5` und `nav.kit-breadcrumb` (`aria-label="breadcrumb"`) mit `ol.kit-breadcrumb__list`.
- **AK14** Gegeben alle Dateien unter `src/Views/kit/kern/`, Wenn ihr Quelltext gelesen wird, Dann enthält kein `class`-Wert eine Klasse ohne Vorsilbe `kit-` außer den Zuständen, und es gibt keine Icon-Schrift (`fa-`, `bi-`).
- **AK15** Gegeben `vue3/themes/bootstrap5.scss` und `vue3/themes/tailwind.css`, Wenn sie gelesen werden, Dann enthält jede eine Regel für `kit-button`, `kit-card`, `kit-card__header`, `kit-card__body`, `kit-card__footer`, `kit-tabs__body`, `kit-breadcrumb`, `kit-breadcrumb__list`, `kit-show-euro`, `kit-show-date` und für `kit-button` je `data-variant` aus AK7; `bootstrap5.scss` nutzt nur Variablen aus Bootstrap 5.2.
- **AK16** Gegeben README und CHANGELOG des Kits, Wenn gelesen wird, Dann nennt die README im Abschnitt „Theme-Schalter“ den Wert `kern` mit den CSS-Dateien, die ihn stylen, und sagt bei `bootstrap5` und `tailwind` „eingefroren, wird nicht mehr gepflegt (Entscheidung 0013)“; der CHANGELOG nennt das Theme unter „Unveröffentlicht“.
- **AK17** Gegeben der Host `packages_dev`, Wenn `npm run production` läuft, Dann baut er ohne Fehler, und die neue Prüfseite `/ui-kit-blade-kern` zeigt alle 34 Bausteine im Theme `kern` mit dem Bootstrap-5-Theme ohne Konsolenfehler; Tabs schalten per Klick, die Combobox öffnet eine sichtbare Liste.
- **AK18** (#1715, Spec-Lücke 2026-11-19) Gegeben `laravelkit.theme = bootstrap5` oder `tailwind` (auch `tailwind` als Standard ohne `LARAVELKIT_THEME`), Wenn in einem Request zwei Kit-Components rendern, Dann löst das Kit genau ein `E_USER_DEPRECATED` aus; die Meldung beginnt mit `laravel-kit:`, nennt das Theme, „läuft aus“ und `LARAVELKIT_THEME=kern`. Gegeben `laravelkit.theme = kern`, Wenn Kit-Components rendern, Dann gibt es kein `E_USER_DEPRECATED` des Kits.
- **AK19** (#1715) Gegeben `vue/vueapp_TW.js`, `vue/vueapp_B5.js` und `vue/vueapp_B4.js`, Wenn eine davon geladen wird und `process.env.NODE_ENV` nicht `production` ist, Dann gibt sie genau einmal `console.warn` mit einer Meldung aus, die mit `laravel-kit:` beginnt und `vue3/` und `mountInseln` nennt; mit `NODE_ENV = production` gibt sie keine Warnung aus.
- **AK20** (#1715) Gegeben die README des Kits, Wenn sie gelesen wird, Dann steht vor dem Abschnitt „Installation“ ein Abschnitt „Auslaufend“. Er nennt `vue/bootstrap4`, `vue/bootstrap5`, `vue/tailwind`, die Blade-Themes `bootstrap5` und `tailwind` und `vue/vueapp_TW.js`, `vueapp_B5.js`, `vueapp_B4.js` je mit Ersatz (`vue3/`, Theme `kern`, `mountInseln`). Er sagt, dass neue Seiten sie nicht mehr verwenden und dass eine neue Major-Version sie entfernt; er nennt den Laufzeit-Hinweis aus AK18 und den Kanal `LOG_DEPRECATIONS_CHANNEL`.
- **AK21** (#1715) Gegeben der CHANGELOG des Kits, Wenn er gelesen wird, Dann gibt es unter „Unveröffentlicht“ einen Abschnitt „Deprecated“ mit denselben alten Teilen wie AK20.

## Randfälle
1. Theme `kern` und ein Feld ohne `label`: kein `__label`-Element.
2. `select` bzw. `combobox` ohne `options`: kein Fehler, leeres `<select>`.
3. `tab-body` ohne `active` in einer Gruppe ohne aktiven Reiter: alle Panels `hidden`, kein Fehler im Skript.
4. Ein Wert mit HTML (`<b>`) in `label`, `value` oder Option: escaped ausgegeben wie im Theme `bootstrap5`.
5. `laravelkit.fehleranzeige` aus (Standard, `FehleranzeigeSchalterTest`): wie im Theme `bootstrap5` kein `__error`-Span, kein `is-invalid`, kein `aria-invalid`, ein übergebenes `aria-describedby` bleibt unverändert.
6. (#1715) Ein Request ohne Kit-Component: kein `E_USER_DEPRECATED` des Kits. Ein ungültiger Theme-Wert wirft weiter die bisherige Exception, ohne vorherige Meldung.
7. (#1715) Laravel leitet `E_USER_DEPRECATED` an den Kanal `deprecations` weiter (ab Werk `null`); das Kit schreibt nicht selbst in ein Log und ändert `LOG_DEPRECATIONS_CHANNEL` nicht.

## Nicht-Ziele
- Standard-Theme ändern, `userauth` oder andere Projekte umstellen, Release-Version.
- Schalter „anzeigen“ am Passwortfeld in Blade (nur Vue-Kern).
- Neue Bausteine, die es in `bootstrap5` nicht gibt (`radio`, `toggle`, `input-group`).
- Alte Themes entfernen (#1546).

## Entscheidungsspielraum
Interne Struktur der Templates (gemeinsame Partials), Form der SVG-Symbole, Name der `data-`-Attribute für die Bindung der Skripte, Aufbau der Theme-Regeln. Keine neuen Pakete.
