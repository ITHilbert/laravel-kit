# Plan log-taeglich
laravel-kit `feature/log-taeglich` (von `main` 2cf8248e). Dashboard #1716.
Spec: [spec.md](spec.md) — freigegeben (Joerg, Chat 2026-11-19).

## Aufgaben
- [x] TW (test-writer a90bf8e12c3594f88, rot: 36/39 — „14 matches expected 45/30“, „null is identical to 'daily'“, Tagesdatei fehlt, „The command \"log:prune\" does not exist“ (14), README/CHANGELOG ohne Log-Abschnitt; 3 grün = Wächter AK5/Stack ohne `single`; grünbar 19/19, 14/14, 6/6; Hilfs-Trait `tests/Feature/Logs/ConfigMitEnv.php` für T1 und T2) test-writer (AK1–AK14, Randfälle 1–8) — Tests:
  - `tests/Feature/Logs/LogTaeglichTest.php` (AK1–AK7, Randfälle 6, 7)
  - `tests/Feature/Logs/LogPruneCommandTest.php` (AK8–AK12, Randfälle 1–5)
  - `tests/Feature/Logs/LogZeitplanDokuTest.php` (AK13, AK14)
- [x] T1 (zusammengelegt T1+T2: coder-pro a22a3a65c6d5a49f7, GATE GRÜN: 82 passed, Pint ok) (AK1–AK7) [pro] Config `logs` in `src/Config/laravelkit.php`, Umstellung `single` → `daily` im ServiceProvider — Tests: `tests/Feature/Logs/LogTaeglichTest.php`
- [x] T2 (zusammengelegt T1+T2: coder-pro a22a3a65c6d5a49f7) (AK8–AK14) [pro] Action für das Löschen, Command `log:prune`, Eintrag im Scheduler, README, CHANGELOG — Tests: `tests/Feature/Logs/LogPruneCommandTest.php`, `tests/Feature/Logs/LogZeitplanDokuTest.php`

Bestehende Tests: `tests/Feature/BootTest.php`, `tests/Feature/LogMigrationOptionalTest.php`, `tests/Feature/BackupSeedersTest.php`
Marketing: entfällt (keine Seite).
Design: entfällt (keine Oberfläche).
Review: nötig (Risiko kritisch: löscht Dateien, Querschnitt über alle Projekte).

## Stand
Tests rot committet. Randfall 8 (leeres `LARAVELKIT_LOGS_TAGE` → 30) vom Moderator ergänzt, von Joerg bestätigt (2026-11-19); Zeitpunkt 03:17 von Joerg ok. T1+T2 umgesetzt (zusammengelegt, teilen Config und ServiceProvider). Nächster Schritt: Review.

NACHWEIS: GATE GRÜN a22a3a65c6d5a49f7 (82 passed). Echtlauf im Host (`php artisan`, Kit-Arbeitsstand): `logging.default` stack, `stack.channels` ["daily"], `daily.days` 30; `Log::info` → `storage/logs/laravel-2026-11-19.log` (1 Treffer), `laravel.log` unverändert (16:44 vom 18.11.); `log:prune --dry-run` → „würde gelöscht: browser.log“, „1 Dateien würde gelöscht“, Exit 0, nichts gelöscht; `log:prune --days=0` → Fehler mit „--days“, Exit 1; `schedule:list` → `17 3 * * * php artisan log:prune`; nach `config:cache` stack.channels weiter ["daily"] (Randfall 6), danach `config:clear`.
Abweichung a22a3a65c6d5a49f7: Fehlalarm: keine gemeldet
Offener Punkt a22a3a65c6d5a49f7: AK-fremd (Umstellung in boot() statt register(): Paket-Provider booten vor den App-Providern, Kanäle entstehen erst beim ersten Log-Aufruf — Echtlauf und Randfall 6 bestätigen; Zeitpunkt 03:17 frei gewählt, Spec sagt „täglich“; Ordner `src/Logs/` für die Action = Entscheidungsspielraum; bei `taeglich=false` bleibt auch `days` unverändert = AK5 „unverändert“; Pint im Submodul läuft nur im Paketordner — GATE-Hinweis, kein AK)
