# Spec log-taeglich
Risiko: kritisch (löscht Dateien; ändert das Logging jedes Projekts, das das Kit einbindet)
Art: Feature — #1716 (Joerg, Chat 2026-11-19: „für jeden Tag eine Log-Datei“, „wenn das laravel-kit installiert ist, dann automatisch daily“, Command zum Löschen älterer Log-Dateien, „die 30 ein Übergabe-Parameter“).
Status: Entwurf — Freigabe durch Joerg offen.
Rückbau: entfällt (kein Übergangsstand)

## Ziel
`storage/logs/laravel.log` wächst in den Projekten ohne Grenze, teils über 1 GB. Mit dem Kit schreibt Laravel künftig je Tag eine Datei (`laravel-JJJJ-MM-TT.log`), ohne Änderung an `.env` oder `config/logging.php` des Projekts. Der Command `log:prune` löscht Log-Dateien, die älter als N Tage sind (Standard 30). Das Kit trägt ihn täglich im Scheduler ein.

Laravel bringt beides schon mit (Treiber `daily`, Option `days`). Die Projekte nutzen es nicht, weil ihre `config/logging.php` älter ist (`stack` fest auf `['single']`, `days` fest 14). Das Kit setzt die Werte deshalb zur Laufzeit.

## Konfiguration (`config/laravelkit.php`, Schlüssel `logs`)
| Schlüssel | Env | Standard | Wirkung |
|---|---|---|---|
| `logs.taeglich` | `LARAVELKIT_LOGS_TAEGLICH` | `true` | Kit stellt `single` auf `daily` um (AK1–AK5) |
| `logs.tage` | `LARAVELKIT_LOGS_TAGE` | `30` | `days` des Kanals `daily` und Standard von `log:prune --days` |
| `logs.zeitplan` | `LARAVELKIT_LOGS_ZEITPLAN` | `true` | `log:prune` läuft täglich im Scheduler (AK13) |

## Akzeptanzkriterien
### Tägliche Log-Datei
- **AK1** Gegeben das Kit mit Standard-Config und `logging.default = stack` mit `logging.channels.stack.channels = ['single']`, Wenn die App einen Eintrag loggt, Dann steht er in `storage/logs/laravel-<heutiges Datum JJJJ-MM-TT>.log`, und `storage/logs/laravel.log` entsteht nicht.
- **AK2** Gegeben `logging.default = single`, Wenn die App einen Eintrag loggt, Dann steht er in der Tagesdatei wie in AK1.
- **AK3** Gegeben `logging.channels.stack.channels = ['single', 'slack']`, Wenn die App bootet, Dann ist der Wert `['daily', 'slack']` — nur `single` wird ersetzt, Reihenfolge und andere Kanäle bleiben.
- **AK4** Gegeben `laravelkit.logs.tage = 45`, Wenn die App bootet, Dann ist `logging.channels.daily.days = 45`; ohne Angabe ist er `30`.
- **AK5** Gegeben `laravelkit.logs.taeglich = false`, Wenn die App bootet, Dann sind `logging.default`, `logging.channels.stack.channels` und `logging.channels.daily` unverändert, und ein Eintrag landet in `laravel.log`.
- **AK6** Gegeben `logging.default` ist ein anderer Kanal als `single` oder `stack` (z. B. `stderr`, `syslog`, `daily`), Wenn die App bootet, Dann ändert das Kit `logging.default` nicht; nur `logging.channels.daily.days` folgt AK4.
- **AK7** Gegeben `logging.channels.daily` fehlt in der Config des Projekts, Wenn die App bootet, Dann legt das Kit ihn an: Treiber `daily`, `path` und `level` aus `logging.channels.single`, `days` nach AK4.

