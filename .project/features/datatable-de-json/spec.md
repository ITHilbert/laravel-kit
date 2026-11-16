# Spec datatable-de-json (#1344)
Risiko: normal
Art: Bugfix/Spec-Lücke — userauth 3 lädt `vendor/laravelkit/DataTable_DE.json`; laravel-kit 1.6.0 liefert die Datei nicht mehr und kein Publish-Tag dafür.

## Akzeptanzkriterien
- **AK1** Gegeben das Paket, Wenn `src/Publish/Public/laravelkit/DataTable_DE.json` gelesen wird, Dann existiert die Datei und ist gültiges JSON mit dem Schlüssel `sEmptyTable` = „Keine Daten in der Tabelle vorhanden“.
- **AK2** Gegeben der ServiceProvider, Wenn `ServiceProvider::pathsToPublish(LaravelKitServiceProvider::class, 'laravelkit-assets')` abgefragt wird, Dann enthält das Ergebnis die Quelle `src/Publish/Public/laravelkit/DataTable_DE.json` (bzw. das Verzeichnis `…/laravelkit`) mit Ziel `public/vendor/laravelkit`.
- **AK3** Gegeben die README, Wenn nach `laravelkit-assets` gesucht wird, Dann steht der Publish-Tag samt Befehl `php artisan vendor:publish --tag=laravelkit-assets` dort.

## Randfälle
- Der bestehende Tag `public` bleibt unverändert (keine Verhaltensänderung).

## Nicht-Ziele
- Keine weiteren Assets (datatables.js, CSS) zurückholen.

## Entscheidungsspielraum
Datei unverändert aus Commit 8dda0865^ wiederherstellen; Tag `laravelkit-assets` zusätzlich zu `public`.
