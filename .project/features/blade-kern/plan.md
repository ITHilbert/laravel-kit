# Plan blade-kern
laravel-kit `feature/blade-kern` (von `main` 4019bd53). Host `packages_dev` `feature/blade-kern` für T5.
Spec: [spec.md](spec.md) (freigegeben 2026-11-18, Moderator in Vollmacht). Kein Dashboard-Task.

## Aufgaben
- [x] TW (test-writer a4e2281b975eed015, rot: PHP 43/44 — Meldung `Unbekanntes Theme "kern"`, `src/Views/kit/kern/ fehlt`, `keine Regel für kit-button`, README/CHANGELOG ohne `kern`; grün 1 = Gegenprobe des Klassen-Prüfers; Host 2/2 `Expected 200 but received 404`; JS 7/9, 2 grün = Wächter für alte Themes; grünbar: JS 5/5 und 4/4, PHP entfällt bei Risiko normal; Hilfs-Trait `KernHilfe.php` für T1–T3) test-writer (AK1–AK17, Randfälle 1–5) — Tests:
  - `tests/Feature/KitComponents/KernFelderTest.php` (AK1, AK2–AK6, Randfälle 1, 2, 4, 5; Feldteil von AK14)
  - `tests/Feature/KitComponents/KernLayoutTest.php` (AK7, AK8, AK13)
  - `tests/Feature/KitComponents/KernTabsComboboxTest.php` (AK9, AK11)
  - `tests/js/kernTabs.test.mjs` (AK10, Randfall 3)
  - `tests/js/kernCombobox.test.mjs` (AK12)
  - `tests/Feature/KitComponents/KernQuelltextTest.php` (AK14, AK15, AK16)
  - Host `tests/Feature/UiKitBladeKernTest.php` (AK17: Route 200, Theme `kern` auf der Seite, alle 34 Bausteine mit `kit-`-Markup; Browserteil per Echtlauf)
- [x] T1 (coder-pro a1938bb19f7597140, HANDOFF Umgebung: GATE ROT nur durch `KernQuelltextTest` (Gruppe `arch`, läuft in jedem GATE, gehört T4) — Plan-Fehler des Moderators; Nachweis Moderator: 7 Dateien aus TESTS: 1802 passed, `--group=arch` 6 rot = nur KernQuelltextTest → T2, T3, T4 zusammengelegt) (AK1–AK6) [pro] `KitTheme::Kern`, Feld-Templates unter `src/Views/kit/kern/` (`input-*`, `text-area`, `select`, `checkbox`, `label`, `input-hidden`, `partials/`) — Tests: `tests/Feature/KitComponents/KernFelderTest.php`
- [x] T2 (AK7, AK8, AK13) (zusammengelegt T2+T3+T4: coder-pro a603d65301ee8265f, GATE GRÜN: PHP 161, JS 104; Moderator: Kit-Feature 2227 passed, JS 249/249) [pro] Buttons, Cards, `show-euro`, `show-date`, `breadcrumb` unter `src/Views/kit/kern/` — Tests: `tests/Feature/KitComponents/KernLayoutTest.php`
- [x] T3 (AK9–AK12) (zusammengelegt T2+T3+T4: coder-pro a603d65301ee8265f, GATE GRÜN: PHP 161, JS 104; Moderator: Kit-Feature 2227 passed, JS 249/249) [pro] Tabs- und Combobox-Templates, `js/tabs.js`, `js/combobox.js` — Tests: `tests/Feature/KitComponents/KernTabsComboboxTest.php`, `tests/js/kernTabs.test.mjs`, `tests/js/kernCombobox.test.mjs`
- [x] T4 (AK14–AK16) (zusammengelegt T2+T3+T4: coder-pro a603d65301ee8265f, GATE GRÜN: PHP 161, JS 104; Moderator: Kit-Feature 2227 passed, JS 249/249) [pro] Themes, `kit.css`, README, CHANGELOG — Tests: `tests/Feature/KitComponents/KernQuelltextTest.php`
- [ ] T5 (AK17) [pro] Host: Prüfseite `/ui-kit-blade-kern`, Build — Tests: Host `tests/Feature/UiKitBladeKernTest.php`; Nachweis per Echtlauf (Tabs, Combobox, Konsole)

