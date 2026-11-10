# Plan log-migration-optional
- T1 [pro] Config `log_tabelle`, Provider lädt Log-Migration nur bei true (AK1–AK3) — Tests: tests/Feature/LogMigrationOptionalTest.php
Bestehende Tests: tests/Feature/BootTest.php tests/Unit/LogUndTimeStopTest.php
Review: entfällt (<50 Zeilen, keine Route, keine Rechte; Migration nur optional geladen)
Stand: Spec geschrieben
