# Plan log-taeglich
laravel-kit `feature/log-taeglich` (von `main` 2cf8248e). Dashboard #1716.
Spec: [spec.md](spec.md) — freigegeben (Joerg, Chat 2026-11-19).

## Aufgaben
- [ ] TW test-writer (AK1–AK14, Randfälle 1–7) — Tests:
  - `tests/Feature/Logs/LogTaeglichTest.php` (AK1–AK7, Randfälle 6, 7)
  - `tests/Feature/Logs/LogPruneCommandTest.php` (AK8–AK12, Randfälle 1–5)
  - `tests/Feature/Logs/LogZeitplanDokuTest.php` (AK13, AK14)
- [ ] T1 (AK1–AK7) [pro] Config `logs` in `src/Config/laravelkit.php`, Umstellung `single` → `daily` im ServiceProvider — Tests: `tests/Feature/Logs/LogTaeglichTest.php`
- [ ] T2 (AK8–AK14) [pro] Action für das Löschen, Command `log:prune`, Eintrag im Scheduler, README, CHANGELOG — Tests: `tests/Feature/Logs/LogPruneCommandTest.php`, `tests/Feature/Logs/LogZeitplanDokuTest.php`

Bestehende Tests: `tests/Feature/BootTest.php`, `tests/Feature/LogMigrationOptionalTest.php`, `tests/Feature/BackupSeedersTest.php`
Marketing: entfällt (keine Seite).
Design: entfällt (keine Oberfläche).
Review: nötig (Risiko kritisch: löscht Dateien, Querschnitt über alle Projekte).

## Stand
Spec freigegeben. Nächster Schritt: test-writer (nach #1715).
