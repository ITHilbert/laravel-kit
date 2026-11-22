# Prüfung: kontakt-leiste — 2026-11-22

Geprüft: Kit `git diff origin/main...55f89cc4` (3 Commits: Spec/Plan, Tests, Feature) im Review-Platz `/home/joerg/projekte/.worktrees/packages_dev/reviewer` (Host 9337659).
Browser-Nachweis: eigener Headless Chrome (`--remote-debugging-port=0`, eigenes Profil im Scratchpad), Komponente per `Blade::render` mit Test-Config in statische Seiten gerendert (`leiste.html`: 80 Absätze + Footer + Leiste; `gruen.html`: dieselbe Seite mit `.kontakt-leiste{--kontakt-leiste-bg:#107c41}` im `<head>`; `borderbox.html`: mit globalem `box-sizing:border-box`; `zwei.html`: ohne E-Mail-Link). Gemessen per CDP (`getComputedStyle`, `getBoundingClientRect`, `elementFromPoint`, echte Maus- und Tab-Ereignisse, `Emulation.setEmulatedMedia`), dazu Screenshots 1280 px (Hover) und 375 px.

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „dann entsteht genau ein `<nav class="kontakt-leiste" aria-label="Direktkontakt">`“ | bestanden | `test_genau_eine_nav_mit_drei_links_in_reihenfolge` grün; gerendertes HTML: 1× `<nav class="kontakt-leiste" aria-label="Direktkontakt">` |
| AK1 „mit genau drei Links in dieser Reihenfolge: Telefon, E-Mail, WhatsApp“ | bestanden | derselbe Test (DOM, Reihenfolge tel:/mailto:/wa.me); Browser: 3 `a` in dieser Reihenfolge |
| AK2 „Der Telefon-Link hat `href="tel:+<nummer>"` (Nummer im Format E.164 aus der Config, z. B. `tel:+4943443899378`)“ | bestanden | `test_telefon_link_mit_e164_und_anzeigeform` grün; gerendert `href="tel:+4943443899378"` |
| AK2 „und zeigt die Anzeigeform der Nummer aus der Config als Text (z. B. `+49 4344 - 389 937 8`)“ | bestanden | Test grün; Browser 1280: Hover-Fahne zeigt „+49 4344 - 389 937 8“ (`.lang`), mobil Kurztext „Anrufen“ laut AK9 |
| AK3 „Der E-Mail-Link hat `href="mailto:<adresse>"`“ | bestanden | `test_email_link_mit_adresse_als_text` grün |
| AK3 „und zeigt die Adresse als Text“ | bestanden | Test grün; Browser: Hover-Fahne „info@example.com“ |
| AK4 „Der WhatsApp-Link hat `href="https://wa.me/<nummer>?text=<text>"` mit dem URL-kodierten Text aus der Config“ | bestanden | `test_whatsapp_link_mit_kodiertem_text_und_attributen` grün; gerendert `https://wa.me/4943443899378?text=Hallo%20%26%20Gr%C3%BC%C3%9Fe%3F%20%231` |
| AK4 „dazu `target="_blank"`, `rel="noopener noreferrer"`“ | bestanden | Test grün, Attribute im HTML |
| AK4 „und `aria-label="Per WhatsApp schreiben (öffnet neues Fenster)"`“ | bestanden | Test grün |
| AK5 „Telefon-Link und WhatsApp-Link nutzen dieselbe Nummer aus `laravelkit.kontakt_leiste.telefon` (E.164 ohne `+`)“ | bestanden | `test_nummer_in_jeder_schreibweise_…` grün; ohne `whatsapp` → `wa.me/4943443899378` |
| AK5 „sofern `laravelkit.kontakt_leiste.whatsapp` nicht eine eigene Nummer setzt“ | bestanden | derselbe Test: `whatsapp=+49 170 1234567` → `wa.me/491701234567`, tel bleibt |
| AK5 „Die Komponente akzeptiert die Nummer auch mit `+`, Leerzeichen oder Bindestrichen und bildet daraus nur Ziffern“ | bestanden | derselbe Test mit drei Schreibweisen; Code `preg_replace('/\D+/', '', …)` |
| AK6 „Die gerenderte Ausgabe enthält keine Quelle und keine Anfrage an `whatsapp.com`, `whatsapp.net`, `facebook.com`, `facebook.net` oder `meta.com`“ | bestanden | `test_keine_fremden_quellen_…` grün (mit Gegenprobe); Browser: `Network.requestWillBeSent` nur die lokale Datei, keine Konsolenmeldung |
| AK6 „und kein `<script>`“ | bestanden | Test; gerendertes HTML: 0× `<script`, 0× `<link`, 0× `<img` |
| AK6 „Symbole sind Inline-SVG mit `aria-hidden="true"` (kein Font Awesome, kein Bild)“ | bestanden | Test (3× `<svg`, 3× `aria-hidden="true"`); HTML ohne `fa-`, ohne `<img`; Browser: SVG 22 × 22 sichtbar |
| AK7 „Fehlt ein Kontaktweg in der Config (leer oder `null`), entfällt nur dieser Link“ | bestanden | `test_fehlender_kontaktweg_entfaellt_nur_dieser_link` grün (je Weg, `''` und `null`); Browser `zwei.html`: 2 Links |
| AK7 „fehlen alle drei, gibt die Komponente nichts aus“ | bestanden | `test_ohne_kontaktwege_oder_inaktiv_keine_ausgabe` grün (Ausgabe leer, auch kein `<style>`) |
| AK7 „Mit `laravelkit.kontakt_leiste.aktiv = false` gibt sie nichts aus“ | bestanden | derselbe Test |
| AK8 „Die Links stehen als Fähnchen (je 52 × 52 px, nur Symbol)“ | nicht bestanden | Browser 1280 ohne Box-Sizing-Reset: je Link **53 × 54 px** (`box-sizing: content-box`, `width/height:52px` + 1 px Rahmen links/oben/unten). Nur mit globalem `box-sizing:border-box` des Projekts (`borderbox.html`) 52 × 52. „nur Symbol“ erfüllt (`.kurz` display:none, `.lang` hidden/0) |
| AK8 „untereinander am rechten Rand“ | bestanden | Browser 1280: alle drei bei x=1212, y=320/378/436, rechter Rand = Rand des Viewports (ohne Scrollleiste) |
| AK8 „fest beim Scrollen“ | bestanden | `position: fixed`; nach `scrollTo(0,2500)` Rechteck der nav unverändert [1212,320,1265,490] |
| AK8 „Bei Hover oder Tastatur-Fokus klappt links daneben eine Fahne mit Nummer, Adresse bzw. „WhatsApp schreiben“ aus“ | bestanden | echte Mausbewegung je Fähnchen: `.lang` visible/1, rechte Kante 1213 = linke Kante des Links, gleiche Höhe, Texte „+49 4344 - 389 937 8“ / „info@example.com“ / „WhatsApp schreiben“; Maus weg → hidden. Tab: Fahne visible/1 je fokussiertem Link (Screenshot geprüft) |
| AK9 „Die Links stehen als feste Leiste über die ganze Breite am unteren Rand“ | bestanden | Browser 375 × 667 (mobil): nav fixed, [0,607,375,667] |
| AK9 „gleich breit“ | bestanden | 3 × 125 px; mit 2 Links 2 × 187,5 px |
| AK9 „je Symbol und kurzer Text („Anrufen“, „E-Mail“, „WhatsApp“)“ | bestanden | `.kurz` sichtbar mit diesen Texten, SVG 22 × 22, `.lang` display:none; Screenshot geprüft |
| AK9 „Höhe ca. 60 px“ | bestanden | Links 60 px hoch |
| AK9 „mit `env(safe-area-inset-bottom)`“ | bestanden | CSS `padding-bottom:env(safe-area-inset-bottom)` an der nav, im body-Abstand eingerechnet; Test prüft Vorkommen (Wert ohne Notch-Gerät 0, nicht messbar) |
| AK9 „bekommt `body` unter 768 px unten so viel Innenabstand wie die Leiste hoch ist“ | bestanden | body `padding-bottom` 60px bei 375 und 767 px, 0px bei 768 und 1280 px; Seitenende: Footer-Unterkante 607 = Oberkante Leiste, nicht verdeckt |
| AK9 Grenze „unter 768 px“ / AK8 „ab 768 px“ | bestanden | 767 px → Leiste unten, 768 px → Fähnchen rechts |
| AK10 „Das Paket-CSS setzt Farben, Rahmen, Schrift und Abstände nur über CSS-Variablen mit neutralen Standardwerten“ | nicht bestanden | Mobile Leiste: `box-shadow:0 -4px 14px rgba(17,24,39,.18)` — Farbliteral außerhalb der Variablen (kontakt-leiste.blade.php, `@media (max-width:767.98px)`). Alle übrigen Farben (Hintergrund, Text, Rahmen, Fokus-Outline) über Variablen. Der Test `test_css_setzt_farben_nur_ueber_variablen` prüft nur das Vorhandensein der Variablen, nicht „nur“ |
| AK10 „mindestens `--kontakt-leiste-bg`, `--kontakt-leiste-text`, `--kontakt-leiste-rand`, `--kontakt-leiste-font`, `--kontakt-leiste-top` (Abstand oben der Fähnchen), `--kontakt-leiste-z`“ | bestanden | Test grün; Browser: z-index 1030, top 40vh (320 px bei 800 px Höhe) aus den Variablen |
| AK10 „Ein Projekt ändert Farben, ohne Paket-CSS zu überschreiben, z. B. `.kontakt-leiste { --kontakt-leiste-bg: #107c41; }`“ | bestanden | `gruen.html` (Regel im `<head>`, also vor dem Paket-CSS): Links desktop und nav mobil `rgb(16, 124, 65)` — wirkt dank `:where()` |
| AK10 „Die Standardwerte erreichen Kontrast ≥ 4.5:1“ | bestanden | #fff auf #1f2937 = ca. 14,7:1 (selbst berechnet; kein Test) |
| AK11 „Das Basis-CSS kommt mit der Komponente (einmal je Seite, `@once`)“ | bestanden | `@once` im Template; `test_css_kommt_mit_der_komponente_…` und Randfall-4-Test grün |
| AK11 „und braucht weder Tailwind noch Bootstrap noch einen Build-Schritt im Projekt“ | bestanden | Browser-Seiten ohne jedes Framework-CSS: Darstellung vollständig (siehe AK8/AK9); keine Framework-Klassen im Markup (Test) |
| Randfall 1 „Druckansicht: Die Leiste erscheint nicht.“ | bestanden | `media: print` emuliert: nav `display:none` bei 1280 und 375, body-Abstand 0px |
| Randfall 2 „`prefers-reduced-motion: reduce`: Die ausklappende Fahne erscheint ohne Animation.“ | bestanden | emuliert: `.lang` `transition-duration: 0s`, `transform: none` |
| Randfall 3 „Tastatur: Alle Links sind per Tab erreichbar“ | bestanden | echte Tab-Ereignisse: Telefon → E-Mail → WhatsApp → BODY |
| Randfall 3 „der Fokus ist sichtbar (`:focus-visible`)“ | bestanden | je Link `:focus-visible` = true, Outline `solid 2px rgb(255,255,255)` auf dunklem Grund, Fahne klappt aus |
| Randfall 4 „Die Komponente wird zweimal auf einer Seite eingebunden: Das CSS steht trotzdem nur einmal im Quelltext.“ | bestanden | `test_zweimal_eingebunden_css_nur_einmal` grün (2× nav, 1× `<style>`) |
| Randfall 5 „Sonderzeichen im WhatsApp-Text (Umlaute, `&`, `?`, `#`) werden URL-kodiert“ | bestanden | `test_sonderzeichen_im_whatsapp_text_sind_kodiert` grün; gerendert `%C3%BC %26 %3F %23` |
| Randfall 5 „im `href` steht kein unkodiertes Leerzeichen“ | bestanden | derselbe Test; gerendert `%20` |
| Nicht-Ziel „kein JavaScript“, kein Widget/Plugin/API | bestanden | kein `<script>`, keine Anfragen |
| Nicht-Ziel „Keine Datenschutz-Texte“, kein „nach oben“-Pfeil, keine Projektfarben | bestanden | nicht gebaut; Standardfarben neutral (Grau/Weiß) |

