# Plan loesch-dialog-blade (#2298)

Stand: Spec geschrieben (selbst freigegeben, automode), Tests offen.
Branch `feature/loesch-dialog-blade` (Paket-Repo), Host-Branch `feature/loesch-dialog-blade-2298`.

## Aufgaben
- T1 [senior] Component + Templates kern/bootstrap5/tailwind, `js/loesch-dialog.js`, CSS, README, CHANGELOG — AK1–AK12 — Tests: tests/Feature/KitComponents/DialogDeleteTest.php tests/js/loeschDialog.test.mjs

Bestehende Tests: tests/js/loeschen.test.mjs tests/Feature/KitComponents/BausteineTest.php tests/Feature/KitComponents/KernQuelltextTest.php

Offener Punkt a29a1c6c17db91f22 (1 Test verlangte nicht leeren _token ohne Session): AK-fremd (Testumgebung; AK2 „einem `_token`-Feld“ unverändert, Test vom test-writer a1bd590572dd51db4 auf Session mit Token umgestellt)
Offener Punkt a29a1c6c17db91f22 (2 Formular steht in der Component statt in den Theme-Templates): AK-fremd (AK2 und AK5 erfüllt, Tests grün)
Abweichung a29a1c6c17db91f22: Fehlalarm: Coder meldet „keine“
Echtlauf: browser: /index.html (statische Seite mit echtem vue3/kit.css, js/loesch-dialog.js; Fixture in Scratchpad) — 0 Konsolenfehler, Klick: #auf ausgelöst, Klick: #ok ausgelöst, Dialog sichtbar nach Öffnen, danach hidden/display none, Formular-action /kunden/5; Abbrechen schließt ohne Senden → OK
Review: ausstehend (am Ende des Stapels)
Stand: T1 umgesetzt, Tests grün (PHP 483, JS 43), Echtlauf im Browser OK, Review am Ende.

Review 0/2: 1 muss, 3 sollte, 2 kann (nicht freigegeben: Risiko kritisch + Fehlerpfad-Tests; Sollte → Folge-Task #2435). Behebung: Spec-Kopf kritisch, Tests Fehlerpfade (Öffnen ohne ID, fehlendes #formDelete, leere ID).
