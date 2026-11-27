# Spec loesch-dialog-blade (#2298)
Risiko: normal
Art: Feature — Blade-Component `<x-kit::dialog-delete>` für den Lösch-Dialog, damit userauth und kunden.lan keine eigenen Kopien pflegen.
Status: freigegeben (Task #2298 Modus `umsetzung`, Joerg/Admin „ja“ im Chat 2026-11-27; Moderator selbst im automode)
Rückbau: entfällt (kein Übergangsstand; Umstellung von userauth und kunden.lan sind eigene Tasks)

## Ziel
Für das Theme `kern` gibt es eine Blade-Component `<x-kit::dialog-delete titel="…" body="…" route="…" />` mit Markup im Kit-Kern-Stil, passender Zustandslogik in `js/loesch-dialog.js` (Funktion `initLoeschDialog()`) und dem Formular `#formDelete` (CSRF, DELETE). Sie ersetzt die zwei Eigenbauten (userauth `partials/loeschen.blade.php`, kunden.lan `include/dialogdelete.blade.php`). Die Vue-Insel `dialog-delete` und `erzeugeLoeschDialog()` bleiben unverändert.

## Vertrag
- **Component:** `src/Views/components/kit/dialog-delete.blade.php` löst über `KitView::fuer('dialog-delete')` auf. Props: `titel` (Standard `Löschen`), `body` (Standard `Diesen Eintrag wirklich löschen?`), `route` (Pflicht; endet auf ein Pfadsegment, das durch die ID ersetzt wird). Alle Werte werden escaped.
- **Theme `kern`** (`src/Views/kit/kern/dialog-delete.blade.php`): Wurzel `div.kit-dialog` mit `data-kit-delete-dialog`, `data-route="<route>"` und Attribut `hidden`. Darin `div.kit-dialog__overlay` (`data-kit-delete-abbrechen`) und `div.kit-dialog__content` (`role="dialog"`, `aria-modal="true"`, `aria-labelledby` auf die `id` der Überschrift) mit `h2.kit-dialog__title`, `p` mit dem Text, zwei `button.kit-button` (`type="button"`): `data-variant="cancel"` mit `data-kit-delete-abbrechen` („Abbrechen“) und `data-variant="delete"` mit `data-kit-delete-bestaetigen` („Löschen“). Danach `form#formDelete` (`method="POST"`, ohne `action`) mit `@csrf` und `@method('DELETE')`. Klassenregel aus Spec blade-kern: nur `kit-…` und Zustände.
- **Themes `bootstrap5` und `tailwind`** (eingefroren): Das Template gibt die Vue-Insel aus, `div` mit `data-vue="dialog-delete"` und `data-props` (JSON mit `titel`, `body`, `route`), danach dasselbe Formular `#formDelete`.
- **JS** `js/loesch-dialog.js`: `initLoeschDialog(root = document)` bindet jedes `[data-kit-delete-dialog]` unter `root` einmal. Es nutzt `initLoeschen()`, `onLoeschen()` und `erzeugeLoeschDialog()` aus `js/loeschen.js` (Zustandslogik nicht kopieren). Offen/zu steht am Attribut `hidden` der Wurzel.
- **CSS:** `vue3/kit.css` macht den Dialog sichtbar, wenn er nicht `hidden` ist (Overlay deckt die Seite, Inhalt zentriert); Optik wie der bestehende `kit-dialog`.
- **Keine neuen npm- oder Composer-Pakete.** `js/loeschen.js` ändert sein Verhalten nicht.

## Akzeptanzkriterien
- **AK1** Gegeben Theme `kern`, Wenn `<x-kit::dialog-delete titel="Kunde löschen" body="Wirklich löschen?" route="/kunden/0" />` rendert, Dann gibt es `div.kit-dialog` mit `data-kit-delete-dialog`, `data-route="/kunden/0"` und `hidden`, darin `div.kit-dialog__content` (`role="dialog"`, `aria-modal="true"`, `aria-labelledby` zeigt auf die `id` von `h2.kit-dialog__title` mit „Kunde löschen“), den Text „Wirklich löschen?“ und genau zwei Buttons `button.kit-button[type=button]`: „Abbrechen“ (`data-variant="cancel"`, `data-kit-delete-abbrechen`) und „Löschen“ (`data-variant="delete"`, `data-kit-delete-bestaetigen`).
- **AK2** Gegeben Theme `kern`, Wenn die Component rendert, Dann gibt es genau ein `form#formDelete` mit `method="POST"`, einem `_token`-Feld, `<input name="_method" value="DELETE">` und ohne `action`.
- **AK3** Gegeben Theme `kern` und `titel="<b>X</b>"`, `body="a & b"`, Wenn sie rendert, Dann erscheinen die Werte als Text (escaped), nicht als Markup.
- **AK4** Gegeben Theme `kern` ohne `titel` und `body`, Wenn sie rendert, Dann zeigt der Dialog „Löschen“ als Titel und „Diesen Eintrag wirklich löschen?“ als Text.
- **AK5** Gegeben Theme `bootstrap5` oder `tailwind`, Wenn die Component rendert, Dann gibt es `div[data-vue="dialog-delete"]` mit `data-props`, dessen JSON `titel`, `body` und `route` enthält, und danach `form#formDelete` wie in AK2; kein `data-kit-delete-dialog`.
- **AK6** Gegeben die Component aus AK1 und `initLoeschDialog()`, Wenn ein Element mit `data-kit-delete="5"` angeklickt wird, Dann verliert die Wurzel `hidden` (Dialog offen).
- **AK7** Gegeben der offene Dialog aus AK6, Wenn „Löschen“ (`data-kit-delete-bestaetigen`) angeklickt wird, Dann bekommt `#formDelete` die `action` `/kunden/5` (letztes Pfadsegment der Route durch die ID ersetzt, Query bleibt), das Formular wird gesendet und der Dialog ist wieder `hidden`.
- **AK8** Gegeben der offene Dialog aus AK6, Wenn „Abbrechen“, das Overlay oder die Taste Escape betätigt wird, Dann ist der Dialog `hidden`, und das Formular wurde nicht gesendet. Danach öffnet ein Klick auf `data-kit-delete="7"` den Dialog, und „Löschen“ sendet an `/kunden/7` (die ID von 5 ist vergessen).
- **AK9** Gegeben `initLoeschDialog()` wird zweimal aufgerufen (auch mit demselben `root`), Wenn „Löschen“ geklickt wird, Dann wird das Formular genau einmal gesendet. Gegeben die Seite hat keinen `[data-kit-delete-dialog]`, Wenn `initLoeschDialog()` läuft, Dann gibt es keinen Fehler.
- **AK10** Gegeben `vue3/kit.css`, Wenn sie gelesen wird, Dann gibt es Regeln, die `.kit-dialog[hidden]` ausblenden und ein sichtbares `.kit-dialog` als feste, die Seite deckende Ebene mit zentriertem `.kit-dialog__content` darstellen.
- **AK11** Gegeben README und CHANGELOG des Kits, Wenn gelesen wird, Dann beschreibt die README im Abschnitt „Lösch-Button und Lösch-Dialog“ `<x-kit::dialog-delete>` (Props, Formular `#formDelete` kommt aus der Component, `initLoeschDialog()`), und der CHANGELOG nennt „Neu: `<x-kit::dialog-delete>` …“ unter „Unveröffentlicht“ mit Verweis `#2298`.
- **AK12** Gegeben die bestehenden Tests `tests/js/loeschen.test.mjs` und die Template-Prüfungen für Theme `kern` (Quelltext, Anzahl der Bausteine), Wenn sie laufen, Dann sind sie grün (der Baustein `dialog-delete` ist in der Liste der Bausteine aller drei Themes enthalten; die Vue-Insel und `erzeugeLoeschDialog()` bleiben unverändert).

## Randfälle
- Mehrere `data-kit-delete`-Buttons und ein Dialog: gilt die zuletzt geklickte ID.
- Zwei Dialoge auf einer Seite sind kein Ziel (ein Dialog pro Seite, wie bei der Insel).
- `route` mit Query (`/kunden/0?x=1`): Query bleibt (Logik aus `loeschRoute`).

## Nicht-Ziele
- Umstellung von userauth und kunden.lan (eigene Tasks, nach Release).
- Änderungen an Vue-Insel, Theme-Dateien außer `kit.css`.

## Entscheidungsspielraum
IDs der Überschrift, Klassen innerhalb der Regeln, Aufbau des Adapters zwischen `hidden`-Attribut und `erzeugeLoeschDialog()`, Prüfseite `/ui-kit-blade-kern` ergänzen (optional).