## Sicherheit
- Alle Werte kommen aus der Config (Betreiber, keine Nutzereingabe) und werden mit `{{ }}` escaped (Text, `aria-label`, `href`).
- Link-Schemata fest vorgegeben (`tel:+` nur Ziffern, `mailto:`, `https://wa.me/` nur Ziffern + `rawurlencode`) — kein `javascript:` einschleusbar.
- `target="_blank"` mit `rel="noopener noreferrer"`.
- Keine Route, kein Endpunkt, keine DB, keine Rechte berührt. Risiko `normal` passt (kein Login, keine Zahlung, keine Geheimnisse, kein Löschen, keine Kundendaten).

## Regressionen
- `php artisan test --compact` mit den Dateien `packages/laravel-kit/tests/Feature/KitComponents/KontaktLeisteTest.php`, `…/KitComponents/BausteineTest.php`, `…/Feature/BootTest.php`, `…/KitComponents/FehleranzeigeSchalterTest.php`, `…/KitComponents/ThemeTest.php`, `…/Feature/LogMigrationOptionalTest.php`, `…/Unit/BrowserAltlastenTest.php` (Plan „Bestehende Tests“ + `git grep` auf Config/Komponente): **1090 passed** (der Aufruf mit mehreren Pfaden lief über die ganze Host-Suite), 7,3 s.
- `KontaktLeisteTest.php` einzeln (`phpunit -c phpunit.xml --testdox`): 16/16 grün.
- `storage/logs/laravel.log` des Platzes: keine Einträge vom 2026-11-22. Browser-Konsole: leer.
- Bestehendes Verhalten: nur neue Datei, neuer Config-Schlüssel (per `mergeConfigFrom` auch bei veröffentlichter Config vorhanden), README/CHANGELOG — keine bestehende Komponente geändert.