### Command `log:prune`
- **AK8** Gegeben in `storage/logs` die Dateien `a.log` (zuletzt geändert vor 31 Tagen), `b.log` (vor 29 Tagen) und `unter/c.log` (vor 40 Tagen), Wenn `php artisan log:prune --days=30` läuft, Dann sind `a.log` und `unter/c.log` gelöscht, `b.log` bleibt, die Ausgabe nennt beide gelöschten Dateien und „2 Dateien gelöscht“, Exit-Code 0.
- **AK9** Gegeben die Dateien aus AK8 und `laravelkit.logs.tage = 30`, Wenn `php artisan log:prune` ohne `--days` läuft, Dann ist das Ergebnis wie in AK8; mit `laravelkit.logs.tage = 35` wird nur `unter/c.log` gelöscht.
- **AK10** Gegeben die Dateien aus AK8, Wenn `php artisan log:prune --days=30 --dry-run` läuft, Dann nennt die Ausgabe `a.log` und `unter/c.log` als „würde gelöscht“, und alle drei Dateien bestehen weiter.
- **AK11** Gegeben `--days=0`, `--days=-5` oder `--days=abc`, Wenn `log:prune` läuft, Dann meldet er einen Fehler mit „--days“, Exit-Code ≠ 0, und keine Datei wird gelöscht.
- **AK12** Gegeben in `storage/logs` die vor 60 Tagen geänderten Dateien `.gitignore`, `claude-gate.jsonl`, `claude-abweichungen-offen` und `x.log.gz`, Wenn `log:prune --days=30` läuft, Dann bestehen alle vier weiter — gelöscht werden nur Dateien mit der Endung `.log`.

### Zeitplan und Doku
- **AK13** Gegeben `laravelkit.logs.zeitplan = true` (Standard), Wenn der Scheduler der App gelesen wird (`schedule:list`), Dann steht dort `log:prune` täglich; mit `false` steht er dort nicht.
- **AK14** Gegeben README und CHANGELOG des Kits, Wenn sie gelesen werden, Dann beschreibt die README in einem Abschnitt „Log-Dateien“ die Umstellung auf `daily`, die drei Config-Schlüssel mit Env-Namen, `log:prune` mit `--days` und `--dry-run` und den nötigen Cron-Eintrag `schedule:run`; der CHANGELOG nennt beides unter „Unveröffentlicht“.

## Randfälle
1. `storage/logs` fehlt: `log:prune` meldet „nichts zu tun“, Exit-Code 0.
2. Eine Datei lässt sich nicht löschen (Rechte): Warnung mit Dateiname, die übrigen werden gelöscht, Exit-Code ≠ 0.
3. Symbolische Links: `log:prune` folgt ihnen nicht und löscht sie nicht.
4. Grenze: Eine Datei, die genau vor N Tagen geändert wurde, bleibt; gelöscht wird nur, was älter ist.
5. Ein großes `laravel.log` aus der Zeit vor der Umstellung wird nicht mehr beschrieben und nach N Tagen von `log:prune` gelöscht.
6. Gecachte Config (`php artisan config:cache`): Die Umstellung aus AK1–AK7 wirkt trotzdem.
7. Ein eigener `daily`-Kanal des Projekts mit anderem `path` bleibt bei seinem `path`; nur `days` folgt AK4.

## Nicht-Ziele
- `.env` oder `config/logging.php` der Projekte ändern.
- Log-Dateien komprimieren, nach Größe begrenzen oder außerhalb von `storage/logs` löschen.
- Die Datenbank-Tabelle `log` des Kits (`hLogger`, `LogMigrationOptionalTest`) — bleibt unberührt.
- MCP-Tool oder Web-Oberfläche für `log:prune`.
- Release-Version.

## Entscheidungsspielraum
Name und Ort der Klasse, die die Logging-Config setzt (ServiceProvider-Methode oder eigene Klasse), Aufbau der Ausgabe von `log:prune` (Wortlaut aus AK8/AK10 muss vorkommen). Die Löschlogik liegt in einer eigenen Action/UseCase-Klasse, der Command ruft sie nur auf (Skill `core-software-grundsaetze`, ein UseCase für spätere Kanäle). Keine neuen Pakete.

## Folgen außerhalb des Kits
- Agenten-Werkzeuge lesen fest `storage/logs/laravel.log` (`.claude/bin/echtlauf`, `log-fehler-seit`, Reviewer, Skill `code-debugging`, Command `/fehler`). Nach dem Update des Kits steht der aktuelle Eintrag in der Tagesdatei → Task an Projekt `claude` (id 14) nach Freigabe.
- Auf den Servern läuft `log:prune` nur, wenn der Cron `schedule:run` eingerichtet ist; ohne Cron räumt Monolog die Tagesdateien nach AK4 trotzdem auf.
