<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\Vue3;

use PHPUnit\Framework\TestCase;

// testebene: Quelltext-Wächter (Arch) — liest nur Dateien des Kits, braucht keine App.
/**
 * Wächter für die Logik-Komponenten von `vue3/` (AK15, AK16, AK19).
 * Die Klassenregel über alle Dateien prüft zusätzlich Vue3QuelltextTest; hier gilt sie gezielt für die vier neuen.
 */
final class Vue3LogikQuelltextTest extends TestCase
{
    private const VERTRAG = [
        'combobox' => ['kit-combobox', 'kit-combobox__input', 'kit-combobox__trigger', 'kit-combobox__list', 'kit-combobox__item', 'kit-combobox__empty'],
        'search' => ['kit-search', 'kit-search__icon', 'kit-search__input', 'kit-search__clear'],
        'input-token' => ['kit-input-token', 'kit-input-token__input', 'kit-input-token__toggle', 'kit-input-token__copy', 'kit-input-token__status'],
        'input-list' => ['kit-input-list', 'kit-input-list__input', 'kit-input-list__datalist'],
    ];

    private const ZUSTAENDE = ['is-open', 'is-active', 'is-invalid', 'disabled'];

    private const BOOTSTRAP = '/^(btn|modal|card|row)(-.*)?$|^(nav|form|col)-/';

    private const TAILWIND = '/^-?(p[xytrbl]?|m[xytrbl]?|text|bg|flex|grid|rounded|gap|w|h|border|shadow|font|items|justify|space)(-|$)/';

    private function kit(string $pfad = ''): string
    {
        return dirname(__DIR__, 3).'/'.$pfad;
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

    // AK15 (Gegenprobe des Wächters)
    public function test_waechter_erkennt_fremde_klassen(): void
    {
        $this->assertEqualsCanonicalizing(['btn', 'px-4', 'form-control'], $this->verstoesse('<div class="btn px-4 kit-x form-control is-open">'));
        $this->assertSame(['foo'], $this->verstoesse('<i :class="{ \'is-invalid\': a, foo: b }" class="kit-a__b">'));
        $this->assertSame([], $this->verstoesse('<i class="kit-a kit-a__b" :class="{ \'is-open\': x, disabled: y }">'));
    }

    // AK15
    public function test_vier_neue_komponenten_tragen_alle_vertragsklassen_und_nur_kit_klassen_oder_zustaende(): void
    {
        foreach (self::VERTRAG as $ordner => $klassen) {
            $datei = $this->kit("vue3/components/{$ordner}/{$ordner}.vue");
            $this->assertFileExists($datei);
            $text = (string) file_get_contents($datei);
            $this->assertSame([], $this->verstoesse($text), "Fremde Klassen in {$ordner}.vue");
            $vorhanden = $this->vueKlassen($text);
            $this->assertSame([], array_values(array_diff($klassen, $vorhanden)), "Vertragsklassen fehlen in {$ordner}.vue");
        }
    }

    /** Text vor jeder öffnenden Klammer. @return list<string> */
    private function selektoren(string $css): array
    {
        preg_match_all('~([^{}]+)\{~', $css, $m);

        return array_map('trim', $m[1]);
    }

    /** @return list<string> */
    private function fehlendeRegeln(string $text, array $klassen): array
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

        return $fehlt;
    }

    /** Rumpf der ersten Regel mit genau diesem Selektor (Klammern gezählt). */
    private function regelRumpf(string $text, string $selektor): ?string
    {
        $text = $this->ohneKommentare($text);
        if (! preg_match('~(?:^|[}\s;])'.preg_quote($selektor, '~').'\s*\{~', $text, $m, PREG_OFFSET_CAPTURE)) {
            return null;
        }
        $start = $m[0][1] + strlen($m[0][0]);
        $tiefe = 1;
        for ($i = $start, $n = strlen($text); $i < $n; $i++) {
            $tiefe += match ($text[$i]) {
                '{' => 1,
                '}' => -1,
                default => 0,
            };
            if ($tiefe === 0) {
                return substr($text, $start, $i - $start);
            }
        }

        return null;
    }

