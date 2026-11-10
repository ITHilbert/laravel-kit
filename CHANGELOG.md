# Changelog

## 1.6.0 – 2026-11-10
- Änderung: Die Migration der Tabelle `log` lässt sich über die Config `laravelkit.log_tabelle` (Env `LARAVELKIT_LOG_TABELLE=false`) abschalten; Standard bleibt an.

## 1.5.0 – 2026-11-10
- `db:backup-seeders`: ein `iseed`-Aufruf je Tabelle mit `--orderby` und `--skip-fk-checks`, `migrations` nicht mehr exportiert, Sperre von `db:seed` außerhalb `local`/`testing` im `DatabaseSeeder`, Exit-Code 1 bei Fehlern.
- `install:iSeed`: `composer require --dev orangehill/iseed`.
