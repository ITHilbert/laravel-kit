# Plan hbutton-svg
laravel-kit `fix/hbutton-svg` (von eacf9c8f). Dashboard #1795.
Spec: [spec.md](spec.md) — selbst freigegeben (automode).

## Aufgaben
- [ ] TW test-writer (AK1–AK6) — Tests: `tests/Unit/HButtonSvgTest.php`, angepasst: `tests/Unit/HButtonTest.php`, `tests/Unit/HButtonEscapeTest.php`
- [ ] T1 [pro] HButton auf Inline-SVG und aria-label, CHANGELOG — Tests: `tests/Unit/HButtonSvgTest.php` `tests/Unit/HButtonTest.php` `tests/Unit/HButtonEscapeTest.php` `tests/Unit/HButtonDeleteTest.php`

Bestehende Tests: `tests/Unit/HButtonDeleteTest.php`
Marketing: entfällt (keine Seite).
Design: entfällt (Icons wie die vorhandenen Vue-Buttons).
Review: entfällt (Bugfix ohne Sicherheitsbezug, keine Route, keine Migration, unter 50 Zeilen Logik).

## Stand
Spec geschrieben.
