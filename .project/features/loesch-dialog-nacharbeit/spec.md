# Spec loesch-dialog-nacharbeit (#2435)
Risiko: normal — ändert nur Fokusführung, Doku und Tests des Blade-Lösch-Dialogs; Ziel-Route und Senden bleiben unverändert (Spec loesch-dialog-blade, AK6–AK13 müssen grün bleiben)
Art: Refactoring/Nacharbeit zu `loesch-dialog-blade` (Review-Funde „sollte“, Task #2435)
Status: freigegeben (Task #2435 `freigabe`, Reviewer-Fund nach #1934; Moderator selbst im automode)
Rückbau: entfällt (kein Übergangsstand)

## Akzeptanzkriterien
- **AK1** Gegeben der Blade-Lösch-Dialog (Spec loesch-dialog-blade) und `initLoeschDialog()`, Wenn ein `data-kit-delete="5"`-Element den Dialog öffnet, Dann steht der Fokus (`document.activeElement`) auf dem Button „Abbrechen“ (`[data-kit-delete-abbrechen]` mit `data-variant="cancel"`).
- **AK2** Gegeben der offene Dialog aus AK1, Wenn er schließt (Abbrechen, Overlay, Escape oder Löschen), Dann kehrt der Fokus zum Element zurück, das ihn geöffnet hat (der angeklickte `data-kit-delete`-Button), sofern es noch in der Seite ist; fehlt es, bleibt der Fokus unverändert (kein Fehler).
- **AK3** Gegeben der offene Dialog, Wenn die Taste Tab auf dem letzten fokussierbaren Element des Dialogs gedrückt wird, Dann springt der Fokus auf das erste (Abbrechen); Umschalt+Tab auf dem ersten springt auf das letzte (Löschen). Der Fokus verlässt den Dialog nicht. Der Standardablauf der Taste wird dabei unterdrückt (`preventDefault`).
- **AK4** Gegeben ein geschlossener Dialog, Wenn Tab gedrückt wird, Dann greift die Fokusfalle nicht (kein `preventDefault`, kein Fokuswechsel).
- **AK5** Gegeben die README des Kits, Wenn gelesen wird, Dann nennt die Tabelle der Kern-Templates (Abschnitt „Theme `kern`: Klassen und Skript-Bindung“) `dialog-delete` mit Wurzel `kit-dialog` und Teilen `__overlay`, `__content`, `__title`; die Liste „Die Skripte binden die Kern-Templates …“ nennt `js/loesch-dialog.js`; die Liste „Verfügbare Kit-Components“ nennt `dialog-delete`; ein Satz sagt, dass `vue3/kit.css` für die feste Ebene geladen sein muss.
- **AK6** Gegeben `src/Views/kit/kern/dialog-delete.blade.php` und `js/loesch-dialog.js`, Wenn ein PHP-Test beide liest, Dann kommt jeder Selektor, den das Skript verwendet (`[data-kit-delete-dialog]`, `[data-kit-delete-bestaetigen]`, `[data-kit-delete-abbrechen]`, Attribut `data-route`, Attribut `hidden`), im gerenderten Template vor; ändert jemand das Template, wird der Test rot.
- **AK7** Gegeben die bestehenden Tests `tests/js/loeschDialog.test.mjs`, `tests/js/loeschen.test.mjs`, `DialogDeleteTest.php`, `KernQuelltextTest.php`, Wenn sie laufen, Dann sind sie grün (kein Verhalten der AK6–AK13 aus loesch-dialog-blade ändert sich).

## Nicht-Ziele
`js/loeschen.js`, Vue-Insel, Theme-Dateien.

## Entscheidungsspielraum
Umsetzung der Fokusfalle (keydown-Listener am Dialog oder `document`), Testaufbau (Fake-DOM; fehlt dort `focus()`/`activeElement`, dort ergänzen oder die Tests von `tests/js/fake-dom.mjs` entsprechend erweitern lassen — das macht der test-writer).
