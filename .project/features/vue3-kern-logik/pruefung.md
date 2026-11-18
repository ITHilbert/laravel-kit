# Prüfung: vue3-kern-logik — 2026-11-18 (Nachprüfung seit 61bf96ff)

Geprüft im Review-Platz `/home/joerg/projekte/.worktrees/packages_dev/reviewer`: Kit `61bf96ff..661ccad8` (2 Commits: `af6664de` Test, `661ccad8` Fix), Host `05561bd` (nur Submodul-Stand). Die Spec ist seit `61bf96ff` unverändert. Der Fix ändert `js/mount-inseln.js` (geteilt mit vue3-kern-grundlage), aber nur um drei Zeilen (je Insel `app.config.idPrefix`), ohne neue Klassen und ohne Migration. Deshalb Nachprüfung statt vollem Review. Voll geprüft wurden AK12, alles, was `mountInseln` berührt (AK17, Insel-Tests aus vue3-kern-grundlage), README und CHANGELOG (AK19) sowie der Vertrag „Keine neuen npm-Pakete“ (wegen `package.json`).

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „Dann gibt es `.kit-combobox`, darin `.kit-combobox__input` mit `role="combobox"` und `aria-expanded="false"`“ | bestanden (Prüfung 61bf96ff) | Code unverändert; Vitest 53/53 grün |
| AK1 „und `.kit-combobox__trigger`“ | bestanden (Prüfung 61bf96ff) | |
| AK1 „die Liste ist nicht sichtbar“ | bestanden (Prüfung 61bf96ff) | |
| AK2 „Dann ist `.kit-combobox__list` (`role="listbox"`) sichtbar“ | bestanden (Prüfung 61bf96ff) | |
| AK2 „`.kit-combobox` trägt `is-open`“ | bestanden (Prüfung 61bf96ff) | |
| AK2 „das Eingabefeld hat `aria-expanded="true"`“ | bestanden (Prüfung 61bf96ff) | |
| AK2 „es gibt drei `.kit-combobox__item` (`role="option"`) mit den Texten der Labels“ | bestanden (Prüfung 61bf96ff) | |
| AK3 „Wenn „st“ … getippt wird, Dann bleibt genau ein `.kit-combobox__item` („Österreich“) übrig“ | bestanden (Prüfung 61bf96ff) | |
| AK3 „wenn „xyz“ getippt wird, Dann gibt es kein `.kit-combobox__item`“ | bestanden (Prüfung 61bf96ff) | |
| AK3 „und ein sichtbares `.kit-combobox__empty`“ | bestanden (Prüfung 61bf96ff) | |
| AK4 „Wenn Pfeil-runter und Enter gedrückt werden … Dann ist die Liste geschlossen“ | bestanden (Prüfung 61bf96ff) | |
| AK4 „oder „Schweiz“ angeklickt wird, Dann ist die Liste geschlossen“ | bestanden (Prüfung 61bf96ff) | |
| AK4 „das Eingabefeld zeigt das Label der Auswahl“ | bestanden (Prüfung 61bf96ff) | |
| AK4 „das gewählte `.kit-combobox__item` trägt beim nächsten Öffnen `is-active`“ | bestanden (Prüfung 61bf96ff) | |
| AK4 „`update:modelValue` meldet den `value` der Auswahl“ | bestanden (Prüfung 61bf96ff) | |
| AK4 „ein Formular um die Combobox sendet unter `land` diesen `value`“ | bestanden (Prüfung 61bf96ff) | |
| AK5 „mit `land = 'at'` … Dann zeigt das Eingabefeld „Österreich““ | bestanden (Prüfung 61bf96ff) | |
| AK5 „wenn `land` von außen auf `'ch'` gesetzt wird, Dann zeigt es „Schweiz““ | bestanden (Prüfung 61bf96ff) | |
| AK6 „Dann gibt es `.kit-search`, `.kit-search__icon` (`aria-hidden="true"`)“ | bestanden (Prüfung 61bf96ff) | |
| AK6 „und `.kit-search__input` mit `type="search"` und `name="q"`“ | bestanden (Prüfung 61bf96ff) | |
| AK6 „aber kein `.kit-search__clear`“ | bestanden (Prüfung 61bf96ff) | |
| AK7 „meldet sie `update:modelValue` sofort“ | bestanden (Prüfung 61bf96ff) | |
| AK7 „und das Ereignis `suche` mit „abc“ genau einmal 300 ms nach dem letzten Tastendruck“ | bestanden (Prüfung 61bf96ff) | |
| AK7 „`.kit-search__clear` (Schaltfläche mit `aria-label="Suche leeren"`) ist sichtbar“ | bestanden (Prüfung 61bf96ff) | |
| AK8 „Wenn `.kit-search__clear` angeklickt … Dann ist der Wert leer“ | bestanden (Prüfung 61bf96ff) | |
| AK8 „oder im Eingabefeld Escape gedrückt wird, Dann ist der Wert leer“ | bestanden (Prüfung 61bf96ff) | |
| AK8 „`suche` meldet sofort „“ (ohne Verzögerung)“ | bestanden (Prüfung 61bf96ff) | |
| AK8 „der Fokus liegt im Eingabefeld“ | bestanden (Prüfung 61bf96ff) | |
| AK8 „und `.kit-search__clear` ist weg“ | bestanden (Prüfung 61bf96ff) | |
| AK9 „`.kit-input-token__input` mit `type="password"`, `name="api_token"` und dem Wert „geheim123““ | bestanden (Prüfung 61bf96ff) | |
| AK9 „`.kit-input-token__toggle` (Schaltfläche, `aria-pressed="false"`, `aria-label="Token anzeigen"`)“ | bestanden (Prüfung 61bf96ff) | |
| AK9 „und `.kit-input-token__copy` (Schaltfläche, `aria-label="Token kopieren"`)“ | bestanden (Prüfung 61bf96ff) | |
| AK10 „Dann hat das Eingabefeld `type="text"`“ | bestanden (Prüfung 61bf96ff) | |
| AK10 „der Schalter `aria-pressed="true"` und `aria-label="Token verbergen"` und trägt `is-active`“ | bestanden (Prüfung 61bf96ff) | |
| AK10 „ein zweiter Klick stellt AK9 wieder her“ | bestanden (Prüfung 61bf96ff) | |
| AK11 „Dann steht „geheim123“ in der Zwischenablage (`navigator.clipboard.writeText`)“ | bestanden (Prüfung 61bf96ff) | |
| AK11 „`.kit-input-token__copy` trägt `is-active`“ | bestanden (Prüfung 61bf96ff) | |
| AK11 „und `.kit-input-token__status` (`role="status"`) zeigt „Kopiert““ | bestanden (Prüfung 61bf96ff) | |
| AK11 „nach 2000 ms sind `is-active` und der Text wieder weg“ | bestanden (Prüfung 61bf96ff) | |
| AK12 „`.kit-input-list__input` mit `name="stadt"` und einem `list`-Attribut, das auf die `id` von `.kit-input-list__datalist` (`<datalist>`) zeigt“ | bestanden | Erneut geprüft, weil sich das id-Format ändert: Vitest `input-list.test.ts` grün (in einer App); Sonde mit zwei Inseln über `mountInseln(kitKomponenten)`: `list` = `["kit-input-list-kit1-0","kit-input-list-kit2-0"]` = ids der Datalists |
| AK12 „und die Datalist hat zwei `<option>`“ | bestanden | Vitest AK12 grün; Sonde: jede Insel-Datalist hat ihre eigenen Optionen (`[2,1]` bei 2 bzw. 1 übergebenen Optionen) |
| AK12 „zwei Listen mit gleichem `name` auf einer Seite haben verschiedene Datalist-`id`s“ | bestanden | Neuer Test `tests/vue3/inseln-ids.test.ts` (echtes `mountInseln`, echtes Vue 3.5.45, mit Gegenprobe: zwei Listen, ids nicht leer, `list` passt) grün. Eigene Sonde (Vitest, Konfiguration im Scratchpad): alte Fassung `61bf96ff:js/mount-inseln.js` → `["kit-input-list-v-0","kit-input-list-v-0"]` (der Test wäre damit rot), neue Fassung → `kit1-0`/`kit2-0`. Zweiter `mountInseln`-Aufruf für nachgeladenen Inhalt zählt weiter: vier Listen mit vier verschiedenen ids (`kit3-0` … `kit6-0`). In einer App weiterhin verschieden (Vitest `input-list.test.ts`). |
| AK13 „mit `invalid` … trägt die Wurzel `is-invalid` und das Eingabefeld `aria-invalid="true"`“ | bestanden (Prüfung 61bf96ff) | |
| AK13 „mit `disabled` trägt die Wurzel `disabled`, das Eingabefeld ist `disabled`“ | bestanden (Prüfung 61bf96ff) | |
| AK13 „und Klicks auf Trigger, Schalter oder Kopieren ändern nichts“ | bestanden (Prüfung 61bf96ff) | |
| AK14 „stehen `id` und `required` am Eingabefeld und nicht an der Wurzel“ | bestanden (Prüfung 61bf96ff) | |
| AK15 „gilt AK10 aus `vue3-kern-grundlage` auch für die neuen Dateien“ | bestanden | `Vue3QuelltextTest`, `Vue3LogikQuelltextTest` grün; Fix ändert keine Datei unter `vue3/` |
| AK16 „enthält jede eine Regel für jede Hauptklasse und jeden Teil aus dem Vertrag“ | bestanden (Prüfung 61bf96ff) | Themes unverändert |
| AK16 „und für `is-invalid` an den vier Wurzeln“ | bestanden (Prüfung 61bf96ff) | |
| AK17 „mountet die Insel ohne Konsolenfehler“ | bestanden | Erneut geprüft (`mountInseln` geändert): Vitest `inseln-logik.test.ts` grün; Sonde mit zwei `kit-combobox`- und zwei `kit-input-list`-Inseln: keine `console.error`/`console.warn`, alle ids im DOM eindeutig |
| AK17 „nach einer Auswahl sendet das Formular unter `land` den `value`“ | bestanden | Vitest `inseln-logik.test.ts` grün (FormData `land=ch`) |
| AK18 „Wenn `npm run production` läuft, Dann baut er ohne Fehler“ | bestanden (Prüfung 61bf96ff) | Host bündelt `mountInseln` nicht (kein Treffer in `resources/js`, `webpack.mix.js`); `public/` seit der Prüfung unverändert |
| AK18 „die Prüfseite `/ui-kit-vue3` zeigt zusätzlich Combobox, Suche, Token-Eingabe und Liste mit dem Bootstrap-5-Theme“ | bestanden (Prüfung 61bf96ff) | GET `http://127.0.0.1:8104/ui-kit-vue3` → 200 |
| AK18 „ohne Konsolenfehler“ | bestanden (Prüfung 61bf96ff) | |
| AK18 „die Combobox öffnet per Klick eine sichtbare Liste über dem folgenden Inhalt“ | bestanden (Prüfung 61bf96ff) | |
| AK19 „nennt die README im Abschnitt „Vue 3“ die vier Komponenten mit Tag-Name, Props, Ereignissen und Klassen“ | bestanden | Erneut geprüft (README geändert): `Vue3LogikQuelltextTest` grün; Diff ändert nur „vue ab 3.5“ in Einbinden und Abhängigkeitstabelle |
| AK19 „und der CHANGELOG nennt sie unter „Unveröffentlicht““ | bestanden | Test grün; Diff ändert nur „vue ab 3.5“ in derselben Rubrik |
| Randfall 1 „Combobox ohne `options` oder mit leerer Liste: kein Fehler“ | bestanden (Prüfung 61bf96ff) | |
| Randfall 1 „geöffnet zeigt sie `.kit-combobox__empty`“ | bestanden (Prüfung 61bf96ff) | |
| Randfall 2 „Combobox mit `modelValue`, der in keiner Option vorkommt: Eingabefeld leer, kein Fehler“ | bestanden (Prüfung 61bf96ff) | |
| Randfall 3 „Das Item trägt `disabled`“ | bestanden (Prüfung 61bf96ff) | |
| Randfall 3 „und lässt sich weder per Klick noch per Enter wählen“ | bestanden (Prüfung 61bf96ff) | |
| Randfall 4 „Kopieren nutzt den Weg über ein unsichtbares `<textarea>` und `document.execCommand('copy')`“ | bestanden (Prüfung 61bf96ff) | |
| Randfall 4 „schlägt beides fehl, zeigt `__status` „Kopieren fehlgeschlagen““ | bestanden (Prüfung 61bf96ff) | |
| Randfall 4 „und `__copy` trägt kein `is-active`“ | bestanden (Prüfung 61bf96ff) | |
| Randfall 5 „Ein neuer Tastendruck innerhalb der Verzögerung verschiebt das Ereignis `suche`“ | bestanden (Prüfung 61bf96ff) | |
| Randfall 5 „nach dem Aushängen der Komponente kommt kein verspätetes `suche`“ | bestanden (Prüfung 61bf96ff) | |
| Nicht-Ziele (Mehrfachauswahl, Nachladen, Gruppen, DataTable, Editor, einfache Felder, Blade, Release) | bestanden | Fix-Diff enthält nichts davon, keine Versionsnummer |
| Vertrag „Keine neuen npm-Pakete“ | bestanden | `package.json`: nur peerDependency `vue` von `^3.4` auf `^3.5`, kein neues Paket. Sonde `npm ci --dry-run --offline` mit `package.json` und `package-lock.json` des Fixes: läuft durch (Exit 0) |

