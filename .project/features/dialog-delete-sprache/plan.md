# Plan dialog-delete-sprache (#2451)

Stand: Spec geschrieben (selbst freigegeben, automode), Tests offen.
Branch `feature/dialog-delete-sprache` (Paket-Repo laravel-kit).

## Aufgaben
- T1 [pro] Sprachdateien, ServiceProvider, vier Blade-Templates (components + 3 Themes), README, CHANGELOG 1.14.0 — AK1–AK6 — Tests: tests/Feature/KitComponents/DialogDeleteSpracheTest.php

Bestehende Tests: tests/Feature/KitComponents/DialogDeleteTest.php tests/Feature/KitComponents/DialogDeleteSelektorenTest.php tests/js/loeschDialogFokus.test.mjs tests/js/loeschen.test.mjs

Offener Punkt torrent-packages_dev: keine
Echtlauf: entfällt (nur Textquelle, Rendern per Blade-Test belegt)
Layout: entfällt (kein Seitenlayout, Markup unverändert)
Review: entfällt (unter 50 Zeilen Produktivcode, keine Route/Migration/Rechte, Markup und Skripte unverändert)
