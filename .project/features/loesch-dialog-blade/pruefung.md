# Prüfung: loesch-dialog-blade — 2026-11-27
Geprüft: laravel-kit `6ecdf721..db803fbd` (3 Commits: Spec/Plan, rote Tests, Umsetzung). Eigene Nachweise: Blade-Ausgabe im Platz gerendert (`php artisan tinker --execute`, nur lesend), daraus eine Seite mit echtem `vue3/kit.css`, `js/loesch-dialog.js`, `js/loeschen.js` gebaut und in eigenem Headless Chrome (Port 0, Profil im Scratchpad) per CDP mit echten Maus- und Tastaturereignissen bedient (Sonde `scratchpad/ld-sonde/sonde.mjs`).

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „gibt es `div.kit-dialog` mit `data-kit-delete-dialog`, `data-route="/kunden/0"` und `hidden`“ | bestanden | `test_kern_rendert_dialog_mit_rollen_und_genau_zwei_buttons` grün; eigene Render-Ausgabe zeigt genau dieses Markup |
| AK1 „darin `div.kit-dialog__content` (`role="dialog"`, `aria-modal="true"`, `aria-labelledby` zeigt auf die `id` von `h2.kit-dialog__title` mit „Kunde löschen“)“ | bestanden | Test prüft id = aria-labelledby; Render: `aria-labelledby="kit-dialog-delete-titel"` = `id` der `h2` |
| AK1 „den Text „Wirklich löschen?““ | bestanden | Test (`//p`), Render |
| AK1 „genau zwei Buttons `button.kit-button[type=button]`: „Abbrechen“ (`data-variant="cancel"`, `data-kit-delete-abbrechen`) und „Löschen“ (`data-variant="delete"`, `data-kit-delete-bestaetigen`)“ | bestanden | Test `count(//button)=2` und beide per XPath; Render |
| AK2 „genau ein `form#formDelete` mit `method="POST"`“ | bestanden | `test_kern_hat_genau_ein_lösch_formular_…` (count 1, auch count(//form)=1) |
| AK2 „einem `_token`-Feld“ | bestanden | Test mit Session-Token `abc123` im `value` |
| AK2 „`<input name="_method" value="DELETE">`“ | bestanden | Test, Render |
| AK2 „ohne `action`“ | bestanden | Test `[@action]` fehlt; Render |
| AK3 „erscheinen die Werte als Text (escaped), nicht als Markup“ | bestanden | Test grün; eigene Sonde mit `<b>X</b> "q" 's`, `a & b`, Route mit `&` und `"`: kern gibt `&lt;b&gt;…`, `&amp;`, `&quot;` aus, DOM-Parser liest die Originalwerte zurück |
| AK4 „zeigt der Dialog „Löschen“ als Titel“ | bestanden | `test_kern_ohne_titel_und_body_zeigt_die_standardwerte` |
| AK4 „und „Diesen Eintrag wirklich löschen?“ als Text“ | bestanden | ebd. |
| AK5 „gibt es `div[data-vue="dialog-delete"]` mit `data-props`, dessen JSON `titel`, `body` und `route` enthält“ | bestanden | Test je Theme bootstrap5/tailwind; eigene Sonde: `data-props` mit Sonderzeichen ist gültiges JSON und liefert die Originalwerte |
| AK5 „danach `form#formDelete` wie in AK2“ | bestanden | Test `following-sibling::form`, POST, `_token`, `_method`, ohne `action` |
| AK5 „kein `data-kit-delete-dialog`“ | bestanden | Test `sieHatNicht` |
| AK6 „Wenn ein Element mit `data-kit-delete="5"` angeklickt wird, Dann verliert die Wurzel `hidden` (Dialog offen)“ | bestanden | JS-Test (Fake-DOM, nachgebautes Markup); selbst im Browser mit gerendertem `<x-kit::button-delete value="5">`: vorher `display:none`, nach echtem Mausklick `hidden=false`, `display:block`, `position:fixed`, Inhalt mittig (600/357 bei 1200×800) |
| AK7 „bekommt `#formDelete` die `action` `/kunden/5` (letztes Pfadsegment der Route durch die ID ersetzt, Query bleibt)“ | bestanden | JS-Test inkl. Query; Browser mit Route `/kunden/0?x=1`: action `/kunden/5?x=1` |
| AK7 „das Formular wird gesendet“ | bestanden | Browser: `submit` genau einmal mit `/kunden/5?x=1` |
| AK7 „und der Dialog ist wieder `hidden`“ | bestanden | Browser: danach `hidden`, `display:none` |
| AK8 „Wenn „Abbrechen“, das Overlay oder die Taste Escape betätigt wird, Dann ist der Dialog `hidden`“ | bestanden | JS-Test je Weg; Browser: Klick auf „Abbrechen“, Mausklick auf Punkt (5,5) (trifft laut `elementFromPoint` das Overlay), CDP-Taste Escape → jeweils `hidden`; Klick in den Inhalt (Absatz) lässt ihn offen (Gegenprobe) |
| AK8 „und das Formular wurde nicht gesendet“ | bestanden | Browser: Liste der Sendungen nach allen drei Wegen unverändert |
| AK8 „Danach öffnet ein Klick auf `data-kit-delete="7"` den Dialog, und „Löschen“ sendet an `/kunden/7` (die ID von 5 ist vergessen)“ | bestanden | JS-Test; Browser: `/kunden/7?x=1` |
| AK9 „`initLoeschDialog()` wird zweimal aufgerufen (auch mit demselben `root`) … wird das Formular genau einmal gesendet“ | bestanden | JS-Test (doc, doc, Standard); Browser: Seite ruft `initLoeschDialog()` zweimal, je Löschen genau ein Eintrag; Zusatz `initLoeschDialog(document)` + `(document.body)`: ein Senden |
| AK9 „Gegeben die Seite hat keinen `[data-kit-delete-dialog]` … gibt es keinen Fehler“ | bestanden | JS-Test `doesNotThrow` mit Gegenprobe |
| AK10 „Regeln, die `.kit-dialog[hidden]` ausblenden“ | bestanden | Test liest Regel; im Browser berechnet `display:none` |
| AK10 „ein sichtbares `.kit-dialog` als feste, die Seite deckende Ebene mit zentriertem `.kit-dialog__content`“ | bestanden | Test; Browser: `position:fixed`, Overlay liegt an der Ecke (5,5), Inhalt in der Mitte. Fall #677: `.kit-dialog[hidden]` hat Spezifität 0,2,0 und schlägt eine einfache `.kit-dialog{display:…}`-Regel; der Vue-Dialog trägt keine Wurzelklasse `.kit-dialog`, also keine Kollision |
| AK11 „beschreibt die README im Abschnitt „Lösch-Button und Lösch-Dialog“ `<x-kit::dialog-delete>` (Props, Formular `#formDelete` kommt aus der Component, `initLoeschDialog()`)“ | bestanden | README Z. 323–336 gelesen: Props mit Standardwerten, „Das Formular `#formDelete` … kommt aus der Component“, Import und Aufruf `initLoeschDialog()`; Aussagen stimmen mit dem Code (ruft `initLoeschen()` selbst auf) |
| AK11 „der CHANGELOG nennt „Neu: `<x-kit::dialog-delete>` …“ unter „Unveröffentlicht“ mit Verweis `#2298`“ | bestanden | CHANGELOG Z. 4–5 gelesen; Test grün |
| AK12 „die bestehenden Tests `tests/js/loeschen.test.mjs` und die Template-Prüfungen für Theme `kern` … sind grün“ | bestanden | `node --test` 43/43; `KernQuelltextTest`, `BausteineTest` grün |
| AK12 „der Baustein `dialog-delete` ist in der Liste der Bausteine aller drei Themes enthalten“ | bestanden | je Theme 36 Templates inkl. `dialog-delete.blade.php`; `KernQuelltextTest` (35 = bootstrap5 = kern, ohne `partials`) |
| AK12 „die Vue-Insel und `erzeugeLoeschDialog()` bleiben unverändert“ | bestanden | `git diff 6ecdf721..db803fbd --stat -- vue vue3 js/loeschen.js` zeigt nur `vue3/kit.css` |
| Randfall „Mehrere `data-kit-delete`-Buttons und ein Dialog: gilt die zuletzt geklickte ID“ | bestanden | Browser: 5 öffnen/abbrechen, 7 öffnen → `/kunden/7?x=1` |
| Randfall „Zwei Dialoge auf einer Seite sind kein Ziel“ | bestanden | nicht gebaut (feste Überschrift-id, ein `#formDelete`) |
| Randfall „`route` mit Query (`/kunden/0?x=1`): Query bleibt“ | bestanden | JS-Test und Browser |
| Nicht-Ziel „Umstellung von userauth und kunden.lan“ | bestanden | Diff berührt nur laravel-kit |
| Nicht-Ziel „Änderungen an Vue-Insel, Theme-Dateien außer `kit.css`“ | bestanden | siehe AK12 |
| Vertrag „Keine neuen npm- oder Composer-Pakete“ | bestanden | kein `package.json`/`composer.json` im Diff |
| Kopf „Risiko: normal“ | nicht bestanden | Der Diff bestimmt das Ziel einer DELETE-Anfrage (`action` aus Route + ID, AK7/AK8). Ein Fehler hier löscht den falschen Datensatz → Bereich „Löschen bzw. Datenverlust“ (`/neues-feature` 1.4, „im Zweifel: kritisch“); die Zeile nennt keinen Grund |

## Sicherheit
- Ausgabe-Escaping: alle Props über `{{ }}`, auch `data-route` und `data-props` (eigene Sonde mit `<`, `&`, `"`, `'`). Kein `{!! !!}`.
- CSRF: `@csrf` im Formular, `@method('DELETE')`. Autorisierung des Löschens liegt beim Host-Controller (unverändert, nicht Teil des Pakets).
- JS setzt nur `action` aus `loeschRoute()` (ID per `encodeURIComponent`); kein `innerHTML`.
- Fehlerpfade im Browser geprüft: Dialog ohne Ereignis geöffnet (nur `hidden` entfernt) → „Löschen“ sendet nichts und schließt; `#formDelete` fehlt → kein Fehler, nichts gesendet.

## Regressionen
- Feature: `tests/Feature/KitComponents/DialogDeleteTest.php`, `KernQuelltextTest.php`, `BausteineTest.php` — 483 grün; JS `tests/js/loeschDialog.test.mjs`, `tests/js/loeschen.test.mjs` — 43/43.
- Tests der berührten Dateien (`git grep` auf kit.css, dialog-delete, README, CHANGELOG): `ComboboxOhneJqueryUiTest`, `DatatableDeJsonTest`, `DesignTokensReadmeTest`, `KitComponents/AuslaufDokuTest`, `KitComponents/ReadmeTest`, `Logs/LogZeitplanDokuTest`, `Vue3/Vue3FelderQuelltextTest`, `Vue3/Vue3LogikQuelltextTest`, `Vue3/Vue3QuelltextTest`, `Vue3/Vue3ReadmeInselnTest`, `Unit/BrowserOhneServerTest`, `Unit/HButtonEscapeTest`, `Unit/MyDateTimeUnvollstaendigTest` — 393 grün; `tests/js/datatableAktionen.test.mjs` 11/11.
- Ganze Suite nicht gelaufen (kein `SUITE: ja`).

## Funde: 1 muss, 3 sollte, 3 kann
- muss: Risiko kritisch nötig — Spec-Kopf `Risiko: normal` ohne Grund, obwohl die Component das Ziel der DELETE-Anfrage festlegt (Löschen/Datenverlust). Folge: Spec-Kopf auf `Risiko: kritisch — <Grund>`, `Rechtegrenze: entfällt — <Grund>` (Rechte prüft der Host-Controller), Tests nach Umfang „kritisch“ (Fehlerpfad der Aussage „das Formular wurde nicht gesendet“: Öffnen ohne ID, fehlendes `#formDelete`, leere ID). Die Fehlerpfade habe ich im Browser bereits als gutartig gesehen (siehe Sicherheit); es fehlen Einstufung und Tests.
- sollte: Fokusführung — nach dem Öffnen bleibt der Fokus auf dem Auslöser hinter dem Overlay (gemessen: `document.activeElement` = Lösch-Button), keine Rückgabe beim Schließen, keine Fokusfalle trotz `aria-modal="true"` (nicht in der Spec) (→ Folge-Task #2435)
- sollte: README-Listen unvollständig — Tabelle der Kern-Templates, „Verfügbare Kit-Components“ und die Liste der Skripte nennen `dialog-delete` bzw. `js/loesch-dialog.js` nicht; Hinweis, dass `vue3/kit.css` geladen sein muss, fehlt (→ #2435)
- sollte: `tests/js/loeschDialog.test.mjs` baut das Markup in `baue()` selbst nach; eine Änderung an Selektoren im Blade-Template bliebe in den JS-Tests grün. AK6–AK9 sind nur durch meinen Browserlauf mit gerendertem Markup voll belegt (→ #2435)
- kann: Das Formular `#formDelete` steht in der Component statt (wie im Vertrag) in den Theme-Templates. Ausgabe identisch, AK2/AK5 erfüllt; für userauth/kunden.lan sogar günstiger (eine Stelle). Vertragstext bei Gelegenheit anpassen.
- kann: Hinweis für die Umstellung: userauth `src/Resources/views/partials/loeschen.blade.php` und kunden.lan `include/dialogdelete.blade.php` haben ein eigenes `#formDelete`; beim Wechsel auf die Component dort entfernen, sonst doppelte IDs und `querySelector` nimmt das erste Formular.
- kann: Der Escape-Listener liegt je Wurzel am `document` und wird nie entfernt; bei einem Dialog pro Seite folgenlos.

## Neue Tasks: #2435 Lösch-Dialog (Blade) – Nacharbeit aus Review loesch-dialog-blade

## Fehlerprotokoll
- {"schwere":"muss","ak":"","fund":"Spec mit Risiko: normal ohne Grund, obwohl die Component die action der DELETE-Anfrage aus Route und ID bildet","ursache":"Risiko-Einstufung nur nach Code-Art (Blade/CSS/JS) statt nach fachlicher Wirkung (falsche ID = falscher Datensatz gelöscht)","anweisung":"Bestimmt ein Feature Ziel oder Auslösung einer Lösch-, Zahlungs- oder Login-Aktion, Risiko: kritisch mit Grund setzen, auch wenn nur UI-Code geändert wird"}
- {"schwere":"sollte","ak":"","fund":"Blade-Dialog mit aria-modal ohne Fokus beim Öffnen, ohne Rückgabe und ohne Fokusfalle","ursache":"Spec nennt nur Rollen und Attribute, nicht das Fokusverhalten","anweisung":"Bei modalen Dialogen Fokus beim Öffnen hinein, beim Schließen zurück an den Auslöser und Tab im Dialog halten, auch wenn die Spec es nicht nennt"}
- {"schwere":"sollte","ak":"AK11","fund":"README-Tabelle der Kern-Templates, Component-Liste und Skript-Liste ohne dialog-delete und loesch-dialog.js","ursache":"Nur der im AK genannte Abschnitt wurde ergänzt","anweisung":"Bei neuem Baustein jede Übersichtsliste der README durchsuchen (grep nach einem Geschwister-Baustein) und mitpflegen"}
- {"schwere":"sollte","ak":"AK6","fund":"JS-Tests bauen das Dialog-Markup selbst nach statt das gerenderte Blade-Template zu nutzen","ursache":"Test für JS und Template getrennt, kein Test koppelt Selektoren an das Template","anweisung":"Wenn JS ein Blade-Template über data-Attribute bindet, die Selektoren im Template prüfen oder das JS gegen die gerenderte Ausgabe testen"}

## Fazit: nicht freigegeben (nur mit muss)
Funktional sind alle AK1–AK12, Randfälle und Nicht-Ziele erfüllt, auch im echten Browser. Offen ist allein die Risiko-Einstufung (muss): Spec-Kopf auf `kritisch` heben, `Rechtegrenze: entfällt — <Grund>` setzen und die Fehlerpfade testen lassen.
