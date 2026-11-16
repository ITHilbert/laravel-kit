# Plan datatable-de-json
- T1 [junior] Datei wiederherstellen, Publish-Tag `laravelkit-assets`, README, CHANGELOG (AK1–AK3) — Tests: tests/Feature/DatatableDeJsonTest.php
Bestehende Tests: tests/Feature/BootTest.php
Review: entfällt (keine Route/Rechte, Datei + Publish-Tag, <50 Zeilen Code)
Stand: Spec geschrieben (selbst freigegeben, automode)
Echtlauf: entfällt (Paket ohne UI; Publish-Tag per Test belegt)
Offener Punkt aec911656b92e84ef: AK-fremd (Datei liegt auch im bestehenden Tag `public`, laut Randfall unverändert gewollt; Archiv-Inhalt folgt aus Git-Tracking, in Schritt geprüft per git ls-files)
Stand: umgesetzt, Tests grün (DatatableDeJsonTest, BootTest; Suite 3213 OK)
