# Plan combobox-ohne-jquery-ui
- T1 [senior] `js/combobox.js` (Vanilla-Combobox) und Einbindung in die drei `search.vue` (AK1–AK8) — Tests: tests/js/combobox.test.mjs
- T2 [junior] Installer ohne `npm install jquery-ui`, README, CHANGELOG 1.7.0 (AK9, AK10) — Tests: tests/Feature/ComboboxOhneJqueryUiTest.php
Bestehende Tests: tests/Feature/BootTest.php tests/js/mount-inseln.test.mjs tests/js/vueappOhneLivewireVue.test.mjs
Review 0/2: 0 muss, 0 sollte, 3 kann — freigegeben (Folge-Task #1366 bugfix)
Stand: Spec geschrieben (selbst freigegeben, automode)
Offener Punkt a2c0483157c4df6ea: AK-fremd (kein `change` am Select bei abgetipptem Text nach blur: altes Widget löste auch kein natives change aus, AK5 verlangt es nicht)
Offener Punkt a2c0483157c4df6ea: AK-fremd (ArrowDown bei geschlossener Liste öffnet die ungefilterte Liste: Spec lässt es offen, wie der Knopf)
Offener Punkt a2c0483157c4df6ea: AK-fremd (Eingabefeld folgt keiner Wertänderung von außen: wie beim alten Widget)
Offener Punkt a2c0483157c4df6ea: AK-fremd (Webpack-Auflösung des Imports ../../../js/combobox.js: gleiches Muster wie checkbox.vue → shared/*.mjs)
Echtlauf: entfällt (Paket ohne eigene Seite; Verhalten per Fake-DOM-Tests belegt, Browser-Lauf im Host-Bundle steht aus — unter „Zum Prüfen“)
Stand: umgesetzt, GATE grün (JS 231/231, PHP 3/3), NACHWEIS ok
- T3 [pro] (#1366, Spec-Lücke AK11, AK12) `aktualisiereCombobox` in `js/combobox.js`, `search.vue` hält die Auswahl in `data`, `watch` auf `value`/`options` — Tests: tests/js/comboboxAktualisieren.test.mjs
Bestehende Tests (T3): tests/js/combobox.test.mjs
Offener Punkt a7768d0b93047a23f: AK-fremd (README nannte aktualisiereCombobox(select): vom Moderator auf element korrigiert)
Echtlauf: browser: Headless Chrome mit Vue 2.7.16 und echter search.vue (bootstrap5/input): Auswahl „Eins“ + Neu-Rendern → select.value 1 / Eingabefeld Eins; value und options von außen geändert → Eingabefeld „Vier“, Liste mit Vier, keine Konsolenfehler (Sonden im Scratchpad cb/)
Review: entfällt (Bugfix #1366 ohne Sicherheitsbezug, Fund aus Review #1359)
Stand: #1366 umgesetzt, GATE grün (JS 35/35 Kern, PHP 3/3), NACHWEIS ok
