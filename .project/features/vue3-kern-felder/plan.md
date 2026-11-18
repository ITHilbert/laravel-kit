# Plan vue3-kern-felder
laravel-kit `feature/vue3-kern-felder` (von `feature/vue3-kern-logik` 77e3ba03 — baut auf Einstieg, `kit.css` und Themes von vue3-kern-logik auf, das im Review ist). Host `packages_dev` `feature/vue3-kern-felder` (von `feature/vue3-kern-logik` 3dbe4dd) für T6.
Spec: [spec.md](spec.md) (freigegeben 2026-11-18, Moderator in Vollmacht). Kein Dashboard-Task.

## Aufgaben
- [x] TW (test-writer aed6469519f935f21, rot: Vitest 100/100 neue Fälle rot — Komponenten und Exporte fehlen; PHP 4 rot/3 grün = Gegenproben; Grünbar entfällt) test-writer (AK1–AK21, Randfälle 1–5) — Tests:
  - `tests/vue3/felder-text.test.ts` (AK1–AK4, AK8, Randfall 1; AK2–AK4 je für alle Felder der Tabelle)
  - `tests/vue3/felder-zahl.test.ts` (AK6, AK7, Randfall 2)
  - `tests/vue3/felder-auswahl.test.ts` (AK5, AK9–AK13, Randfälle 3–5)
  - `tests/vue3/felder-rahmen.test.ts` (AK14–AK16, AK19)
  - `tests/Feature/Vue3/Vue3FelderQuelltextTest.php` (AK17, AK18, AK21)
- [x] T1 (coder-pro ac2bc00a12d814215, HANDOFF Scope: Umsetzung fertig, 73 grün; 18 rote Fälle in `felder-text.test.ts` = AK2/AK4 je Feld für T2/T3-Felder — Plan-Fehler des Moderators, Testdatei gehört T1–T3 gemeinsam → T2 und T3 zusammengelegt, GATE von T1 über T2+T3) (AK1–AK4, AK8) [pro] gemeinsame Feld-Logik (`vue3/components/feld/`), `kit-input-text`, `kit-input-email`, `kit-input-password`, `kit-input-date`, `kit-input-color`, `kit-textarea`, Einstieg — Tests: `tests/vue3/felder-text.test.ts`
- [x] T2+T3 (coder-pro a379461d0f01e857b, GATE GRÜN: Vitest 125 pass, JS 24 pass; T1 mit geprüft) (AK2, AK4, AK5, AK6, AK7, AK9–AK13; zusammengelegt) [pro] — T2: `kit-input-number`, `kit-input-int`, `kit-input-euro`, `kit-input-percent`, Einstieg; T3: `kit-select`, `kit-checkbox`, `kit-radio`, `kit-toggle`, `kit-input-file`, Einstieg — Tests: `tests/vue3/felder-text.test.ts`, `tests/vue3/felder-zahl.test.ts`, `tests/vue3/felder-auswahl.test.ts`
- [x] T4 (coder-pro af4d8f78135e366b1, GATE GRÜN: Vitest 152 pass) (AK14–AK16, AK19) [pro] `kit-label`, `kit-form`, `kit-input-group`, `kit-input-group-text`, Einstieg vollständig — Tests: `tests/vue3/felder-rahmen.test.ts`
- [x] T5 (coder-pro a2cf6af8e264f6b59, GATE GRÜN: 24 passed; Sass-Probe fehlerfrei) (AK17, AK18, AK21) [pro] Themes, README, CHANGELOG — Tests: `tests/Feature/Vue3/Vue3FelderQuelltextTest.php`
- [x] T6 (coder-pro a7047fc1b090ac61a, GATE GRÜN: 8 passed; Build ok) (AK20) [pro] Host: Prüfseite `/ui-kit-vue3` um alle Felder erweitern, Build — Tests: Host `tests/Feature/HostGetRoutenTest.php`, `tests/Feature/KitVue3CssTest.php`; Nachweis per Echtlauf (AK20 nicht automatisch prüfbar)

