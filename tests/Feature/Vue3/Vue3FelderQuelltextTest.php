<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\Vue3;

use ITHilbert\LaravelKit\Tests\Feature\ChangelogHilfe;
use PHPUnit\Framework\TestCase;

// testebene: Quelltext-Wächter (Arch) — liest nur Dateien des Kits, braucht keine App.
/**
 * Wächter für die Felder von `vue3/` (AK17, AK18, AK21).
 * Die Klassenregel über alle Dateien prüft zusätzlich Vue3QuelltextTest; hier gilt sie gezielt für die Felder.
 */
final class Vue3FelderQuelltextTest extends TestCase
{
    use ChangelogHilfe;

    /** Felder mit __label, __input, __error. */
    private const FELDER = [
        'input-text', 'input-email', 'input-password', 'input-number', 'input-int', 'input-euro', 'input-percent',
        'input-date', 'input-color', 'input-file', 'textarea', 'select', 'checkbox', 'radio', 'toggle',
    ];

    private const EXTRA_TEILE = ['kit-input-password__toggle', 'kit-input-euro__suffix', 'kit-input-percent__suffix', 'kit-toggle__thumb'];

    private const ZUSTAENDE = ['is-open', 'is-active', 'is-invalid', 'disabled'];

    private const BOOTSTRAP = '/^(btn|modal|card|row)(-.*)?$|^(nav|form|col)-/';

    private const TAILWIND = '/^-?(p[xytrbl]?|m[xytrbl]?|text|bg|flex|grid|rounded|gap|w|h|border|shadow|font|items|justify|space)(-|$)/';

    /** Alt → neu laut Spec, Spalte „Ersetzt“. */
    private const ERSETZT = [
        'kit-input-text' => ['input-text'], 'kit-input-email' => ['input-email'], 'kit-input-password' => ['input-password'],
        'kit-input-number' => ['input-number'], 'kit-input-int' => ['input-int'], 'kit-input-euro' => ['input-euro'],
        'kit-input-percent' => ['input-percent'], 'kit-input-date' => ['input-date'], 'kit-input-color' => ['input-color'],
        'kit-input-file' => ['input-file', 'input-file-img'], 'kit-textarea' => ['text-area'],
        'kit-select' => ['combobox', 'vue-select'], 'kit-checkbox' => ['checkbox'], 'kit-radio' => ['radiobox'],
        'kit-toggle' => ['toggle'], 'kit-label' => ['hlabel'], 'kit-form' => ['hform'],
        'kit-input-group' => ['input-group'], 'kit-input-group-text' => ['input-group-text'],
    ];

    private function kit(string $pfad = ''): string
    {
        return dirname(__DIR__, 3).'/'.$pfad;
    }

    /** @return list<string> */
    private function wurzeln(): array
    {
        return array_keys(self::ERSETZT);
    }

    /** @return list<string> */
    private function teile(): array
    {
        $teile = self::EXTRA_TEILE;
        foreach (self::FELDER as $f) {
            array_push($teile, "kit-{$f}__label", "kit-{$f}__input", "kit-{$f}__error");
        }

        return $teile;
    }

    private function ohneKommentare(string $text): string
    {
        return (string) preg_replace(['~/\*.*?\*/~s', '~<!--.*?-->~s', '~(?<![:"\'])//[^\n]*~'], '', $text);
    }

