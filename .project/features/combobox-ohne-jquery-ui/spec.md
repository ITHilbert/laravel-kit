# Spec combobox-ohne-jquery-ui (#1359)
Risiko: normal
Art: Refactoring mit neuem Skript — die Vue-Komponente `search` (Bootstrap 4/5, durchsuchbares Select, Klasse `selectcb`) nutzt das jQuery-UI-Widget `ui.combobox`. Joerg (Task-Chat, 2026-11-16): „Dann nicht ausbauen“ → Komponente bleibt, das Widget wird durch ein Vanilla-Skript ersetzt, `npm install jquery-ui` entfällt. Version 1.7.0 (Minor).
Rückbau: entfällt (kein Übergangsstand)

## Vertrag
Neues Modul `js/combobox.js` exportiert `initCombobox(root = document)`. Es bindet jedes `select.selectcb` unterhalb von `root` (auch `root` selbst, wenn es ein solches Select ist) genau einmal. Aufbau je Select:
- Das Select bleibt im DOM, ist aber ausgeblendet (`style.display = 'none'`) und bleibt die Quelle des Formularwerts.
- Direkt dahinter steht `span.kit-combobox`, darin ein `input[type=text].kit-combobox-input`, ein `button[type=button].kit-combobox-button` und eine `ul.kit-combobox-list` (anfangs ausgeblendet: Attribut `hidden`).
- Die Liste zeigt `li`-Einträge je `option` mit nicht leerem `value`, die nicht `style="display: none"` hat. Der Text des Eintrags ist der Text der Option (nie als HTML gedeutet); der gesuchte Teil steht in `<strong>`.
Die drei Dateien `vue/bootstrap4/input/search.vue`, `vue/bootstrap5/input/search.vue`, `vue/bootstrap5/forms/search.vue` bleiben inhaltlich gleich und rufen in `mounted()` `initCombobox(this.$el)` auf (statt `window.onload` + jQuery). Die jQuery-UI-Klassen `ui-combobox-*` im `<style>` werden durch `kit-combobox-*` ersetzt.