## Funde: 2 muss, 1 sollte, 3 kann
- muss: AK8 „je 52 × 52 px“ nicht erfüllt ohne Box-Sizing-Reset im Projekt — gemessen 53 × 54 px (content-box: 52 + Rahmen); 52 × 52 nur, wenn das Projekt global `border-box` setzt. Die Komponente soll ohne Framework auskommen (AK11), die Größe hängt also vom Projekt ab. Behebung naheliegend: `box-sizing:border-box` für `.kontakt-leiste a` und `.lang` im Paket-CSS.
- muss: AK10 „setzt Farben … nur über CSS-Variablen“ — Schattenfarbe `rgba(17,24,39,.18)` der mobilen Leiste fest im CSS; ein Projekt kann sie nur durch Überschreiben des Paket-CSS ändern.
- sollte: `test_css_setzt_farben_nur_ueber_variablen` verspricht „nur“, prüft aber nur das Vorhandensein der Variablen; Kontrast ≥ 4.5:1 ungetestet (→ Folge-Task #2098).
- kann: Feste Schriftgrößen (13/15 px), `font-weight`, Innenabstände, Rahmenbreite und Radius sind Literale; die Spec nennt für Rahmen/Schrift/Abstände nur die Mindest-Variablen `rand`/`font`/`top`, daher kein AK-Verstoß. Wer streng liest, könnte weitere Variablen erwarten.
- kann: `<style>` steht im `<body>` (README: vor `</body>`); funktioniert in allen Browsern, ist nach HTML-Validator aber nicht konform. Alternative wäre ein `@push('styles')`-Stack — würde aber ein Layout mit Stack voraussetzen.
- kann: `--kontakt-leiste-z` 1030 kann mit Cookie-Bannern (consentLaw) am unteren Rand kollidieren; Projekte setzen die Variable bei Bedarf.

## Neue Tasks: #2098 laravel-kit/kontakt-leiste – Nacharbeit aus Review kontakt-leiste

## Fehlerprotokoll
- {"schwere":"muss","ak":"AK8","fund":"Fähnchen 53 × 54 px statt 52 × 52 px, weil width/height ohne box-sizing:border-box gesetzt sind und der Rahmen dazukommt; nur in Projekten mit globalem border-box-Reset stimmt die Größe","ursache":"Größe nur in einer Testseite mit Projekt-Reset bzw. nur die Breite angesehen, nicht in einer Seite ohne Framework-CSS gemessen","anweisung":"Komponenten, die ohne Framework-CSS auskommen sollen, setzen box-sizing selbst und werden im Browser auf einer Seite ohne Reset gegen die Pixelmaße der Spec gemessen (getBoundingClientRect)"}
- {"schwere":"muss","ak":"AK10","fund":"box-shadow der mobilen Leiste nutzt das Farbliteral rgba(17,24,39,.18) statt einer CSS-Variable","ursache":"Test prüft nur das Vorhandensein der geforderten Variablen, nicht die Aussage 'nur über CSS-Variablen'; Schatten nicht als Farbe erkannt","anweisung":"Bei 'Farben nur über Variablen' jedes Farbliteral im CSS (auch in box-shadow, outline, gradient) suchen und durch eine Variable ersetzen; der Test prüft die Abwesenheit von Literalen außerhalb der Variablen-Definition"}
- {"schwere":"sollte","ak":"AK10","fund":"Testname verspricht 'Farben nur über Variablen', Test prüft nur vorhandene Variablen und keinen Kontrast","ursache":"Aussage mit 'nur' als Vorhandensein getestet statt als Abwesenheit des Gegenteils","anweisung":"Aussagen mit 'nur' oder 'kein' als Abwesenheit des Unerwünschten testen, mit Gegenprobe; jede messbare Zahl der Spec (Kontrast) bekommt eine Prüfung"}

## Rechtshinweise
- Komponente `kontakt-leiste` / README: WhatsApp-Link überträgt beim Klick Daten an Meta (wa.me); die Datenschutzerklärung jedes einbindenden Projekts muss das nennen (Spec „Offen vor dem Livegang“, für excel-programmierer.com dort als vorhanden vermerkt).

## Fazit: nicht freigegeben
Zwei AK-Aussagen nicht erfüllt (AK8 Fähnchengröße ohne Projekt-Reset, AK10 feste Schattenfarbe); beide klein und lokal im Paket-CSS behebbar. Alles Übrige ist selbst nachgewiesen, Tests grün, keine Regression.
