# Plan: kontakt-leiste (Feature)

Branch: `feature/kontakt-leiste` (Paket laravel-kit) · Spec: `spec.md` · Spec freigegeben: User 2026-11-22
Marketing: entfällt (Baustein ohne eigene Seite und ohne Botschaft; Vorbild it-entwickler-hilbert.de vom User vorgegeben)
Design: entfällt (Vorlage it-entwickler-hilbert.de `kontakt-leiste`, vom User vorgegeben; Farben setzt das Projekt)

## Aufgaben
- [x] T1 (AK1–AK7, AK10, AK11, Randfall 1–5) test-writer: Feature-Tests Markup, Config, CSS-Ausgabe — Tests: `tests/Feature/KitComponents/KontaktLeisteTest.php`
- [x] T2 (AK1–AK11) [pro] anonyme Komponente `src/Views/components/kit/kontakt-leiste.blade.php` mit Inline-SVG und `@once`-CSS (Variablen, Medienabfragen 768 px, Druck, reduzierte Bewegung), Config `kontakt_leiste` in `src/Config/laravelkit.php`, README-Abschnitt, CHANGELOG „Unveröffentlicht“ — Tests: `tests/Feature/KitComponents/KontaktLeisteTest.php`
- [x] T3 NACHWEIS (Moderator): GATE, Tests, Sichtprüfung 375/1280 im Host packages_dev
- [x] T4 REVIEW (reviewer, Review-Platz) → `pruefung.md` — nicht freigegeben (2 muss: AK8, AK10)
- [x] T5 (AK8, AK10) test-writer: Regressionstests zu den muss-Funden aus `pruefung.md` — Tests: `tests/Feature/KitComponents/KontaktLeisteTest.php`
- [x] T6 (AK8, AK10) [junior] Behebung im Paket-CSS `src/Views/components/kit/kontakt-leiste.blade.php` — Tests: `tests/Feature/KitComponents/KontaktLeisteTest.php`
- [x] T7 NACHWEIS (Moderator)
- [ ] T8 Nachprüfung (reviewer, `NACHPRUEFUNG:`)

Bestehende Tests: `tests/Feature/KitComponents/BausteineTest.php`, `tests/Feature/BootTest.php`

## Nachweis
GATE coder-pro ad36994d350d9e1b6: GRÜN (527 passed, Pint ok), Log `excel-programmierer.com/storage/logs/claude-gate.log` 2026-11-22 21:47:02
Grünbar: `KontaktLeisteTest.php` 16/16 manuell (pruefen kennt keinen Testbefehl im Paket)
Grünbar T5: `KontaktLeisteTest.php` 18/18 (test-writer, Wegwerf-Umsetzung); Rot 2/18: AK8 „Fähnchen ohne box-sizing:border-box“, AK10 Literal `rgba(` im box-shadow
Echtlauf: browser: Komponente über Host packages_dev gerendert (Testseiten `leiste.html`, `leiste-gruen.html`, `kurz.html`, `hover.html`), Chrome headless 1280 und 375 px angesehen — Fähnchen rechts 52 px, Fahne bei Hover sichtbar, Projektfarbe über `--kontakt-leiste-bg` greift, mobil 3 gleich breite Spalten unten, Footer am Seitenende nicht verdeckt
Layout: layout: leiste-gruen.html kurz.html --breiten 375,1280 --hoehen 650 --lang .kontakt-leiste .lang → 0 Befunde
Offener Punkt ad36994d350d9e1b6: AK-fremd (Umsetzungsdetails im Rahmen von AK8–AK10: Standardwerte in `:where(.kontakt-leiste)` mit Spezifität 0, damit Projektwerte auch aus dem `<head>` gewinnen; `--kontakt-leiste-top` 40vh, `--kontakt-leiste-z` 1030; Mobil-Grid mit `grid-auto-columns`, damit bei 2 Wegen 2 gleich breite Spalten entstehen)

### Nachweis nach Review 0 (T6, 2026-11-22)
GATE coder-junior a258442981b09bde2: GRÜN (491 passed aus TESTS:, Pint ok, PHPStan ok), Log `packages_dev/storage/logs/claude-gate.log` 2026-11-22 22:03:58
Echtlauf: browser: Komponente über Host packages_dev neu gerendert (Seite ohne globalen box-sizing-Reset, geprüft), Chrome headless per CDP gemessen — 1280 und 768 px: 3 Fähnchen je 52 × 52 px (border-box); Hover und Tab-Fokus: Fahne 177,5 × 52 px, bündig mit dem Fähnchen, sichtbar; 767 und 375 px: 3 gleich breite Links × 60 px unten, body-Abstand 60 px, Footer endet an der Leiste (607/607); Schatten `rgba(17, 24, 39, 0.18)` aus `--kontakt-leiste-schatten`, mit Projektwert `rgb(255, 0, 0)` überschrieben ohne Paket-CSS
Layout: layout: leiste.html gruen.html --breiten 375,768,1280 --hoehen 650 --lang .kontakt-leiste .lang → 0 Befunde
Offener Punkt a258442981b09bde2: AK-fremd (Browsermessung 52 × 52 px und Schatten: im Echtlauf oben erledigt; border-box mobil: bei 375/767 px gemessen, Maße unverändert 125 × 60; Testschärfe „nur über Variablen“ und Kontrast: sollte-Fund, Folge-Task #2098)
Annahme Moderator: neue Variable `--kontakt-leiste-schatten` statt Rahmenfarbe für den Schatten. AK10 verlangt „mindestens“ die genannten Variablen, eine weitere ist zulässig.

## Stand
2026-11-22: T4 REVIEW nicht freigegeben. Review 0/2: 2 muss, 1 sollte, 3 kann. Fehlerprotokoll eingetragen (Wiki `agents/fehlerprotokoll/2026-11-22/packages_dev`). sollte → #2098. Rechtshinweis WhatsApp/Meta: steht in der Spec unter „Offen vor dem Livegang“; Rechtsprüfung im Projekt `nein`, daher kein Eintrag in `hinweise.md`. T5–T7 erledigt (Regressionstests, CSS-Fix, Nachweis grün). Als Nächstes T8 Nachprüfung.
