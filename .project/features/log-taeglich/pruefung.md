# Prüfung: log-taeglich — 2026-11-19
Geprüft: laravel-kit `3e16cb70` (Diff `20730bb3...3e16cb70`), Host `f9d8ce5` (nur Submodul-Stand). Risiko laut Spec: kritisch.
Echtlauf im Review-Platz (`/home/joerg/projekte/.worktrees/packages_dev/reviewer`, Host mit älterer `config/logging.php`: `stack` → `['single']`, `daily.days` 14, `LOG_CHANNEL=stack`); Probedateien danach wieder entfernt.

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „Dann steht er in `storage/logs/laravel-<heutiges Datum JJJJ-MM-TT>.log`“ | bestanden | `test_stack_mit_single_schreibt_in_die_tagesdatei` grün; Echtlauf `Log::info('review-probe-log-taeglich-1716')` → 1 Treffer in `storage/logs/laravel-2026-11-19.log` |
| AK1 „und `storage/logs/laravel.log` entsteht nicht“ | bestanden | Test prüft `assertFileDoesNotExist`; Echtlauf: bestehendes `laravel.log` bleibt unverändert (mtime 2026-11-18 16:39, 0 Treffer der Probe) |
| AK1 Standard-Config (Env `LARAVELKIT_LOGS_TAEGLICH`, Standard `true`) | bestanden | `test_env_name_taeglich_und_standard` grün; Echtlauf `config('laravelkit.logs')` = `{"taeglich":true,"tage":30,"zeitplan":true}` |
| AK2 „Dann steht er in der Tagesdatei wie in AK1.“ | bestanden | `test_default_single_schreibt_in_die_tagesdatei` grün; Echtlauf `LOG_CHANNEL=single` → `logging.default` = `daily` |
| AK3 „Dann ist der Wert `['daily', 'slack']`“ | bestanden | `test_nur_single_wird_im_stack_ersetzt` (Fall „Beispiel der Spec“) grün |
| AK3 „nur `single` wird ersetzt, Reihenfolge und andere Kanäle bleiben.“ | bestanden | Datensätze `['stderr','single','slack']` → `['stderr','daily','slack']`, ohne `single` unverändert; `slack`/`single`-Definition unverändert geprüft |
| AK4 „Dann ist `logging.channels.daily.days = 45`“ | bestanden | `test_tage_aus_config_landen_im_daily_kanal`, `test_env_name_der_tage_und_standard` grün; Echtlauf `LARAVELKIT_LOGS_TAGE=45` → `days` 45 |
| AK4 „ohne Angabe ist er `30`.“ | bestanden | `test_ohne_angabe_sind_es_30_tage` grün; Echtlauf ohne Env → 30 (Projekt-Config hatte 14) |
| AK5 „Dann sind `logging.default`, `logging.channels.stack.channels` und `logging.channels.daily` unverändert“ | bestanden | `test_taeglich_aus_laesst_die_config_unveraendert_und_schreibt_laravel_log`, `test_taeglich_aus_legt_keinen_daily_kanal_an` grün; Echtlauf `LARAVELKIT_LOGS_TAEGLICH=false` → `["stack",["single"],14]` |
| AK5 „und ein Eintrag landet in `laravel.log`.“ | bestanden | Test schreibt Marker und findet ihn in `laravel.log`, keine Tagesdatei |
| AK6 „Dann ändert das Kit `logging.default` nicht“ | bestanden | `test_anderer_default_bleibt_und_nur_days_folgt` (stderr, syslog, daily) grün; Echtlauf `LOG_CHANNEL=stderr` → `stderr` |
| AK6 „nur `logging.channels.daily.days` folgt AK4.“ | teilweise | `days` folgt (Test, Echtlauf 30). Bei `LOG_CHANNEL=stderr` stellt das Kit zusätzlich `stack.channels` von `['single']` auf `['daily']` (Echtlauf `["stderr",["daily"],30]`) — so verlangt es AK3 ohne Bedingung; AK3 und AK6 widersprechen sich hier. Test prüft `stack` nicht. → sollte, Folge-Task #1732 |
| AK7 „Dann legt das Kit ihn an: Treiber `daily`“ | bestanden | `test_fehlender_daily_kanal_wird_aus_single_angelegt` grün |
| AK7 „`path` und `level` aus `logging.channels.single`“ | bestanden | Test prüft `path` und `level` `notice` aus `single`, Eintrag landet in `laravel-<datum>.log` |
| AK7 „`days` nach AK4“ | bestanden | Test prüft `days` 45 |
| AK8 „Dann sind `a.log` und `unter/c.log` gelöscht“ | bestanden | `test_loescht_nur_dateien_aelter_als_die_tage` grün; Echtlauf `php artisan log:prune --days=30` → `gelöscht: a.log`, `gelöscht: unter/c.log`, beide weg |
| AK8 „`b.log` bleibt“ | bestanden | Test und Echtlauf (`b.log` 29 Tage besteht) |
| AK8 „die Ausgabe nennt beide gelöschten Dateien und „2 Dateien gelöscht““ | bestanden | Test; Echtlauf-Ausgabe „2 Dateien gelöscht“ |
| AK8 „Exit-Code 0“ | bestanden | Test `assertSame(0, …)`; Echtlauf-Exit 1 nur wegen bewusst gesperrter Zusatzdatei (Randfall 2), Test ohne Sperre 0 |
| AK9 „Dann ist das Ergebnis wie in AK8“ | bestanden | `test_ohne_days_gilt_die_config_30` grün (Löschung + „2 Dateien gelöscht“ + Exit 0) |
| AK9 „mit `laravelkit.logs.tage = 35` wird nur `unter/c.log` gelöscht.“ | bestanden | `test_ohne_days_gilt_die_config_35` grün |
| AK10 „Dann nennt die Ausgabe `a.log` und `unter/c.log` als „würde gelöscht““ | bestanden | `test_dry_run_nennt_dateien_und_loescht_nichts` grün; Echtlauf `--days=30 --dry-run` → „würde gelöscht: a.log“, „würde gelöscht: unter/c.log“ |
| AK10 „und alle drei Dateien bestehen weiter.“ | bestanden | Test mit Gegenprobe (ohne `--dry-run` wird gelöscht); Echtlauf `find` nach Dry-Run: alle vorhanden |
| AK11 „Dann meldet er einen Fehler mit „--days““ | bestanden | `test_ungueltige_days_melden_fehler_und_loeschen_nichts` (0, -5, abc) grün; Echtlauf 0, -5, abc, 1.5 → „--days muss eine ganze Zahl größer 0 sein.“ |
| AK11 „Exit-Code ≠ 0“ | bestanden | Test; Echtlauf je `exit=1` |
| AK11 „und keine Datei wird gelöscht.“ | bestanden | Test mit Gegenprobe; Echtlauf `a.log` nach den vier Aufrufen vorhanden |
| AK12 „Dann bestehen alle vier weiter“ | bestanden | `test_loescht_nur_dateien_mit_endung_log` grün (Gegenprobe `weg.log` gelöscht); Fehlerpfad: Echtlauf mit nicht löschbarer Datei (Exit 1) — `.gitignore`, `claude-gate.jsonl`, `claude-abweichungen-offen`, `x.log.gz` (60 Tage) bestehen; Test Randfall 2 prüft `.gitignore` im Fehlerpfad |
| AK12 „gelöscht werden nur Dateien mit der Endung `.log`.“ | bestanden | Code `str_ends_with($name, '.log')` + `is_file`; Test und Echtlauf wie oben |
| AK13 „Dann steht dort `log:prune` täglich“ | bestanden | `test_standard_traegt_log_prune_taeglich_im_scheduler_ein` (Ausdruck `\d+ \d+ * * *`, `schedule:list`) grün; Echtlauf `php artisan schedule:list` → `17 3 * * *  php artisan log:prune` |
| AK13 „mit `false` steht er dort nicht.“ | bestanden | `test_zeitplan_false_traegt_log_prune_nicht_ein` mit Gegenprobe grün; Echtlauf `LARAVELKIT_LOGS_ZEITPLAN=false php artisan schedule:list` → 0 Zeilen `log:prune` |
| AK14 „beschreibt die README in einem Abschnitt „Log-Dateien“ die Umstellung auf `daily`“ | bestanden | `test_readme_hat_abschnitt_log_dateien_mit_allen_angaben` grün; README `## Log-Dateien` gelesen |
| AK14 „die drei Config-Schlüssel mit Env-Namen“ | bestanden | Tabelle mit `logs.taeglich`/`LARAVELKIT_LOGS_TAEGLICH`, `logs.tage`/`…_TAGE`, `logs.zeitplan`/`…_ZEITPLAN` |
| AK14 „`log:prune` mit `--days` und `--dry-run`“ | bestanden | beide Optionen beschrieben |
| AK14 „und den nötigen Cron-Eintrag `schedule:run`“ | bestanden | `* * * * * php artisan schedule:run` |
| AK14 „der CHANGELOG nennt beides unter „Unveröffentlicht““ | bestanden | `test_changelog_nennt_umstellung_und_command_unter_unveroeffentlicht` grün; CHANGELOG `### Log-Dateien` unter `## Unveröffentlicht` |
| Randfall 1 „`log:prune` meldet „nichts zu tun““ | bestanden | `test_fehlendes_logs_verzeichnis_meldet_nichts_zu_tun` grün; Sonde `useStoragePath(<leer>)` + `Artisan::call('log:prune')` → „nichts zu tun“ |
| Randfall 1 „Exit-Code 0“ | bestanden | Test; Sonde `exit=0` |
| Randfall 2 „Warnung mit Dateiname“ | bestanden | `test_nicht_loeschbare_datei_wird_gewarnt_der_rest_geloescht_exit_nicht_null` grün; Echtlauf (Ordner `gesperrt` 0555) → „Nicht löschbar: gesperrt/fest.log“ |
| Randfall 2 „die übrigen werden gelöscht“ | bestanden | Test; Echtlauf `a.log`, `unter/c.log` gelöscht |
| Randfall 2 „Exit-Code ≠ 0“ | bestanden | Test; Echtlauf `exit=1` |
| Randfall 3 „`log:prune` folgt ihnen nicht“ | bestanden | `test_folgt_symbolischen_links_nicht_und_loescht_sie_nicht` grün; Echtlauf: Ziel `extern/ziel.log` (60 Tage, über `ordner-link` und `link.log` erreichbar) besteht |
| Randfall 3 „und löscht sie nicht.“ | bestanden | Test; Echtlauf `link.log`, `ordner-link` bestehen, auch im Fehlerpfad (Exit 1) |
| Randfall 4 „Eine Datei, die genau vor N Tagen geändert wurde, bleibt“ | bestanden | `test_grenze_genau_n_tage_bleibt_aelter_wird_geloescht` (Zeit eingefroren, Sekundengrenze) grün; Code `$zeit >= $grenze` → bleibt |
| Randfall 4 „gelöscht wird nur, was älter ist.“ | bestanden | derselbe Test (`aelter.log` = Grenze − 1 s gelöscht, `juenger.log` bleibt) |
| Randfall 5 „wird nicht mehr beschrieben“ | bestanden | Echtlauf: `laravel.log` (1,6 MB) nach zwei Log-Einträgen unverändert, Einträge in der Tagesdatei |
| Randfall 5 „und nach N Tagen von `log:prune` gelöscht.“ | bestanden | `test_altes_laravel_log_wird_nach_n_tagen_geloescht_ein_junges_bleibt` grün; Echtlauf `log:prune --days=1 --dry-run` → „würde gelöscht: laravel.log“ |
| Randfall 6 „Die Umstellung aus AK1–AK7 wirkt trotzdem.“ | bestanden | `test_umstellung_wirkt_bei_gecachter_config_ohne_logs_schluessel` grün (Cache ohne `logs`-Schlüssel, Gegenprobe `configurationIsCached`); Echtlauf `php artisan config:cache` → `[true,"stack",["daily"],30]`, Eintrag in Tagesdatei, danach `config:clear` |
| Randfall 7 „bleibt bei seinem `path`“ | bestanden | `test_eigener_daily_kanal_behaelt_seinen_pfad_und_nur_days_folgt` grün (Eintrag in `eigen/projekt-<datum>.log`) |
| Randfall 7 „nur `days` folgt AK4.“ | bestanden | derselbe Test (`days` 45); Code ändert am vorhandenen Kanal nur `days` |
| Randfall 8 „Es gilt der Standard 30 — für `days` des Kanals `daily`“ | bestanden | `test_leere_tage_ergeben_den_standard_30` grün; Echtlauf `LARAVELKIT_LOGS_TAGE=` → `days` 30 |
| Randfall 8 „und für `log:prune` ohne `--days`.“ | bestanden | Selbst nachgewiesen: Sonde mit `LARAVELKIT_LOGS_TAGE=` und mit `config(['laravelkit.logs.tage' => ''])` → `--dry-run` nennt `alt31.log`, nicht `jung29.log`, Exit 0. Der Test `test_leere_config_tage_loescht_nichts_unter_30_tagen` allein belegt es nicht (wäre auch bei Fehlerabbruch grün), Zeile fehlt in `abdeckung.md` → sollte, #1732 |
| Nicht-Ziel „`.env` oder `config/logging.php` der Projekte ändern“ | eingehalten | Host-Diff nur Submodul-Stand; Umstellung nur zur Laufzeit per `config([...])` |
| Nicht-Ziel „komprimieren, nach Größe begrenzen oder außerhalb von `storage/logs` löschen“ | eingehalten | Action arbeitet nur auf `storage_path('logs')`, keine Symlinks |
| Nicht-Ziel „Datenbank-Tabelle `log` des Kits“ | eingehalten | nicht berührt; `LogMigrationOptionalTest` grün |
| Nicht-Ziel „MCP-Tool oder Web-Oberfläche“ / „Release-Version“ | eingehalten | `registerMcpTools` unverändert, keine Route; keine Versionsänderung, CHANGELOG unter „Unveröffentlicht“ |

