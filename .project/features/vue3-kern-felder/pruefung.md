# Prüfung: vue3-kern-felder — 2026-11-18

Geprüft im Review-Platz `/home/joerg/projekte/.worktrees/packages_dev/reviewer`: Kit `6788419e` (Diff `origin/main...HEAD`, main = `65c149cc`), Host `d45f096` (Diff `main...HEAD`).

Nachweise:
- **Vitest:** Kit-Vitest ganz (15 Dateien, 155 grün), darin `felder-text`, `felder-zahl`, `felder-auswahl`, `felder-rahmen` und `toggle-ohne-model`.
- **PHP:** `php artisan test packages/laravel-kit/tests/Feature/Vue3 tests/Feature/HostGetRoutenTest.php tests/Feature/KitVue3CssTest.php` (32 grün).
- **Browser-Sonde:** Eigenes Headless Chrome, Port 0, eigenes Profil im Scratchpad, `--lang=de-DE`. Echte Maus-Klicks, Tastatur-Eingaben und Leertaste per CDP `Input.*`, Dateiwahl per `DOM.setFileInputFiles`. Formulardaten per `new FormData(form)`. Zwei Ziele:
  - (a) eigene Seite mit dem Kit-Einstieg (`vue3/index.ts` + `js/mount-inseln.js`, mit Vite gebündelt) und `<meta name="csrf-token" content="t0k">`;
  - (b) Host `http://127.0.0.1:8104/ui-kit-vue3`, Server des Platzes.

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „gibt es `.kit-input-text`, darin `.kit-input-text__label` (`<label>`) mit dem Text „Vorname““ | bestanden | Test `felder-text` AK1 grün; Browser (a): `LABEL`, „Vorname“ |
| AK1 „und `.kit-input-text__input` (`type="text"`, `name="vorname"`)“ | bestanden | Browser (a): `type=text`, `name=vorname` |
| AK1 „das `for` des Labels zeigt auf die `id` des Eingabefelds, auch ohne `id`-Attribut (dann eine erzeugte, je Instanz eindeutige `id`)“ | bestanden | Browser (a): id `kit-feld-v-0` = `for`; zwei weitere Instanzen `v-1`, `v-2` |
| AK2 „hat das Eingabefeld `id="eigen"` und das Label `for="eigen"`“ | bestanden | `felder-text` AK2 je Feld der Tabelle (15), mit Gegenprobe: Wurzel ohne id |
| AK2 „ohne `label` gibt es kein `__label`“ | bestanden | ebd., je Feld |
| AK3 „Wenn „abc“ getippt wird, Dann ist `wert` „abc““ | bestanden | `felder-text` AK3; Browser (a): Tastatur „abc“ → `wert` „abc“ |
| AK3 „wenn `wert` von außen auf „xyz“ gesetzt wird, Dann zeigt das Eingabefeld „xyz““ | bestanden | ebd.; Browser (a): Feld zeigt „xyz“ |
| AK3 „Gleiches gilt für `kit-input-email`, `kit-input-password`, `kit-input-date`, `kit-input-color` und `kit-textarea`“ | bestanden | `felder-text` AK3 `test.each` über alle sechs Felder (Ereignis `input`) |
| AK4 „trägt die Wurzel `is-invalid`“ | bestanden | `felder-text` AK4 je Feld (15) |
| AK4 „das Eingabefeld hat `aria-invalid="true"` und `aria-describedby` mit der `id` von `__error`“ | bestanden | ebd. |
| AK4 „und `__error` zeigt „Pflichtfeld““ | bestanden | ebd. |
| AK4 „Mit `invalid` ohne `error` gibt es `is-invalid` und `aria-invalid="true"`, aber kein `__error`“ | bestanden | ebd. |
| AK4 „Ohne beides gibt es keins davon.“ | bestanden | ebd. (Gegenprobe im selben Test) |
| AK5 „trägt die Wurzel `disabled`“ | bestanden | `felder-auswahl` AK5 je Feld (15), mit Gegenprobe; Browser (a): Passwort, Toggle |
| AK5 „und das Eingabefeld ist `disabled` (bei `kit-toggle` die Schaltfläche)“ | bestanden | ebd.; Browser (a): `#td.disabled` = true |
| AK5 „ein Klick auf `kit-toggle`, `kit-checkbox` oder den Schalter von `kit-input-password` ändert den Wert nicht“ | bestanden | `felder-auswahl` AK5; Browser (a): echter Klick auf Toggle → `false`; Klick auf Checkbox-Label → `false`; Klick auf Passwort-Schalter → `type=password` |
| AK6 „Wenn „12,5“ bzw. „12.5“ eingegeben wird …, Dann ist der Wert die Zahl `12.5` (bei `kit-input-int` mit „12“ die Zahl `12`)“ | bestanden | `felder-zahl` AK6 („12.5“); Browser (a), Tastatur: number „12,5“ → 12.5, euro „12,5“ → 12.5, percent „12.5“ → 12.5, int „12“ → 12 (alle `number`) |
| AK6 „wird das Feld geleert, ist der Wert `null`“ | bestanden | `felder-zahl` AK6; Browser (a): Euro geleert → `null` |
| AK6 „Das Eingabefeld von `kit-input-euro` hat `step="0.01"` und `inputmode="decimal"`“ | bestanden | `felder-zahl` Attribute-Test |
| AK6 „`kit-input-int` hat `step="1"` und `inputmode="numeric"`“ | bestanden | ebd. (Fund **sollte** 1 zu `:step="0.5"` unten) |
| AK7 „folgt dem Eingabefeld `.kit-input-euro__suffix` mit „€“ bzw. `.kit-input-percent__suffix` mit „%““ | bestanden | `felder-zahl` AK7 (DOM-Reihenfolge); Darstellung: Zusatz bricht unter das Feld um (Fund **sollte** 2) |
| AK7 „jeweils `aria-hidden="true"`“ | bestanden | ebd. |
| AK7 „das Eingabefeld trägt ohne `label` und ohne `aria-label`-Attribut `aria-label="Betrag in Euro"` bzw. `aria-label="Prozent"`“ | bestanden | ebd., mit Gegenproben `label` und eigenes `aria-label` |
| AK8 „`.kit-input-password__toggle` (Schaltfläche, `aria-pressed="false"`, `aria-label="Passwort anzeigen"`)“ | bestanden | `felder-text` AK8 |
| AK8 „Dann hat das Eingabefeld `type="text"`“ | bestanden | ebd.; Browser (a): echter Klick → `text`, Wert „geheim“ bleibt |
| AK8 „der Schalter `aria-pressed="true"`, `aria-label="Passwort verbergen"` und `is-active`“ | bestanden | ebd. |
| AK8 „ein zweiter Klick stellt den Ausgangszustand her“ | bestanden | ebd.; Browser (a): `password`, `false`, „Passwort anzeigen“, ohne `is-active` |
| AK9 „hat `.kit-select__input` (`<select>`) drei `<option>`: zuerst „Bitte wählen“ mit leerem Wert, dann die beiden Optionen“ | bestanden | `felder-auswahl` AK9; Browser (a): `["","Bitte wählen"]`, `de`, `at`; Platzhalter gewählt |
| AK9 „„Österreich“ `disabled`“ | bestanden | ebd. |
| AK9 „wenn „Deutschland“ gewählt wird, ist `land` `'de'`“ | bestanden | ebd. |
| AK9 „wenn `land` von außen `'at'` ist, ist „Österreich“ ausgewählt“ | bestanden | ebd.; Browser (a): `value=at`, gewählte Option „Österreich“ |
| AK9 „Zahlen-Werte kommen als Zahl zurück“ | bestanden | ebd.; Browser (a): Wahl „Zwei“ → `2` (`number`) |
| AK10 „gibt es `.kit-checkbox__input` (`type="checkbox"`, `name="agb"`, `value="1"`)“ | bestanden | `felder-auswahl` AK10 |
| AK10 „und danach `.kit-checkbox__label` mit dem Slot-Text und `for` auf das Eingabefeld“ | bestanden | ebd.; Browser (a): Reihenfolge `__input`, `__label` |
| AK10 „ein Klick auf das Label setzt `ok` auf `true`, ein zweiter auf `false`“ | bestanden | ebd.; Browser (a): echter Klick aufs Label → `true`, zweiter → `false` |
| AK10 „ist `ok` von außen `true`, ist das Feld angehakt“ | bestanden | ebd.; Browser (a) |
| AK11 „Wenn „b“ angeklickt wird, Dann ist `art` „b“ und nur „b“ angehakt“ | bestanden | `felder-auswahl` AK11; Browser (a): Klick aufs Label „B“ → `b`, `[false, true]` |
| AK11 „ist `art` von außen „a“, ist nur „a“ angehakt“ | bestanden | ebd.; Browser (a): `[true, false]` |
| AK12 „gibt es `.kit-toggle` mit einer Schaltfläche `role="switch"` (`.kit-toggle__input`, `aria-checked="false"`, `type="button"`)“ | bestanden | `felder-auswahl` AK12; Browser (a): `BUTTON`, `switch`, `false`, `button` |
| AK12 „`.kit-toggle__thumb` (`aria-hidden="true"`) und das Label“ | bestanden | ebd.; Browser (a): Label „Aktiv“ |
| AK12 „ein Klick oder Leertaste setzt `an` auf `true`, `aria-checked="true"` und `is-active` an der Wurzel“ | bestanden | Klick: `felder-auswahl` AK12 und `toggle-ohne-model` (ohne `v-model`). Leertaste: Test nur angenähert (`Toggle_angenaehert_Leertaste`); selbst bestätigt in Browser (a): fokussiert, echte Leertaste schaltet → `true`/`"true"`/`is-active` |
| AK12 „mit `name` sendet ein Formular `aktiv=1` bzw. `aktiv=0`“ | bestanden | ebd.; Browser (a): FormData `aktiv` 0 → 1 |
| AK13 „meldet `update:modelValue` die `FileList`“ | bestanden | Test nur mit nachgebautem Array; selbst bestätigt in Browser (a): zwei Dateien gewählt → `instanceof FileList`, Länge 2 |
| AK13 „das Eingabefeld hat `accept` und `multiple`“ | bestanden | `felder-auswahl` AK13; Browser (a): `image/*`, `true` |
| AK14 „ist es ein `<label class="kit-label" for="x">Name</label>`“ | bestanden | `felder-rahmen`; Browser (a): genau dieses `outerHTML` |
| AK15 „ist es `<form class="kit-form" action="/speichern" method="post">` mit einem versteckten Feld `_token` = „t0k““ | bestanden | `felder-rahmen`; Browser (a): `method=post`, `action=/speichern`, `_token=t0k` |
| AK15 „mit `method="put"` bzw. `"delete"` hat das Formular `method="post"` und ein verstecktes Feld `_method` = „PUT“ bzw. „DELETE““ | bestanden | ebd.; Browser (a): `[post,t0k,PUT]`, `[post,t0k,DELETE]` |
| AK15 „mit `method="get"` gibt es weder `_token` noch `_method`“ | bestanden | ebd.; Browser (a): `[get,null,null]` |
| AK15 „Fehlt das Meta-Tag, gibt es kein `_token` und keinen Fehler.“ | bestanden | ebd.; Browser (a): Meta entfernt, neues Formular ohne `_token`, Konsole leer |
| AK16 „gibt es `.kit-input-group` mit `.kit-input-group-text` und dem Feld in dieser Reihenfolge“ | bestanden | `felder-rahmen`; Browser (a) |
| AK17 „gilt AK10 aus `vue3-kern-grundlage` auch für die neuen Dateien“ | bestanden | `Vue3FelderQuelltextTest`, `Vue3QuelltextTest` grün; selbst gelesen: keine `<style>`, kein `@apply` in `kit.css`. Klassen in Template-Strings (`` `kit-${basis}__…` ``) sieht der Wächter nicht, sie tragen aber alle `kit-` (kann 1) |
| AK18 „enthält jede eine Regel für jede Wurzelklasse und jeden Teil aus dem Vertrag und für `is-invalid`, `is-active` und `disabled`“ | bestanden | `Vue3FelderQuelltextTest` AK18 grün. Sonden gegen den echten Compiler: `bootstrap5.scss` im Host-Build fehlerfrei. `tailwind.css` mit Tailwind 4.2.1 (`@tailwindcss/node` compile) fehlerfrei, Regeln für `__thumb`, `__toggle`, `__suffix`, `.is-active` im Ergebnis |
| AK19 „gibt es jede Komponente als benannten Export, in `kitKomponenten` und im Plugin `KitVue3`“ | bestanden | `felder-rahmen` Einstieg (19 Tags); Browser (a): keine Lücke |
| AK19 „`mountInseln(kitKomponenten)` mountet `<div data-vue="kit-input-text" …>` ohne Konsolenfehler“ | bestanden | `felder-rahmen` (Spion auf `console.warn`/`error`); Browser (a): Label „Vorname“, Konsole leer |
| AK19 „und ein Formular um die Insel sendet den getippten Wert unter `vorname`“ | bestanden | ebd.; Browser (a): Tastatur „Max“ → FormData `vorname=Max` |
| AK20 „Wenn `npm run production` läuft, Dann baut er ohne Fehler“ | bestanden | `mix --production` in einer Kopie des Platzes (Scratchpad, `node_modules`/`vendor` verlinkt): „Compiled successfully“. `kit-vue3.css` und `app.css` bytegleich zum Commit, `app.js` nur in Modul-IDs verschieden |
| AK20 „und die Prüfseite `/ui-kit-vue3` zeigt zusätzlich alle Felder aus der Tabelle mit dem Bootstrap-5-Theme“ | bestanden | `echtlauf`-Werkzeug `browser-lauf` (Klick `.kit-toggle__input`) → OK. Browser (b): alle 19 Tags im DOM, keine unaufgelösten `KIT-`-Elemente, `kit-vue3.css` geladen, Fehlerrahmen `rgb(220,53,69)` |
| AK20 „ohne Konsolenfehler“ | bestanden | `browser-lauf`: 0 Konsolenfehler; Browser (b): nur Chrome-Hinweise (verbose) zu Passwortfeldern |
| AK20 „je eins mit `error`“ | bestanden | Browser (b): jedes der 15 Felder hat mindestens ein `__error` |
| AK21 „nennt die README im Abschnitt „Vue 3“ jede Komponente aus der Tabelle mit Tag-Name, Props und Klassen“ | bestanden | `Vue3FelderQuelltextTest` AK21; selbst gelesen (`### Felder`) |
| AK21 „und eine Zuordnung alt → neu (Tabelle „Ersetzt“)“ | bestanden | ebd. (`#### Ersetzt`) |
| AK21 „und der CHANGELOG nennt sie unter „Unveröffentlicht““ | bestanden | ebd. |
| Randfall 1 „Zwei Felder mit gleichem `name` und ohne `id`: verschiedene erzeugte `id`s“ | bestanden | `felder-text` Randfall 1; Browser (a): `v-1` ≠ `v-2` |
| Randfall 1 „jedes Label zeigt auf sein Feld“ | bestanden | ebd.; Browser (a): A→v-1, B→v-2 |
| Randfall 2 „Zahlenfeld mit Text, den der Browser nicht als Zahl liest (`valueAsNumber` = `NaN`): Wert `null`“ | bestanden | `felder-zahl` Randfall 2; Browser (a): Tastatur „12.5e“ bzw. „-“ → `badInput`, Wert `null` |
| Randfall 2 „kein Fehler“ | bestanden | ebd. (Spion), Browser-Konsole leer |
| Randfall 3 „`kit-select` ohne `placeholder`: keine leere erste Option.“ | **nicht bestanden** | Im DOM keine leere `<option>` (Test prüft nur das). In Chrome startet das Feld aber ohne Auswahl: Browser (a) `selectedIndex -1`, `value ""`. Es zeigt leer an und fehlt in den Formulardaten (`FormData.has('ohne') = false`), mit und ohne `v-model`. Ursache: `select.vue` bindet `:value="props.modelValue ?? ''"`, ohne passende Option setzt Chrome `selectedIndex = -1`. happy-dom wählt die erste Option, darum grün |
| Randfall 4 „`kit-checkbox` ohne `value`: Formular sendet `on` (Browser-Standard).“ | bestanden | `felder-auswahl` Randfall 4; Browser (a): echter Label-Klick → FormData `cb=on` |
| Randfall 5 „`kit-toggle` ohne `name`: kein verstecktes Feld.“ | bestanden | `felder-auswahl` Randfall 5; Browser (a): 0 `input` im Toggle ohne `name`, kein Schlüssel in FormData |
| Vertrag „Keine neuen npm-Pakete.“ | bestanden | `package.json` von Kit und Host nicht im Diff |
| Vertrag „Symbole als inline-SVG mit `aria-hidden="true"`“ | bestanden | `input-password.vue`: SVG mit `aria-hidden="true"`, `focusable="false"` |
| Nicht-Ziele (Rich-Text, `show-*`, Blade-Components, Validierung, andere Projekte, Release) | bestanden | Diff enthält nichts davon; keine Versionsnummer |

