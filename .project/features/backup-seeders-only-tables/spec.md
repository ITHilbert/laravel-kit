# Spec backup-seeders-only-tables
Risiko: normal (Konfiguration und Nachbearbeitung von `db:backup-seeders`, keine neue Route, keine Migration)
Art: Feature — #1780 (Fund aus consentLaw 2026-11-20, Task `freigabe` im Modus `umsetzung`). Erweitert `kit-backup-seeders` (#1084).
Status: selbst freigegeben (automode torrent-packages_dev, 2026-11-20, Task #1780 ist von Joerg freigegeben; Annahmen siehe plan.md).
Rückbau: entfällt (kein Übergangsstand)

## Ziel
1. `db:backup-seeders` kennt neben der Ausschlussliste `backup_seeders.ignore_tables` eine Positivliste `backup_seeders.only_tables`. Neue Tabellen landen so nicht ungefragt im Git.
2. iseed trägt für jeden exportierten Seeder einen `$this->call(X::class);` in `DatabaseSeeder::run()` ein, auch wenn `DatabaseSeeder` ihn schon aufruft (z. B. innerhalb `call([...])`). Der Befehl entfernt solche doppelten Zeilen.

## Konfiguration
`config/laravelkit.php`, Schlüssel `backup_seeders.only_tables`, Standard `[]` (= nicht gesetzt, bisheriges Verhalten).

## Akzeptanzkriterien
- **AK1** Gegeben `laravelkit.backup_seeders.only_tables = ['vorlagen']` und die Tabellen `vorlagen` und `kunden`, Wenn `php artisan db:backup-seeders` läuft, Dann ruft der Befehl iseed nur für `vorlagen` auf, nicht für `kunden`.
- **AK2** Gegeben `only_tables = ['vorlagen']` und `ignore_tables = ['vorlagen']`, Wenn der Befehl läuft, Dann wird `vorlagen` exportiert — `ignore_tables` gilt nicht, solange `only_tables` nicht leer ist.
- **AK3** Gegeben `only_tables = []` (Standard), Wenn der Befehl läuft, Dann gilt wie bisher `ignore_tables`: alle Tabellen außer den dort genannten werden exportiert.
- **AK4** Gegeben `only_tables = ['vorlagen', 'gibt_es_nicht']`, Wenn der Befehl läuft, Dann wird nur `vorlagen` exportiert, die Ausgabe warnt für `gibt_es_nicht` („existiert nicht“), und der Exit-Code ist 0.
- **AK5** Gegeben `DatabaseSeeder::run()` ruft `VorlagenTableSeeder` schon innerhalb eines `$this->call([...])`-Arrays auf und iseed hängt danach die Zeile `$this->call(VorlagenTableSeeder::class);` an, Wenn der Befehl endet, Dann steht der Seeder genau einmal in `DatabaseSeeder.php`; die vom Befehl ergänzte Zeile ist entfernt.
- **AK6** Gegeben die Lage aus AK5, Wenn `db:backup-seeders` zweimal hintereinander läuft, Dann ist `DatabaseSeeder.php` nach dem zweiten Lauf byte-gleich zum Stand nach dem ersten.
- **AK7** Gegeben `DatabaseSeeder::run()` ruft einen Seeder noch nicht auf und iseed trägt ihn ein, Wenn der Befehl endet, Dann bleibt der neue Aufruf genau einmal stehen (die Bereinigung entfernt nur Doppelte).

## Randfälle
1. Eine schon vor dem Lauf vorhandene Einzelzeile `$this->call(X::class);` bleibt unverändert (nur Zeilen, die der Lauf ergänzt hat, werden entfernt).
2. Fehlt `DatabaseSeeder.php`, läuft der Befehl wie bisher (Warnung der Sperre), ohne Fehler durch die Bereinigung.
3. README und CHANGELOG nennen `only_tables` (Abschnitt zu `db:backup-seeders`, CHANGELOG „Unveröffentlicht“).

## Nicht-Ziele
- Kein Eingriff in iseed selbst (Fremdpaket).
- Kein Löschen alter Seeder-Dateien nicht mehr gelisteter Tabellen.

## Entscheidungsspielraum
Namen der Hilfsmethoden, Wortlaut der Warnung bei unbekannter Tabelle (muss „existiert nicht“ enthalten).