Bestehende Tests: `tests/vue3/*.test.ts` aus vue3-kern-grundlage und vue3-kern-logik, `tests/Feature/Vue3/Vue3QuelltextTest.php`, `tests/Feature/Vue3/Vue3LogikQuelltextTest.php`, `tests/js/mount-inseln.test.mjs`
Hinweis Aufträge: Kit-Tests (`*.test.ts`) nur mit `PROJEKT: …/packages/laravel-kit` in die `TESTS:`-Zeile (ohne `PROJEKT:` startet das GATE sie per `node --test`, HANDOFF in vue3-kern-logik F2).
Marketing: entfällt (Bausteine ohne neue öffentliche Seite).
Design: entfällt (Optik nur über die vorhandenen Theme-Dateien, Prüfseite im Host).
Offener Punkt aed6469519f935f21: AK-fremd (AK12 „Leertaste“ angenähert über `type=button` + Klick, happy-dom simuliert Leertaste→Klick nicht — Nachweis im Echtlauf T6; Hinweise an Coder: Datei je Tag `vue3/components/<name>/<name>.vue`, `kit-input-group-text` neben `input-group/`, README-Tabelle „Ersetzt“ mit Backticks, Theme-Regeln ohne `@each`/Mixin, `label`-Prop auch an `kit-checkbox`/`kit-radio` — steht im Vertrag „gemeinsame Props der Felder“)
Abweichung ac2bc00a12d814215: Fehlalarm: keine gemeldet
Offener Punkt ac2bc00a12d814215: AK-fremd (keine gemeldet; HANDOFF-Grund siehe T1)
Abweichung a379461d0f01e857b: Fehlalarm: keine gemeldet
Offener Punkt a379461d0f01e857b: AK-fremd (keine gemeldet)
Abweichung af4d8f78135e366b1: Fehlalarm: keine gemeldet
Offener Punkt af4d8f78135e366b1: AK-fremd (keine gemeldet)
Abweichung a2cf6af8e264f6b59: Fehlalarm: keine gemeldet
Offener Punkt a2cf6af8e264f6b59: AK-fremd (Tailwind-Theme nicht kompiliert — Probe im Review; Optik von Schalter/Suffix → Echtlauf T6)
Review: nötig (Querschnitt Kit und Host, > 50 Zeilen Produktivcode).

Abweichung a7047fc1b090ac61a: Fehlalarm: keine gemeldet
Offener Punkt a7047fc1b090ac61a: AK-fremd (keine gemeldet)
Echtlauf T6: browser: /ui-kit-vue3 — 0 Konsolenfehler, keine XHR/fetch, Klick: .kit-toggle__input ausgelöst, .kit-input-text__input: 4 sichtbar, .kit-toggle.is-active: 0 → ROT. CDP-Sonde (echte Klicks/Eingaben, :8225, Felder ohne `v-model` wie in Blade/Inseln): Text, Zahl, Euro, Textarea, Select, Checkbox (Label-Klick → `zustimmung=on`), Radio (`art=b`), Passwort-Schalter (`type=text`) arbeiten; `kit-toggle` bleibt `aria-checked=false`, Formular sendet `aktiv=0`.
Befund Moderator (Echtlauf, muss, AK12): `kit-toggle` ohne `v-model` ändert bei Klick weder `aria-checked` noch `is-active` noch das versteckte Feld — der Test prüft nur mit `v-model`. → Fix F1: test-writer (Regressionstest ohne `v-model`), coder-pro.
Befund Moderator (Screenshot, Optik): `kit-input-password__toggle` steht unter dem Eingabefeld statt rechts daneben (Token-Eingabe zeigt, wie es aussehen soll); `kit-input-group` mit einem Feld mit `label`: Label und Feld stehen neben dem Zusatztext, Feld nicht volle Breite. → Fix F2 (Optik) im selben Lauf: Passwortfeld als Zeile, Feld in der Gruppe füllt die Breite; Prüfseite: Gruppen-Beispiele ohne inneres `label` (wie AK16), Beschriftung per `kit-label` davor.