## Sicherheit
- Fix-Diff: nur ein Zähler auf Modulebene und `app.config.idPrefix` (fester Text `kit<n>`, keine Eingabe von außen), keine Ausgabe von Nutzerdaten, keine Routen, Rechte oder Daten.

## Regressionen
- Kit Vitest, alle 10 Dateien (`tests/vue3/combobox`, `search`, `input-token`, `input-list`, `inseln-logik`, `inseln-ids` (neu), `dialog`, `tabs`, `accordion`, `inseln`): 53/53 grün.
- Kit `node --test tests/js/*.test.mjs` (u. a. `mount-inseln.test.mjs` aus vue3-kern-grundlage): 240/240 grün.
- PHP über den Host: `tests/Feature/HostGetRoutenTest.php`, `tests/Feature/KitVue3CssTest.php`, `packages/laravel-kit/tests/Feature/Vue3/` (`Vue3QuelltextTest`, `Vue3LogikQuelltextTest`): 25 passed (`tests-fuer --geaendert --nur-pfade` lieferte im Platz keine weiteren Pfade).
- Ganze Suite nicht gelaufen (kein `SUITE: ja`).

## Funde: 0 muss, 0 sollte, 2 kann
- erledigt: muss AK12 aus Prüfung 61bf96ff (gleiche Datalist-id je Insel) — siehe Zeile AK12.
- erledigt: sollte `useId` ab Vue 3.5 vs. peerDependency `^3.4` — peerDependency, README und CHANGELOG jetzt `^3.5`. Folge-Task #1610 hat damit keinen offenen Inhalt mehr.
- kann: `package-lock.json` nennt im Wurzeleintrag weiter `peerDependencies.vue: "^3.4"` (Zeile 26), `package.json` `^3.5`. `npm ci` läuft trotzdem (Sonde oben); das nächste `npm install` gleicht es an.
- kann: Der Zähler `inselZaehler` gilt je Modulkopie. Bündelt ein Projekt `mountInseln` in zwei getrennte Bundles auf derselben Seite, beginnen beide bei `kit1` und die ids können sich wieder doppeln. Für den dokumentierten Weg (ein Bundle) ohne Belang.
- kann (übernommen aus 61bf96ff, unverändert): Escape in leerer Suche meldet erneut `suche` mit „“; Test zu Randfall 4 prüft das `<textarea>` nicht; `.kit-combobox { display: flex }` trifft auch die Wurzel der alten `js/combobox.js` (bis Rückbau #1546).

## Neue Tasks: keine

## Fehlerprotokoll
- keine

## Fazit: freigegeben
Nachprüfung statt vollem Review: Spec unverändert, Fix auf drei Zeilen in `js/mount-inseln.js` plus Versionsangaben begrenzt; alles, was `mountInseln` berührt, wurde voll nachgeprüft.