Bestehende Tests: `tests/Feature/KitComponents/*.php` (BausteineTest, ThemeTest, LabelForTest, FormularOldFehlerTest, FehleranzeigeSchalterTest, CheckboxCheckedTest, ComboboxTest, Bootstrap5LabelTest, FormularNeuTest, ReadmeTest), `tests/js/tabs.test.mjs`, `tests/js/combobox.test.mjs`, `tests/js/comboboxAktualisieren.test.mjs`, `tests/js/loeschen.test.mjs`, `tests/Feature/Vue3/*.php`
Marketing: entfällt (Bausteine ohne neue öffentliche Seite).
Design: entfällt (Optik über die vorhandenen Theme-Dateien, Prüfseite im Host).
Review: nötig (Querschnitt Kit und Host, > 50 Zeilen Produktivcode).

Offener Punkt a4e2281b975eed015: AK-fremd (Namen der `data-`-Attribute aus den alten Themes übernommen: `data-kit-tabs`, `data-kit-tab`, `data-kit-tabs-body`, `data-kit-tab-body`, `data-kit-combobox` — Entscheidungsspielraum der Spec; Panel `id=<name>`, Reiter `aria-controls=<name>` wie bisher; Host-Seite bindet Kit-CSS per `<link>`)

Abweichung a1938bb19f7597140: Fehlalarm: keine gemeldet
Offener Punkt a1938bb19f7597140: AK-fremd (Minimal-Template `kern/combobox` vorgezogen, weil KernFelderTest AK1/AK14 sie rendert — T3 baut sie nach AK11/AK12 aus; Doku-Zeile in `src/Config/laravelkit.php` → T4; Passwort ohne Schalter = Nicht-Ziel)

Abweichung a603d65301ee8265f: Fehlalarm: keine gemeldet
Offener Punkt a603d65301ee8265f: AK-fremd (`combobox.js` verschiebt im Kern die `id` vom `<select>` auf `__input` und kopiert `aria-invalid`/`aria-describedby`, damit `label for` nach dem Binden auf das Eingabefeld zeigt — AK11 prüft das Markup vor dem Binden, AK12 regelt es nicht; Annahme Barrierefreiheit. Liste unter einer Fehlermeldung der Combobox → im Echtlauf T5 ansehen. Button-Farben je `data-variant` frei gewählt — Spec legt keine fest)

T5 Host: coder-pro a1ed247cfc4a1b255 (GATE GRÜN: 10 passed). NACHWEIS 1 ROT: `ReferenceError: dt is not defined` auf jeder Seite mit `app.js` (ES-`import` in `resources/js/app.js` machte die Datei zum strikten Modul) → derselbe Coder: `require` statt `import`; danach `/ui-kit`, `/ui-kit-vue3`, `/ui-kit-blade-kern` je 0 Konsolenfehler.
Abweichung a1ed247cfc4a1b255: Fehlalarm: keine gemeldet
Offener Punkt a1ed247cfc4a1b255: AK-fremd (`loeschen.js` auf der Prüfseite nicht eingebunden — AK17 verlangt nur Tabs und Combobox; Theme per `config()` je Anfrage, Host ohne Octane)
Befund Moderator (Screenshot, Optik): Trigger der Kern-Combobox als leeres Kästchen unter dem Feld; „€“ von `input-euro` unter dem Feld (Blade und Vue, = #1611 Punkt 2); Buttons ohne Abstand auf der Prüfseite.
Optik-Fix Kit: coder-junior a9d2c3299a0c03eae (GATE GRÜN: PHP 30, JS 249/249, Vitest 160/160): `kit.css` Combobox als Grid (Trigger rechts im Feld, Liste direkt unter dem Feld), Zusatz-Felder als Zeile; `bootstrap5.scss` Trigger/Zusatz; `combobox.js` Pfeil-SVG im Kern-Trigger.
Abweichung a9d2c3299a0c03eae: Fehlalarm: `tailwind.css` nicht angepasst (Notbremse 3 Dateien) — Layout über `kit.css` korrigiert, AK15 unberührt
Offener Punkt a9d2c3299a0c03eae: AK-fremd (Zusatz im Tailwind-Theme ohne Anschluss an das Feld → #1611; Vue-Combobox geöffnet vom Moderator im Screenshot geprüft: Liste unter dem Feld, Trigger rechts)

## Stand
T1–T4 fertig. Nächster Schritt: T5 (Host-Prüfseite), dann Echtlauf und Review.
