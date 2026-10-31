# Plan: zeitzone (Bugfix, Dashboard #92)

Branch: `fix/zeitzone` (Paket laravel-kit) · Spec: `spec.md` · Spec selbst freigegeben (Nachtschicht 2026-10-31)

## Aufgaben
- [ ] T1 (AK1–AK3) test-writer: Regressionstests Zeitzone und `HForm::date()` mit Wert — Tests: `tests/Unit/MyDateTimeZeitzoneTest.php`
- [ ] T2 (AK1–AK3) [pro] `date_default_timezone_set` aus `src/Helpers/MyDateTime.php` entfernen, Import in `src/Helpers/HForm.php` auf `ITHilbert\LaravelKit\Helpers\MyDateTime` korrigieren; Kommentar im Kopf von `tests/Unit/MyDateTimeTest.php` ist dann veraltet (meldet der Coder, ändert der test-writer); README-Hinweis „MyDateTime nutzt die Zeitzone der App“ — Tests: `tests/Unit/MyDateTimeZeitzoneTest.php`
- [ ] T3 NACHWEIS (Moderator)
- [ ] T4 REVIEW (reviewer, Review-Platz) → `pruefung.md`

## Stand
Plan angelegt.
