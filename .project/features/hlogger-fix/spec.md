# Spec: hLogger – Dateiname und Verzeichnis

## Ziel
`ITHilbert\LaravelKit\Helpers\hLogger::Log()` schreibt in eine Tagesdatei mit korrektem Datum und bricht nie die Anfrage ab, nur weil das Log-Verzeichnis fehlt.

## Akzeptanzkriterien
- **AK1 — Dateiname mit vierstelligem Jahr:** Gegeben das Datum 2026-10-20, wenn `hLogger::Log('x')` aufgerufen wird, dann landet die Zeile in `storage/logs/hLogger/2026-10-20.txt` (Format `Y-m-d`), nicht in `2626-10-20.txt`.
- **AK2 — Verzeichnis wird angelegt:** Gegeben `storage/logs/hLogger/` existiert nicht, wenn `hLogger::Log('x')` aufgerufen wird, dann wird das Verzeichnis angelegt, die Zeile geschrieben, und es wird keine Exception geworfen.
- **AK3 — Anhängen:** Gegeben die Tagesdatei existiert schon, wenn erneut geloggt wird, dann wird die Zeile (mit Zeilenumbruch) angehängt; vorhandene Zeilen bleiben erhalten.

## Nicht-Ziele
- Umbenennen alter Dateien `26yy-…`.
- Rotation/Löschung, weitere Felder (Zeit, IP).
