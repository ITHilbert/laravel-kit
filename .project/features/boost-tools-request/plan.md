# Plan boost-tools-request (#2470)

Stand: Spec fertig, Test-Writer als Nächstes.
Branch `bugfix/2470-boost-tools-request` (Paket-Repo laravel-kit).

## Aufgaben
- T1 [pro] RemoteExecuteTool und RemoteSyncTool auf `handle(Request $request)` umstellen, CHANGELOG 1.14.1 — AK1–AK4 — Tests: tests/Feature/BoostToolsRequestTest.php

Bestehende Tests: tests/Feature/BootTest.php

Offener Punkt torrent-packages_dev: keine
Echtlauf: entfällt (Tools sprechen per SSH mit dem Live-Server, im Automode verboten; Aufrufstil per Test mit Process-Fake belegt)
Layout: entfällt (kein Seitenlayout)
Review: entfällt (unter 50 Zeilen Produktivcode, keine Route/Migration/Rechte)
