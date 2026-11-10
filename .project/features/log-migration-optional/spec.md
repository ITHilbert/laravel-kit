# log-migration-optional (#1109)

Die Tabelle `log` (Migration `2020_02_28_000000_create_log_table`) wird bisher bedingungslos geladen. Projekte, die `Entities\Log` nicht nutzen (z. B. excel-programmierer.com, #1100), müssen sie selbst droppen. Ein Ausbau bricht externe Nutzer von `Log::write()` – daher: abschaltbar, Standard bleibt an (rückwärtskompatibel).

## Akzeptanzkriterien
- AK1: Gegeben Config `laravelkit.log_tabelle` ist `true` (Standard) · Wenn `migrate` läuft · Dann existiert die Tabelle `log` mit den Spalten id, log, created_at, updated_at.
- AK2: Gegeben Config `laravelkit.log_tabelle` ist `false` · Wenn `migrate` läuft · Dann wird die Tabelle `log` nicht angelegt.
- AK3: Gegeben `LARAVELKIT_LOG_TABELLE=false` in der Umgebung · Dann ist `config('laravelkit.log_tabelle')` `false`; ohne Variable `true`.

## Randfälle
- Die Config wird beim Boot gelesen; die Migrationen werden nur geladen, wenn sie `true` ist. Bereits angelegte Tabellen bleiben unberührt.

## Nicht-Ziele
- Kein Entfernen von `Entities\Log` oder der Migration. `Log::write()` ohne Tabelle wirft die übliche DB-Exception.

## Entscheidungsspielraum
- Migrationen liegen weiter in `src/Database/Migrations`; nur die Log-Migration ist schaltbar (Unterordner oder Pfadfilter nach Wahl).
Risiko: normal