## Sicherheit
- **Ausgabe-Escaping:** Label, Fehlertext, Optionen und Slots nur per Interpolation, kein `v-html`.
- **CSRF (`kit-form`):** liest `_token` aus `meta[name=csrf-token]` der eigenen Seite und setzt es nur bei Nicht-GET ins Formular. Fehlt das Meta-Tag, gibt es kein Feld und keinen Fehler. Das entspricht `@csrf` in Blade. Ein `action` auf eine fremde Domain bekäme das Token wie bei `@csrf` auch (kein neuer Weg).
- **`_method`:** nur `PUT`/`PATCH`/`DELETE`, andere Werte enden als `post` ohne `_method`.
- **Risiko-Einstufung:** `Risiko: normal` passt. Der Diff berührt keine Anmeldung, Rechte, Zugangsdaten, Zahlung, kein Löschen und keine Kundendaten. Das CSRF-Feld ist Formular-Infrastruktur ohne neuen Datenfluss.
- Mandantentrennung, Mass-Assignment, serverseitige Validierung: nicht berührt (nur Frontend, keine Route neu).

## Regressionen
- **Kit, Vitest ganz:** 15 Dateien / 155 Tests grün (`accordion`, `combobox`, `dialog`, `felder-*` (4), `input-list`, `input-token`, `inseln-ids`, `inseln-logik`, `inseln`, `search`, `tabs`, `toggle-ohne-model`).
- **Kit, node --test:** `packages/laravel-kit/tests/js/*.test.mjs` (darin `mount-inseln.test.mjs`): 240 grün.
- **PHP:** `packages/laravel-kit/tests/Feature/Vue3/{Vue3FelderQuelltextTest,Vue3LogikQuelltextTest,Vue3QuelltextTest}.php`, Host `tests/Feature/HostGetRoutenTest.php`, `tests/Feature/KitVue3CssTest.php`: 32 grün.
- `tests-fuer --geaendert --nur-pfade` lieferte nichts, weil der Host-Diff nur aus Blade und Build-Artefakten besteht. `git grep` nach `ui-kit-vue3`/`kit-vue3.css`/Theme-Pfaden fand die oben genannten PHP-Tests.
- Ganze Suite nicht gelaufen (kein `SUITE: ja`). `storage/logs/laravel.log` des Platzes ohne Einträge von heute.