## Akzeptanzkriterien
- **AK1** Gegeben ein `select.selectcb` mit den Optionen A (value 1), B (value 2, ausgewählt), Wenn `initCombobox(document)` läuft, Dann ist das Select ausgeblendet, direkt dahinter steht `span.kit-combobox` mit Eingabefeld (Wert „B“), Knopf und ausgeblendeter Liste, und ein zweiter Aufruf von `initCombobox` erzeugt keinen zweiten Aufbau.
- **AK2** Gegeben die Optionen „Apfel“, „Birne“, „Ananas“, „Banane“ und eine leere Platzhalter-Option (value ""), Wenn „AN“ ins Eingabefeld getippt wird (Ereignis `input`), Dann zeigt die Liste genau „Ananas“ und „Banane“ (Teiltreffer an beliebiger Stelle, ohne Beachtung der Groß-/Kleinschreibung), jedes Vorkommen des Suchtexts steht in `<strong>` (Schreibweise wie im Optionstext, z. B. „Banane“ → `B<strong>an</strong><strong>an</strong>e`), „Apfel“ und „Birne“ sowie die Platzhalter-Option erscheinen nicht.
- **AK3** Gegeben die Optionen aus AK2, Wenn der Knopf geklickt wird, Dann zeigt die Liste alle Optionen mit nicht leerem value (ohne Filter); ein zweiter Klick schließt sie.
- **AK4** Gegeben eine geöffnete Liste, Wenn ein Eintrag angeklickt wird (Ereignis `mousedown` oder `click`), Dann steht die zugehörige Option im Select ausgewählt (`select.value`), das Eingabefeld zeigt ihren Text, die Liste ist geschlossen, und das Select hat ein Ereignis `change` ausgelöst (bubbelnd).
- **AK5** Gegeben ein Eingabefeld mit Text, Wenn es den Fokus verliert (`blur`/`change`), Dann gilt: passt der Text ohne Beachtung der Groß-/Kleinschreibung genau auf den Text einer Option, ist diese ausgewählt; passt er auf keine, ist das Eingabefeld leer und `select.selectedIndex` ist -1.
- **AK6** Gegeben das Eingabefeld, Wenn die Taste Pfeil-runter gedrückt wird, Dann öffnet sich die Liste und der erste Eintrag ist markiert (Klasse `active`); weitere Pfeil-runter/Pfeil-hoch wandern durch die Einträge; Enter wählt den markierten Eintrag wie in AK4; Escape schließt die Liste ohne Änderung.
- **AK7** Gegeben Optionstexte mit HTML-Zeichen (z. B. `<b>x</b>` oder `A&B`) oder mit RegExp-Zeichen im Suchtext (z. B. `(`), Wenn gesucht wird, Dann wirft das Skript keinen Fehler, die Liste zeigt den Text als Text (keine `<b>`-Elemente aus Optionstexten), nur das gesuchte Wort steht in `<strong>`.
- **AK8** Gegeben die Dateien `js/combobox.js` und die drei `search.vue`, Wenn ihr Quelltext gelesen wird, Dann enthält keine von ihnen `jQuery`, `$(`, `.autocomplete`, `.combobox(` oder `window.onload`, und jede `search.vue` importiert `initCombobox` und ruft es in `mounted` mit `this.$el` auf.
- **AK9** Gegeben `src/Commands/LaravelKitInstallMix.php`, Wenn der Quelltext gelesen wird, Dann enthält er keinen Aufruf `npm install jquery-ui` (der Aufruf `npm install jquery` bleibt, DataTables braucht ihn).
- **AK10** Gegeben README und CHANGELOG, Wenn gelesen wird, Dann nennt das CHANGELOG einen Abschnitt `## 1.7.0` mit dem Hinweis auf die Combobox ohne jQuery UI, und die README nennt das Select-Skript `initCombobox` samt Hinweis, dass jQuery UI nicht mehr nötig ist (Host-Projekte entfernen `jquery-ui` aus ihrer `package.json`).
- **AK11** (#1366) Gegeben eine initialisierte Combobox, Wenn das Select von außen geändert wird (Auswahl per `selectedIndex`/`value` gesetzt oder die Optionen ausgetauscht) und `aktualisiereCombobox(select)` (neuer Export von `js/combobox.js`) aufgerufen wird, Dann zeigt das Eingabefeld den Text der aktuell gewählten Option (leer, wenn keine gewählt ist), und die Liste zeigt beim nächsten Öffnen die aktuellen Optionen; ein Aufruf für ein Select ohne Combobox tut nichts.
- **AK12** (#1366) Gegeben die drei `search.vue`, Wenn ihr Quelltext gelesen wird, Dann hält die Komponente die Auswahl selbst in `data` (`gewaehlt`) und bindet das Select daran statt direkt an die Prop `value`, übernimmt bei einem `change`-Ereignis des Selects dessen Wert in `gewaehlt` und meldet ihn per `$emit('input', …)`, beobachtet `value` und `options` (`watch`) und ruft dabei `aktualisiereCombobox(this.$el)` (nach `$nextTick`); die drei Dateien bleiben identisch.

## Randfälle
- Ein Select ohne ausgewählte Option: Eingabefeld leer (kein Fehler).
- Option mit `style="display: none"` erscheint nicht in der Liste (Verhalten wie bisher).
- `initCombobox` auf einem Wurzelelement ohne Treffer tut nichts.
- Kein Auto-Ausfüllen bei nur einem Treffer (war im alten Widget standardmäßig aus).

## Nicht-Ziele
- Kein neues Aussehen über die Umbenennung der CSS-Klassen hinaus; keine Mehrfachauswahl; keine Tailwind-Variante (die `search` dort ist ein Textfeld).
- Kein Entfernen von jQuery selbst (DataTables).
- `window.onload`-Verhalten für Select-Elemente außerhalb der Komponente entfällt; Host-Projekte rufen `initCombobox()` selbst auf (README).

## Entscheidungsspielraum
Interne Struktur von `js/combobox.js`, CSS-Details, Namen von Hilfsfunktionen. Kein neues npm-Paket.
