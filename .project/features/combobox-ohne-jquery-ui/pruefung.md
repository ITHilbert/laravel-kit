# Prüfung: combobox-ohne-jquery-ui (#1359) — 2026-11-16
Geprüft: `git -C packages/laravel-kit diff 2493d370...46a5aa96` im Review-Platz. Verhalten zusätzlich im echten Headless Chrome (eigene Instanz, `--remote-debugging-port=0`, Profil im Scratchpad) mit echten Maus- und Tastaturereignissen per CDP (`Input.dispatchMouseEvent`, `Input.dispatchKeyEvent`, `Input.insertText`). Dazu die echte `vue/bootstrap5/input/search.vue` mit Vue 2.7.16 aus `node_modules` gemountet.

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „Dann ist das Select ausgeblendet“ | bestanden | Chrome: `getComputedStyle(select).display` = none; Fake-DOM-Test AK1 grün |
| AK1 „direkt dahinter steht `span.kit-combobox` mit Eingabefeld (Wert „B“), Knopf und ausgeblendeter Liste“ | bestanden | Chrome: `nextElementSibling` = `SPAN.kit-combobox`, Kinder `INPUT.kit-combobox-input[text]`, `BUTTON.kit-combobox-button[button]`, `UL.kit-combobox-list` mit `hidden`; Eingabefeld = Text der ausgewählten Option („Banane“) |
| AK1 „ein zweiter Aufruf von `initCombobox` erzeugt keinen zweiten Aufbau“ | bestanden | Chrome: `initCombobox(document)` zweimal plus `initCombobox(select)`, danach genau 2 `span.kit-combobox` für 2 Selects |
| AK2 „Dann zeigt die Liste genau „Ananas“ und „Banane“ (Teiltreffer an beliebiger Stelle, ohne Beachtung der Groß-/Kleinschreibung)“ | bestanden | Chrome: Eingabe „AN“ per `insertText` → Liste genau 2 Einträge |
| AK2 „jedes Vorkommen des Suchtexts steht in `<strong>` (Schreibweise wie im Optionstext, z. B. „Banane“ → `B<strong>an</strong><strong>an</strong>e`)“ | bestanden | Chrome: innerHTML `B<strong>an</strong><strong>an</strong>e` und `<strong>An</strong><strong>an</strong>as` |
| AK2 „„Apfel“ und „Birne“ sowie die Platzhalter-Option erscheinen nicht“ | bestanden | Chrome: nicht in der Liste; Fake-DOM-Test AK2 grün |
| AK3 „Dann zeigt die Liste alle Optionen mit nicht leerem value (ohne Filter)“ | bestanden | Chrome: Klick auf Knopf bei Eingabe „Ananas“ → alle 7 sichtbaren Optionen mit value, ohne Platzhalter und ohne `display: none` |
| AK3 „ein zweiter Klick schließt sie“ | bestanden | Chrome: zweiter Mausklick → `hidden`, berechnet `display: none` |
| AK4 „Dann steht die zugehörige Option im Select ausgewählt (`select.value`)“ | bestanden | Chrome: echter Mausklick auf „Ananas“ → `select.value` = "3"; FormData `obst=2` nach späterer Auswahl „Birne“ |
| AK4 „das Eingabefeld zeigt ihren Text“ | bestanden | Chrome: Eingabefeld „Ananas“ |
| AK4 „die Liste ist geschlossen“ | bestanden | Chrome: `hidden` |
| AK4 „und das Select hat ein Ereignis `change` ausgelöst (bubbelnd)“ | bestanden | Chrome: Listener auf `document` zählt genau 1 `change` je Mausklick (mousedown wählt, folgendes click greift nicht — kein doppeltes change); Vue: `@change.native` am Komponenten-Root erhält es |
| AK5 „passt der Text ohne Beachtung der Groß-/Kleinschreibung genau auf den Text einer Option, ist diese ausgewählt“ | bestanden | Chrome: „bIrNe“, dann Klick in anderes Feld (echter blur) → `select.value` = "2"; „ananas“ + Tab → "3" |
| AK5 „passt er auf keine, ist das Eingabefeld leer und `select.selectedIndex` ist -1“ | bestanden | Chrome: „Bir“ + blur → Eingabefeld leer, `selectedIndex` -1, FormData ohne Wert |
| AK6 „Wenn die Taste Pfeil-runter gedrückt wird, Dann öffnet sich die Liste und der erste Eintrag ist markiert (Klasse `active`)“ | bestanden | Chrome: `keyDown ArrowDown` → Liste offen, Index 0 `active` |
| AK6 „weitere Pfeil-runter/Pfeil-hoch wandern durch die Einträge“ | bestanden | Chrome: 2× runter, 1× hoch → Index 1 `active` |
| AK6 „Enter wählt den markierten Eintrag wie in AK4“ | bestanden | Chrome: Enter → `select.value` = "2", Eingabefeld „Birne“, Liste zu, 1 `change` |
| AK6 „Escape schließt die Liste ohne Änderung“ | bestanden | Chrome: Escape → Liste zu, Wert und Eingabefeld unverändert, kein `change` |
| AK7 „Dann wirft das Skript keinen Fehler“ | bestanden | Chrome: Suche „(“, „&“, „b“ ohne Fehler (`window.onerror` leer, keine Konsolenausnahme); Treffer `K<strong>(</strong>lammer)` |
| AK7 „die Liste zeigt den Text als Text (keine `<b>`-Elemente aus Optionstexten)“ | bestanden | Chrome: `querySelectorAll('ul b').length` = 0; innerHTML `&lt;<strong>b</strong>&gt;x&lt;/<strong>b</strong>&gt;`. Code baut nur Textknoten (`createTextNode`), kein `innerHTML` |
| AK7 „nur das gesuchte Wort steht in `<strong>`“ | bestanden | Chrome: `A<strong>&amp;</strong>B` bei Suche „&“ |
| AK8 „enthält keine von ihnen `jQuery`, `$(`, `.autocomplete`, `.combobox(` oder `window.onload`“ | bestanden | `grep -E 'jQuery\|\$\(\|\.autocomplete\|\.combobox\(\|window\.onload'` über die 4 Dateien: kein Treffer; JS-Test AK8 grün |
| AK8 „jede `search.vue` importiert `initCombobox` und ruft es in `mounted` mit `this.$el` auf“ | bestanden | grep: Import Zeile 19, `initCombobox(this.$el)` Zeile 57 in allen drei; Importpfad `../../../js/combobox.js` per `realpath -e` aus allen drei Ordnern aufgelöst; echte bootstrap5/input/search.vue in Vue 2.7.16 gemountet → Combobox aufgebaut, keine Vue-Warnung |
| Vertrag „Die drei Dateien … bleiben inhaltlich gleich“ | bestanden | `md5sum`: alle drei `2c9cf44e…` |
| Vertrag „Die jQuery-UI-Klassen `ui-combobox-*` im `<style>` werden durch `kit-combobox-*` ersetzt“ | bestanden | Diff; grep `ui-combobox` im Paket (ohne Tests) leer |
| AK9 „enthält er keinen Aufruf `npm install jquery-ui`“ | bestanden | `LaravelKitInstallMix.php` Zeile 41–44; `php artisan test --compact --filter=ComboboxOhneJqueryUiTest` 3/3 grün |
| AK9 „(der Aufruf `npm install jquery` bleibt, DataTables braucht ihn)“ | bestanden | Zeile 41 `exec('npm install jquery');` |
| AK10 „nennt das CHANGELOG einen Abschnitt `## 1.7.0` mit dem Hinweis auf die Combobox ohne jQuery UI“ | bestanden | CHANGELOG `## 1.7.0 – 2026-11-16`, „Combobox ohne jQuery UI“; PHP-Test grün |
| AK10 „die README nennt das Select-Skript `initCombobox`“ | bestanden | README Abschnitt „Durchsuchbares Select“ |
| AK10 „samt Hinweis, dass jQuery UI nicht mehr nötig ist (Host-Projekte entfernen `jquery-ui` aus ihrer `package.json`)“ | bestanden | README: „jQuery UI ist dafür nicht mehr nötig: Host-Projekte entfernen `jquery-ui` aus ihrer `package.json`“ |
| Randfall 1 „Ein Select ohne ausgewählte Option: Eingabefeld leer (kein Fehler)“ | bestanden | Chrome: `selectedIndex = -1` → Eingabefeld leer, kein Fehler. Hinweis: Ohne `selected`-Attribut wählt der Browser selbst die erste Option; dann zeigt das Feld deren Text (wie das alte Widget, siehe kann) |
| Randfall 2 „Option mit `style="display: none"` erscheint nicht in der Liste“ | bestanden | Chrome: „Versteckt“ fehlt bei Knopf-Liste |
| Randfall 3 „`initCombobox` auf einem Wurzelelement ohne Treffer tut nichts“ | bestanden | Chrome: `<div><p>x</p></div>` unverändert; `initCombobox(null)` ohne Fehler |
| Randfall 4 „Kein Auto-Ausfüllen bei nur einem Treffer“ | bestanden | Chrome: „Apf“ + blur → Feld leer, `selectedIndex` -1 |
| Nicht-Ziel „keine Tailwind-Variante“ / „Kein Entfernen von jQuery selbst“ | bestanden | `vue/tailwind/forms/search.vue` nicht im Diff; `npm install jquery` bleibt |

## Sicherheit
- Ausgabe-Escaping: Optionstexte nur über `createTextNode`/`textContent`, kein `innerHTML` im Skript; im Browser mit `<b>x</b>`, `<i>Drei</i>` (aus Vue-Prop) und `A&B` belegt.
- Keine RegExp aus Nutzereingabe (Suche per `indexOf`), daher kein Fehler und kein ReDoS bei Sonderzeichen.
- Keine Server-Teile, keine Rechte, Zugänge oder Kundendaten berührt; `Risiko: normal` passt.

## Regressionen
- `node --test tests/js/*.test.mjs` im Paket: 231/231 grün (enthält die bestehenden `mount-inseln`, `vueappOhneLivewireVue`).
- `php artisan test --compact --filter=ComboboxOhneJqueryUiTest` im Platz: 3/3 grün (liest die Dateien aus `packages/laravel-kit` des Platzes).
- Ganze Suite nicht gelaufen (Auftrag: läuft einmal später für den Stapel).
- Vue-Lauf: Optionen nachträglich ergänzt → Combobox zeigt beim Öffnen die neue Option, kein zweiter Aufbau, keine Vue-Warnung.
- Verhalten außerhalb der Spec (vorbestehend, siehe Neue Tasks): Rendert die Komponente neu (Elternteil ändert Zustand; wegen `v-bind="$attrs"` reicht jede Änderung), setzt Vue `select.value` per `:value` auf die Prop zurück, das Eingabefeld bleibt bei der Nutzerauswahl. Mit dem neuen `change` (AK4) leicht auslösbar, wenn der Host darauf reagiert. Die Spec regelt das nicht → Task #1366.

## Funde: 0 muss, 0 sollte, 3 kann
- kann: Beim Fokusverlust passt der genaue Treffer auch auf die Platzhalter-Option und auf Optionen mit `display: none` (z. B. „versteckt“ eintippen wählt die versteckte Option). Gleiches Verhalten wie das alte Widget; AK5 sagt „einer Option“.
- kann: `tests/js/fake-dom.mjs` setzt für ein Select ohne `selected` `selectedIndex` -1, der Browser wählt die erste Option (Kommentar Zeile 301 nennt es). Die Tests prüfen damit Randfall 1 in einem Zustand, den der Browser nur per `selectedIndex = -1` herstellt; im Browser bestanden.
- kann: Der Knopf hat nur `aria-label` und `::after`-Zeichen; Liste ohne `id`/`aria-controls`/`aria-activedescendant` — Bildschirmleser melden den markierten Eintrag nicht.

## Neue Tasks: #1366 Vue search (Bootstrap 4/5) – Auswahl geht bei Neu-Rendern verloren, Eingabefeld und Select laufen auseinander

## Fehlerprotokoll
- keine

## Fazit: freigegeben
