# Spec: kontakt-leiste — Telefon, E-Mail und WhatsApp als feste Kontakt-Leiste

Anlass: Joerg 2026-11-22 (Session excel-programmierer.com): „WhatsApp-Fähnchen und bei kleiner Auflösung Buttons, so wie bei it-entwickler-hilbert.de“ — als Baustein in laravel-kit für alle Homepages. „CSS nur für grundsätzliche Funktionalität und Erscheinung, Farben dann im Projekt.“
Risiko: normal (Darstellungs-AK, neue öffentliche Komponente)
Skills: tech-whatsapp, core-dsgvo-frontend
Freigabe: User 2026-11-22 (Inhalt in der Session excel-programmierer.com freigegeben, Ort laravel-kit, AK5 an Paket angepasst)

## Ziel
Eine Blade-Komponente `<x-kit::kontakt-leiste />` gibt auf jeder Seite drei Kontaktwege aus: Telefon, E-Mail und WhatsApp. Auf dem Desktop stehen sie als Fähnchen am rechten Rand, auf dem Handy als feste Leiste unten. Vorbild ist die `kontakt-leiste` von it-entwickler-hilbert.de. Das Paket liefert Aufbau, Verhalten und eine neutrale Erscheinung; Farben setzt jedes Projekt über CSS-Variablen.

## Ist-Stand
- laravel-kit registriert anonyme Komponenten unter `src/Views/components/kit` mit Präfix `kit` (`LaravelKitServiceProvider::registerKitComponents`). Die Kit-Komponenten hängen sonst am Theme (`laravelkit.theme`); diese Komponente ist themenunabhängig.
- Config `src/Config/laravelkit.php`, veröffentlichbar unter Tag `config`.

## Akzeptanzkriterien
- **AK1:** Gegeben die Config `laravelkit.kontakt_leiste` mit Telefon, E-Mail und WhatsApp, wenn `<x-kit::kontakt-leiste />` gerendert wird, dann entsteht genau ein `<nav class="kontakt-leiste" aria-label="Direktkontakt">` mit genau drei Links in dieser Reihenfolge: Telefon, E-Mail, WhatsApp.
- **AK2:** Der Telefon-Link hat `href="tel:+<nummer>"` (Nummer im Format E.164 aus der Config, z. B. `tel:+4943443899378`) und zeigt die Anzeigeform der Nummer aus der Config als Text (z. B. `+49 4344 - 389 937 8`).
- **AK3:** Der E-Mail-Link hat `href="mailto:<adresse>"` und zeigt die Adresse als Text.
- **AK4:** Der WhatsApp-Link hat `href="https://wa.me/<nummer>?text=<text>"` mit dem URL-kodierten Text aus der Config, dazu `target="_blank"`, `rel="noopener noreferrer"` und `aria-label="Per WhatsApp schreiben (öffnet neues Fenster)"`.
- **AK5:** Telefon-Link und WhatsApp-Link nutzen dieselbe Nummer aus `laravelkit.kontakt_leiste.telefon` (E.164 ohne `+`), sofern `laravelkit.kontakt_leiste.whatsapp` nicht eine eigene Nummer setzt. Die Komponente akzeptiert die Nummer auch mit `+`, Leerzeichen oder Bindestrichen und bildet daraus nur Ziffern.
- **AK6:** Die gerenderte Ausgabe enthält keine Quelle und keine Anfrage an `whatsapp.com`, `whatsapp.net`, `facebook.com`, `facebook.net` oder `meta.com` und kein `<script>`; Symbole sind Inline-SVG mit `aria-hidden="true"` (kein Font Awesome, kein Bild).
- **AK7:** Fehlt ein Kontaktweg in der Config (leer oder `null`), entfällt nur dieser Link; fehlen alle drei, gibt die Komponente nichts aus. Mit `laravelkit.kontakt_leiste.aktiv = false` gibt sie nichts aus.
- **AK8 (Darstellung Desktop, ab 768 px):** Die Links stehen als Fähnchen (je 52 × 52 px, nur Symbol) untereinander am rechten Rand, fest beim Scrollen. Bei Hover oder Tastatur-Fokus klappt links daneben eine Fahne mit Nummer, Adresse bzw. „WhatsApp schreiben“ aus.
- **AK9 (Darstellung mobil, unter 768 px):** Die Links stehen als feste Leiste über die ganze Breite am unteren Rand, gleich breit, je Symbol und kurzer Text („Anrufen“, „E-Mail“, „WhatsApp“), Höhe ca. 60 px, mit `env(safe-area-inset-bottom)`. Damit die Leiste keinen Inhalt verdeckt, bekommt `body` unter 768 px unten so viel Innenabstand wie die Leiste hoch ist.
- **AK10 (Farben im Projekt):** Das Paket-CSS setzt Farben, Rahmen, Schrift und Abstände nur über CSS-Variablen mit neutralen Standardwerten: mindestens `--kontakt-leiste-bg`, `--kontakt-leiste-text`, `--kontakt-leiste-rand`, `--kontakt-leiste-font`, `--kontakt-leiste-top` (Abstand oben der Fähnchen), `--kontakt-leiste-z`. Ein Projekt ändert Farben, ohne Paket-CSS zu überschreiben, z. B. `.kontakt-leiste { --kontakt-leiste-bg: #107c41; }`. Die Standardwerte erreichen Kontrast ≥ 4.5:1.
- **AK11 (CSS ohne Build):** Das Basis-CSS kommt mit der Komponente (einmal je Seite, `@once`) und braucht weder Tailwind noch Bootstrap noch einen Build-Schritt im Projekt.

