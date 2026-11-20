# Plan hbutton-svg
laravel-kit `fix/hbutton-svg` (von eacf9c8f). Dashboard #1795.
Spec: [spec.md](spec.md) — selbst freigegeben (automode).

## Aufgaben
- [x] TW (accc7861b65a726e2, rot 26/27 + Bestandstests, grünbar entfällt) test-writer (AK1–AK6) — Tests: `tests/Unit/HButtonSvgTest.php`, angepasst: `tests/Unit/HButtonTest.php`, `tests/Unit/HButtonEscapeTest.php`
- [x] T1 (adf0df021fe3ef442, GATE GRÜN 243 passed, Pint ok) [pro] HButton auf Inline-SVG und aria-label, CHANGELOG — Tests: `tests/Unit/HButtonSvgTest.php` `tests/Unit/HButtonTest.php` `tests/Unit/HButtonEscapeTest.php` `tests/Unit/HButtonDeleteTest.php`

Bestehende Tests: `tests/Unit/HButtonDeleteTest.php`
Marketing: entfällt (keine Seite).
Design: entfällt (Icons wie die vorhandenen Vue-Buttons).
Review: entfällt (Bugfix ohne Sicherheitsbezug, keine Route, keine Migration, unter 50 Zeilen Logik).

## Stand
Umgesetzt. NACHWEIS: GATE GRÜN; selbst erneut 232 passed (4 HButton-Dateien).
Echtlauf: entfällt (reine HTML-Ausgabe, per Unit-Test auf Markup geprüft, kein eigener Server im Kit).
Abweichung adf0df021fe3ef442: Fehlalarm: keine gemeldet
Offener Punkt adf0df021fe3ef442: keine gemeldet