## Funde: 1 muss, 2 sollte, 3 kann
- **muss:** Randfall 3. `kit-select` ohne `placeholder` startet in Chrome ohne gewählte Option (`selectedIndex -1`). Es zeigt leer an und sendet das Feld nicht mit, obwohl keine leere Option existiert. Nachweis: Browser-Sonde (a) `#ohne` → `[2, -1, "", false]`, gleich mit `v-model` auf `null`. Erwartet: wie ein natives `<select>` ohne Platzhalter die erste wählbare Option. Dazu gehört ein Test, der den Startzustand prüft (`selectedIndex`/FormData), möglichst ohne Abhängigkeit vom happy-dom-Verhalten.
- **sollte 1:** `<kit-input-number :step="0.5">` (Markup aus AK6) wirft im Dev-Build `[Vue warn]: Invalid prop: type check failed for prop "step". Expected String …`. Ursache: `feld/zahl-feld.vue:12` `step?: string`. Folge: Warnung bei jedem Host-`npm start` (→ Folge-Task #1611).
- **sollte 2:** Zusatz „€“/„%“ von `kit-input-euro`/`-percent` bricht in beiden Themes unter das Feld um. Ursache: Feld `width:100%`/`w-full`, dazu `kit.css` `flex: 1 1 auto`. Browser (b): Feld rechts 1217 px, Zusatz links 302 px, 38 px tiefer; Screenshot bestätigt (→ Folge-Task #1611).
- **kann 1:** Der Klassen-Wächter (`Vue3FelderQuelltextTest::vueKlassen`) erkennt keine Klassen aus Template-Strings (`` :class="`kit-${props.basis}`" `` in `feld/text-feld.vue`, `feld/zahl-feld.vue`). Sie sind heute korrekt, ein Verstoß dort bliebe aber unentdeckt.
- **kann 2:** Ein `kit-toggle` mit `disabled` und `name` sendet sein verstecktes Feld trotzdem (`td=0`). Ein natives deaktiviertes Feld wird nicht gesendet; die Spec regelt es nicht.
- **kann 3:** `kit-form` ohne `method` sendet per `post` mit `_token`; HTML-Standard wäre `get`. Die Spec regelt den Standard nicht, README nennt ihn nicht.

## Neue Tasks: #1611 laravel-kit vue3 – Nacharbeit aus Review vue3-kern-felder

## Fehlerprotokoll
- {"schwere":"muss","ak":"Randfall 3","fund":"kit-select ohne placeholder startet in Chrome mit selectedIndex -1 (leere Anzeige, Feld fehlt im Formular), obwohl keine leere Option im DOM steht","ursache":"Test prüft nur die Zahl der option-Elemente in happy-dom; :value=\"modelValue ?? ''\" erzwingt einen Wert ohne passende Option, happy-dom wählt trotzdem die erste, Chrome keine","anweisung":"Bei Formularfeldern den Startzustand (gewählte Option, gesendete Formulardaten) im Test prüfen und Verhalten, das zwischen happy-dom und Browser abweichen kann (select.value, selectedIndex), im echten Browser bestätigen"}
- {"schwere":"sollte","ak":"AK6","fund":"kit-input-number :step=\"0.5\" erzeugt im Dev-Build die Vue-Warnung 'Invalid prop: type check failed for prop step'","ursache":"Prop step in feld/zahl-feld.vue als string typisiert, das AK-Markup übergibt eine Zahl; der Test spioniert console.warn nur im Randfall 2","anweisung":"Props, die im AK mit v-bind (:prop=\"Zahl\") stehen, mit dem Typ aus dem AK deklarieren und im Test mit dem wörtlichen AK-Markup auf Vue-Warnungen prüfen"}
- {"schwere":"sollte","ak":"AK7","fund":"Zusatz € bzw. % bricht im Bootstrap- und Tailwind-Theme unter das Eingabefeld um","ursache":"Feld width:100% aus dem Theme trifft auf flex 1 1 auto mit flex-wrap in kit.css; beim Passwort-Schalter behoben, bei Euro/Prozent nicht übertragen","anweisung":"Wird ein Layout-Fehler an einem Feld behoben, alle Felder mit gleichem Aufbau (Zusatz neben dem Feld) im selben Lauf mitprüfen und per Screenshot belegen"}

## Fazit: nicht freigegeben
Grund: Randfall 3 (`kit-select` ohne `placeholder` startet im Browser ohne Auswahl und sendet das Feld nicht). Alle AKs 1–21 und die Randfälle 1, 2, 4 und 5 sind bestanden. Die beiden sollte-Funde stehen in #1611 und blockieren nicht.