## Randfälle
- Randfall 1: Druckansicht: Die Leiste erscheint nicht.
- Randfall 2: `prefers-reduced-motion: reduce`: Die ausklappende Fahne erscheint ohne Animation.
- Randfall 3: Tastatur: Alle Links sind per Tab erreichbar, der Fokus ist sichtbar (`:focus-visible`).
- Randfall 4: Die Komponente wird zweimal auf einer Seite eingebunden: Das CSS steht trotzdem nur einmal im Quelltext.
- Randfall 5: Sonderzeichen im WhatsApp-Text (Umlaute, `&`, `?`, `#`) werden URL-kodiert; im `href` steht kein unkodiertes Leerzeichen.

## Nicht-Ziele
- Kein WhatsApp-Widget, kein Chat-Plugin, keine WhatsApp-Business-API, kein JavaScript.
- Keine Datenschutz-Texte (kommen aus consentLaw).
- Kein „nach oben“-Pfeil und keine Projektfarben im Paket.
- Einbau in excel-programmierer.com ist ein eigener Schritt im Projekt (Paket-Update, alte Fähnchen entfernen, Farben setzen).

## Entscheidungsspielraum
- Keine neuen Composer- oder npm-Pakete.
- Config-Schlüssel und Struktur unter `laravelkit.kontakt_leiste` (z. B. `aktiv`, `telefon`, `telefon_anzeige`, `email`, `whatsapp`, `whatsapp_text`, Env-Variablen `LARAVELKIT_KONTAKT_*`) legt der Coder fest; README nennt sie.
- Texte („Anrufen“, „E-Mail“, „WhatsApp“, „WhatsApp schreiben“) dürfen über Slots oder Config überschreibbar sein, Pflicht ist es nicht.
- Darstellungs-AKs (AK8, AK9) prüft der Moderator im Browser (layout-pruefung 375/1280) im Host packages_dev; PHPUnit prüft Markup, Config und CSS-Inhalt (AK10/AK11 als vorhandene Variablen und Medienabfragen im ausgegebenen CSS).

## Offen vor dem Livegang (je Projekt)
- WhatsApp Business auf der Nummer: `https://wa.me/<nummer>` am Handy öffnen, der Chat muss mit der Nummer erscheinen (tech-whatsapp Regel 4).
- Datenschutzerklärung des Projekts enthält einen Abschnitt zu WhatsApp (excel-programmierer.com: „4.6 Kontakt über WhatsApp“ vorhanden, geprüft 2026-11-22).

Rückbau: entfällt (keine Übergangslösung)
