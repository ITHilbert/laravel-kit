# Spec vue3-kern-felder
Risiko: normal
Art: Feature — dritter Teil des Vue-3-Kerns aus Entscheidung 0013 (Host `packages_dev`, `.project/doku/decisions/0013-laravel-kit-vue3-kern-ohne-framework-optik.md`, Schritt 3: einfache Felder). Kein Dashboard-Task.
Status: freigegeben (Moderator in Vollmacht; Joerg, Chat 2026-11-18: „die Komponenten zu Ende umbauen ohne weitere Rückfragen“).
Rückbau: entfällt (kein Übergangsstand; der Rückbau der alten Varianten ist #1546)

## Ziel
Der Kern `vue3/` bekommt die einfachen Formularfelder. Sie ersetzen die Felder aus `vue/tailwind/forms/` (`input-*`, `text-area`, `checkbox`, `radiobox`, `toggle`, `hlabel`, `hform`, `input-group*`, Auswahl per `<select>`). Damit ist der Vue-3-Kern aus 0013 Schritt 3 vollständig. Vorbild für Aufbau, Klassenregel, Einstieg und Tests sind `vue3-kern-grundlage` und `vue3-kern-logik`.

## Vertrag
- **Ordner:** `vue3/components/<name>/<name>.vue`, je `<script setup lang="ts">`. Gemeinsame Logik (Label, Fehler, IDs) darf in `vue3/components/feld/` liegen.
- **Komponenten und Tag-Namen** (Wurzelklasse = Tag-Name):
  | Tag | Eingabefeld | Ersetzt |
  |---|---|---|
  | `kit-input-text` | `<input type="text">` | `input-text` |
  | `kit-input-email` | `<input type="email">` | `input-email` |
  | `kit-input-password` | `<input type="password">` mit Schalter „anzeigen“ | `input-password` |
  | `kit-input-number` | `<input type="number">`, Props `step`, `min`, `max` | `input-number` |
  | `kit-input-int` | `<input type="number" step="1" inputmode="numeric">` | `input-int` |
  | `kit-input-euro` | `<input type="number" step="0.01" inputmode="decimal">` + Zusatz „€“ | `input-euro` |
  | `kit-input-percent` | `<input type="number" inputmode="decimal">` + Zusatz „%“ | `input-percent` |
  | `kit-input-date` | `<input type="date">` | `input-date` |
  | `kit-input-color` | `<input type="color">` | `input-color` |
  | `kit-input-file` | `<input type="file">`, Prop `accept`, `multiple` | `input-file`, `input-file-img` |
  | `kit-textarea` | `<textarea>`, Prop `rows` | `text-area` |
  | `kit-select` | `<select>`, Prop `options`, `placeholder` | `combobox` (natives select), `vue-select` |
  | `kit-checkbox` | `<input type="checkbox">`, Label rechts | `checkbox` |
  | `kit-radio` | `<input type="radio">`, Prop `value`, Label rechts | `radiobox` |
  | `kit-toggle` | Schaltfläche `role="switch"` | `toggle` |
  | `kit-label` | `<label>` | `hlabel` |
  | `kit-form` | `<form>` mit CSRF-Feld | `hform` |
  | `kit-input-group`, `kit-input-group-text` | Rahmen bzw. Zusatztext neben einem Feld | `input-group`, `input-group-text` |
- **Teile der Felder** (alle außer `kit-label`, `kit-form`, `kit-input-group*`): `kit-<tag ohne kit->__label`, `__input`, `__error`; dazu `kit-input-password__toggle`, `kit-input-euro__suffix`, `kit-input-percent__suffix`, `kit-toggle__thumb`. Zustände: `is-invalid`, `is-active`, `disabled`.
- **Gemeinsame Props der Felder:** `modelValue` mit `update:modelValue` (`v-model`; bei Zahlenfeldern Zahl oder `null` bei leerem Feld, bei `kit-checkbox` und `kit-toggle` Boolean, bei `kit-radio` der `value` des gewählten Knopfs, bei `kit-input-file` die `FileList`), `name`, `label`, `error` (Text), `invalid` (Boolean), `disabled`. Übrige Attribute (`id`, `required`, `placeholder`, `autocomplete`, `aria-*`, `min`, `max`) landen am Eingabefeld, nicht an der Wurzel.
- **Optionen** (`kit-select`): Liste von `{ value: string | number, label: string, disabled?: boolean }`.
- **Keine Optik im Kern:** wie `vue3-kern-grundlage` AK10. Symbole als inline-SVG mit `aria-hidden="true"`.
- **Keine neuen npm-Pakete.**

## Akzeptanzkriterien
- **AK1** Gegeben `<kit-input-text name="vorname" label="Vorname">`, Wenn die Komponente rendert, Dann gibt es `.kit-input-text`, darin `.kit-input-text__label` (`<label>`) mit dem Text „Vorname“ und `.kit-input-text__input` (`type="text"`, `name="vorname"`); das `for` des Labels zeigt auf die `id` des Eingabefelds, auch ohne `id`-Attribut (dann eine erzeugte, je Instanz eindeutige `id`).
- **AK2** Gegeben ein Feld aus der Tabelle mit `label` und dem Attribut `id="eigen"`, Wenn es rendert, Dann hat das Eingabefeld `id="eigen"` und das Label `for="eigen"`; ohne `label` gibt es kein `__label`.
- **AK3** Gegeben `<kit-input-text v-model="wert">`, Wenn „abc“ getippt wird, Dann ist `wert` „abc“; wenn `wert` von außen auf „xyz“ gesetzt wird, Dann zeigt das Eingabefeld „xyz“. Gleiches gilt für `kit-input-email`, `kit-input-password`, `kit-input-date`, `kit-input-color` und `kit-textarea`.
- **AK4** Gegeben ein Feld mit `error="Pflichtfeld"`, Wenn es rendert, Dann trägt die Wurzel `is-invalid`, das Eingabefeld hat `aria-invalid="true"` und `aria-describedby` mit der `id` von `__error`, und `__error` zeigt „Pflichtfeld“. Mit `invalid` ohne `error` gibt es `is-invalid` und `aria-invalid="true"`, aber kein `__error`. Ohne beides gibt es keins davon.
- **AK5** Gegeben ein Feld mit `disabled`, Wenn es rendert, Dann trägt die Wurzel `disabled` und das Eingabefeld ist `disabled` (bei `kit-toggle` die Schaltfläche); ein Klick auf `kit-toggle`, `kit-checkbox` oder den Schalter von `kit-input-password` ändert den Wert nicht.
- **AK6** Gegeben `<kit-input-number v-model="n" :step="0.5" :min="0">`, `<kit-input-int v-model="i">`, `<kit-input-euro v-model="e">` und `<kit-input-percent v-model="p">`, Wenn „12,5“ bzw. „12.5“ eingegeben wird (Feld `type="number"` liefert „12.5“), Dann ist der Wert die Zahl `12.5` (bei `kit-input-int` mit „12“ die Zahl `12`); wird das Feld geleert, ist der Wert `null`. Das Eingabefeld von `kit-input-euro` hat `step="0.01"` und `inputmode="decimal"`, `kit-input-int` hat `step="1"` und `inputmode="numeric"`.
- **AK7** Gegeben `kit-input-euro` und `kit-input-percent`, Wenn sie rendern, Dann folgt dem Eingabefeld `.kit-input-euro__suffix` mit „€“ bzw. `.kit-input-percent__suffix` mit „%“, jeweils `aria-hidden="true"`, und das Eingabefeld trägt ohne `label` und ohne `aria-label`-Attribut `aria-label="Betrag in Euro"` bzw. `aria-label="Prozent"`.
- **AK8** Gegeben `<kit-input-password name="pw">`, Wenn `.kit-input-password__toggle` (Schaltfläche, `aria-pressed="false"`, `aria-label="Passwort anzeigen"`) angeklickt wird, Dann hat das Eingabefeld `type="text"`, der Schalter `aria-pressed="true"`, `aria-label="Passwort verbergen"` und `is-active`; ein zweiter Klick stellt den Ausgangszustand her.
- **AK9** Gegeben `<kit-select name="land" placeholder="Bitte wählen" :options="[{value:'de',label:'Deutschland'},{value:'at',label:'Österreich',disabled:true}]" v-model="land">`, Wenn sie rendert, Dann hat `.kit-select__input` (`<select>`) drei `<option>`: zuerst „Bitte wählen“ mit leerem Wert, dann die beiden Optionen, „Österreich“ `disabled`; wenn „Deutschland“ gewählt wird, ist `land` `'de'`; wenn `land` von außen `'at'` ist, ist „Österreich“ ausgewählt. Zahlen-Werte kommen als Zahl zurück.
- **AK10** Gegeben `<kit-checkbox name="agb" value="1" v-model="ok">Ich stimme zu</kit-checkbox>`, Wenn sie rendert, Dann gibt es `.kit-checkbox__input` (`type="checkbox"`, `name="agb"`, `value="1"`) und danach `.kit-checkbox__label` mit dem Slot-Text und `for` auf das Eingabefeld; ein Klick auf das Label setzt `ok` auf `true`, ein zweiter auf `false`; ist `ok` von außen `true`, ist das Feld angehakt.
- **AK11** Gegeben zwei `<kit-radio name="art" v-model="art">` mit `value="a"` und `value="b"`, Wenn „b“ angeklickt wird, Dann ist `art` „b“ und nur „b“ angehakt; ist `art` von außen „a“, ist nur „a“ angehakt.
- **AK12** Gegeben `<kit-toggle v-model="an" label="Aktiv" name="aktiv">`, Wenn sie rendert, Dann gibt es `.kit-toggle` mit einer Schaltfläche `role="switch"` (`.kit-toggle__input`, `aria-checked="false"`, `type="button"`), `.kit-toggle__thumb` (`aria-hidden="true"`) und das Label; ein Klick oder Leertaste setzt `an` auf `true`, `aria-checked="true"` und `is-active` an der Wurzel; mit `name` sendet ein Formular `aktiv=1` bzw. `aktiv=0`.
- **AK13** Gegeben `<kit-input-file name="bild" accept="image/*" multiple>`, Wenn Dateien gewählt werden, Dann meldet `update:modelValue` die `FileList`; das Eingabefeld hat `accept` und `multiple`.
- **AK14** Gegeben `<kit-label for="x">Name</kit-label>`, Wenn sie rendert, Dann ist es ein `<label class="kit-label" for="x">Name</label>`.
- **AK15** Gegeben `<kit-form action="/speichern" method="post">` auf einer Seite mit `<meta name="csrf-token" content="t0k">`, Wenn sie rendert, Dann ist es `<form class="kit-form" action="/speichern" method="post">` mit einem versteckten Feld `_token` = „t0k“; mit `method="put"` bzw. `"delete"` hat das Formular `method="post"` und ein verstecktes Feld `_method` = „PUT“ bzw. „DELETE“; mit `method="get"` gibt es weder `_token` noch `_method`. Fehlt das Meta-Tag, gibt es kein `_token` und keinen Fehler.
- **AK16** Gegeben `<kit-input-group><kit-input-group-text>https://</kit-input-group-text><kit-input-text name="url" /></kit-input-group>`, Wenn sie rendert, Dann gibt es `.kit-input-group` mit `.kit-input-group-text` und dem Feld in dieser Reihenfolge.
- **AK17** Gegeben alle Dateien unter `vue3/components/` und `vue3/kit.css`, Wenn ihr Quelltext gelesen wird, Dann gilt AK10 aus `vue3-kern-grundlage` auch für die neuen Dateien.
- **AK18** Gegeben `vue3/themes/tailwind.css` und `vue3/themes/bootstrap5.scss`, Wenn sie gelesen werden, Dann enthält jede eine Regel für jede Wurzelklasse und jeden Teil aus dem Vertrag und für `is-invalid`, `is-active` und `disabled`.
- **AK19** Gegeben `vue3/index.ts`, Wenn die Komponenten importiert werden, Dann gibt es jede Komponente als benannten Export, in `kitKomponenten` und im Plugin `KitVue3`; `mountInseln(kitKomponenten)` mountet `<div data-vue="kit-input-text" data-props='{"name":"vorname","label":"Vorname"}'>` ohne Konsolenfehler, und ein Formular um die Insel sendet den getippten Wert unter `vorname`.
- **AK20** Gegeben der Host `packages_dev`, Wenn `npm run production` läuft, Dann baut er ohne Fehler, und die Prüfseite `/ui-kit-vue3` zeigt zusätzlich alle Felder aus der Tabelle mit dem Bootstrap-5-Theme ohne Konsolenfehler, je eins mit `error`.
- **AK21** Gegeben README und CHANGELOG des Kits, Wenn gelesen wird, Dann nennt die README im Abschnitt „Vue 3“ jede Komponente aus der Tabelle mit Tag-Name, Props und Klassen und eine Zuordnung alt → neu (Tabelle „Ersetzt“), und der CHANGELOG nennt sie unter „Unveröffentlicht“.
- **AK22** (#1612, ergänzt 2026-11-18, Spec-Lücke, Moderator in Vollmacht) Gegeben `<kit-select :options="[{value:'de',label:'Deutschland'},{value:'at',label:'Österreich'}]" v-model="m" />` ohne `placeholder` und `m` ist `null`, `undefined` oder passt zu keiner Option, Wenn die Komponente rendert (Mount) oder sich `options` so ändern, dass `modelValue` zu keiner Option passt, Dann sendet sie `update:modelValue` mit dem Wert der ersten wählbaren Option (`'de'`), sodass `m` dem sichtbaren und gesendeten Wert entspricht. Mit passendem `modelValue`, mit `placeholder` oder wenn keine Option wählbar ist (keine Optionen oder alle `disabled`), sendet sie beim Start kein `update:modelValue`.

## Randfälle
1. Zwei Felder mit gleichem `name` und ohne `id`: verschiedene erzeugte `id`s, jedes Label zeigt auf sein Feld.
2. Zahlenfeld mit Text, den der Browser nicht als Zahl liest (`valueAsNumber` = `NaN`): Wert `null`, kein Fehler.
3. `kit-select` ohne `placeholder`: keine leere erste Option. Passt kein `modelValue` zu einer Option (auch ohne `v-model` oder mit `null`), ist wie bei einem nativen `<select>` die erste wählbare Option gewählt, und ein Formular sendet unter `name` ihren Wert. (Ergänzt 2026-11-18 nach Review 1, Moderator in Vollmacht.)
4. `kit-checkbox` ohne `value`: Formular sendet `on` (Browser-Standard).
5. `kit-toggle` ohne `name`: kein verstecktes Feld.

## Nicht-Ziele
- Rich-Text-Editor, Markdown-Viewer, Anzeige-Komponenten (`show-*`), Karten, Raster, Navigation, Schaltflächen-Sammlung (`button-*`) — eigene Features bei Bedarf.
- Blade-Components (0013 Punkt 5, eigenes Feature danach).
- Clientseitige Validierung über HTML-Attribute hinaus.
- Umstellen anderer Projekte, Release-Version.

## Entscheidungsspielraum
Interne Struktur (z. B. gemeinsame Basiskomponente oder Composable), weitere Props über die genannten hinaus, Aufbau des Basis-CSS. Keine neuen npm-Pakete.
