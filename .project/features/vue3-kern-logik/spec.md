# Spec vue3-kern-logik
Risiko: normal
Art: Feature — zweiter Teil des Vue-3-Kerns aus Entscheidung 0013 (Host `packages_dev`, `.project/doku/decisions/0013-laravel-kit-vue3-kern-ohne-framework-optik.md`, Schritt 3: Komponenten mit Logik). Kein Dashboard-Task.
Status: freigegeben (Moderator in Vollmacht; Joerg, Chat 2026-11-18: „die Komponenten zu Ende umbauen ohne weitere Rückfragen“).
Rückbau: entfällt (kein Übergangsstand; der Rückbau der alten Varianten ist #1546)

## Ziel
Der Kern `vue3/` bekommt die restlichen Komponenten mit Logik: Combobox, Suche, Token-Eingabe und Liste. Sie ersetzen `combobox`/`vue-select`, `search`, `input-token` und `input-list` aus `vue/tailwind/forms/`. Vorbild für Aufbau, Klassenregel, Einstieg und Tests ist das Feature `vue3-kern-grundlage`.

## Vertrag
- **Ordner und Dateien:** `vue3/components/combobox/combobox.vue`, `vue3/components/search/search.vue`, `vue3/components/input-token/input-token.vue`, `vue3/components/input-list/input-list.vue`, je `<script setup lang="ts">`.
- **Tag-Namen:** `kit-combobox`, `kit-search`, `kit-input-token`, `kit-input-list`. Jede Komponente steht im Einstieg `vue3/index.ts` als benannter Export, in `kitKomponenten` und im Plugin `KitVue3`.
- **Klassen** (Regel aus 0013): Jedes gerenderte Teil trägt genau eine Hauptklasse `kit-<komponente>` bzw. `kit-<komponente>__<teil>`. Zustände: `is-open`, `is-active`, `is-invalid`, `disabled`.
  - Combobox: `kit-combobox` (Wurzel), `kit-combobox__input`, `kit-combobox__trigger`, `kit-combobox__list`, `kit-combobox__item`, `kit-combobox__empty`
  - Suche: `kit-search` (Wurzel), `kit-search__icon`, `kit-search__input`, `kit-search__clear`
  - Token-Eingabe: `kit-input-token` (Wurzel), `kit-input-token__input`, `kit-input-token__toggle`, `kit-input-token__copy`, `kit-input-token__status`
  - Liste: `kit-input-list` (Wurzel), `kit-input-list__input`, `kit-input-list__datalist`
- **Gemeinsame Props aller vier:** `modelValue` (mit `update:modelValue`, also `v-model`), `name`, `disabled`, `invalid` (Boolean). Übrige Attribute (`id`, `required`, `placeholder`, `autocomplete`, `aria-*`) landen am Eingabefeld (`__input`), nicht an der Wurzel.
- **Optionen** (Combobox, Liste): Prop `options` = Liste von `{ value: string | number, label: string, disabled?: boolean }`.
- **Keine Optik im Kern:** wie `vue3-kern-grundlage` (keine Bootstrap-Klassen, keine Tailwind-Utilities, keine Klasse ohne `kit-` außer den Zuständen). Symbole (Lupe, Auge, Kopieren) als inline-SVG mit `aria-hidden="true"`, keine Icon-Schrift.
- **Abhängigkeit:** Combobox baut auf Reka UI `~2.11` (`ComboboxRoot` & Co.). Keine neuen npm-Pakete.

## Akzeptanzkriterien
- **AK1** Gegeben `<kit-combobox name="land" :options="[{value:'de',label:'Deutschland'},{value:'at',label:'Österreich'},{value:'ch',label:'Schweiz'}]">`, Wenn die Komponente rendert, Dann gibt es `.kit-combobox`, darin `.kit-combobox__input` mit `role="combobox"` und `aria-expanded="false"` und `.kit-combobox__trigger`; die Liste ist nicht sichtbar.
- **AK2** Gegeben die Combobox aus AK1, Wenn der Trigger angeklickt wird, Dann ist `.kit-combobox__list` (`role="listbox"`) sichtbar, `.kit-combobox` trägt `is-open`, das Eingabefeld hat `aria-expanded="true"`, und es gibt drei `.kit-combobox__item` (`role="option"`) mit den Texten der Labels.
- **AK3** Gegeben die offene Combobox aus AK2, Wenn „st“ ins Eingabefeld getippt wird, Dann bleibt genau ein `.kit-combobox__item` („Österreich“) übrig (Filter ohne Groß-/Kleinschreibung auf das Label, Teilstring); wenn „xyz“ getippt wird, Dann gibt es kein `.kit-combobox__item` und ein sichtbares `.kit-combobox__empty`.
- **AK4** Gegeben die offene Combobox aus AK2, Wenn Pfeil-runter und Enter gedrückt werden oder „Schweiz“ angeklickt wird, Dann ist die Liste geschlossen, das Eingabefeld zeigt das Label der Auswahl, das gewählte `.kit-combobox__item` trägt beim nächsten Öffnen `is-active`, `update:modelValue` meldet den `value` der Auswahl, und ein Formular um die Combobox sendet unter `land` diesen `value`.
- **AK5** Gegeben `<kit-combobox v-model="land" :options="…">` mit `land = 'at'`, Wenn die Komponente rendert, Dann zeigt das Eingabefeld „Österreich“; wenn `land` von außen auf `'ch'` gesetzt wird, Dann zeigt es „Schweiz“.
- **AK6** Gegeben `<kit-search name="q" :verzoegerung="300">`, Wenn die Komponente rendert, Dann gibt es `.kit-search`, `.kit-search__icon` (`aria-hidden="true"`) und `.kit-search__input` mit `type="search"` und `name="q"`, aber kein `.kit-search__clear`.
- **AK7** Gegeben die Suche aus AK6, Wenn „abc“ getippt wird, Dann meldet sie `update:modelValue` sofort und das Ereignis `suche` mit „abc“ genau einmal 300 ms nach dem letzten Tastendruck; `.kit-search__clear` (Schaltfläche mit `aria-label="Suche leeren"`) ist sichtbar.
- **AK8** Gegeben die Suche mit dem Wert „abc“, Wenn `.kit-search__clear` angeklickt oder im Eingabefeld Escape gedrückt wird, Dann ist der Wert leer, `suche` meldet sofort „“ (ohne Verzögerung), der Fokus liegt im Eingabefeld und `.kit-search__clear` ist weg.
- **AK9** Gegeben `<kit-input-token name="api_token" model-value="geheim123">`, Wenn die Komponente rendert, Dann gibt es `.kit-input-token`, `.kit-input-token__input` mit `type="password"`, `name="api_token"` und dem Wert „geheim123“, `.kit-input-token__toggle` (Schaltfläche, `aria-pressed="false"`, `aria-label="Token anzeigen"`) und `.kit-input-token__copy` (Schaltfläche, `aria-label="Token kopieren"`).
- **AK10** Gegeben die Token-Eingabe aus AK9, Wenn `.kit-input-token__toggle` angeklickt wird, Dann hat das Eingabefeld `type="text"`, der Schalter `aria-pressed="true"` und `aria-label="Token verbergen"` und trägt `is-active`; ein zweiter Klick stellt AK9 wieder her.
- **AK11** Gegeben die Token-Eingabe aus AK9, Wenn `.kit-input-token__copy` angeklickt wird, Dann steht „geheim123“ in der Zwischenablage (`navigator.clipboard.writeText`), `.kit-input-token__copy` trägt `is-active` und `.kit-input-token__status` (`role="status"`) zeigt „Kopiert“; nach 2000 ms sind `is-active` und der Text wieder weg.
- **AK12** Gegeben `<kit-input-list name="stadt" :options="[{value:'Berlin',label:'Berlin'},{value:'Bonn',label:'Bonn'}]">`, Wenn die Komponente rendert, Dann gibt es `.kit-input-list`, `.kit-input-list__input` mit `name="stadt"` und einem `list`-Attribut, das auf die `id` von `.kit-input-list__datalist` (`<datalist>`) zeigt, und die Datalist hat zwei `<option>`; zwei Listen mit gleichem `name` auf einer Seite haben verschiedene Datalist-`id`s.
- **AK13** Gegeben eine der vier Komponenten mit `invalid`, Wenn sie rendert, Dann trägt die Wurzel `is-invalid` und das Eingabefeld `aria-invalid="true"`; mit `disabled` trägt die Wurzel `disabled`, das Eingabefeld ist `disabled`, und Klicks auf Trigger, Schalter oder Kopieren ändern nichts.
- **AK14** Gegeben eine der vier Komponenten mit den Attributen `id="feld"` und `required`, Wenn sie rendert, Dann stehen `id` und `required` am Eingabefeld und nicht an der Wurzel.
- **AK15** Gegeben alle Dateien unter `vue3/components/` und `vue3/kit.css`, Wenn ihr Quelltext gelesen wird, Dann gilt AK10 aus `vue3-kern-grundlage` auch für die neuen Dateien (keine Bootstrap-Klassen, keine Tailwind-Utilities, keine Klasse ohne `kit-` außer Zuständen).
- **AK16** Gegeben `vue3/themes/tailwind.css` und `vue3/themes/bootstrap5.scss`, Wenn sie gelesen werden, Dann enthält jede eine Regel für jede Hauptklasse und jeden Teil aus dem Vertrag (Combobox, Suche, Token-Eingabe, Liste) und für `is-invalid` an den vier Wurzeln.
- **AK17** Gegeben `vue3/index.ts`, Wenn ein Projekt `mountInseln(kitKomponenten)` auf einer Seite mit `<form><div data-vue="kit-combobox" data-props='{"name":"land","options":[…]}'></div></form>` aufruft, Dann mountet die Insel ohne Konsolenfehler, und nach einer Auswahl sendet das Formular unter `land` den `value`.
- **AK18** Gegeben der Host `packages_dev`, Wenn `npm run production` läuft, Dann baut er ohne Fehler, und die Prüfseite `/ui-kit-vue3` zeigt zusätzlich Combobox, Suche, Token-Eingabe und Liste mit dem Bootstrap-5-Theme ohne Konsolenfehler; die Combobox öffnet per Klick eine sichtbare Liste über dem folgenden Inhalt.
- **AK19** Gegeben README und CHANGELOG des Kits, Wenn gelesen wird, Dann nennt die README im Abschnitt „Vue 3“ die vier Komponenten mit Tag-Name, Props, Ereignissen und Klassen, und der CHANGELOG nennt sie unter „Unveröffentlicht“.

## Randfälle
1. Combobox ohne `options` oder mit leerer Liste: kein Fehler; geöffnet zeigt sie `.kit-combobox__empty`.
2. Combobox mit `modelValue`, der in keiner Option vorkommt: Eingabefeld leer, kein Fehler.
3. Option mit `disabled: true`: Das Item trägt `disabled` und lässt sich weder per Klick noch per Enter wählen.
4. Token-Eingabe ohne `navigator.clipboard` (kein sicherer Kontext): Kopieren nutzt den Weg über ein unsichtbares `<textarea>` und `document.execCommand('copy')`; schlägt beides fehl, zeigt `__status` „Kopieren fehlgeschlagen“ und `__copy` trägt kein `is-active`.
5. Suche: Ein neuer Tastendruck innerhalb der Verzögerung verschiebt das Ereignis `suche`; nach dem Aushängen der Komponente kommt kein verspätetes `suche`.

## Nicht-Ziele
- Mehrfachauswahl in der Combobox, Nachladen der Optionen vom Server, Gruppen.
- DataTable (`vue/tailwind/datatable/`), Editor, Viewer.
- Einfache Felder (Feature `vue3-kern-felder` danach), Blade-Components.
- Umstellen anderer Projekte, Release-Version.

## Entscheidungsspielraum
Interne Struktur, weitere Props über die genannten hinaus, Aufbau des Basis-CSS (z. B. Position der Combobox-Liste). Keine neuen npm-Pakete.
