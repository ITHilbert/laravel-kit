# Prüfung: vue3-kern-grundlage — 2026-11-17
Geprüft im Review-Platz: Host 22d704f, laravel-kit e0021f7e (Basis Kit 418f830a, Host main). Browser = eigene Headless-Chrome-Instanz (Port 0, Profil im Scratchpad) per CDP mit echten Maus- und Tastenereignissen auf http://127.0.0.1:8104/ui-kit-vue3.

| AK / Aussage (Zitat) | Ergebnis | Nachweis |
|---|---|---|
| AK1 „Wenn der Trigger angeklickt wird, Dann ist der Dialog offen: Ein Element `.kit-dialog__overlay` … stehen im DOM“ | bestanden | Vitest `dialog.test.ts` „Klick auf den Trigger öffnet …“ grün; Browser: nach Klick Overlay da, `position: fixed` |
| AK1 „und ein Element `.kit-dialog__content` mit `role="dialog"`“ | bestanden | Browser: `role="dialog"`, Inhalt sichtbar und oben (`elementFromPoint`) |
| AK1 „`.kit-dialog__content` trägt die Klasse `is-open`“ | bestanden | Browser: `class="kit-dialog__content is-open"`; Vitest |
| AK1 „und `aria-labelledby` zeigt auf das Element `.kit-dialog__title`“ | bestanden | Browser: `aria-labelledby` = Titel-ID (`reka-dialog-title-v-11`); Vitest |
| AK2 „Wenn Escape gedrückt … Dann ist `.kit-dialog__content` nicht mehr im DOM“ | bestanden | Browser: echte Escape-Taste → 0 Inhalte, 0 Overlays; Vitest |
| AK2 „… oder `<kit-dialog-close>` (Klasse `kit-dialog__close`) angeklickt wird“ → nicht mehr im DOM | bestanden | Browser: Klick auf `.kit-dialog__close` → 0 Inhalte; Vitest |
| AK2 „und der Fokus liegt wieder auf dem Trigger“ | bestanden | Browser: nach Escape und nach Schließen-Knopf `activeElement` = Trigger; Vitest |
| AK3 „Wenn Tab auf der letzten fokussierbaren Schaltfläche gedrückt wird, Dann springt der Fokus zur ersten fokussierbaren Stelle im Dialog (Fokusfalle)“ | bestanden | Vitest `dialog.test.ts` „Tab auf der letzten Schaltfläche …“ (DOM-Test, zwei Schaltflächen, Reka-FocusScope reagiert auf das keydown) grün; Browser (Prüfseite mit nur einer Schaltfläche): echte Tab-Taste zweimal → Fokus bleibt im Dialog |
| AK4 „Wenn `offen` von außen auf `true` gesetzt wird, Dann öffnet sich der Dialog“ | bestanden | Vitest „v-model:open …“ grün |
| AK4 „und wenn er per Escape schließt, ist `offen` wieder `false`“ | bestanden | Vitest, Gegenprobe Inhalt weg |
| AK5 „Dann gibt es `.kit-tabs`, `.kit-tabs__list` (`role="tablist"`)“ | bestanden | Browser: Liste `role="tablist"`; Vitest |
| AK5 „drei `.kit-tabs__tab` (`role="tab"`)“ | bestanden | Browser: 3 Tabs, je `role="tab"` |
| AK5 „und genau ein sichtbares `.kit-tabs__panel` (`role="tabpanel"`) für „a““ | bestanden | Browser (berechneter Stil): 1 von 3 Panels sichtbar, „Inhalt von Tab A“ |
| AK5 „Tab „a“ und Panel „a“ tragen `is-active`“ | bestanden | Browser: genau Tab A und Panel A mit `is-active`; Vitest inkl. Gegenprobe inaktive Panels |
| AK5 „`aria-selected="true"` steht nur an Tab „a““ | bestanden | Browser: `true,false,false` |
| AK6 „Wenn Pfeil-rechts gedrückt wird, Dann liegt der Fokus auf Tab „b““ | bestanden | Browser: echte ArrowRight-Taste auf Tab A → Fokus Tab B; Vitest |
| AK6 „wenn Tab „c“ angeklickt wird, Dann ist Panel „c“ sichtbar und trägt `is-active`“ | bestanden | Vitest „Pfeil-rechts …“ und „Panel trägt is-active nur …“ (DOM-Test, Sichtbarkeit über `hidden`/`display`); Browser gleiches Verhalten mit Tab B (Tab C ist auf der Prüfseite deaktiviert): nur Panel B sichtbar und `is-active` |
| AK6 „Panel „a“ ist nicht sichtbar“ | bestanden | Vitest; Browser: nach Wechsel nur das neue Panel sichtbar |
| AK7 „Dann gibt es `.kit-accordion`, je Eintrag `.kit-accordion__item`“ | bestanden | Vitest `accordion.test.ts` AK7; Browser 3 Einträge |
| AK7 „`.kit-accordion__trigger` (Schaltfläche mit `aria-expanded="false"`)“ | bestanden | Browser: `BUTTON`, `aria-expanded="false"` je Eintrag |
| AK7 „und ein nicht sichtbares `.kit-accordion__content`“ | bestanden | Browser: Inhalt je Eintrag nicht sichtbar (berechneter Stil) |
| AK8 „Wenn der Trigger von „x“ angeklickt wird, Dann ist der Inhalt von „x“ sichtbar“ | bestanden | Browser: Klick Eintrag 1 → Inhalt sichtbar; Vitest |
| AK8 „`.kit-accordion__item` von „x“ trägt `is-open`“ | bestanden | Browser + Vitest |
| AK8 „und der Trigger `aria-expanded="true"`“ | bestanden | Browser + Vitest |
| AK8 „wenn danach der Trigger von „y“ angeklickt wird, ist „x“ geschlossen und „y“ offen“ | bestanden | Browser: `[[false,"false",false],[true,"true",true],…]`; Vitest |
| AK8 „ein zweiter Klick auf „y“ schließt „y““ | bestanden | Browser: alle zu; Vitest |
| AK9 „(`kit-tab` …) Dann trägt es die Klasse `disabled`“ | bestanden | Browser: Tab C `kit-tabs__tab disabled`; Vitest |
| AK9 „(`kit-tab`) … und lässt sich nicht per Klick aktivieren“ | bestanden | Browser: Klick auf Tab C → `aria-selected="false"`, Panel A bleibt; Vitest mit Gegenprobe |
| AK9 „(`kit-accordion-item`) … trägt die Klasse `disabled`“ | bestanden | Browser: Eintrag 3 `kit-accordion__item disabled`; Vitest |
| AK9 „(`kit-accordion-item`) … lässt sich nicht per Klick aktivieren“ | bestanden | Browser: Klick Eintrag 3 → bleibt zu; Vitest mit Gegenprobe |
| AK10 „enthalten sie keine Bootstrap-Klassen (`btn`, `modal`, `nav-`, `card`, `form-`, `col-`, `row`)“ | bestanden | `Vue3QuelltextTest` grün (mit Gegenprobe des Wächters); eigenes grep: `modal` nur in Kommentaren |
| AK10 „und keine Tailwind-Utilities (… `@apply`)“ | bestanden | wie oben |
| AK10 „und keine Klasse ohne Vorsilbe `kit-` außer den Zustandsklassen“ | bestanden | eigenes grep aller `class`/`:class`: nur `kit-…`, `is-open`, `is-active`, `disabled` |
| AK11 „`vue3/themes/tailwind.css` … enthält … eine Regel für jede Hauptklasse und jeden Teil aus AK1–AK8 (Liste)“ | bestanden | `Vue3QuelltextTest` grün; Sonde: kompiliert mit Tailwind 4.2 (`@tailwindcss/node`, `@import "tailwindcss"` + kit.css + Theme) ohne Fehler, Regeln im Ergebnis |
| AK11 „… `vue3/themes/bootstrap5.scss` … (Liste)“ | bestanden | `Vue3QuelltextTest` grün; Sonde: kompiliert mit Dart Sass 1.62 und vollem Bootstrap 5.3 ohne Fehler (Host-Build ebenso) |
| AK11 „und die Zustände `is-open`, `is-active`, `disabled`“ | bestanden | Test grün |
| AK12 „Wenn ein Projekt `mountInseln(kitKomponenten)` … aufruft, Dann mountet die Insel die Tabs ohne Konsolenfehler“ | bestanden | Vitest `inseln.test.ts` (console.warn/error gespiegelt, nicht aufgerufen) grün |
| AK12 „zwei Inseln auf einer Seite (Dialog und Tabs) arbeiten unabhängig“ | bestanden | Test mountet beide; eigene Vitest-Sonde (Scratchpad, Projektkonfiguration des Kits): Escape schließt die Dialog-Insel, Tabs-Insel bleibt unverändert, kein console.error |
| AK13 „Wenn er `vue3/index.ts` einbindet und `npm run production` läuft, Dann baut er ohne Fehler“ | bestanden | `mix --production` in Kopie des Platzes (Scratchpad, node_modules des Platzes): Exit 0, „Compiled successfully“, keine Warnung; `public/css/kit-vue3.css` byte-gleich mit dem Commit |
| AK13 „und die Seite `/ui-kit-vue3` (neu, nur im Host) zeigt Dialog, Tabs und Accordion“ | bestanden | GET → 200; Browser: alle drei gemountet, 0 unaufgelöste `kit-*`-Tags |
| AK13 „mit dem Bootstrap-5-Theme“ | nicht bestanden | Tabs, Accordion, Trigger, Schließen-Knopf, Overlay tragen die Bootstrap-Optik; **der geöffnete Dialog nicht**: `.kit-dialog__content` hat `background-color: rgba(0,0,0,0)`, `border: 0px none`, `border-radius: 0px` — Titel und Text stehen ohne Fläche über der abgedunkelten Seite (Screenshot). Ursache: `@extend .modal-content` übernimmt `var(--bs-modal-bg)` usw., diese Variablen setzt Bootstrap nur an `.modal`; ein solches Element gibt es nicht. Betrifft jedes Projekt mit `vue3/themes/bootstrap5.scss` (Sonde mit vollem Bootstrap: gleiche Regel) |
| AK13 „ohne Konsolenfehler“ | bestanden | Browser: Konsole nur Boost-Logger-Meldung, keine Fehler, keine Ausnahmen, auch nach allen Klicks |
| AK14 „beschreibt die README den Ordner `vue3/`, die Klassenregel (`kit-`, `__`, Zustände)“ | bestanden | README Abschnitt „Vue 3“ gelesen; Test grün |
| AK14 „das Einbinden (`KitVue3`, `kitKomponenten` mit `mountInseln`, Theme-Datei, `npm install reka-ui`)“ | bestanden | README „Einbinden“ 1–4 |
| AK14 „und `vue/README.md` sagt, dass nur `vue/tailwind/` Vue 3 ist und die Varianten unter `vue/` nicht mehr gepflegt werden“ | bestanden | `vue/README.md` Zeile 3 |
| Randfall 1 „Ein Dialog ohne `<kit-dialog-title>`: kein Fehler, Reka-Warnung in der Konsole ist erlaubt.“ | bestanden | Vitest „ohne Titel …“ (console.error nicht aufgerufen); Sonde zeigt nur die Reka-Warnung |
| Randfall 2 „Tabs ohne `default-value`: kein Tab aktiv“ | bestanden | Vitest „ohne default-value …“ |
| Randfall 2 „kein Fehler“ | bestanden | Vitest (console.error gespiegelt) |
| Randfall 3 „Zwei Dialoge auf einer Seite: Es ist immer nur der zuletzt geöffnete fokussiert“ | bestanden | Vitest „zwei Dialoge …“ mit Gegenprobe A fokussiert |
| Randfall 3 „Escape schließt nur ihn“ | bestanden | Vitest: b=false, a=true, Knopf A bleibt |
| Randfall 4 „Der Dialog rendert per Teleport an `body`“ | bestanden | Browser: Elternelement von `.kit-dialog__content` = `BODY`, nicht in `#vue-app` |
| Randfall 4 „im Insel-Betrieb liegt der Inhalt trotzdem außerhalb der Insel“ | bestanden | Vitest `inseln.test.ts` (Inhalt nicht in `#d`/`#t`, in body) |
| Vertrag „`js/mount-inseln.js` bleibt unverändert“ | bestanden | `git diff 418f830a...e0021f7e -- js/` leer |
| Vertrag „Abhängigkeit: `reka-ui` `~2.11` in `package.json` des Kits“ | bestanden | peerDependencies `reka-ui ~2.11`, `vue ^3.4`; README nennt `npm install reka-ui` |
| Nicht-Ziele (Combobox, Felder, Blade-Components, andere Projekte, Release-Version) | eingehalten | Diff enthält nur Dialog, Tabs, Accordion, Themes, Doku, Prüfseite; CHANGELOG „Unveröffentlicht“, kein Tag |