Fix F1 Test: test-writer afc32203ec6927bd0, `tests/vue3/toggle-ohne-model.test.ts` (rot 2/2: `expected 'false' to be 'true'` nach Klick, Gegenprobe vorher grün; Grünbar entfällt, Bugfix).

Fix F1/F2 Kit: coder-pro a81b626c1bb7d45df (GATE GRÜN: PHP 20, JS 24, Vitest 105 aus TESTS:, ganzes Vitest 155/155): `toggle.vue` mit eigenem Zustand, der `modelValue` folgt; `kit.css` Passwortfeld und Input-Group als Zeile; Themes Abstand des Schalters.
Abweichung a81b626c1bb7d45df: Fehlalarm: keine gemeldet
Offener Punkt a81b626c1bb7d45df: AK-fremd (Browser-Nachweis und Host-Build folgen im Host-Schritt F2b; Bootstrap-Theme wird dort mit kompiliert)

F2b Host: coder-junior a1f1dff86cae016e4 (GATE siehe Log): Prüfseite Input-Group-Beispiele wie AK16 (`kit-label` vor der Gruppe), Build.
Abweichung a1f1dff86cae016e4: Fehlalarm: keine gemeldet
Offener Punkt a1f1dff86cae016e4: AK-fremd (Browser-Nachweis → unten)
F2c: coder-junior afd127e2f103d2729: Toggle-Knopf eingeschaltet außerhalb der Spur (`kit.css` `right:0` plus Theme-`translate`) → nur noch Theme-`translateX(Spur − Knopf − Rahmen)`, beide Themes; Build.
Abweichung afd127e2f103d2729: Fehlalarm: keine gemeldet
Offener Punkt afd127e2f103d2729: AK-fremd (Knopf vertikal oben statt mittig — im Screenshot nicht sichtbar, Spur und Knopf passen; kann)
Echtlauf F1/F2: browser: /ui-kit-vue3 — 0 Konsolenfehler, keine XHR/fetch, Klick: .kit-toggle__input ausgelöst, .kit-input-text__input: 4 sichtbar, .kit-toggle.is-active: 1 vorhanden → OK. CDP-Sonde (:8225, ohne `v-model`): Toggle `aria-checked=true`, `is-active`, Formular `aktiv=1`; Passwort `type=text`; Checkbox `zustimmung=on`; Radio `art=b`; Text/Zahl/Euro/Textarea/Select per Eingabe gesetzt. Screenshots: Passwort-Schalter rechts neben dem Feld, Input-Group in einer Zeile, Toggle-Knopf innen rechts.

Review 1/2: nicht freigegeben ([pruefung.md](pruefung.md)) — muss: Randfall 3 (`kit-select` ohne `placeholder` startet in Chrome ohne Auswahl, fehlt in FormData). sollte → #1611 (step-Typ, Suffix bricht um). kann nur in pruefung.md.
Spec-Lücke Randfall 3: ergänzt (Startauswahl wie natives `<select>`), Moderator in Vollmacht → Fix F3: test-writer (Regressionstest), coder-pro.
Review 2/2: freigegeben ([pruefung.md](pruefung.md), Abschnitt „Review 2“) — Randfall 3 im Chrome bestätigt; sollte → #1612 (Modell bleibt `null`, während das Feld die erste Option zeigt; Verhaltensentscheidung, mit dem kann-Fund „alle Optionen disabled“).
Fehlerprotokoll: eingetragen 2026-11-18.

## Stand
Freigegeben 2026-11-18 (Review 2). Nächster Schritt: DOKU, dann Merge nach `main`.
