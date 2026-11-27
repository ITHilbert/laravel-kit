# Plan loesch-dialog-nacharbeit (#2435)

Stand: Spec geschrieben (selbst freigegeben, automode), Tests offen.
Branch `feature/loesch-dialog-nacharbeit` (Paket-Repo).

## Aufgaben
- T1 [pro] Fokusführung in `js/loesch-dialog.js`, README — AK1–AK5, AK7 — Tests: tests/js/loeschDialogFokus.test.mjs
- T2 [junior] PHP-Test Selektoren — AK6 — Tests: tests/Feature/KitComponents/DialogDeleteSelektorenTest.php (kein Produktivcode; Test-Writer allein)

Bestehende Tests: tests/js/loeschDialog.test.mjs tests/js/loeschen.test.mjs tests/Feature/KitComponents/DialogDeleteTest.php tests/Feature/KitComponents/KernQuelltextTest.php

Offener Punkt ae45698af2dc57cb5 (Fokusfalle bezieht sich auf alle `button` im Dialog; Auslöser-Merker modulweit): AK-fremd (AK3 erfüllt, ein Dialog pro Seite laut Spec loesch-dialog-blade)
Abweichung ae45698af2dc57cb5: Fehlalarm: Coder meldet „keine“
Echtlauf: browser: /index.html (statische Seite mit echtem vue3/kit.css und js/loesch-dialog.js) — 0 Konsolenfehler, Klick: #auf ausgelöst, Klick: #ab ausgelöst, Klick: #ok ausgelöst; Fokus auf Abbrechen beim Öffnen, Rückgabe an den Auslöser bei Abbrechen und Löschen, action /kunden/5 → OK
Layout: entfällt (Dialog ohne Seitenlayout, Verhalten im Browser geprüft)
Review: entfällt (46 Zeilen Produktivcode, Ziel-Route und Senden unverändert, keine Route/Migration/Rechte, Fokusverhalten im Browser belegt)
Stand: T1 umgesetzt, Tests JS 282/282, PHP 2182 (KitComponents), GATE GRÜN, Merge in Stapel 2.
