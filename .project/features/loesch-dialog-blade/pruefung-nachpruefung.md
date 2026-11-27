# Prüfung: loesch-dialog-blade — 2026-11-27 (Nachprüfung seit 34c2fecf)
Geprüft: laravel-kit `34c2fecf..c028e903` (3 Commits: Fehlerpfad-Tests und Spec-Lücke AK13, roter Test AK13, Fix in `js/loesch-dialog.js`). Diff: `spec.md` (+AK13), `plan.md`, `js/loesch-dialog.js` (+5/−1), `tests/js/loeschDialog.test.mjs` (+3 Tests). Eigene Browser-Sonde neu gebaut: echte Blade-Ausgabe (`<x-kit::button-delete value="">`, `value="5"`, `<x-kit::dialog-delete … route="/kunden/0?x=1">`, gerendert per `php artisan tinker --execute`, nur lesend) mit `vue3/kit.css`, `js/loesch-dialog.js`, `js/loeschen.js` in eigenem Headless Chrome (Port 0, Profil im Scratchpad), echte CDP-Mausklicks (`scratchpad/ld-sonde2/sonde.mjs`).

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| Kopf „Risiko: kritisch — die Component legt fest, an welche Route die DELETE-Anfrage geht (Löschen, Datenverlust bei falscher ID oder Route)“ | bestanden | Spec Z. 2, mit Grund (Fund der ersten Prüfung behoben) |
| Kopf „Rechtegrenze: entfällt — Rechte prüft der Host-Controller der Lösch-Route, die Component sendet nur das Formular“ | bestanden | Spec Z. 3; `php .claude/hooks/abdeckung.php grenze <spec>` Exit 0. Begründung trägt: das Paket hat keine Lösch-Route und keinen Controller, die Component setzt nur `action` und sendet mit CSRF |
| AK13 „Wenn ein Element mit `data-kit-delete=""` (leere ID) angeklickt wird, Dann öffnet der Dialog nicht“ | bestanden | JS-Test `leere ID öffnet den Dialog nicht …` grün; Browser: Klick auf gerendertes `<x-kit::button-delete value="">` (`data-kit-delete=""`) → `hidden=true`, `display:none`; ebenso `data-kit-delete=" "` (nur Leerzeichen) |
| AK13 „und es wird nichts gesendet“ | bestanden | JS-Test (`gesendet` = `[]` nach Klick auf „Löschen“); Browser: nach Klick auf leere ID und JS-Klick auf „Löschen“ Sendeliste leer (vor dem Fix ging es an `/kunden/`, laut Test-Meldung) |
| AK13 „ein danach angeklicktes `data-kit-delete="5"` öffnet ihn“ | bestanden | JS-Test (Gegenprobe); Browser: `hidden=false`, `position:fixed`, Inhalt mittig (600/357) |
| AK13 „und sendet wie in AK7“ | bestanden | JS-Test `['/kunden/5']`; Browser: genau einmal `/kunden/5?x=1`, danach `hidden` |
| AK13 „Die Vue-Insel `dialog-delete` und `js/loeschen.js` bleiben unverändert (Fix nur in `js/loesch-dialog.js`)“ | bestanden | `git diff --stat 34c2fecf..HEAD -- vue vue3 js/loeschen.js src` leer; einzige Produktivänderung `js/loesch-dialog.js` |
| Fehlerpfad AK7 (kritisch): ID mit Sonderzeichen | bestanden | neuer JS-Test `eine ID mit Sonderzeichen wird kodiert …`: `a/b?c` → `/kunden/a%2Fb%3Fc?x=1`, mit Gegenprobe Normalfall |
| Fehlerpfad AK8 „und das Formular wurde nicht gesendet“ (immer gültig, auch ohne `#formDelete`) | bestanden | neuer JS-Test `ohne #formDelete …`: kein Fehler, schließt, nichts gesendet, Gegenprobe „Dialog war offen“; Browser: `#formDelete` entfernt, 5 öffnen, „Löschen“ → schließt, Sendeliste unverändert, Konsole ohne Fehler |
| Fehlerpfad „Öffnen ohne ID“ (Dialog ohne Ereignis geöffnet) | bestanden | Kein eigener Test im Blade-Teil; die Logik ist in `tests/js/loeschen.test.mjs` („nach Absenden ist die Ereignis-ID vergessen: alter Weg ohne deleteID sendet nichts“, „deleteID nicht gesetzt oder leer …“) geprüft. Selbst bestätigt im Browser: `hidden` von außen entfernt, „Löschen“ → schließt, nichts gesendet |
| AK1–AK12, Randfälle, Nicht-Ziele, Vertrag | bestanden (Prüfung `34c2fecf`) | vom Fix nicht berührt (Diff nur `js/loesch-dialog.js`, wirkt erst im Ereignis-Callback vor `oeffnenPerEreignis`); Tests AK6–AK9 weiter grün (46/46), PHP 19/19 |

## Sicherheit
Nur Diff des Fixes: Die Prüfung `id === null || undefined || String(id).trim() === ''` liegt vor `oeffnenPerEreignis`, kein neuer DOM-Schreibweg, kein `innerHTML`. Leere und Leerzeichen-IDs öffnen nicht mehr und können daher nie die Sammel-Route (`/kunden/`) treffen. Die ID wird weiter per `encodeURIComponent` kodiert (Test mit `/` und `?`).

## Regressionen
- JS im Paket: `node --test tests/js/loeschDialog.test.mjs tests/js/loeschen.test.mjs` — 46/46 grün.
- PHP im Platz: `php artisan test --compact packages/laravel-kit/tests/Feature/KitComponents/DialogDeleteTest.php packages/laravel-kit/tests/Feature/KitComponents/KernQuelltextTest.php` — 19 grün (211 Assertions).
- Tests der berührten Datei (`git grep -l loesch-dialog -- tests`): `DialogDeleteTest.php`, `loeschDialog.test.mjs` — oben enthalten.
- Ganze Suite nicht gelaufen (kein `SUITE: ja`).

## Funde: 0 muss, 0 sollte, 1 kann
- kann: `loeschRoute()` (in `js/loeschen.js`, bestehender Code, nicht Teil des Fixes) kodiert `.` und `..` nicht weg; `data-kit-delete=".."` ergäbe `/kunden/..?x=1`, das der Browser zu `/?x=1` auflöst. IDs kommen vom Server und sind Zahlen, also praktisch folgenlos; bei Gelegenheit Punkt-Segmente wie leere IDs ablehnen.
- Die sollte-Funde der ersten Prüfung stehen in #2435 (unverändert offen).

## Neue Tasks: keine

## Fehlerprotokoll
- keine

## Fazit: freigegeben
Der muss-Fund ist behoben: `Risiko: kritisch` und `Rechtegrenze: entfällt` stehen mit Grund im Spec-Kopf, die Fehlerpfade (fehlendes `#formDelete`, Sonderzeichen, leere ID) haben Tests mit Gegenprobe, AK13 ist im Test und im echten Browser erfüllt. Nachprüfung statt vollem Review, obwohl die Spec seit `34c2fecf` geändert ist: Die Änderung fügt nur AK13 an (voll geprüft), der Fix ist eine Bedingung in einer Datei ohne neue Klassen, Migration oder geteilte Komponenten, und der Moderator hat den Umfang so beauftragt.
