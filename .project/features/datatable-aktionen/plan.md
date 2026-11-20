# Plan datatable-aktionen
laravel-kit `feature/datatable-aktionen` (von Stapel nach #1795). Dashboard #1794.
Spec: [spec.md](spec.md) — selbst freigegeben (automode). Annahme: Server-Aktionsspalte ersetzt die Standard-Buttons (AK3); umkehrbar.

## Aufgaben
- [ ] TW test-writer (AK1–AK7, AK8 als Quelltext-Test, AK9/Randfall 3 nicht prüfbar) — Tests: `tests/js/datatableAktionen.test.mjs`
- [ ] T1 [pro] columnDefs.mjs, optionen.mjs, columnDefs.d.mts, neu zeilenLink.mjs, DataTable.vue, Doku vue-datatable.md, CHANGELOG — Tests: `tests/js/datatableAktionen.test.mjs`

Bestehende Tests: `tests/js/datatableSpalten.test.mjs` `tests/js/datatableOptionen.test.mjs` `tests/js/datatableLeereFelder.test.mjs`
Marketing: entfällt (keine Seite).
Design: entfällt (kein neues Design, Verhalten der bestehenden Komponente).
Review: entfällt (Risiko normal, Komponente im Kit ohne Route/Migration/Sicherheitsbezug).

## Stand
Spec geschrieben.
