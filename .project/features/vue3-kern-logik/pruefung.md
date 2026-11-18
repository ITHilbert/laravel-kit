# Prüfung: vue3-kern-logik — 2026-11-18

Geprüft im Review-Platz `/home/joerg/projekte/.worktrees/packages_dev/reviewer`: Kit `e855b3cc..77e3ba03` (6 Commits), Host `main..3dbe4dd` (2 Commits). Browser: eigene Headless-Chrome-Instanz (Port 0, Profil im Scratchpad) per CDP mit echten Maus- und Tastaturereignissen gegen `http://127.0.0.1:8104/ui-kit-vue3` (Server des Platzes).

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „Dann gibt es `.kit-combobox`, darin `.kit-combobox__input` mit `role="combobox"` und `aria-expanded="false"`“ | bestanden | Vitest `combobox.test.ts` AK1 grün; Chrome: role=combobox, aria-expanded=false |
| AK1 „und `.kit-combobox__trigger`“ | bestanden | Vitest AK1; Chrome: Trigger vorhanden |
| AK1 „die Liste ist nicht sichtbar“ | bestanden | Vitest AK1 (`sichtbar()` über computed style); Chrome: `.kit-combobox__list` nicht im DOM |
| AK2 „Dann ist `.kit-combobox__list` (`role="listbox"`) sichtbar“ | bestanden | Chrome nach echtem Mausklick: role=listbox, display flex, 835×137 px |
| AK2 „`.kit-combobox` trägt `is-open`“ | bestanden | Vitest AK2; Chrome: is-open=true |
| AK2 „das Eingabefeld hat `aria-expanded="true"`“ | bestanden | Vitest AK2; Chrome: aria-expanded=true |
| AK2 „es gibt drei `.kit-combobox__item` (`role="option"`) mit den Texten der Labels“ | bestanden | Vitest AK2 (drei Optionen, Texte, role=option); Chrome mit vier Optionen der Prüfseite: alle role=option, Texte = Labels |
| AK3 „Wenn „st“ … getippt wird, Dann bleibt genau ein `.kit-combobox__item` („Österreich“) übrig“ | bestanden | Vitest AK3; Chrome mit echten Tastenereignissen: „st“ → [„Österreich“] |
| AK3 „wenn „xyz“ getippt wird, Dann gibt es kein `.kit-combobox__item`“ | bestanden | Vitest AK3; Chrome: 0 Items |
| AK3 „und ein sichtbares `.kit-combobox__empty`“ | bestanden | Vitest AK3; Chrome: display block, 833×32 px |
| AK4 „Wenn Pfeil-runter und Enter gedrückt werden … Dann ist die Liste geschlossen“ | bestanden | Vitest AK4 (Tastatur); Chrome: ArrowDown+Enter → is-open=false |
| AK4 „oder „Schweiz“ angeklickt wird, Dann ist die Liste geschlossen“ | bestanden | Vitest AK4 (Klick); Chrome: Mausklick → Liste nicht mehr im DOM |
| AK4 „das Eingabefeld zeigt das Label der Auswahl“ | bestanden | Chrome: Tastatur → „Österreich“, Klick → „Schweiz“; Vitest beide Wege |
| AK4 „das gewählte `.kit-combobox__item` trägt beim nächsten Öffnen `is-active`“ | bestanden | Vitest AK4; Chrome: nach Wiederöffnen genau [„Schweiz“] bzw. [„Österreich“] mit is-active |
| AK4 „`update:modelValue` meldet den `value` der Auswahl“ | bestanden | Vitest AK4 (Protokoll `gemeldet` enthält 'ch' bzw. genau einen value) |
| AK4 „ein Formular um die Combobox sendet unter `land` diesen `value`“ | bestanden | Vitest AK4 (FormData); Chrome: FormData `land=at` (Tastatur), `land=ch` (Klick) |
| AK5 „mit `land = 'at'` … Dann zeigt das Eingabefeld „Österreich““ | bestanden | Vitest AK5 grün |
| AK5 „wenn `land` von außen auf `'ch'` gesetzt wird, Dann zeigt es „Schweiz““ | bestanden | Vitest AK5 grün |
| AK6 „Dann gibt es `.kit-search`, `.kit-search__icon` (`aria-hidden="true"`)“ | bestanden | Vitest AK6; Chrome: icon aria-hidden=true |
| AK6 „und `.kit-search__input` mit `type="search"` und `name="q"`“ | bestanden | Vitest AK6; Chrome: type=search, name=q |
| AK6 „aber kein `.kit-search__clear`“ | bestanden | Vitest AK6; Chrome: kein Clear-Knopf |
| AK7 „meldet sie `update:modelValue` sofort“ | bestanden | Vitest AK7 (vor jedem Timer-Vorlauf) |
| AK7 „und das Ereignis `suche` mit „abc“ genau einmal 300 ms nach dem letzten Tastendruck“ | bestanden | Vitest AK7 mit Fake-Timern: 299 ms leer, 300 ms ['abc'], +2000 ms weiter ['abc']; Chrome: je Tastendruck ein 300-ms-Timer |
| AK7 „`.kit-search__clear` (Schaltfläche mit `aria-label="Suche leeren"`) ist sichtbar“ | bestanden | Vitest AK7; Chrome: aria-label „Suche leeren“ |
| AK8 „Wenn `.kit-search__clear` angeklickt … Dann ist der Wert leer“ | bestanden | Vitest AK8 (Klick); Chrome: Mausklick → Wert „“ |
| AK8 „oder im Eingabefeld Escape gedrückt wird, Dann ist der Wert leer“ | bestanden | Vitest AK8 (Escape); Chrome: echte Escape-Taste → Wert „“, kein zusätzlicher 300-ms-Timer durch das native Leeren von `type=search` |
| AK8 „`suche` meldet sofort „“ (ohne Verzögerung)“ | bestanden | Vitest AK8: `suchen` = [''] ohne Timer-Vorlauf, beide Wege |
| AK8 „der Fokus liegt im Eingabefeld“ | bestanden | Vitest AK8; Chrome: `document.activeElement` = Eingabefeld nach Klick und nach Escape |
| AK8 „und `.kit-search__clear` ist weg“ | bestanden | Vitest AK8; Chrome: Knopf nicht mehr im DOM |
| AK9 „`.kit-input-token__input` mit `type="password"`, `name="api_token"` und dem Wert „geheim123““ | bestanden | Vitest AK9; Chrome (Prüfseite, eigener Wert): type=password, name, Wert |
| AK9 „`.kit-input-token__toggle` (Schaltfläche, `aria-pressed="false"`, `aria-label="Token anzeigen"`)“ | bestanden | Vitest AK9; Chrome: BUTTON, false, „Token anzeigen“ |
| AK9 „und `.kit-input-token__copy` (Schaltfläche, `aria-label="Token kopieren"`)“ | bestanden | Vitest AK9; Chrome: BUTTON, „Token kopieren“ |
| AK10 „Dann hat das Eingabefeld `type="text"`“ | bestanden | Vitest AK10; Chrome Mausklick: text |
| AK10 „der Schalter `aria-pressed="true"` und `aria-label="Token verbergen"` und trägt `is-active`“ | bestanden | Vitest AK10; Chrome: true, „Token verbergen“, is-active |
| AK10 „ein zweiter Klick stellt AK9 wieder her“ | bestanden | Vitest AK10; Chrome: password, false, „Token anzeigen“, ohne is-active |
| AK11 „Dann steht „geheim123“ in der Zwischenablage (`navigator.clipboard.writeText`)“ | bestanden | Vitest AK11 (writeText mit Wert); Chrome mit Zwischenablage-Recht: `navigator.clipboard.readText()` = Wert des Felds |
| AK11 „`.kit-input-token__copy` trägt `is-active`“ | bestanden | Vitest AK11; Chrome |
| AK11 „und `.kit-input-token__status` (`role="status"`) zeigt „Kopiert““ | bestanden | Vitest AK11; Chrome: role=status, „Kopiert“ |
| AK11 „nach 2000 ms sind `is-active` und der Text wieder weg“ | bestanden | Vitest AK11 (1999 ms noch aktiv, 2000 ms weg); Chrome nach 2,1 s: beides weg |
| AK12 „`.kit-input-list__input` mit `name="stadt"` und einem `list`-Attribut, das auf die `id` von `.kit-input-list__datalist` (`<datalist>`) zeigt“ | bestanden | Vitest AK12; Chrome: list = id, Tag DATALIST |
| AK12 „und die Datalist hat zwei `<option>`“ | bestanden | Vitest AK12 (zwei); Chrome mit vier Optionen der Prüfseite: 4 |
| AK12 „zwei Listen mit gleichem `name` auf einer Seite haben verschiedene Datalist-`id`s“ | nicht bestanden | Nur innerhalb einer Vue-App erfüllt (Vitest, Host-Prüfseite: `kit-input-list-v-13`/`-v-14`). Als Inseln über `mountInseln(kitKomponenten)` (Einbindung laut AK17) bekommen beide `kit-input-list-v-0`: Sonde (Vitest, eigene Konfiguration im Scratchpad) mit zwei `<div data-vue="kit-input-list" data-props='{"name":"stadt",…}'>` → `IDS ["kit-input-list-v-0","kit-input-list-v-0"]`. Ursache: `useId()` zählt je App, jede Insel ist eine eigene App (`js/mount-inseln.js:54`). Das zweite Feld zeigt so die Vorschläge der ersten Liste. |
| AK13 „mit `invalid` … trägt die Wurzel `is-invalid` und das Eingabefeld `aria-invalid="true"`“ | bestanden | Vitest AK13 je Komponente (mit Gegenprobe); Chrome: alle vier Wurzeln is-invalid, aria-invalid=true, Rahmen rot rgb(220,53,69) |
| AK13 „mit `disabled` trägt die Wurzel `disabled`, das Eingabefeld ist `disabled`“ | bestanden | Vitest AK13 je Komponente |
| AK13 „und Klicks auf Trigger, Schalter oder Kopieren ändern nichts“ | bestanden | Vitest AK13: Combobox-Trigger öffnet nicht, Token-Schalter/Kopieren ohne Wirkung (writeText nicht gerufen), Suche-Leeren ohne Wirkung; Code: Schaltflächen `disabled` und Abbruch bei `props.disabled` |
| AK14 „stehen `id` und `required` am Eingabefeld und nicht an der Wurzel“ | bestanden | Vitest AK14 je Komponente; Chrome: `aria-label` der Prüfseite am Eingabefeld, Wurzeln nur mit class (Combobox zusätzlich `dir`/`style` von Reka) |
| AK15 „gilt AK10 aus `vue3-kern-grundlage` auch für die neuen Dateien“ | bestanden | `Vue3QuelltextTest` (alle Dateien unter `vue3/components/` und `kit.css`) und `Vue3LogikQuelltextTest` grün, je mit Gegenprobe des Wächters; Diff gelesen: nur `kit-…` und Zustände |
| AK16 „enthält jede eine Regel für jede Hauptklasse und jeden Teil aus dem Vertrag“ | bestanden | `Vue3LogikQuelltextTest::test_beide_themes_…` grün; Sonde Tailwind v4 (`@tailwindcss/node` 4.2.1, `@import "tailwindcss"` + `kit.css` + Theme) kompiliert fehlerfrei; Bootstrap-Theme kompiliert im Host-Build (Bootstrap 5.2.3) fehlerfrei |
| AK16 „und für `is-invalid` an den vier Wurzeln“ | bestanden | Test (s. o.); Chrome: roter Rahmen an allen vier |
| AK17 „mountet die Insel ohne Konsolenfehler“ | bestanden | Vitest `inseln-logik.test.ts` (echtes `mountInseln`, Spies auf console.error/warn) grün |
| AK17 „nach einer Auswahl sendet das Formular unter `land` den `value`“ | bestanden | Vitest `inseln-logik.test.ts`: FormData `land=ch` |
| AK18 „Wenn `npm run production` läuft, Dann baut er ohne Fehler“ | bestanden | Build in einer Kopie des Platzes (Scratchpad, node_modules des Platzes verlinkt): „webpack compiled successfully“; erzeugtes `kit-vue3.css` byte-gleich mit dem eingecheckten |
| AK18 „die Prüfseite `/ui-kit-vue3` zeigt zusätzlich Combobox, Suche, Token-Eingabe und Liste mit dem Bootstrap-5-Theme“ | bestanden | Chrome: alle vier je zweimal gerendert, Theme greift (Rahmen, Hintergrund weiß, is-invalid rot); Screenshot geprüft |
| AK18 „ohne Konsolenfehler“ | bestanden | `browser-lauf` → 0 Konsolenfehler; CDP-Sonde: keine Exception, kein console.error/warning über alle Schritte |
| AK18 „die Combobox öffnet per Klick eine sichtbare Liste über dem folgenden Inhalt“ | bestanden | Chrome: Liste bg weiß, unteres Item endet bei y=559 unter dem Absatz-Beginn y=495, `elementFromPoint` auf dem letzten Item liefert das Item |
| AK19 „nennt die README im Abschnitt „Vue 3“ die vier Komponenten mit Tag-Name, Props, Ereignissen und Klassen“ | bestanden | `Vue3LogikQuelltextTest` README grün; README gelesen: Tabelle mit allen vier |
| AK19 „und der CHANGELOG nennt sie unter „Unveröffentlicht““ | bestanden | Test grün; CHANGELOG gelesen |
| Randfall 1 „Combobox ohne `options` oder mit leerer Liste: kein Fehler“ | bestanden | Vitest Randfall 1 (console.error-Spy) |
| Randfall 1 „geöffnet zeigt sie `.kit-combobox__empty`“ | bestanden | Vitest Randfall 1 (beide Varianten) |
| Randfall 2 „Combobox mit `modelValue`, der in keiner Option vorkommt: Eingabefeld leer, kein Fehler“ | bestanden | Vitest Randfall 2 |
| Randfall 3 „Das Item trägt `disabled`“ | bestanden | Vitest Randfall 3 |
| Randfall 3 „und lässt sich weder per Klick noch per Enter wählen“ | bestanden | Vitest Randfall 3 (Klick und ArrowDown+Enter, Gegenprobe mit aktiver Option) |
| Randfall 4 „Kopieren nutzt den Weg über ein unsichtbares `<textarea>` und `document.execCommand('copy')`“ | bestanden | Vitest Randfall 4 prüft nur `execCommand('copy')`; selbst in Chrome bestätigt: `navigator.clipboard` entfernt → genau ein `<textarea>` (`position: fixed; opacity: 0; pointer-events: none`), danach wieder entfernt, Status „Kopiert“ |
| Randfall 4 „schlägt beides fehl, zeigt `__status` „Kopieren fehlgeschlagen““ | bestanden | Vitest Randfall 4; Chrome (execCommand → false) |
| Randfall 4 „und `__copy` trägt kein `is-active`“ | bestanden | Vitest Randfall 4; Chrome |
| Randfall 5 „Ein neuer Tastendruck innerhalb der Verzögerung verschiebt das Ereignis `suche`“ | bestanden | Vitest Randfall 5 (Fake-Timer, 400/200/300 ms) |
| Randfall 5 „nach dem Aushängen der Komponente kommt kein verspätetes `suche`“ | bestanden | Vitest Randfall 5 (v-if aus, +2000 ms, Gegenprobe) |
| Nicht-Ziele (Mehrfachauswahl, Nachladen, Gruppen, DataTable, Editor, einfache Felder, Blade, Release) | bestanden | Diff enthält nichts davon; keine Versionsnummer |
| Vertrag „Keine neuen npm-Pakete“ | bestanden | `package.json` des Kits und des Hosts im Diff unverändert |

