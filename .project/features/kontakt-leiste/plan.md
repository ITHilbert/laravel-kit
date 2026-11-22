# Plan: kontakt-leiste (Feature)

Branch: `feature/kontakt-leiste` (Paket laravel-kit) · Spec: `spec.md` · Spec freigegeben: User 2026-11-22
Marketing: entfällt (Baustein ohne eigene Seite und ohne Botschaft; Vorbild it-entwickler-hilbert.de vom User vorgegeben)
Design: entfällt (Vorlage it-entwickler-hilbert.de `kontakt-leiste`, vom User vorgegeben; Farben setzt das Projekt)

## Aufgaben
- [x] T1 (AK1–AK7, AK10, AK11, Randfall 1–5) test-writer: Feature-Tests Markup, Config, CSS-Ausgabe — Tests: `tests/Feature/KitComponents/KontaktLeisteTest.php`
- [x] T2 (AK1–AK11) [pro] anonyme Komponente `src/Views/components/kit/kontakt-leiste.blade.php` mit Inline-SVG und `@once`-CSS (Variablen, Medienabfragen 768 px, Druck, reduzierte Bewegung), Config `kontakt_leiste` in `src/Config/laravelkit.php`, README-Abschnitt, CHANGELOG „Unveröffentlicht“ — Tests: `tests/Feature/KitComponents/KontaktLeisteTest.php`
- [x] T3 NACHWEIS (Moderator): GATE, Tests, Sichtprüfung 375/1280 im Host packages_dev
- [ ] T4 REVIEW (reviewer, Review-Platz) → `pruefung.md`

Bestehende Tests: `tests/Feature/KitComponents/BausteineTest.php`, `tests/Feature/BootTest.php`

## Nachweis
GATE coder-pro ad36994d350d9e1b6: GRÜN (527 passed, Pint ok), Log `excel-programmierer.com/storage/logs/claude-gate.log` 2026-11-22 21:47:02
Grünbar: `KontaktLeisteTest.php` 16/16 manuell (pruefen kennt keinen Testbefehl im Paket)
Echtlauf: browser: Komponente über Host packages_dev gerendert (Testseiten `leiste.html`, `leiste-gruen.html`, `kurz.html`, `hover.html`), Chrome headless 1280 und 375 px angesehen — Fähnchen rechts 52 px, Fahne bei Hover sichtbar, Projektfarbe über `--kontakt-leiste-bg` greift, mobil 3 gleich breite Spalten unten, Footer am Seitenende nicht verdeckt
Layout: layout: leiste-gruen.html kurz.html --breiten 375,1280 --hoehen 650 --lang .kontakt-leiste .lang → 0 Befunde
Offener Punkt ad36994d350d9e1b6: AK-fremd (Umsetzungsdetails im Rahmen von AK8–AK10: Standardwerte in `:where(.kontakt-leiste)` mit Spezifität 0, damit Projektwerte auch aus dem `<head>` gewinnen; `--kontakt-leiste-top` 40vh, `--kontakt-leiste-z` 1030; Mobil-Grid mit `grid-auto-columns`, damit bei 2 Wegen 2 gleich breite Spalten entstehen)

## Stand
2026-11-22: T1–T3 erledigt, T4 REVIEW als Nächstes.