## Sicherheit
- Neue Route `GET /ui-kit-vue3` liefert eine statische View ohne Eingaben und Daten, wie die bestehende `ui-kit`-Prüfseite; nur im Host.
- Komponenten: kein `v-html`, keine Ausgabe fremder Daten; `$attrs` des Dialogs gehen an den Inhalt (Vue bindet escaped).
- Keine Zahlung, kein Login/Rechte, keine Geheimnisse, kein Löschen, keine Kundendaten → `Risiko: normal` passt.
- `npm audit` im Kit: Funde nur in Entwicklungsabhängigkeiten (picomatch-Kette über vitest), im Plan als AK-fremd entschieden.

## Regressionen
- Volle Host-Suite im Platz: `PAO_DISABLE=1 php artisan test --parallel --compact` → OK (6663 Tests, 52168 Assertions), einmal gelaufen; schließt `packages/laravel-kit/tests/Feature` ein.
- Kit: `npx vitest run tests/vue3` → 17/17 grün; `node --test tests/js/*.test.mjs` → 240/240 grün (u. a. `mount-inseln.test.mjs`).
- `routes/web.php`: Pint entfernte zwei ungenutzte `use`-Zeilen und Klammern bei `new Breadcrumb` — ohne Verhaltensänderung.
- `laravel.log` im Platz: nur `telescope_entries doesn't exist` (Review-DB ohne Telescope-Tabelle, Umgebung des Platzes, nicht das Feature).

