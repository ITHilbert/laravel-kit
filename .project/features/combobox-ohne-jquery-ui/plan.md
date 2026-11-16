# Plan combobox-ohne-jquery-ui
- T1 [senior] `js/combobox.js` (Vanilla-Combobox) und Einbindung in die drei `search.vue` (AK1–AK8) — Tests: tests/js/combobox.test.mjs
- T2 [junior] Installer ohne `npm install jquery-ui`, README, CHANGELOG 1.7.0 (AK9, AK10) — Tests: tests/Feature/ComboboxOhneJqueryUiTest.php
Bestehende Tests: tests/Feature/BootTest.php tests/js/mount-inseln.test.mjs tests/js/vueappOhneLivewireVue.test.mjs
Review: ausstehend (neues Skript > 50 Zeilen Produktivcode)
Stand: Spec geschrieben (selbst freigegeben, automode)
Offener Punkt a2c0483157c4df6ea: AK-fremd (kein `change` am Select bei abgetipptem Text nach blur: altes Widget löste auch kein natives change aus, AK5 verlangt es nicht)
Offener Punkt a2c0483157c4df6ea: AK-fremd (ArrowDown bei geschlossener Liste öffnet die ungefilterte Liste: Spec lässt es offen, wie der Knopf)
Offener Punkt a2c0483157c4df6ea: AK-fremd (Eingabefeld folgt keiner Wertänderung von außen: wie beim alten Widget)
Offener Punkt a2c0483157c4df6ea: AK-fremd (Webpack-Auflösung des Imports ../../../js/combobox.js: gleiches Muster wie checkbox.vue → shared/*.mjs)
Echtlauf: entfällt (Paket ohne eigene Seite; Verhalten per Fake-DOM-Tests belegt, Browser-Lauf im Host-Bundle steht aus — unter „Zum Prüfen“)
Stand: umgesetzt, GATE grün (JS 231/231, PHP 3/3), NACHWEIS ok