    /** Klassen aus `class="…"` und Zeichenketten bzw. Objektschlüsseln in `:class="…"`. @return list<string> */
    private function vueKlassen(string $text): array
    {
        $text = $this->ohneKommentare($text);
        $text = (string) preg_replace('~<style\b[^>]*>.*?</style>~s', '', $text);
        $klassen = [];
        $teile = static fn (string $wert): array => preg_split('/\s+/', trim($wert), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if (preg_match_all('~(?<![:\w-])class\s*=\s*"([^"]*)"~', $text, $m)) {
            foreach ($m[1] as $wert) {
                array_push($klassen, ...$teile($wert));
            }
        }
        if (preg_match_all('~(?::|v-bind:)class\s*=\s*"([^"]*)"~', $text, $m)) {
            foreach ($m[1] as $ausdruck) {
                if (preg_match_all('~\'([^\']*)\'~', $ausdruck, $z)) {
                    foreach ($z[1] as $wert) {
                        array_push($klassen, ...$teile($wert));
                    }
                }
                if (preg_match_all('~(?:^|[{,]\s*)([A-Za-z_][\w-]*)\s*:~', $ausdruck, $z)) {
                    array_push($klassen, ...$z[1]);
                }
            }
        }

        return array_values(array_unique($klassen));
    }

    /** @return list<string> */
    private function verstoesse(string $text): array
    {
        $fehler = [];
        foreach ($this->vueKlassen($text) as $k) {
            $erlaubt = str_starts_with($k, 'kit-') || in_array($k, self::ZUSTAENDE, true);
            if (! $erlaubt || preg_match(self::BOOTSTRAP, $k) || preg_match(self::TAILWIND, $k)) {
                $fehler[] = $k;
            }
        }

        return $fehler;
    }

    // AK17 (Gegenprobe des Wächters)
    public function test_waechter_erkennt_fremde_klassen(): void
    {
        $this->assertEqualsCanonicalizing(['btn', 'px-4', 'form-control'], $this->verstoesse('<div class="btn px-4 kit-x form-control is-invalid">'));
        $this->assertSame([], $this->verstoesse('<i class="kit-a kit-a__b" :class="{ \'is-invalid\': x, disabled: y }">'));
    }

    // AK17
    public function test_jedes_feld_hat_seine_komponente_und_alle_dateien_tragen_nur_kit_klassen_oder_zustaende(): void
    {
        foreach ($this->wurzeln() as $tag) {
            $name = substr($tag, 4);
            if ($name === 'input-group-text') {
                continue; // darf neben input-group/ liegen
            }
            $this->assertFileExists($this->kit("vue3/components/{$name}/{$name}.vue"), "Komponente {$tag} fehlt");
        }
        $dateien = glob($this->kit('vue3/components/*/*.vue')) ?: [];
        $this->assertNotEmpty(array_filter($dateien, static fn (string $d): bool => str_contains($d, '/input-text/')));
        $verstoesse = [];
        foreach ($dateien as $datei) {
            $v = $this->verstoesse((string) file_get_contents($datei));
            if ($v !== []) {
                $verstoesse[basename(dirname($datei)).'/'.basename($datei)] = $v;
            }
        }
        $this->assertSame([], $verstoesse);
    }

    /** Text vor jeder öffnenden Klammer. @return list<string> */
    private function selektoren(string $css): array
    {
        preg_match_all('~([^{}]+)\{~', $css, $m);

        return array_map('trim', $m[1]);
    }

    /** @return list<string> */
    private function fehlendeRegeln(string $text, array $klassen, array $zustaende = []): array
    {
        $praeludien = implode("\n", $this->selektoren($this->ohneKommentare($text)));
        $fehlt = [];
        foreach ($klassen as $klasse) {
            $teil = preg_quote(substr($klasse, strpos($klasse, '__') ?: 0), '~');
            $voll = preg_quote($klasse, '~');
            $muster = str_contains($klasse, '__') ? "(\.{$voll}(?![\w-])|&{$teil}(?![\w-]))" : "\.{$voll}(?![\w-])";
            if (! preg_match("~{$muster}~", $praeludien)) {
                $fehlt[] = '.'.$klasse;
            }
        }
        foreach ($zustaende as $zustand) {
            if (! preg_match('~\.'.preg_quote($zustand, '~').'(?![\w-])~', $praeludien)) {
                $fehlt[] = '.'.$zustand;
            }
        }

        return $fehlt;
    }

    // AK18 (Gegenprobe der Prüfer)
    public function test_theme_pruefer_melden_fehlendes_und_erkennen_verschachtelung(): void
    {
        $this->assertSame(['.kit-a__b', '.is-active'], $this->fehlendeRegeln('.kit-a { } /* .kit-a__b { } */ .kit-a__bb { }', ['kit-a', 'kit-a__b'], ['is-active']));
        $this->assertSame([], $this->fehlendeRegeln('.kit-a { &__b { x: y } &.is-active { x: y } }', ['kit-a', 'kit-a__b'], ['is-active']));
    }

    // AK18
    public function test_beide_themes_haben_eine_regel_je_wurzel_teil_und_zustand(): void
    {
        $klassen = array_merge($this->wurzeln(), $this->teile());
        foreach (['vue3/themes/tailwind.css', 'vue3/themes/bootstrap5.scss'] as $pfad) {
            $text = (string) file_get_contents($this->kit($pfad));
            $this->assertNotSame('', $text, "{$pfad} fehlt oder ist leer");
            $this->assertSame([], $this->fehlendeRegeln($text, $klassen, ['is-invalid', 'is-active', 'disabled']), "Fehlende Regeln in {$pfad}");
        }
    }

    private function abschnitt(string $text, string $ueberschriftMuster): string
    {
        $ebene = null;
        $ausgabe = [];
        foreach (explode("\n", $text) as $zeile) {
            if (preg_match('/^(#{1,6})\s+(.*)$/', $zeile, $m)) {
                if ($ebene !== null && strlen($m[1]) <= $ebene) {
                    break;
                }
                if ($ebene === null && preg_match($ueberschriftMuster, $m[2])) {
                    $ebene = strlen($m[1]);
                }
            }
            if ($ebene !== null) {
                $ausgabe[] = $zeile;
            }
        }

        return implode("\n", $ausgabe);
    }

    // AK21 (Gegenprobe des Abschnitt-Helfers)
    public function test_abschnitt_schneidet_bis_zur_naechsten_gleichrangigen_ueberschrift(): void
    {
        $this->assertSame("## Vue 3\nin\n### U\nauch", $this->abschnitt("# A\n## Vue 3\nin\n### U\nauch\n## Ende\naus", '/^Vue 3$/'));
        $this->assertSame('', $this->abschnitt("# A\nx", '/^Vue 3$/'));
    }

    // AK21
    public function test_readme_nennt_komponenten_props_klassen_und_zuordnung_alt_neu(): void
    {
        $abschnitt = $this->abschnitt((string) file_get_contents($this->kit('README.md')), '/^Vue 3$/');
        $this->assertNotSame('', $abschnitt, 'README braucht den Abschnitt „Vue 3“');

        $muss = ['modelValue', 'update:modelValue', 'error', 'invalid', 'label', 'disabled', 'step', 'min', 'max', 'accept', 'multiple', 'rows', 'options', 'placeholder', 'value'];
        foreach (array_merge($this->wurzeln(), $this->teile(), $muss) as $begriff) {
            $this->assertStringContainsString($begriff, $abschnitt, "README-Abschnitt Vue 3 nennt „{$begriff}“ nicht");
        }

        $zeilen = explode("\n", $abschnitt);
        foreach (self::ERSETZT as $neu => $alt) {
            $tokens = array_map(static fn (string $t): string => "`{$t}`", [$neu, ...$alt]);
            $gefunden = array_filter($zeilen, static function (string $z) use ($tokens): bool {
                foreach ($tokens as $t) {
                    if (! str_contains($z, $t)) {
                        return false;
                    }
                }

                return true;
            });
            $this->assertNotEmpty($gefunden, "Zuordnung alt → neu fehlt: {$neu} ersetzt ".implode(', ', $alt));
        }
    }

    // AK21
    public function test_changelog_nennt_die_komponenten(): void
    {
        // Einträge dieses Features stehen im Abschnitt der Version 1.10.0
        $abschnitt = $this->changelogVersion((string) file_get_contents($this->kit('CHANGELOG.md')), '1.10.0');
        $this->assertNotSame('', $abschnitt);
        foreach ($this->wurzeln() as $tag) {
            $this->assertStringContainsString($tag, $abschnitt, "CHANGELOG nennt {$tag} nicht in Version 1.10.0");
        }
    }
}
