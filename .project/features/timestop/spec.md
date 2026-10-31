# Spec: TimeStop speichert die Startzeit als Zahl (Dashboard #93)

## Ausgangslage
`Classes\TimeStop::start()` legt mit `Cache::rememberForever('TimeStop_start', fn () => now())` ein Carbon-Objekt im Cache ab. Unter Laravel 13 setzt das Skeleton `cache.serializable_classes => false`; serialisierende Stores (`file`, `database`) liefern das Objekt dann als `__PHP_Incomplete_Class` zurück, und `ende()` scheitert.

## Akzeptanzkriterien
- **AK1 — Dauer ohne Fehler bei serialisierendem Store:** Gegeben der Cache-Store `file` (ebenso `database`) und `cache.serializable_classes` = `false` — Wenn `TimeStop::start()` aufgerufen wird und 5 Sekunden später `TimeStop::ende()` — Dann liefert `ende()` `5` (int, ganze Sekunden) ohne Fehler, und `TimeStop_start` ist danach nicht mehr im Cache.
- **AK2 — Zahl im Cache:** Gegeben ein beliebiger Cache-Store — Wenn `TimeStop::start()` aufgerufen wird — Dann liegt unter `TimeStop_start` ein int (Unix-Zeitstempel in Sekunden), kein Objekt.
- **AK3 — Erster Start gilt:** Gegeben `TimeStop::start()` wurde schon aufgerufen — Wenn `start()` vor `ende()` erneut aufgerufen wird — Dann bleibt die erste Startzeit gespeichert (wie bisher).

## Randfälle
- `ende()` ohne vorheriges `start()` → liefert `0`, kein Fehler.
- Im Cache liegt noch ein Eintrag aus dem alten Stand (Objekt bzw. `__PHP_Incomplete_Class`, kein int) → `ende()` behandelt ihn wie fehlend: `0`, kein Fehler, Eintrag danach entfernt.
- `start()` findet eine Altlast aus dem alten Stand (kein int) → überschreibt sie mit der neuen Startzeit; das folgende `ende()` liefert die Dauer ab diesem `start()`. *Ergänzt 2026-10-31 (Abweichung coder-pro, selbst freigegeben in der Nachtschicht).*
- Die Zeit folgt der Laravel-Uhr (`now()`), damit `travel()`/`Carbon::setTestNow()` in Tests wirken; der bestehende Test in `tests/Unit/LogUndTimeStopTest.php` bleibt grün.

## Nicht-Ziele
- Auflösung unter einer Sekunde (`hrtime`/`microtime`) — Rückgabe bleibt ganze Sekunden.
- Mehrere parallele Stoppuhren (fester Schlüssel bleibt).
- Release/Tag.

## Entscheidungsspielraum
- Keine neuen Pakete.
- Spec selbst freigegeben (Nachtschicht 2026-10-31). Abweichend vom Vorschlag im Task (`hrtime`/`microtime`) Unix-Sekunden über `now()`, weil die Rückgabe ganze Sekunden sind und Tests die Laravel-Uhr nutzen.
