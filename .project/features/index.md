## In Arbeit
- [vue3-kern-grundlage](vue3-kern-grundlage/spec.md) — Vue-3-Kern: Ordner `vue3/`, Basis-CSS, Themes, Dialog, Tabs, Accordion auf Reka UI (Host-Entscheidung 0013)
- [timestop](timestop/spec.md) — #93 TimeStop speichert die Startzeit als Zahl (Laravel 13, `serializable_classes = false`)
- [hbutton-svg](hbutton-svg/spec.md) — #1795 HButton gibt Inline-SVG statt Font-Awesome-Icons aus, aria-label ohne Text
- [datatable-aktionen](datatable-aktionen/spec.md) — #1794 DataTable-Props `aktionsSpalte` (Server-HTML) und `zeilenLink` (URL mit `:id`)
- [zeitzone](zeitzone/spec.md) — #92 MyDateTime setzt die Zeitzone nicht mehr global, `HForm::date()` mit Wert repariert

## Geplant

## Fertig
- [kontakt-leiste](kontakt-leiste/spec.md) — `<x-kit::kontakt-leiste />`: Telefon, E-Mail, WhatsApp als Fähnchen bzw. mobile Leiste, Farben per CSS-Variablen im Projekt — gemergt 2026-11-22
- [backup-seeders-only-tables](backup-seeders-only-tables/spec.md) — #1780 `backup_seeders.only_tables` (Positivliste) und keine doppelten call-Zeilen im DatabaseSeeder — gemergt 2026-11-20
- [log-taeglich](log-taeglich/spec.md) — #1716 Log-Dateien je Tag (Kit stellt `single` auf `daily` um), `log:prune --days=30`, täglich im Scheduler — gemergt 2026-11-19
- [combobox-ohne-jquery-ui](combobox-ohne-jquery-ui/spec.md) — #1359/#1366 Vue-Komponente `search`: Vanilla-Combobox statt jQuery UI, Installer ohne `jquery-ui`, Auswahl bleibt beim Neu-Rendern — gemergt 2026-11-16 (1.7.0)
- [hlogger-fix](hlogger-fix/spec.md) — Dateiname mit vierstelligem Jahr, Verzeichnis bei Bedarf anlegen; lokal gemergt
