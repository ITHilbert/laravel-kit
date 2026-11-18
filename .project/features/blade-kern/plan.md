# Plan blade-kern
laravel-kit `feature/blade-kern` (von `main` 4019bd53). Host `packages_dev` `feature/blade-kern` für T5.
Spec: [spec.md](spec.md) (freigegeben 2026-11-18, Moderator in Vollmacht). Kein Dashboard-Task.

## Aufgaben
- [ ] TW test-writer (AK1–AK17, Randfälle 1–5) — Tests:
  - `tests/Feature/KitComponents/KernFelderTest.php` (AK1, AK2–AK6, Randfälle 1, 2, 4, 5; Feldteil von AK14)
  - `tests/Feature/KitComponents/KernLayoutTest.php` (AK7, AK8, AK13)
  - `tests/Feature/KitComponents/KernTabsComboboxTest.php` (AK9, AK11)
  - `tests/js/kernTabs.test.mjs` (AK10, Randfall 3)
  - `tests/js/kernCombobox.test.mjs` (AK12)
  - `tests/Feature/KitComponents/KernQuelltextTest.php` (AK14, AK15, AK16)
  - Host `tests/Feature/UiKitBladeKernTest.php` (AK17: Route 200, Theme `kern` auf der Seite, alle 34 Bausteine mit `kit-`-Markup; Browserteil per Echtlauf)
- [ ] T1 (AK1–AK6) [pro] `KitTheme::Kern`, Feld-Templates unter `src/Views/kit/kern/` (`input-*`, `text-area`, `select`, `checkbox`, `label`, `input-hidden`, `partials/`) — Tests: `tests/Feature/KitComponents/KernFelderTest.php`
- [ ] T2 (AK7, AK8, AK13) [pro] Buttons, Cards, `show-euro`, `show-date`, `breadcrumb` unter `src/Views/kit/kern/` — Tests: `tests/Feature/KitComponents/KernLayoutTest.php`
- [ ] T3 (AK9–AK12) [pro] Tabs- und Combobox-Templates, `js/tabs.js`, `js/combobox.js` — Tests: `tests/Feature/KitComponents/KernTabsComboboxTest.php`, `tests/js/kernTabs.test.mjs`, `tests/js/kernCombobox.test.mjs`
- [ ] T4 (AK14–AK16) [pro] Themes, `kit.css`, README, CHANGELOG — Tests: `tests/Feature/KitComponents/KernQuelltextTest.php`
- [ ] T5 (AK17) [pro] Host: Prüfseite `/ui-kit-blade-kern`, Build — Tests: Host `tests/Feature/UiKitBladeKernTest.php`; Nachweis per Echtlauf (Tabs, Combobox, Konsole)

Bestehende Tests: `tests/Feature/KitComponents/*.php` (BausteineTest, ThemeTest, LabelForTest, FormularOldFehlerTest, FehleranzeigeSchalterTest, CheckboxCheckedTest, ComboboxTest, Bootstrap5LabelTest, FormularNeuTest, ReadmeTest), `tests/js/tabs.test.mjs`, `tests/js/combobox.test.mjs`, `tests/js/comboboxAktualisieren.test.mjs`, `tests/js/loeschen.test.mjs`, `tests/Feature/Vue3/*.php`
Marketing: entfällt (Bausteine ohne neue öffentliche Seite).
Design: entfällt (Optik über die vorhandenen Theme-Dateien, Prüfseite im Host).
Review: nötig (Querschnitt Kit und Host, > 50 Zeilen Produktivcode).

## Stand
Spec und Plan als Entwurf. Nächster Schritt: test-writer.
