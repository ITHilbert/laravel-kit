# Plan backup-seeders-only-tables
laravel-kit `feature/backup-seeders-only-tables` (von c54892f3, Kit-Stand im Host). Dashboard #1780.
Spec: [spec.md](spec.md) — selbst freigegeben (automode, Annahme: Warnung statt Fehler bei unbekannter Tabelle in only_tables, Exit 0; umkehrbar).

## Aufgaben
- [ ] TW test-writer (AK1–AK7, Randfälle 1–3) — Tests: `tests/Feature/BackupSeedersOnlyTablesTest.php`
- [ ] T1 [pro] `only_tables` in `src/Config/laravelkit.php` und `LaravelKitBackupSeeders::exportTabellen`, Bereinigung doppelter call-Zeilen nach dem Export, README, CHANGELOG — Tests: `tests/Feature/BackupSeedersOnlyTablesTest.php`

Bestehende Tests: `tests/Feature/BackupSeedersTest.php`, `tests/Feature/BootTest.php`
Marketing: entfällt (keine Seite).
Design: entfällt (keine Oberfläche).
Review: entfällt (Risiko normal, unter 50 Zeilen Produktivcode erwartet, keine neue Route, keine Migration, kein Sicherheitsbezug).

## Stand
Spec geschrieben, TESTS offen.
