# Plan: kontakt-leiste (Feature)

Branch: `feature/kontakt-leiste` (Paket laravel-kit) · Spec: `spec.md` · Spec freigegeben: User 2026-11-22
Marketing: entfällt (Baustein ohne eigene Seite und ohne Botschaft; Vorbild it-entwickler-hilbert.de vom User vorgegeben)
Design: entfällt (Vorlage it-entwickler-hilbert.de `kontakt-leiste`, vom User vorgegeben; Farben setzt das Projekt)

## Aufgaben
- [ ] T1 (AK1–AK7, AK10, AK11, Randfall 1–5) test-writer: Feature-Tests Markup, Config, CSS-Ausgabe — Tests: `tests/Feature/KitComponents/KontaktLeisteTest.php`
- [ ] T2 (AK1–AK11) [pro] anonyme Komponente `src/Views/components/kit/kontakt-leiste.blade.php` mit Inline-SVG und `@once`-CSS (Variablen, Medienabfragen 768 px, Druck, reduzierte Bewegung), Config `kontakt_leiste` in `src/Config/laravelkit.php`, README-Abschnitt, CHANGELOG „Unveröffentlicht“ — Tests: `tests/Feature/KitComponents/KontaktLeisteTest.php`
- [ ] T3 NACHWEIS (Moderator): GATE, Tests, Sichtprüfung 375/1280 im Host packages_dev
- [ ] T4 REVIEW (reviewer, Review-Platz) → `pruefung.md`

Bestehende Tests: `tests/Feature/KitComponents/BausteineTest.php`, `tests/Feature/BootTest.php`

## Stand
2026-11-22: Spec und Plan geschrieben, T1 als Nächstes.
