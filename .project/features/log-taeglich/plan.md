# Plan log-taeglich
laravel-kit `feature/log-taeglich` (von `main` 2cf8248e). Dashboard #1716.
Spec: [spec.md](spec.md) — freigegeben (Joerg, Chat 2026-11-19).

## Aufgaben
- [x] TW (test-writer a90bf8e12c3594f88, rot: 36/39 — „14 matches expected 45/30“, „null is identical to 'daily'“, Tagesdatei fehlt, „The command \"log:prune\" does not exist“ (14), README/CHANGELOG ohne Log-Abschnitt; 3 grün = Wächter AK5/Stack ohne `single`; grünbar 19/19, 14/14, 6/6; Hilfs-Trait `tests/Feature/Logs/ConfigMitEnv.php` für T1 und T2) test-writer (AK1–AK14, Randfälle 1–8) — Tests:
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
Tests rot committet. Randfall 8 (leeres `LARAVELKIT_LOGS_TAGE` → 30) vom Moderator ergänzt, Joerg zur Bestätigung gemeldet. Nächster Schritt: T1.
