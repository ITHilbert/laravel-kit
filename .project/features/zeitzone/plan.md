# Plan: zeitzone (Bugfix, Dashboard #92)

Branch: `fix/zeitzone` (Paket laravel-kit) · Spec: `spec.md` · Spec selbst freigegeben (Nachtschicht 2026-10-31)

## Aufgaben
- [x] T1 (AK1–AK3) test-writer (16 Tests, 11 rot: Zeitzone wird Europe/Berlin, HForm Class not found; AK2 auf getDateTimeISO korrigiert): Regressionstests Zeitzone und `HForm::date()` mit Wert — Tests: `tests/Unit/MyDateTimeZeitzoneTest.php`
- [x] T2 (AK1–AK3) [pro] `date_default_timezone_set` aus `src/Helpers/MyDateTime.php` entfernen, Import in `src/Helpers/HForm.php` auf `ITHilbert\LaravelKit\Helpers\MyDateTime` korrigieren; Kommentar im Kopf von `tests/Unit/MyDateTimeTest.php` ist dann veraltet (meldet der Coder, ändert der test-writer); README-Hinweis „MyDateTime nutzt die Zeitzone der App“ — Tests: `tests/Unit/MyDateTimeZeitzoneTest.php`
- [x] T3 NACHWEIS (Moderator)
- [ ] T4 REVIEW (reviewer, Review-Platz) → `pruefung.md`

## Stand
T1–T3 erledigt. NACHWEIS: GATE GRÜN (coder-pro, 07:38); vom Moderator erneut: MyDateTimeZeitzoneTest 16 grün, Paket-Suite 81 grün, Pint der geänderten Dateien grün, Tests seit 2883451 unverändert. Echter Aufruf: `HForm::date('lieferdatum', '2026-03-15 10:00:00')` → `value="15.03.2026"`, Zeitzone bleibt `UTC`, Zeitstempel 1767225600. Pint-Umformatierung als eigener Commit vom Fix getrennt. README-Satz vom Moderator präzisiert (fest eingetragenes `'UTC'` ignoriert `APP_TIMEZONE`). Offen: Kopfkommentar `tests/Unit/MyDateTimeTest.php` veraltet (test-writer); 32 Pint-Altfunde in anderen Dateien des Kits. Weiter: T4 REVIEW (Review-Platz).