## Sicherheit
- Löschen: nur reguläre Dateien mit Endung `.log` unter `storage_path('logs')`; Pfade aus `scandir`, keine Eingabe des Nutzers im Pfad; Symlinks (Datei und Ordner) werden übersprungen, auch `storage/logs` selbst als Symlink (dann „nichts zu tun“).
- Validierung `--days`: `ctype_digit` und > 0; 0, -5, abc, 1.5 abgelehnt ohne Löschung. Ungültige Config-Werte führen nie zu „alles löschen“ (Kanal fällt auf 30, Command bricht ab).
- Nur CLI und Scheduler, keine Route, kein Mandantenbezug, keine Ausgabe in HTML.
- Fremdformat Cron: Ausdruck erzeugt Laravel (`dailyAt`), per `schedule:list` gesehen; Monolog-Tagesdatei echt geschrieben.

## Regressionen
- `php artisan test --compact packages/laravel-kit/tests/Feature/Logs packages/laravel-kit/tests/Feature/BootTest.php packages/laravel-kit/tests/Feature/LogMigrationOptionalTest.php packages/laravel-kit/tests/Feature/BackupSeedersTest.php` → 71 passed (Tests des Features + `Bestehende Tests:` aus `plan.md`).
- Tests der berührten Dateien: Alle Kit-Tests booten den geänderten `LaravelKitServiceProvider` (`tests/TestCase.php`) → `php artisan test --compact --testsuite=LaravelKit` → 3348 passed.
- `vendor/bin/pint --test` auf die geänderten Kit-Dateien und Tests → passed.
- Host-Tests mit Bezug zu `logging.`/`laravel.log`/`log:prune`: keine (`git grep`). Ganze Suite nicht gelaufen (kein `SUITE: ja`).
- `log:prune` kollidiert mit keinem Command aus Framework oder `vendor/`.