## Funde: 1 muss, 5 sollte, 6 kann
- muss: Bootstrap-5-Theme lässt den geöffneten Dialog ohne Hintergrund, Rahmen und Radius (AK13 „mit dem Bootstrap-5-Theme“). `vue3/themes/bootstrap5.scss:16-25` `@extend .modal-content` nutzt die `--bs-modal-*`-Variablen, die nur an `.modal` stehen. Nachweis: Browser `getComputedStyle(.kit-dialog__content)` → `background-color: rgba(0, 0, 0, 0)`, `border: 0px none`; kompiliert `.kit-dialog__content,.modal-content{background-color:var(--bs-modal-bg);…}`. Dabei mitprüfen: Dieselbe Regel setzt `position: relative`; nur weil `kit.css` in der Prüfseite nach dem Bootstrap-Teil steht, bleibt `position: fixed` aus `kit.css` gültig — wird `kit.css` vor der Theme-CSS geladen, steht der Dialog nicht mehr über der Seite.
- sollte: `mountInseln` verwirft den HTML-Inhalt der Insel; `kit-tabs`/`kit-dialog` als Insel rendern ohne Tabs bzw. ohne Trigger/Titel, README empfiehlt den Weg ohne Hinweis (→ Folge-Task #1556)
- sollte: Spec „Entscheidungsspielraum“ erlaubt nur reka-ui, Vitest, @vue/test-utils, happy-dom; package.json nennt zusätzlich `vue`, `@vitejs/plugin-vue`, `typescript` (devDependencies) (→ #1556)
- sollte: Kommentar in `webpack.mix.js` (ts-loader) „Typprüfung macht das Kit selbst (vue-tsc)“ ist falsch, das Kit hat kein vue-tsc (→ #1556)
- sollte: keine Typprüfung der `lang="ts"`-Dateien unter `vue3/` (Vitest und ts-loader `transpileOnly` prüfen keine Typen) (→ #1556)
- sollte: Themes werden nur per Regex geprüft, kein Test baut sie mit Sass bzw. Tailwind (im Review per Sonde gebaut, beide fehlerfrei) (→ #1556)
- kann: `bootstrap5.scss` `@extend .collapsed` am Accordion-Trigger — Bootstrap-Selektor `:not(.collapsed)` wird zu `:not(.collapsed):not(.kit-accordion__trigger)`, der Pfeil des offenen Eintrags dreht sich nie
- kann: `@extend .active` an `.kit-tabs__tab.is-active` erzeugt Nebenselektoren wie `.kit-dialog__close.kit-tabs__tab.is-active` (aufgebläht, ohne Wirkung)
- kann: `AccordionHeader as="div"` (item.vue:24) rendert ein Element ohne `kit-`-Klasse; die Themes können es nicht ansprechen
- kann: `bootstrap5.scss` hat keine Regel für die Hauptklasse `.kit-tabs` (AK5); die Liste in AK11 verlangt sie nicht — Wortlaut „jede Hauptklasse“ bei Gelegenheit klären
- kann: `tabs.vue` und `dialog.vue` rufen den Default-Slot je Render zweimal auf (`teile()` bzw. `ausserhalb()`/`innerhalb()`); ein Wrapper-Element um `kit-tab` wird verworfen
- kann: Review-DB des Platzes ohne Tabelle `telescope_entries` (Fehler in `laravel.log` bei jedem Seitenaufruf)

## Neue Tasks: #1556 laravel-kit vue3 – Nacharbeit aus Review vue3-kern-grundlage

## Fehlerprotokoll
- {"schwere":"muss","ak":"AK13","fund":"Bootstrap-5-Theme: geöffneter Dialog ohne Hintergrund, Rahmen und Radius, weil @extend .modal-content Variablen nutzt, die nur an .modal gesetzt sind","ursache":"Echtlauf prüfte Klassen und Konsolenfehler, nicht die berechnete Optik; AK11-Test prüft nur, dass Regeln existieren","anweisung":"Bei Theme-Dateien mit @extend fremder Komponenten-Klassen prüfen, ob deren CSS-Variablen am Element ankommen, und im Echtlauf die berechneten Stile (Hintergrund, Rahmen) des geöffneten Zustands messen oder einen Screenshot ansehen"}
- {"schwere":"sollte","ak":"AK12","fund":"mountInseln verwirft den HTML-Inhalt der Insel; Kit-Komponenten als Insel bleiben leer, README sagt das nicht","ursache":"AK12 nannte nur Mounten ohne Konsolenfehler, der Nutzen im Insel-Betrieb wurde nicht geprüft","anweisung":"Bei einem neuen Einbindeweg prüfen, ob die Komponente dort mit echtem Inhalt nutzbar ist, und Grenzen in der README nennen"}
- {"schwere":"sollte","ak":"","fund":"devDependencies vue, @vitejs/plugin-vue, typescript außerhalb des Entscheidungsspielraums der Spec","ursache":"Plan nahm nötige Pakete auf, die Spec wurde nicht nachgezogen","anweisung":"Braucht eine Aufgabe ein Paket außerhalb des Entscheidungsspielraums, das als Abweichung melden, damit die Spec ergänzt wird"}
- {"schwere":"sollte","ak":"","fund":"Kommentar in webpack.mix.js behauptet Typprüfung per vue-tsc im Kit, die es nicht gibt","ursache":"Annahme über ein anderes Repo ungeprüft als Kommentar übernommen","anweisung":"Kommentare, die auf Werkzeuge in anderen Repos verweisen, vor dem Schreiben an deren package.json prüfen"}
- {"schwere":"sollte","ak":"","fund":"keine Typprüfung der TypeScript-Dateien unter vue3/","ursache":"Vitest und ts-loader transpileOnly übersetzen nur","anweisung":"Bei neuem TypeScript-Code einen Typprüf-Befehl (vue-tsc --noEmit) einrichten oder den Verzicht als Entscheidung festhalten"}
- {"schwere":"sollte","ak":"AK11","fund":"Themes nur per Regex geprüft, kein Build-Test mit Sass/Tailwind","ursache":"AK11 verlangt nur Lesen; Fremdformat-Regel des test-writers nicht angewandt","anweisung":"Für Dateien, die ein Fremdwerkzeug verarbeitet (Sass, Tailwind), einen Test mit dem echten Werkzeug schreiben"}

## Fazit: nicht freigegeben
Grund: AK13 „mit dem Bootstrap-5-Theme“ für den Dialog nicht erfüllt (muss). Alle übrigen Aussagen bestanden; Suite grün.
