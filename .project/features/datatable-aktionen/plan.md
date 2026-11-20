# Plan datatable-aktionen
laravel-kit `feature/datatable-aktionen` (von Stapel nach #1795). Dashboard #1794.
Spec: [spec.md](spec.md) — selbst freigegeben (automode). Annahme: Server-Aktionsspalte ersetzt die Standard-Buttons (AK3); umkehrbar.

## Aufgaben
- [x] TW (a3e97d4ce6d94513e, rot: Modul zeilenLink.mjs fehlt, grünbar entfällt) test-writer (AK1–AK7, AK8 als Quelltext-Test, AK9/Randfall 3 nicht prüfbar) — Tests: `tests/js/datatableAktionen.test.mjs`
- [x] T1 (a10875d4a054cefe9, GATE GRÜN PHP 38, JS 87) [pro] columnDefs.mjs, optionen.mjs, columnDefs.d.mts, neu zeilenLink.mjs, DataTable.vue, Doku vue-datatable.md, CHANGELOG — Tests: `tests/js/datatableAktionen.test.mjs`

Bestehende Tests: `tests/js/datatableSpalten.test.mjs` `tests/js/datatableOptionen.test.mjs` `tests/js/datatableLeereFelder.test.mjs`
Marketing: entfällt (keine Seite).
Design: entfällt (kein neues Design, Verhalten der bestehenden Komponente).
Review: entfällt (Risiko normal, Komponente im Kit ohne Route/Migration/Sicherheitsbezug).

## Stand
Umgesetzt. NACHWEIS: GATE GRÜN; selbst `node --test tests/js/*.test.mjs` 263 pass, 0 fail.
Echtlauf: entfällt (Kit-Komponente ohne eigenen Server; Logik per Unit-Test, Klick-Handler per Quelltext-Test; Browserprüfung erst im Konsumenten consentLaw, Folge-Task dort).
Abweichung a10875d4a054cefe9: Fehlalarm: keine gemeldet (zusätzlich zeilenLink.d.mts für Typen)
Offener Punkt a10875d4a054cefe9: Spec ergänzt (Doku-Beispiel zu AK9 mit echten HButton-Aufrufen nachgezogen, im Feature-Commit)
