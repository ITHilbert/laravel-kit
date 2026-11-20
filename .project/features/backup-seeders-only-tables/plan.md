# Plan backup-seeders-only-tables
laravel-kit `feature/backup-seeders-only-tables` (von c54892f3, Kit-Stand im Host). Dashboard #1780.
Spec: [spec.md](spec.md) — selbst freigegeben (automode, Annahme: Warnung statt Fehler bei unbekannter Tabelle in only_tables, Exit 0; umkehrbar).

## Aufgaben
- [x] TW (test-writer a1a128fce51c6923a, rot 8/9: only_tables ignoriert, Config-Schlüssel fehlt, doppelte Zeile bleibt; AK7 schon grün als Wächter; Randfall 3 nicht prüfbar, Sichtung im NACHWEIS; grünbar: entfällt) test-writer (AK1–AK7, Randfälle 1–3) — Tests: `tests/Feature/BackupSeedersOnlyTablesTest.php`
- [x] T1 (coder-pro ae3248155312d6329, GATE GRÜN: 46 passed, Pint ok) [pro] `only_tables` in `src/Config/laravelkit.php` und `LaravelKitBackupSeeders::exportTabellen`, Bereinigung doppelter call-Zeilen nach dem Export, README, CHANGELOG — Tests: `tests/Feature/BackupSeedersOnlyTablesTest.php`

Bestehende Tests: `tests/Feature/BackupSeedersTest.php`, `tests/Feature/BootTest.php`
Marketing: entfällt (keine Seite).
Design: entfällt (keine Oberfläche).
Review: entfällt (Risiko normal, unter 50 Zeilen Produktivcode erwartet, keine neue Route, keine Migration, kein Sicherheitsbezug).

## Stand
Umgesetzt. NACHWEIS: GATE GRÜN ae3248155312d6329; selbst erneut 35 passed (OnlyTables, BackupSeeders, Boot); README und CHANGELOG nennen only_tables (Randfall 3 gesichtet). Echtlauf: entfällt (kein UI, Befehl nur im Fake-iseed-Test; Echtlauf mit realem iseed im Kit-Paket nicht möglich, da orangehill/iseed dort nicht installiert).
Abweichung ae3248155312d6329: Fehlalarm: keine gemeldet
Offener Punkt ae3248155312d6329: keine gemeldet