## Sicherheit
- Ausgabe-Escaping: alle Labels und Werte über Vue-Interpolation bzw. Property-Bindung, kein `v-html`.
- Token-Eingabe: Der Wert liegt als DOM-Property am Passwortfeld; das Ersatz-`<textarea>` wird im `finally` entfernt (Chrome: 0 Reste). Kein Logging, kein Netzwerkverkehr (`browser-lauf`: keine XHR/fetch).
- Risiko-Stufe: Die Token-Eingabe zeigt und kopiert Zugangsdaten, speichert, überträgt oder prüft aber keine — reine Darstellungskomponente ohne Server- oder Rechtebezug. Deshalb kein Fund „Risiko kritisch nötig“; Abwägung hier offen genannt.
- Keine Routen, Controller, Migrationen, Rechte oder Mass-Assignment im Diff (Host: nur View und gebaute Assets).

## Regressionen
- Kit Vitest, alle 9 Dateien (`tests/vue3/combobox`, `search`, `input-token`, `input-list`, `inseln-logik`, `dialog`, `tabs`, `accordion`, `inseln`): 52/52 grün.
- Kit `node --test tests/js/*.test.mjs` (u. a. `mount-inseln.test.mjs`): 240/240 grün.
- PHP über den Host: `tests/Feature/HostGetRoutenTest.php`, `tests/Feature/KitVue3CssTest.php` (Ergebnis von `tests-fuer --geaendert`), `packages/laravel-kit/tests/Feature/Vue3/` (`Vue3QuelltextTest`, `Vue3LogikQuelltextTest`): 25 passed.
- Ganze Suite nicht gelaufen (kein `SUITE: ja`).
- Umgebung des Platzes, kein Fund: `storage/logs/laravel.log` zeigt je Seitenaufruf `telescope_entries doesn't exist` (DB `packages_dev_review` ohne Telescope-Tabelle); die Seite antwortet 200.

