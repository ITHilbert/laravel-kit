# Prüfung: timestop (laravel-kit, Dashboard #93) — 2026-10-31

Branch: Paket `fix/timestop` (50d0274), Host `fix/timestop` (3f66e9c). Diff: `src/Classes/TimeStop.php`, `tests/Unit/TimeStopCacheTest.php`, Spec/Plan. Keine `abdeckung.md` vorhanden.

Echtlauf: `php artisan tinker --env=testing --execute="require '<scratchpad>/echtlauf.php';"` im Review-Platz, Laravel 13.38.0, Host-Cache-Konfiguration (`cache.default=file`, Pfad `storage/framework/cache/data`), echte Wartezeit per `sleep()` (keine Testuhr). **Hinweis:** Die Host-`config/cache.php` setzt `serializable_classes` gar nicht (→ `null`, alle Klassen erlaubt). Mit der Host-Konfiguration unverändert tritt der Ausgangsfehler daher nicht auf; der Lauf wurde deshalb zusätzlich mit `cache.serializable_classes=false` (Laravel-13-Skeleton) wiederholt. Gegenprobe alter Stand (`main:src/Classes/TimeStop.php`) mit `false`: `TypeError: strtotime(): Argument #1 ($datetime) must be of type string, __PHP_Incomplete_Class given` — der Aufbau trifft also den Fehler.

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „Gegeben der Cache-Store `file` (ebenso `database`) und `cache.serializable_classes` = `false` — Wenn `TimeStop::start()` aufgerufen wird und 5 Sekunden später `TimeStop::ende()` — Dann liefert `ende()` `5` (int, ganze Sekunden) ohne Fehler“ | bestanden | `test_ende_liefert_fuenf_als_int_ohne_fehler` (file, database) grün, prüft `assertIsInt` + `assertSame(5)`; Echtlauf file+false: `ende(): int 5`; database (SQLite) +false: `ende(): int 5`; kein Fehler |
| AK1 „und `TimeStop_start` ist danach nicht mehr im Cache“ | bestanden | `test_start_zeit_ist_nach_ende_nicht_mehr_im_cache` (file, database) mit Gegenprobe vorher `assertNotNull`; Echtlauf: `danach im Cache: false` für file und database |
| AK2 „Gegeben ein beliebiger Cache-Store — Wenn `TimeStop::start()` aufgerufen wird — Dann liegt unter `TimeStop_start` ein int (Unix-Zeitstempel in Sekunden)“ | nicht bestanden | array/file/database: `test_start_legt_int_zeitstempel_im_cache_ab` grün, Echtlauf `nach start(): int 1793453711`. **Redis:** `RedisStore::serialize()` legt Zahlen unserialisiert ab, `RedisStore::unserialize()` gibt numerische Werte unverändert zurück (`vendor/laravel/framework/src/Illuminate/Cache/RedisStore.php:527–531`), Redis liefert sie als String. Simulation mit echtem `RedisStore` und einer Verbindung, die wie phpredis ohne Serializer Strings zurückgibt: `nach start(): string '1793453744'`, `Cache::get` eines int → `string`. Der Test deckt nur array/file/database ab; ein echter Redis-Server ist im Platz nicht verfügbar (keine Erweiterung, kein Port 6379) |
| AK2 „kein Objekt“ | bestanden | in allen geprüften Stores (array, file, database, Redis-Simulation) kein Objekt |
| AK3 „Gegeben `TimeStop::start()` wurde schon aufgerufen — Wenn `start()` vor `ende()` erneut aufgerufen wird — Dann bleibt die erste Startzeit gespeichert (wie bisher)“ | nicht bestanden | array/file/database: `test_zweiter_start_behaelt_erste_startzeit`, `test_ende_rechnet_ab_der_ersten_startzeit` grün; Echtlauf `nach 2. start(): … (gleich: true)`. **Redis:** `start()` prüft `is_int($vorhanden)` (`src/Classes/TimeStop.php:13`), der von Redis gelesene String ist kein int → jeder `start()` überschreibt. Simulation: `nach 2. start(): '1793453746' gleich: false`; danach `ende(): 0` statt 5 (der String gilt in `ende()` ebenfalls als fehlend). Der alte Stand hielt die erste Startzeit auf Redis (`rememberForever`), das ist also eine Regression gegenüber „wie bisher“ und macht TimeStop auf Redis wirkungslos |
| Randfall „`ende()` ohne vorheriges `start()` → liefert `0`“ | bestanden | `test_ende_ohne_start_liefert_null` (array, file, database) mit Gegenprobe `assertNull` vorher; Echtlauf `ende() ohne start(): 0` (file, file+false, database) |
| Randfall „`ende()` ohne vorheriges `start()` → … kein Fehler“ | bestanden | wie oben, keine Exception, kein Eintrag in `storage/logs/laravel.log` |
| Randfall „Im Cache liegt noch ein Eintrag aus dem alten Stand (Objekt bzw. `__PHP_Incomplete_Class`, kein int) → `ende()` behandelt ihn wie fehlend: `0`“ | bestanden | `__PHP_Incomplete_Class`: `test_ende_behandelt_altlast_im_cache_wie_fehlend` (file, database) mit Gegenprobe; Echtlauf `[Altlast] gelesen: __PHP_Incomplete_Class`, `ende(): 0`. **Objekt** (echtes `Carbon`, Host-Konfig `serializable_classes=null`): kein Test dafür, Echtlauf `[Objekt] gelesen: Illuminate\Support\Carbon`, `ende(): 0` |
| Randfall „… kein Fehler“ | bestanden | Echtlauf beide Varianten ohne Exception; Test ohne Exception grün |
| Randfall „… Eintrag danach entfernt“ | bestanden | `test_ende_entfernt_altlast_aus_dem_cache` (file, database) mit Gegenprobe; Echtlauf `danach im Cache: false` für `__PHP_Incomplete_Class` und `Carbon` |
| Randfall „`start()` findet eine Altlast aus dem alten Stand (kein int) → überschreibt sie mit der neuen Startzeit“ | bestanden | `test_start_ueberschreibt_altlast_mit_int_zeitstempel` (file, database) mit Gegenprobe; Echtlauf `[Altlast start] nach start(): 1793453721` (int, aktuelle Zeit; Altlast war `now()->subHour()`); Carbon-Altlast: `nach start(): int` |
| Randfall „das folgende `ende()` liefert die Dauer ab diesem `start()`“ | bestanden | `test_ende_nach_start_ueber_altlast_liefert_dauer_ab_start` (file, database) → 5; Echtlauf nach `sleep(1)` → `1`, Carbon-Altlast nach `sleep(2)` → `2` (nicht 3600/60) |
| Randfall „Die Zeit folgt der Laravel-Uhr (`now()`), damit `travel()`/`Carbon::setTestNow()` in Tests wirken“ | bestanden | Code nutzt `now()->getTimestamp()` in `start()` und `ende()`; `test_zeit_folgt_der_laravel_uhr` mit `travel(7)` → 7 grün; alle Tests mit `travelTo`/`travel` grün |
| Randfall „der bestehende Test in `tests/Unit/LogUndTimeStopTest.php` bleibt grün“ | bestanden | `php artisan test packages/laravel-kit/tests/Unit/LogUndTimeStopTest.php` → 3 passed; Datei im Diff unverändert |
| Nicht-Ziel „Auflösung unter einer Sekunde (`hrtime`/`microtime`) — Rückgabe bleibt ganze Sekunden“ | eingehalten | kein `hrtime`/`microtime` im Diff; Rückgabe `int` |
| Nicht-Ziel „Mehrere parallele Stoppuhren (fester Schlüssel bleibt)“ | eingehalten | Schlüssel `TimeStop_start` fest, Signaturen unverändert |
| Nicht-Ziel „Release/Tag“ | eingehalten | `git tag --contains 50d0274` leer |
| Entscheidungsspielraum „Keine neuen Pakete“ | eingehalten | `composer.json`/`package.json` des Pakets nicht im Diff |

