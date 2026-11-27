# Spec dialog-delete-sprache (#2451)
Risiko: normal — ändert nur die Textquelle (Wörter) der Component `<x-kit::dialog-delete>`; Markup, Selektoren, Skripte, Route und Senden bleiben unverändert
Art: Feature (Folge aus #2449, userauth übersetzt Abbrechen/Löschen de/en und kann ohne Sprachdateien nicht auf die Component umstellen)
Status: freigegeben (Task #2451 `freigabe`; Empfehlung 2 der Torrent-Admin-Antwort in #2449; Moderator selbst im automode)
Rückbau: entfällt (kein Übergangsstand)

## Akzeptanzkriterien
- **AK1** Gegeben das Theme `kern` und die App-Sprache `en`, Wenn `<x-kit::dialog-delete route="/k/0" />` gerendert wird, Dann tragen die Buttons `[data-kit-delete-abbrechen][data-variant="cancel"]` den Text „Cancel“ und `[data-kit-delete-bestaetigen]` den Text „Delete“; bei App-Sprache `de` „Abbrechen“ und „Löschen“.
- **AK2** Gegeben die Props `titel` und `body` fehlen, Wenn die Component in den Themes `kern`, `bootstrap5` und `tailwind` gerendert wird, Dann kommen Titel und Text aus der Sprachdatei (de: „Löschen“ / „Diesen Eintrag wirklich löschen?“; en: „Delete“ / „Really delete this entry?“) — bei `kern` als Überschrift und Absatz, bei den Vue-Themes in `data-props` (`titel`, `body`).
- **AK3** Gegeben gesetzte Props `titel="Kunde löschen"` und `body="Wirklich?"`, Wenn gerendert wird (alle drei Themes, Sprache `en`), Dann erscheinen genau diese Texte; die Sprachdatei überschreibt sie nicht.
- **AK4** Gegeben die Sprachdateien des Kits, Wenn gelesen, Dann gibt es `de` und `en` für den Namensraum `laravelkit` (Datei `dialog`) mit denselben Schlüsseln `titel`, `body`, `abbrechen`, `loeschen`, und die Dateien liegen unter `src/Lang/<sprache>/dialog.php`; der ServiceProvider lädt sie (`laravelkit::dialog.abbrechen` ist übersetzbar).
- **AK5** Gegeben README und CHANGELOG, Wenn gelesen, Dann nennt die README (Abschnitt zu `dialog-delete`) die Sprachdateien (`laravelkit::dialog.*`, de/en, Überschreiben per Props bzw. `lang/vendor/laravelkit`), und der CHANGELOG hat einen Abschnitt `1.14.0` mit dieser Änderung (#2451).
- **AK6** Gegeben die bestehenden Tests `tests/Feature/KitComponents/DialogDeleteTest.php`, `DialogDeleteSelektorenTest.php`, `tests/js/loeschDialogFokus.test.mjs`, `tests/js/loeschen.test.mjs`, Wenn sie laufen, Dann sind sie grün (App-Sprache der Tests ist `de`; kein Verhalten aus Spec loesch-dialog-blade ändert sich).

## Nicht-Ziele
Texte der Vue-Komponenten (`vue/*/dialog/delete.vue`), weitere Components, andere Sprachen als de/en.

## Entscheidungsspielraum
Namensraum `laravelkit` (wie die Views), Lang-Ort `src/Lang`, Default-Props als `null` mit `??`-Fallback auf die Übersetzung, Testaufbau.
