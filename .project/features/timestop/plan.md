# Plan: timestop (Bugfix, Dashboard #93)

Branch: `fix/timestop` (Paket laravel-kit) · Spec: `spec.md` · Spec selbst freigegeben (Nachtschicht 2026-10-31)

## Aufgaben
- [ ] T1 (AK1–AK3) test-writer: Regressionstests mit Store `file` und `database`, `serializable_classes = false` — Tests: `tests/Unit/TimeStopCacheTest.php`
- [ ] T2 (AK1–AK3) [junior] `src/Classes/TimeStop.php`: Startzeit als int speichern, `ende()` rechnet mit int und behandelt fehlende/alte Einträge als 0 — Tests: `tests/Unit/TimeStopCacheTest.php`
- [ ] T3 NACHWEIS (Moderator)
- [ ] T4 REVIEW (reviewer, Review-Platz) → `pruefung.md`

## Stand
Plan angelegt.
