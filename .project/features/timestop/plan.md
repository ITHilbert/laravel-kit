# Plan: timestop (Bugfix, Dashboard #93)

Branch: `fix/timestop` (Paket laravel-kit) · Spec: `spec.md` · Spec selbst freigegeben (Nachtschicht 2026-10-31)

## Aufgaben
- [x] T1 (AK1–AK3) test-writer (21 Tests, 19 rot: TypeError strtotime mit __PHP_Incomplete_Class, Objekt statt int, ohne Start Zeitstempel statt 0): Regressionstests mit Store `file` und `database`, `serializable_classes = false` — Tests: `tests/Unit/TimeStopCacheTest.php`
- [x] T2 (AK1–AK3) [pro] (vorher [junior] – Verhaltensänderung, Stufe korrigiert) `src/Classes/TimeStop.php`: Startzeit als int speichern, `ende()` rechnet mit int und behandelt fehlende/alte Einträge als 0 — Tests: `tests/Unit/TimeStopCacheTest.php`
- [x] T3 NACHWEIS (Moderator)
- [ ] T4 REVIEW (reviewer, Review-Platz) → `pruefung.md`

Abweichung afbc98885f47d3d3f: Spec ergänzt (bfd7705) — Randfall „`start()` findet eine Altlast …“; Tests dazu (test-writer), vor dem Fix rot (4/4), danach grün.

## Stand
T1–T3 erledigt. NACHWEIS: GATE GRÜN (coder-pro, 07:43, mit gemeldeter Abweichung → Spec ergänzt); vom Moderator erneut: TimeStopCacheTest 25 grün, Paket-Suite 90 grün, Pint grün, Tests nur vom test-writer geändert. Weiter: T4 REVIEW (Review-Platz).
