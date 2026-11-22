# Prüfung: kontakt-leiste — 2026-11-22 (Nachprüfung seit 500faf49)

Geprüft: Kit `git diff 500faf49..d37c8f70` (2 Commits: `dd197c76` Regressionstests AK8/AK10, `d37c8f70` Fix) im Review-Platz `/home/joerg/projekte/.worktrees/packages_dev/reviewer` (Host `review/kontakt-leiste` 94e4e6c). Spec seit 500faf49 unverändert; der Fix ändert nur das Paket-CSS der Komponente (`box-sizing:border-box` an `.kontakt-leiste a` und `.kontakt-leiste .lang`, neue Variable `--kontakt-leiste-schatten` statt Farbliteral im `box-shadow`), README-Zeile und Tests — kein Umbau, daher Nachprüfung statt vollem Review.

Browser-Nachweis: eigener Headless Chrome (`--remote-debugging-port=0`, eigenes Profil im Scratchpad, danach entfernt), Komponente per `php artisan tinker --env=testing` und `Blade::render('<x-kit::kontakt-leiste />')` mit Test-Config gerendert und in statische Seiten eingebettet: `leiste.html` (80 Absätze + Footer + Leiste, **ohne** globalen box-sizing-Reset, `body` = `content-box`), `gruen.html` (dieselbe Seite mit `.kontakt-leiste{--kontakt-leiste-bg:#107c41;--kontakt-leiste-schatten:rgb(255,0,0)}` im `<head>`). Gemessen per CDP (`getBoundingClientRect`, `getComputedStyle`, echte Maus- und Tab-Ereignisse, `Emulation.setEmulatedMedia`) bei 1280, 768, 767 und 375 px; Screenshots 768 (Hover) und 375 angesehen.

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „dann entsteht genau ein `<nav class="kontakt-leiste" aria-label="Direktkontakt">`“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK1 „mit genau drei Links in dieser Reihenfolge: Telefon, E-Mail, WhatsApp“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK2 „Der Telefon-Link hat `href="tel:+<nummer>"` …“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK2 „und zeigt die Anzeigeform der Nummer aus der Config als Text“ | bestanden (Prüfung 500faf49) | Test grün; Hover-Fahne zeigt „+49 4344 - 389 937 8“ |
| AK3 „Der E-Mail-Link hat `href="mailto:<adresse>"`“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK3 „und zeigt die Adresse als Text“ | bestanden (Prüfung 500faf49) | Test grün; Hover-Fahne „info@example.com“ |
| AK4 „Der WhatsApp-Link hat `href="https://wa.me/<nummer>?text=<text>"` mit dem URL-kodierten Text aus der Config“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK4 „dazu `target="_blank"`, `rel="noopener noreferrer"`“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK4 „und `aria-label="Per WhatsApp schreiben (öffnet neues Fenster)"`“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK5 „Telefon-Link und WhatsApp-Link nutzen dieselbe Nummer aus `laravelkit.kontakt_leiste.telefon` (E.164 ohne `+`)“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK5 „sofern `laravelkit.kontakt_leiste.whatsapp` nicht eine eigene Nummer setzt“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK5 „Die Komponente akzeptiert die Nummer auch mit `+`, Leerzeichen oder Bindestrichen und bildet daraus nur Ziffern“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK6 „Die gerenderte Ausgabe enthält keine Quelle und keine Anfrage an `whatsapp.com`, `whatsapp.net`, `facebook.com`, `facebook.net` oder `meta.com`“ | bestanden (Prüfung 500faf49) | Test grün; Browser: `Network.requestWillBeSent` nur die lokale Datei, Konsole leer |
| AK6 „und kein `<script>`“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK6 „Symbole sind Inline-SVG mit `aria-hidden="true"` (kein Font Awesome, kein Bild)“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK7 „Fehlt ein Kontaktweg in der Config (leer oder `null`), entfällt nur dieser Link“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK7 „fehlen alle drei, gibt die Komponente nichts aus“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK7 „Mit `laravelkit.kontakt_leiste.aktiv = false` gibt sie nichts aus“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| AK8 „Die Links stehen als Fähnchen (je 52 × 52 px, nur Symbol)“ | bestanden | Seite ohne Reset (`body` `content-box`): bei 1280 und 768 px je Link **52 × 52** (`box-sizing: border-box` berechnet), `.kurz` display:none, `.lang` hidden. `test_faehnchen_und_fahne_enthalten_den_rahmen_in_52_mal_52_ohne_projekt_reset` grün (prüft nur CSS-Inhalt, mit Gegenprobe; Maß selbst im Browser bestätigt) |
| AK8 „untereinander am rechten Rand“ | bestanden | 1280: x=1228 (= 1280 − 52), y=320/376/432; 768: x=716 (= 768 − 52) |
| AK8 „fest beim Scrollen“ | bestanden | nach `scrollTo(0,2500)` (scrollY 1976) nav-Rechteck unverändert [1228,320,52,164] bzw. [716,320,52,164] |
| AK8 „Bei Hover oder Tastatur-Fokus klappt links daneben eine Fahne mit Nummer, Adresse bzw. „WhatsApp schreiben“ aus“ | bestanden | echte Mausbewegung je Fähnchen (1280 und 768): `.lang` visible/opacity 1, `border-box`, Höhe 52 und y gleich dem Link (320/376/432), rechte Kante an der Innenkante des Links, Texte „+49 4344 - 389 937 8“ / „info@example.com“ / „WhatsApp schreiben“; Maus weg → alle hidden. Tab je Link: `:focus-visible` true, Fahne visible mit denselben Maßen. Screenshot 768 angesehen |
| AK9 „Die Links stehen als feste Leiste über die ganze Breite am unteren Rand“ | bestanden | 375 × 667: nav [0,607,375,60]; 767: [0,607,767,60] |
| AK9 „gleich breit“ | bestanden | 375: 3 × 125 px; 767: 3 × 255,7 px (Links jetzt `border-box`, Maße unverändert) |
| AK9 „je Symbol und kurzer Text („Anrufen“, „E-Mail“, „WhatsApp“)“ | bestanden | `.kurz` display:block mit diesen Texten, `.lang` display:none; Screenshot 375 angesehen |
| AK9 „Höhe ca. 60 px“ | bestanden | Links je 60 px hoch (inkl. Trennrahmen, border-box) |
| AK9 „mit `env(safe-area-inset-bottom)`“ | bestanden (Prüfung 500faf49) | CSS an dieser Stelle unverändert |
| AK9 „bekommt `body` unter 768 px unten so viel Innenabstand wie die Leiste hoch ist“ | bestanden | body `padding-bottom` 60px bei 375 und 767, 0px bei 768 und 1280; Seitenende: Footer-Unterkante 607 = Oberkante Leiste (375 und 767) |
| AK10 „Das Paket-CSS setzt Farben, Rahmen, Schrift und Abstände nur über CSS-Variablen mit neutralen Standardwerten“ | bestanden | Gerendertes CSS ohne `:where(.kontakt-leiste){…}` per grep nach Hex-, `rgb(a)`/`hsl(a)`- und benannten Farben durchsucht: kein Farbliteral (einziger Treffer „white“ aus `white-space`). Schatten jetzt `box-shadow:0 -4px 14px var(--kontakt-leiste-schatten)`, Standard `rgba(17,24,39,.18)`. `test_css_hat_ausserhalb_der_variablen_keine_farbliterale` grün, prüft die Abwesenheit mit vier Gegenproben. Literale Schriftgrößen/Innenabstände wie in 500faf49 als kann bewertet |
| AK10 „mindestens `--kontakt-leiste-bg`, `--kontakt-leiste-text`, `--kontakt-leiste-rand`, `--kontakt-leiste-font`, `--kontakt-leiste-top` (Abstand oben der Fähnchen), `--kontakt-leiste-z`“ | bestanden | alle sechs im `:where`-Block, dazu neu `--kontakt-leiste-schatten` („mindestens“ erlaubt weitere); top 40vh → 320 px bei 800 px Höhe gemessen |
| AK10 „Ein Projekt ändert Farben, ohne Paket-CSS zu überschreiben, z. B. `.kontakt-leiste { --kontakt-leiste-bg: #107c41; }`“ | bestanden | `gruen.html` (Regel im `<head>`, vor dem Paket-CSS): 1280 Links `rgb(16, 124, 65)`; 375 nav und Links `rgb(16, 124, 65)`, Schatten `rgb(255, 0, 0) 0px -4px 14px` — auch die Schattenfarbe jetzt ohne Überschreiben änderbar |
| AK10 „Die Standardwerte erreichen Kontrast ≥ 4.5:1“ | bestanden (Prüfung 500faf49) | Text- und Hintergrundwerte unverändert (#fff auf #1f2937, ca. 14,7:1) |
| AK11 „Das Basis-CSS kommt mit der Komponente (einmal je Seite, `@once`)“ | bestanden (Prüfung 500faf49) | Tests grün; gerendert 1× `<style>` |
| AK11 „und braucht weder Tailwind noch Bootstrap noch einen Build-Schritt im Projekt“ | bestanden | Testseiten ohne jedes Framework-CSS und ohne Reset: Maße laut AK8/AK9 erreicht |
| Randfall 1 „Druckansicht: Die Leiste erscheint nicht.“ | bestanden | `media: print` emuliert (375): nav `display:none`, body-Abstand 0px |
| Randfall 2 „`prefers-reduced-motion: reduce`: Die ausklappende Fahne erscheint ohne Animation.“ | bestanden | emuliert (1280): `.lang` `transition-duration: 0s`, `transform: none` |
| Randfall 3 „Tastatur: Alle Links sind per Tab erreichbar“ | bestanden | echte Tab-Ereignisse 1280: Telefon → E-Mail → WhatsApp → BODY |
| Randfall 3 „der Fokus ist sichtbar (`:focus-visible`)“ | bestanden | je Link `:focus-visible` true, Outline `solid 2px rgb(255, 255, 255)`, Fahne klappt aus (1280 und 768) |
| Randfall 4 „Die Komponente wird zweimal auf einer Seite eingebunden: Das CSS steht trotzdem nur einmal im Quelltext.“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| Randfall 5 „Sonderzeichen im WhatsApp-Text (Umlaute, `&`, `?`, `#`) werden URL-kodiert“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| Randfall 5 „im `href` steht kein unkodiertes Leerzeichen“ | bestanden (Prüfung 500faf49) | Test weiter grün |
| Nicht-Ziele (kein JavaScript, kein Widget, keine Datenschutz-Texte, kein „nach oben“-Pfeil, keine Projektfarben) | bestanden (Prüfung 500faf49) | Fix fügt nichts davon hinzu; Standardschatten neutral (Grau) |

## Sicherheit
- Diff des Fixes: nur CSS-Deklarationen (`box-sizing`, eine neue Variable mit festem Standardwert), README-Zeile, Tests. Keine Ausgabe von Config-Werten ins CSS, keine Route, kein Endpunkt, keine Daten. Nichts Neues zu prüfen.

## Regressionen
- `php vendor/bin/phpunit -c phpunit.xml` je Datei im Platz:
  - `packages/laravel-kit/tests/Feature/KitComponents/KontaktLeisteTest.php` (Feature, `tests-fuer` für die Komponente, `git grep`): 18/18 grün
  - `packages/laravel-kit/tests/Feature/KitComponents/BausteineTest.php` („Bestehende Tests“ laut `plan.md`): 464 grün
  - `packages/laravel-kit/tests/Feature/BootTest.php` („Bestehende Tests“ laut `plan.md`): 9 grün
- `storage/logs/laravel.log` des Platzes: keine Einträge vom 2026-11-22. Browser-Konsole: leer.
- Ganze Suite nicht gelaufen (kein `SUITE: ja` im Auftrag).

## Funde: 0 muss, 0 sollte, 3 kann
- Die beiden muss-Funde aus 500faf49 (AK8 Fähnchengröße, AK10 Schattenfarbe) sind behoben und im Browser nachgewiesen.
- Der sollte-Fund aus 500faf49 (→ #2098) ist zum Teil erledigt: der neue Test `test_css_hat_ausserhalb_der_variablen_keine_farbliterale` prüft „nur über Variablen“ als Abwesenheit. Offen in #2098 bleiben der irreführende Name von `test_css_setzt_farben_nur_ueber_variablen` und der fehlende Kontrast-Test. Kein neuer Folge-Task.
- kann (aus 500faf49, unverändert): literale Schriftgrößen, Innenabstände, Rahmenbreite, Radius; `<style>` im `<body>`; `--kontakt-leiste-z` 1030 kann mit Cookie-Bannern kollidieren.

## Neue Tasks: keine

## Fehlerprotokoll
- keine

## Rechtshinweise
- Unverändert aus 500faf49: Komponente `kontakt-leiste` / README: der WhatsApp-Link überträgt beim Klick Daten an Meta (wa.me); die Datenschutzerklärung jedes einbindenden Projekts muss das nennen.

## Fazit: freigegeben
Nachprüfung (Spec unverändert, Fix nur lokal im Paket-CSS). AK8 „je 52 × 52 px“ hält jetzt auf einer Seite ohne Reset, Fahne und mobile Leiste behalten ihre Maße; AK10: im CSS steht keine Farbe mehr außerhalb der Variablen, und die Schattenfarbe lässt sich ohne Überschreiben ändern. Übrige Aussagen aus 500faf49 übernommen, Tests grün.