`php artisan test packages/laravel-kit/tests/Unit/TimeStopCacheTest.php` → 25 passed (51 assertions). Pint `--test` auf beide Dateien: passed.

## Sicherheit
Keine Eingaben, keine Ausgabe, keine Autorisierung berührt. Die Änderung reduziert sogar die Angriffsfläche: Es wird kein Objekt mehr im Cache abgelegt, `TimeStop` funktioniert damit auch mit `serializable_classes=false`. Keine Befunde.

## Regressionen
- Volle Suite im Platz (`php artisan test --parallel --compact`): 1234 Tests, 1 Fehlschlag: `ITHilbert\Kontaktformular\Tests\Feature\AnfrageHerkunftTest::test_referrer_mit_eigenem_host_gilt_als_kein_referrer` (erwartet `'direkt'`, bekommt `'organisch/sonstige'`) — vom Moderator als feature-fremd angekündigt (#315, auf diesem Branch noch nicht behoben), berührt kein TimeStop-/laravel-kit-Code.
- Weitere Verwender von `TimeStop` in Host und Paketen: keine (grep ohne Treffer außerhalb der Klasse und der Tests).
- **Regression Redis** (siehe AK2/AK3): Auf dem Store `redis` (Standard-Konfiguration ohne phpredis-Serializer) gibt `Cache::get` den Zeitstempel als String zurück; neuer Code wertet ihn als „kein int“ → `start()` überschreibt bei jedem Aufruf, `ende()` liefert immer `0`. Der alte Stand funktionierte auf Redis mit `serializable_classes=null`. Möglicher Ansatz (Entscheidung beim Coder): numerische Strings als Zeitstempel akzeptieren (z. B. `filter_var(..., FILTER_VALIDATE_INT)`) und einen Test mit einem Store ergänzen, der Zahlen als String zurückgibt.
- `storage/logs/laravel.log`: keine Einträge zu TimeStop; vorhandene Einträge („Social login aktiviert, aber Anbieter nicht konfiguriert“) stammen von früher und sind feature-fremd.

## Neue Tasks: keine
(Hinweis an den Moderator, kein Task: Die Host-`config/cache.php` stammt aus einem älteren Skeleton und kennt `serializable_classes` nicht; der Host prüft den Laravel-13-Standard `false` damit nicht von selbst.)

## Fazit: nicht freigegeben
AK2 und AK3 gelten laut Spec für jeden Cache-Store („ein beliebiger Cache-Store“, AK3 ohne Einschränkung). Auf Redis sind beide verletzt, und TimeStop liefert dort immer 0. Das ist eine Regression gegenüber dem alten Stand. Nachgewiesen per Code (`RedisStore::unserialize`) und Simulation mit dem echten `RedisStore`, nicht gegen einen echten Redis-Server (im Platz nicht verfügbar). Für file, database und array sind alle AKs und Randfälle bestanden.

---

# Nachprüfung: timestop (laravel-kit, Dashboard #93) — 2026-10-31 (Nachprüfung seit aad239a)

Branch: Paket `fix/timestop` (f4d776d), Host `fix/timestop-2` (a1e1322). Bezug: die Prüfung oben, im Paket mit aad239a eingemergt (Code-Stand 50d0274). Fix: 593985d (Regressions-Test, `tests/Unit/TimeStopCacheTest.php` +205, `abdeckung.md`) und f4d776d (`src/Classes/TimeStop.php`: `is_int()` an beiden Stellen durch `zeitstempel()` ersetzt, die int und reine Ziffern-Strings annimmt, `ctype_digit` + `FILTER_VALIDATE_INT`). Die Spec ist seit der ersten Prüfung unverändert (`git diff 50d0274 HEAD -- spec.md` leer). Der Fix baut nichts über AK2/AK3 hinaus um: keine neue Klasse, keine Migration, keine geteilte Komponente. Deshalb nur eine Nachprüfung.

Echtlauf Redis: `php artisan tinker --env=testing --execute="require '<scratchpad>/redis2.php';"` im Platz, Laravel 13.38.0, **echter** `Illuminate\Cache\RedisStore` über eine Verbindung, die wie phpredis/predis ohne Serializer Strings liefert (der Platz hat weder die Redis-Erweiterung noch einen Server), jeweils mit `serializable_classes` = `null` (Host) und `false` (L13-Skeleton), echte Wartezeit per `sleep()`.

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „… Dann liefert `ende()` `5` (int, ganze Sekunden) ohne Fehler“ | bestanden | Fix berührt `ende()`, daher neu geprüft: `test_ende_liefert_fuenf_als_int_ohne_fehler` (file, database) grün; zusätzlich Redis-Sim: `ende(): int 5` bei `null` und `false` |
| AK1 „und `TimeStop_start` ist danach nicht mehr im Cache“ | bestanden | `test_start_zeit_ist_nach_ende_nicht_mehr_im_cache` grün (Gegenprobe vorher `assertNotNull`); `Cache::forget` steht weiter vor dem Rückgabe-Zweig; Redis-Sim: `danach im Cache: false`, Rohschlüssel weg |
| AK2 „Gegeben ein beliebiger Cache-Store — Wenn `TimeStop::start()` aufgerufen wird — Dann liegt unter `TimeStop_start` ein int (Unix-Zeitstempel in Sekunden)“ | bestanden | array/file/database: `test_start_legt_int_zeitstempel_im_cache_ab` grün (`assertIsInt`). Redis: `start()` schreibt `now()->getTimestamp()` (int); `RedisStore` legt ihn als rohe Zahl ab (Sim: roh `'1793456895'` = `now`), nicht serialisiert. Redis kennt keine Typen, eine reine Ziffernfolge ist dort die Ablage einer Ganzzahl. Dass `Cache::get` sie als String zurückgibt, liegt am Store; TimeStop wertet sie jetzt als Zeitstempel (siehe AK3). Tests: `test_redisartiger_store_liefert_zahlen_als_string` (Gegenprobe: der Aufbau liefert wirklich einen String), `test_start_legt_zahl_statt_objekt_ab_auch_bei_stringrueckgabe` (redisartig, array) grün |
| AK2 „kein Objekt“ | bestanden (Prüfung aad239a) | vom Fix nicht berührt (`start()` schreibt weiter `now()->getTimestamp()`); Redis-Sim nochmals: `Cache::get` → `string`, kein Objekt |
| AK3 „Gegeben `TimeStop::start()` wurde schon aufgerufen — Wenn `start()` vor `ende()` erneut aufgerufen wird — Dann bleibt die erste Startzeit gespeichert (wie bisher)“ | bestanden | Redis-Sim (`null` und `false`): `nach 2. start(): '1793456895' gleich: true`, danach `ende(): int 5` (2 s + 3 s ab erstem Start). Vorher (erste Prüfung): `gleich: false`, `ende(): 0`. Tests: `test_zweiter_start_behaelt_erste_startzeit_bei_stringrueckgabe` und `test_ende_rechnet_ab_erster_startzeit_bei_stringrueckgabe` (redisartig, array) grün, ebenso die bisherigen `test_zweiter_start_behaelt_erste_startzeit`, `test_ende_rechnet_ab_der_ersten_startzeit` (array/file/database). Die Tests schlagen mit dem alten `is_int()`-Stand fehl: der Testspeicher `ZahlenAlsStringStore` liefert `'<ts>'`, `is_int` ist false, also überschreibt der zweite Start (+3 s) → `assertSame($erste, …)` rot. Das ist am Code nachvollzogen, nicht am alten Stand ausgeführt (Code im Platz unangetastet). |
| Randfall „`ende()` ohne vorheriges `start()` → liefert `0`“ | bestanden | Fix berührt `ende()`: `test_ende_ohne_start_liefert_null` (array/file/database) und `test_ende_ohne_start_liefert_null_bei_redisartigem_store` mit Gegenprobe `assertNull` grün; Redis-Sim `ende() ohne start(): 0` |
| Randfall „`ende()` ohne vorheriges `start()` → … kein Fehler“ | bestanden | wie oben, keine Exception |
| Randfall „Im Cache liegt noch ein Eintrag aus dem alten Stand (Objekt bzw. `__PHP_Incomplete_Class`, kein int) → `ende()` behandelt ihn wie fehlend: `0`“ | bestanden | neu geprüft, weil `zeitstempel()` die Prüfung ersetzt: `test_ende_behandelt_altlast_im_cache_wie_fehlend` (file, database) grün; Redis-Sim: Altlast `Illuminate\Support\Carbon` (`null`) bzw. `__PHP_Incomplete_Class` (`false`) → `ende(): 0`. Weitere Nicht-int-Rohwerte in Redis (`'-5'`, `'1.5'`, `''`, `'abc'`, `'0012'`) → `0` |
| Randfall „… kein Fehler“ | bestanden | keine Exception in allen Varianten (die PHP-Warnung bei `'abc'` stammt aus `RedisStore::unserialize()`, `RedisStore.php:534`, und ist ein künstlicher Rohwert, den TimeStop nie schreibt) |
| Randfall „… Eintrag danach entfernt“ | bestanden | `test_ende_entfernt_altlast_aus_dem_cache` (file, database) grün; Redis-Sim `danach im Cache: false` |
| Randfall „`start()` findet eine Altlast aus dem alten Stand (kein int) → überschreibt sie mit der neuen Startzeit“ | bestanden | neu geprüft, weil `start()` geändert: `test_start_ueberschreibt_altlast_mit_int_zeitstempel` (file, database) grün; Redis-Sim: serialisierter Carbon `now()->subHour()` → nach `start()` `'1793456900'` = `now` |
| Randfall „das folgende `ende()` liefert die Dauer ab diesem `start()`“ | bestanden | `test_ende_nach_start_ueber_altlast_liefert_dauer_ab_start` grün; Redis-Sim nach `sleep(1)` → `1` (nicht 3600) |
| Randfall „Die Zeit folgt der Laravel-Uhr (`now()`), damit `travel()`/`Carbon::setTestNow()` in Tests wirken“ | bestanden | `now()->getTimestamp()` unverändert in `start()`/`ende()`; `test_zeit_folgt_der_laravel_uhr` grün; alle neuen Tests nutzen `travelTo`/`travel` und sind grün |
| Randfall „der bestehende Test in `tests/Unit/LogUndTimeStopTest.php` bleibt grün“ | bestanden | `php artisan test …/LogUndTimeStopTest.php` → 3 passed; Datei weder in 593985d noch in f4d776d |
| Nicht-Ziel „Auflösung unter einer Sekunde (`hrtime`/`microtime`) — Rückgabe bleibt ganze Sekunden“ | eingehalten | kein `hrtime`/`microtime` im Fix; `zeitstempel()` lehnt `'1.5'` ab, Rückgabe `int` |
| Nicht-Ziel „Mehrere parallele Stoppuhren (fester Schlüssel bleibt)“ | eingehalten (Prüfung aad239a) | Schlüssel `TimeStop_start` im Fix unverändert |
| Nicht-Ziel „Release/Tag“ | eingehalten | `git tag --contains f4d776d` leer |
| Entscheidungsspielraum „Keine neuen Pakete“ | eingehalten | `git diff 593985d^ f4d776d --stat`: nur `abdeckung.md`, `TimeStop.php`, `TimeStopCacheTest.php` |

`php artisan test packages/laravel-kit/tests/Unit/TimeStopCacheTest.php packages/laravel-kit/tests/Unit/LogUndTimeStopTest.php` → 38 passed (80 assertions). Pint `--test` auf beide Dateien: passed.

`abdeckung.md` als Gegenliste: Die drei Zeilen (AK2, AK3, Randfall 1) nennen Tests, die es gibt und die grün sind. Inhaltlich prüft `test_start_legt_zahl_statt_objekt_ab_auch_bei_stringrueckgabe` für den Store `array` mit `is_int || is_string` schwächer als „ein int“. Für `array` deckt `test_start_legt_int_zeitstempel_im_cache_ab` mit `assertIsInt` das ab, für den redisartigen Store ist ein String die einzig mögliche Rückgabe. Für AK3 prüfen die neuen Tests die Aussage wirklich, mit der Gegenprobe des Aufbaus in `test_redisartiger_store_liefert_zahlen_als_string`.

## Sicherheit (Diff des Fixes)
`zeitstempel()` nimmt nur `int` oder reine Ziffern-Strings an (`ctype_digit`, kein Vorzeichen, kein Punkt, nicht leer), `FILTER_VALIDATE_INT` fängt Überlauf und führende Nullen ab → `null`. Keine Eingaben von außen, keine Ausgabe, kein `unserialize` im eigenen Code. Der Testspeicher nutzt `unserialize(…, ['allowed_classes' => false])`. Keine Befunde.

## Regressionen
- Volle Suite im Platz (`php artisan test --parallel --compact`, ohne Filter): **OK, 2077 Tests, 21384 Assertions**, 0 Fehlschläge. Der in der ersten Prüfung gemeldete fremde Fehlschlag (`AnfrageHerkunftTest`, #315) tritt auf diesem Stand nicht mehr auf.
- Die in der ersten Prüfung gemeldete Redis-Regression ist behoben (siehe AK3): Redis-Sim verhält sich jetzt wie file/database.
- Verwender von `TimeStop` außerhalb der Klasse und ihrer Tests: weiterhin keine (grep in `app`, `packages`, `routes`, `config`).
- `storage/logs/laravel.log`: kein Eintrag zu TimeStop; die letzten Einträge (14:15–14:23) liegen vor dieser Nachprüfung und sind feature-fremd.

## Neue Tasks: keine

## Fazit: freigegeben
Beide Beanstandungen der ersten Prüfung (AK2, AK3 auf Redis) sind behoben und mit echtem `RedisStore` (simulierte Verbindung) sowie Regressions-Tests nachgewiesen. Alle übrigen Aussagen sind bestanden, neu geprüft oder aus Prüfung aad239a übernommen. Die Suite ist grün. Einschränkung wie in der ersten Prüfung: nicht gegen einen echten Redis-Server geprüft (im Platz nicht verfügbar), sondern gegen den echten `RedisStore`-Code mit einer Verbindung, die Strings zurückgibt wie phpredis/predis ohne Serializer.
