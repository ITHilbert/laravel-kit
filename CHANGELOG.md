# Changelog

## Unreleased
- `db:backup-seeders`: ein `iseed`-Aufruf je Tabelle mit `--orderby` und `--skip-fk-checks`, `migrations` nicht mehr exportiert, Sperre von `db:seed` außerhalb `local`/`testing` im `DatabaseSeeder`, Exit-Code 1 bei Fehlern.
- `install:iSeed`: `composer require --dev orangehill/iseed`.