## Funde: 0 muss, 2 sollte, 5 kann
- sollte: AK6 und AK3 widersprechen sich, wenn `logging.default` weder `single` noch `stack` ist: Das Kit ersetzt `single` im Stack trotzdem (AK3), AK6 sagt „nur `daily.days` folgt“; Test zu AK6 prüft `stack` nicht. Joerg muss den Wortlaut entscheiden (→ Folge-Task #1732).
- sollte: Randfall 8 für `log:prune` ist im Test nicht belegt (`test_leere_config_tage_loescht_nichts_unter_30_tagen` prüft nur, dass Junges bleibt) und fehlt in `abdeckung.md`; Verhalten selbst nachgewiesen (→ Folge-Task #1732).
- kann: Ausgabe-Grammatik „1 Dateien gelöscht“, „3 Dateien würde gelöscht“ (besser „Datei/Dateien“, „würden gelöscht“); Wortlaut aus AK8/AK10 muss erhalten bleiben.
- kann: Umstellung in `boot()` — ein Log-Eintrag, den ein früher bootender Provider schreibt, löst `stack`/`single` auf, bevor das Kit umstellt, und landet für diesen Prozess in `laravel.log`. In `register()` wäre die Umstellung früher. Im Echtlauf kein solcher Fall.
- kann: Ist `storage/logs` selbst ein Symlink (manche Deploy-Layouts), meldet `log:prune` „nichts zu tun“ und räumt nie auf; spec-konform (Randfall 3), aber still. Deploy-Layout der Projekte prüfen.
- kann: `LARAVELKIT_LOGS_TAGE=abc` oder `0`: Kanal `daily` nimmt 30, `log:prune` ohne `--days` bricht mit „--days muss …“ ab (Exit 1) — sicher, aber uneinheitlich und die Meldung nennt die falsche Ursache (Env statt Option).
- kann: Werkzeug-Logs mit Endung `.log` in `storage/logs` (z. B. `claude-gate.log`, `browser.log`) fallen nach 30 Tagen ohne Änderung unter `log:prune`; AK12 schützt nur `claude-gate.jsonl`. Spec-konform; für die Folge-Arbeit an den Agenten-Werkzeugen (Spec „Folgen außerhalb des Kits“) erwähnenswert.

## Neue Tasks: #1732 laravel-kit Log-Dateien – Nacharbeit aus Review log-taeglich (Status `wartet`: Frage zu AK6 an Joerg)

## Fehlerprotokoll
- {"schwere":"sollte","ak":"AK6","fund":"AK6 („nur daily.days folgt AK4“) widerspricht AK3 (single im Stack immer ersetzen), wenn logging.default ein anderer Kanal ist; der Test zu AK6 prüft stack.channels nicht","ursache":"Widerspruch zwischen zwei AKs der Spec nicht gemeldet, Test deckt nur die erste Aussage des AK ab","anweisung":"Bei AKs mit „nur …“ prüfen, ob ein anderes AK für denselben Aufbau etwas zusätzlich verlangt, den Widerspruch melden und im Test jede genannte Konfigurationsstelle auf Gleichbleiben prüfen"}
- {"schwere":"sollte","ak":"","fund":"Randfall 8 („Standard 30 auch für log:prune ohne --days“) ist im Test nur verneinend geprüft (Junges bleibt) und fehlt in abdeckung.md","ursache":"Test wurde für zwei mögliche Annahmen (Standard oder Fehler) offen gelassen und nach der Festlegung des Randfalls nicht nachgeschärft","anweisung":"Nach einer Spec-Ergänzung jeden betroffenen Test auf den festgelegten Wortlaut nachschärfen (positiver Nachweis plus Gegenprobe) und die Zeile in abdeckung.md ergänzen"}

## Fazit: freigegeben