    /** @return list<string> Wurzeln ohne Regel für is-invalid (`.wurzel.is-invalid` oder `&.is-invalid` in der Wurzelregel) */
    private function ohneInvalid(string $text, array $wurzeln): array
    {
        $fehlt = [];
        foreach ($wurzeln as $wurzel) {
            $direkt = preg_match('~\.'.preg_quote($wurzel, '~').'(?:\.is-invalid|:is\(\.is-invalid|:where\(\.is-invalid)~', $this->ohneKommentare($text));
            $rumpf = $this->regelRumpf($text, '.'.$wurzel);
            $verschachtelt = $rumpf !== null && preg_match('~&\.is-invalid(?![\w-])~', $rumpf);
            if (! $direkt && ! $verschachtelt) {
                $fehlt[] = '.'.$wurzel.'.is-invalid';
            }
        }

        return $fehlt;
    }

    // AK16 (Gegenprobe der Prüfer)
    public function test_theme_pruefer_melden_fehlendes_und_erkennen_verschachtelung(): void
    {
        $this->assertSame(['.kit-a__b'], $this->fehlendeRegeln('.kit-a { } /* .kit-a__b { } */ .kit-a__bb { }', ['kit-a', 'kit-a__b']));
        $this->assertSame([], $this->fehlendeRegeln('.kit-a { &__b { x: y } }', ['kit-a', 'kit-a__b']));
        $this->assertSame(['.kit-b.is-invalid'], $this->ohneInvalid('.kit-a { &.is-invalid { x: y } } .kit-b { x: y }', ['kit-a', 'kit-b']));
        $this->assertSame([], $this->ohneInvalid('.kit-a.is-invalid { x: y } .kit-b { &.is-invalid { x: y } }', ['kit-a', 'kit-b']));
    }

    // AK16
    public function test_beide_themes_haben_eine_regel_je_teil_und_is_invalid_an_den_wurzeln(): void
    {
        $alle = array_merge(...array_values(self::VERTRAG));
        foreach (['vue3/themes/tailwind.css', 'vue3/themes/bootstrap5.scss'] as $pfad) {
            $text = (string) file_get_contents($this->kit($pfad));
            $this->assertNotSame('', $text, "{$pfad} fehlt oder ist leer");
            $this->assertSame([], $this->fehlendeRegeln($text, $alle), "Fehlende Regeln in {$pfad}");
            $this->assertSame([], $this->ohneInvalid($text, array_map(static fn (array $k) => $k[0], array_values(self::VERTRAG))), "is-invalid fehlt in {$pfad}");
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

    // AK19 (Gegenprobe des Abschnitt-Helfers)
    public function test_abschnitt_schneidet_bis_zur_naechsten_gleichrangigen_ueberschrift(): void
    {
        $this->assertSame("## Vue 3\nin\n### U\nauch", $this->abschnitt("# A\n## Vue 3\nin\n### U\nauch\n## Ende\naus", '/^Vue 3$/'));
        $this->assertSame('', $this->abschnitt("# A\nx", '/^Vue 3$/'));
    }

    // AK19
    public function test_readme_nennt_die_vier_komponenten_mit_tag_props_ereignissen_und_klassen(): void
    {
        $abschnitt = $this->abschnitt((string) file_get_contents($this->kit('README.md')), '/^Vue 3$/');
        $this->assertNotSame('', $abschnitt, 'README braucht den Abschnitt „Vue 3“');

        $muss = ['kit-combobox', 'kit-search', 'kit-input-token', 'kit-input-list', 'modelValue', 'update:modelValue', 'invalid', 'options', 'verzoegerung', 'suche'];
        foreach (array_merge($muss, array_merge(...array_values(self::VERTRAG))) as $begriff) {
            $this->assertStringContainsString($begriff, $abschnitt, "README-Abschnitt Vue 3 nennt „{$begriff}“ nicht");
        }
    }

    // AK19
    public function test_changelog_nennt_die_komponenten_unter_unveroeffentlicht(): void
    {
        $abschnitt = $this->abschnitt((string) file_get_contents($this->kit('CHANGELOG.md')), '/^Unveröffentlicht/');
        $this->assertNotSame('', $abschnitt);
        foreach (['kit-combobox', 'kit-search', 'kit-input-token', 'kit-input-list'] as $tag) {
            $this->assertStringContainsString($tag, $abschnitt, "CHANGELOG nennt {$tag} nicht unter Unveröffentlicht");
        }
    }
}
