# Plan: timestop (Bugfix, Dashboard #93)

Branch: `fix/timestop` (Paket laravel-kit) · Spec: `spec.md` · Spec selbst freigegeben (Nachtschicht 2026-10-31)

## Aufgaben
- [x] T1 (AK1–AK3) test-writer (21 Tests, 19 rot: TypeError strtotime mit __PHP_Incomplete_Class, Objekt statt int, ohne Start Zeitstempel statt 0): Regressionstests mit Store `file` und `database`, `serializable_classes = false` — Tests: `tests/Unit/TimeStopCacheTest.php`
- [ ] T2 (AK1–AK3) [pro] (vorher [junior] – Verhaltensänderung, Stufe korrigiert) `src/Classes/TimeStop.php`: Startzeit als int speichern, `ende()` rechnet mit int und behandelt fehlende/alte Einträge als 0 — Tests: `tests/Unit/TimeStopCacheTest.php`
- [ ] T3 NACHWEIS (Moderator)
- [ ] T4 REVIEW (reviewer, Review-Platz) → `pruefung.md`

## Stand
T1 erledigt. Weiter: T2 [pro].