## Funde: 1 muss, 1 sollte, 3 kann
- muss: AK12 „zwei Listen mit gleichem `name` auf einer Seite haben verschiedene Datalist-`id`s“ gilt nicht für Inseln. `input-list.vue:19` bildet die id mit `useId()`, das je Vue-App ab 0 zählt; `mountInseln` erzeugt je Insel eine eigene App. Zwei Listen-Inseln → beide `kit-input-list-v-0`, das zweite Feld zeigt die Vorschläge der ersten Datalist. Der Test `input-list.test.ts` prüft nur zwei Listen in einer App. Beleg: Sonde oben.
- sollte: `useId` gibt es erst ab Vue 3.5, das Kit erklärt `vue ^3.4` als peerDependency (`package.json`, README) → `kit-input-list` wirft unter Vue 3.4 (→ Folge-Task #1610; erledigt sich, wenn der AK12-Fix ohne `useId` auskommt).
- kann: Escape in einer schon leeren Suche meldet erneut `suche` mit „“ (`search.vue`, `leeren()` ohne Prüfung auf leeren Wert).
- kann: Der Test zu Randfall 4 prüft das unsichtbare `<textarea>` nicht, nur `execCommand('copy')` (im Review per Chrome bestätigt).
- kann: Die neuen Theme-Regeln `.kit-combobox { display: flex }` treffen auch die Wurzel der alten `js/combobox.js` (`span.kit-combobox`), wenn ein Projekt beides lädt — Übergang bis zum Rückbau #1546.

## Neue Tasks: #1610 laravel-kit vue3 – Nacharbeit aus Review vue3-kern-logik

## Fehlerprotokoll
- {"schwere":"muss","ak":"AK12","fund":"Zwei kit-input-list als Inseln (mountInseln) erhalten dieselbe Datalist-id kit-input-list-v-0","ursache":"id per useId() erzeugt, das je Vue-App zählt; Test prüft zwei Listen nur innerhalb einer App, nicht im dokumentierten Insel-Betrieb","anweisung":"Seitenweit eindeutige ids nie mit useId() bilden, wenn Komponenten als getrennte Apps (Inseln) gemountet werden; Eindeutigkeit 'auf einer Seite' auch mit zwei getrennt gemounteten Instanzen testen"}
- {"schwere":"sollte","ak":"","fund":"input-list.vue nutzt useId (ab Vue 3.5), das Kit erklärt vue ^3.4 als peerDependency","ursache":"Neue Vue-API ohne Abgleich mit der erklärten Mindestversion verwendet","anweisung":"Vor jeder Vue-API die Mindestversion aus peerDependencies prüfen; neuere APIs nur mit angehobener peerDependency und README-Eintrag nutzen"}

## Fazit: nicht freigegeben
