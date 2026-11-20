# Spec hbutton-svg
Risiko: normal (Darstellung von HTML-Buttons, keine Route, keine Migration, kein Sicherheitsbezug; Escaping bleibt unverändert)
Art: Bugfix — #1795 (Fund aus consentLaw `admin-listen` #1785, 2026-11-20). Widerspricht dem Ziel der Vue-Buttons (Inline-SVG) und `core-dsgvo-frontend` (kein Font Awesome).
Status: selbst freigegeben (automode torrent-packages_dev, 2026-11-20; Task #1795 ist von Joerg freigegeben).
Rückbau: entfällt (kein Übergangsstand)

## Ziel
`ITHilbert\LaravelKit\Helpers\HButton` gibt Icons als Inline-SVG aus (gleiche Pfade wie `vue/tailwind/buttons/button-*.vue`) statt `<i class="fas fa-…">`. Knöpfe ohne Text bekommen einen zugänglichen Namen. Font Awesome wird nicht mehr gebraucht.

## Akzeptanzkriterien
- **AK1** Gegeben ein Aufruf von `HButton::edit`, `show`, `delete`, `create`, `save`, `back` oder `project`, Wenn die Ausgabe erzeugt wird, Dann enthält sie ein `<svg … aria-hidden="true">` mit `viewBox="0 0 24 24"` und `stroke="currentColor"`, und keinen `<i`-Tag und kein `fa-`.
- **AK2** Gegeben `HButton::edit`, `show` und `delete`, Wenn sie ausgegeben werden, Dann stimmen die Pfad-Elemente des SVG mit denen von `button-edit.vue`, `button-show.vue` und `button-delete.vue` überein (Stift, Auge, Papierkorb).
- **AK3** Gegeben ein Aufruf mit leerem Text (`''`), Wenn die Ausgabe erzeugt wird, Dann trägt das `<a>` bzw. `<button>` ein Attribut `aria-label` mit dem Tooltip-Text (escaped wie bisher).
- **AK4** Gegeben ein Aufruf mit nichtleerem Text, Wenn die Ausgabe erzeugt wird, Dann steht kein `aria-label` im Element (der Name kommt aus dem Text).
- **AK5** Gegeben beliebige Werte für Route, Text, Tooltip, Klasse und Attribute, Wenn die Ausgabe erzeugt wird, Dann gilt das bisherige Escaping und die bisherige Attribut-Reihenfolge unverändert; nur das Icon-Element ist ersetzt (bestehende Tests `HButtonEscapeTest`, `HButtonDeleteTest` mit angepasstem Icon-Erwartungswert).
- **AK6** Gegeben `src/Helpers/HButton.php`, Wenn man den Quelltext liest, Dann kommt `fa-` nicht mehr vor.

## Randfälle
1. Text als `HtmlString`/`Htmlable` zählt als leer, wenn seine Zeichenkette leer ist (nach `trim`).
2. `$attributes` mit eigenem `aria-label` bleibt erhalten (kein doppeltes Attribut): ein fest gesetztes `aria-label` entfällt, wenn der Aufrufer eines übergibt.
3. README oder CHANGELOG („Unveröffentlicht“) nennen die Änderung.

## Nicht-Ziele
- Die Blade-Components unter `src/Views/kit/bootstrap5/button-*.blade.php` und `Classes/Bootstrap4/Button.php` (ebenfalls `fas`) bleiben unverändert; Folge-Task.
- Kein Umbau der Vue-Buttons.

## Entscheidungsspielraum
SVG-Pfade für `project` (Ordner) und `back` (Pfeil) frei wählen, Stil wie die Vue-Buttons (Feather-Linien, 24er viewBox, Klasse `w-4 h-4`).
