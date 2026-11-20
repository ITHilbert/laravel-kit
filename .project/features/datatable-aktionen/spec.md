# Spec datatable-aktionen
Risiko: normal (Frontend-Komponente im Kit, keine Route, keine Migration; der Server-HTML-Inhalt der Aktionsspalte ist Opt-in und kommt vom Konsumenten wie `html: true`)
Art: Feature — #1794 (Fund aus consentLaw `admin-listen` #1785, 2026-11-20). Erweitert die Komponente `vue/tailwind/datatable/DataTable.vue` (Dokument `.project/doku/vue-datatable.md`).
Status: selbst freigegeben (automode torrent-packages_dev, 2026-11-20; Task #1794 ist von Joerg freigegeben; Annahmen siehe plan.md).
Rückbau: entfällt (kein Übergangsstand; die Hülle `DataTableInsel.vue` in consentLaw entfällt durch einen Folge-Task dort)

## Ziel
Yajra-Listen brauchen zwei Dinge, die heute in einer Hülle um die Komponente liegen:
1. Prop `aktionsSpalte`: Name eines Datenfelds mit fertigem Aktions-HTML vom Server (z. B. Yajra `addColumn('action', …)` mit `HButton`). Die Komponente hängt es als letzte Spalte an.
2. Prop `zeilenLink`: URL-Vorlage mit `:id`. Ein Klick auf eine Zeile öffnet die URL.

Die Logik liegt in reinen ES-Modulen (`columnDefs.mjs`, `optionen.mjs`, neu `zeilenLink.mjs`), prüfbar per `node --test`.

## Schnittstelle
- `buildColumnDefs(columns, hasActions, aktionsSpalte?)` und `baueOptionen(eingabe, …)` mit `eingabe.aktionsSpalte` (string|undefined).
- Neu `vue/tailwind/datatable/zeilenLink.mjs`, Exporte: `zeilenUrl(vorlage, daten)` → string|null und `klickIstZeilenKlick(ziel)` → boolean.

## Akzeptanzkriterien
- **AK1** Gegeben `aktionsSpalte = 'action'`, Wenn `buildColumnDefs` die Definitionen baut, Dann ist die letzte Definition `{ data: 'action', orderable: false, searchable: false }` und ihr Zellwert erscheint unverändert als HTML (keine escapende `render`-Funktion).
- **AK2** Gegeben `aktionsSpalte` ist nicht gesetzt, Wenn `buildColumnDefs` läuft, Dann ist das Ergebnis identisch zu bisher (Spalten und, falls `hasActions`, die Standard-Aktionsspalte).
- **AK3** Gegeben `aktionsSpalte` und `hasActions = true`, Wenn `buildColumnDefs` läuft, Dann gibt es nur die Server-Aktionsspalte, nicht zusätzlich die Standard-Aktionsspalte (Server-HTML ersetzt die Standard-Buttons).
- **AK4** Gegeben `baueOptionen` mit `aktionsSpalte`, Wenn die Optionen gebaut werden, Dann enthält `columns` die Spalte aus AK1 (und `datatableOptions.columns` überschreibt sie weiterhin nicht).
- **AK5** Gegeben `zeilenLink = '/users/:id/edit'` und Zeilendaten `{ id: 7 }`, Wenn `zeilenUrl` aufgerufen wird, Dann ist das Ergebnis `/users/7/edit`; jedes `:id` der Vorlage wird ersetzt, die ID wird URL-kodiert (`a/b` → `a%2Fb`).
- **AK6** Gegeben Zeilendaten ohne `id` (fehlt, `null`, leer) oder eine leere Vorlage, Wenn `zeilenUrl` aufgerufen wird, Dann ist das Ergebnis `null` (kein Link, kein Sprung auf `…/undefined`).
- **AK7** Gegeben ein Klick, dessen Ziel ein `a`, `button`, `input`, `select`, `textarea`, `label` oder ein Element in einem solchen ist, Wenn `klickIstZeilenKlick(ziel)` prüft, Dann ist das Ergebnis `false`; für andere Ziele (z. B. `td`, `span` in einer Zelle) `true`.
- **AK8** Gegeben `DataTable.vue`, Wenn man den Quelltext liest, Dann deklariert sie die Props `aktionsSpalte` und `zeilenLink`, nutzt `zeilenUrl` und `klickIstZeilenKlick` aus `./zeilenLink.mjs` für einen Klick-Handler auf dem `tbody`, ruft die ID über `dtInstance.row(tr).data()` ab und setzt für die Server-Aktionsspalte eine Kopfzelle (Beschriftung `actionsLabel`) und eine leere Filterzelle; `hasActions` ist bei gesetzter `aktionsSpalte` falsch.
- **AK9** Gegeben die Doku `.project/doku/vue-datatable.md`, Wenn man sie liest, Dann beschreibt sie beide Props mit Beispiel (Yajra `addColumn('action', …)` mit `HButton`, `zeilenLink="/users/:id/edit"`).

## Randfälle
1. Zeilen mit `zeilenLink` bekommen `cursor-pointer` nur, wenn die Prop gesetzt ist.
2. Kopf- und Filterzeile zählen nicht als Zeile (Handler nur auf `tbody`).
3. CHANGELOG „Unveröffentlicht“ nennt beide Props.

## Nicht-Ziele
- Kein Server-HTML-Sanitizing (der Konsument verantwortet es, wie bei `html: true`).
- Kein Tastatur-Link auf Zeilen (Aktionsspalte enthält die echten Links).
- Kein Umbau von `vue/bootstrap5` und `vue3/` DataTable (nur die Tailwind-Komponente, wie die Hülle in consentLaw).

## Entscheidungsspielraum
Der Klick-Handler darf `window.location.assign` nutzen; Ctrl/Meta-Klick (neuer Tab) ist freiwillig.
