<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\Vue3;

use ITHilbert\LaravelKit\Tests\Feature\ChangelogHilfe;
use PHPUnit\Framework\TestCase;

// testebene: Quelltext-Wächter (Arch) — liest nur Dateien des Kits, braucht keine App.
/**
 * Wächter für `vue3/` (AK10, AK11, AK14): Quelltexte, Theme-Dateien, README/CHANGELOG.
 * Jeder Suchmuster-Helfer hat eine Gegenprobe an Beispieltexten.
 */
final class Vue3QuelltextTest extends TestCase
{
    use ChangelogHilfe;

    private const ZUSTAENDE = ['is-open', 'is-active', 'is-invalid', 'disabled'];

    private const BOOTSTRAP = '/^(btn|modal|card|row)(-.*)?$|^(nav|form|col)-/';

    private const TAILWIND = '/^-?(p[xytrbl]?|m[xytrbl]?|text|bg|flex|grid|rounded|gap|w|h|border|shadow|font|items|justify|space)(-|$)/';

    private function kit(string $pfad = ''): string
    {
        return dirname(__DIR__, 3).'/'.$pfad;
    }

    /** @return list<string> */
    private function komponentenDateien(): array
    {
        $dateien = glob($this->kit('vue3/components/*/*.vue')) ?: [];
        $css = $this->kit('vue3/kit.css');
        if (is_file($css)) {
            $dateien[] = $css;
        }

        return $dateien;
    }

    private function ohneKommentare(string $text): string
    {
        return (string) preg_replace(['~/\*.*?\*/~s', '~<!--.*?-->~s'], '', $text);
    }

    /**
     * Alle Klassennamen einer Datei: `class="…"`, `:class="…"` (Zeichenketten und Objektschlüssel),
     * Selektoren in CSS bzw. `<style>`.
     *
     * @return list<string>
     */
    private function klassen(string $text, bool $istCss): array
    {
        $text = $this->ohneKommentare($text);
        $klassen = [];
        $css = $istCss ? $text : '';
        if (! $istCss) {
            if (preg_match_all('~<style\b[^>]*>(.*?)</style>~s', $text, $m)) {
                $css = implode("\n", $m[1]);
            }
            $text = (string) preg_replace('~<style\b[^>]*>.*?</style>~s', '', $text);
            if (preg_match_all('~(?<![:\w-])class\s*=\s*"([^"]*)"~', $text, $m)) {
                foreach ($m[1] as $wert) {
                    array_push($klassen, ...preg_split('/\s+/', trim($wert), -1, PREG_SPLIT_NO_EMPTY));
                }
            }
            if (preg_match_all('~(?::|v-bind:)class\s*=\s*"([^"]*)"~', $text, $m)) {
                foreach ($m[1] as $ausdruck) {
                    if (preg_match_all('~\'([^\']*)\'~', $ausdruck, $z)) {
                        foreach ($z[1] as $wert) {
                            array_push($klassen, ...preg_split('/\s+/', trim($wert), -1, PREG_SPLIT_NO_EMPTY));
                        }
                    }
                    if (preg_match_all('~(?:^|[{,]\s*)([A-Za-z_][\w-]*)\s*:~', $ausdruck, $z)) {
                        array_push($klassen, ...$z[1]);
                    }
                }
            }
        }
        $css = (string) preg_replace(['~url\([^)]*\)~', '~"[^"]*"|\'[^\']*\'~'], '', $css);
        foreach ($this->selektoren($css) as $prelude) {
            if (preg_match_all('~\.(-?[_a-zA-Z][\w-]*)~', $prelude, $m)) {
                array_push($klassen, ...$m[1]);
            }
        }

        return array_values(array_unique($klassen));
    }

    /** Text vor jeder öffnenden Klammer (Selektoren, auch verschachtelt). @return list<string> */
    private function selektoren(string $css): array
    {
        preg_match_all('~([^{}]+)\{~', $css, $m);

        return array_map('trim', $m[1]);
    }

    /** @return list<string> verbotene bzw. nicht erlaubte Klassen */
    private function verstoesse(string $text, bool $istCss): array
    {
        $fehler = [];
        foreach ($this->klassen($text, $istCss) as $k) {
            $erlaubt = str_starts_with($k, 'kit-') || in_array($k, self::ZUSTAENDE, true);
            if (! $erlaubt || preg_match(self::BOOTSTRAP, $k) || preg_match(self::TAILWIND, $k)) {
                $fehler[] = $k;
            }
        }
        if (preg_match('~@apply\b~', $text)) {
            $fehler[] = '@apply';
        }

        return $fehler;
    }

    // AK10 (Gegenprobe des Wächters)
    public function test_waechter_erkennt_fremde_klassen_in_vue_und_css(): void
    {
        $this->assertEqualsCanonicalizing(['btn', 'px-4', 'flex'], $this->verstoesse('<div class="btn px-4 kit-x flex is-open">', false));
        $this->assertSame(['foo'], $this->verstoesse('<i :class="{ \'is-open\': a, foo: b, disabled: c }" class="kit-a__b">', false));
        $this->assertSame(['modal-dialog'], $this->verstoesse('<i :class="[\'kit-a\', \'modal-dialog\']">', false));
        $this->assertEqualsCanonicalizing(['nav-link', '@apply'], $this->verstoesse(".kit-a { color: red; }\n.nav-link { @apply px-2; }", true));
        $this->assertSame(['row'], $this->verstoesse('<style>.kit-a .row { top: 0.5em; background: url(a.png) }</style>', false));
        $this->assertSame([], $this->verstoesse('<div class="kit-a kit-a__b is-active" :class="{ \'is-open\': x, disabled: y }">', false));
        $this->assertSame([], $this->verstoesse('/* .btn */ .kit-a.is-open > .kit-a__b { margin: 0.5em; }', true));
    }

    // AK10
    public function test_kern_enthaelt_nur_kit_klassen_und_zustaende_ohne_bootstrap_oder_tailwind(): void
    {
        $komponenten = glob($this->kit('vue3/components/*/*.vue')) ?: [];
        $this->assertFileExists($this->kit('vue3/kit.css'));
        foreach (['dialog', 'tabs', 'accordion'] as $name) {
            $this->assertNotEmpty(glob($this->kit("vue3/components/{$name}/*.vue")), "Keine .vue-Datei in vue3/components/{$name}/");
        }
        $this->assertGreaterThanOrEqual(9, count($komponenten), 'Je Tag eine Komponente erwartet');

        $verstoesse = [];
        foreach ($this->komponentenDateien() as $datei) {
            $v = $this->verstoesse((string) file_get_contents($datei), str_ends_with($datei, '.css'));
            if ($v !== []) {
                $verstoesse[basename(dirname($datei)).'/'.basename($datei)] = $v;
            }
        }
        $this->assertSame([], $verstoesse);
    }

    /** @return list<string> fehlende Einträge (Selektoren in einer Regel, auch als `&__teil` oder `&.zustand`) */
    private function fehlendeRegeln(string $text, array $klassen, array $zustaende): array
    {
        $praeludien = implode("\n", $this->selektoren($this->ohneKommentare($text)));
        $fehlt = [];
        foreach ($klassen as $klasse) {
            $teil = preg_quote(substr($klasse, strpos($klasse, '__')), '~');
            $voll = preg_quote($klasse, '~');
            if (! preg_match("~(\.{$voll}(?![\w-])|&{$teil}(?![\w-]))~", $praeludien)) {
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

    private const TEILE = [
        'kit-dialog__overlay', 'kit-dialog__content', 'kit-dialog__title', 'kit-dialog__close', 'kit-dialog__trigger',
        'kit-tabs__list', 'kit-tabs__tab', 'kit-tabs__panel',
        'kit-accordion__item', 'kit-accordion__trigger', 'kit-accordion__content',
    ];

    // AK11 (Gegenprobe des Wächters)
    public function test_waechter_meldet_fehlende_regeln_und_erkennt_verschachtelung(): void
    {
        $this->assertSame(['.kit-tabs__tab', '.is-active'], $this->fehlendeRegeln(
            '.kit-tabs__list { a: b } /* .kit-tabs__tab { } */ .kit-tabs__tabs { a: b }',
            ['kit-tabs__list', 'kit-tabs__tab'],
            ['is-active'],
        ));
        $this->assertSame([], $this->fehlendeRegeln(
            ".kit-dialog { &__title { a: b } &.is-open { a: b } }\n.kit-tabs__list.disabled { a: b }",
            ['kit-dialog__title', 'kit-tabs__list'],
            ['is-open', 'disabled'],
        ));
    }

    // AK11
    public function test_beide_themes_haben_eine_regel_je_teil_und_zustand(): void
    {
        foreach (['vue3/themes/tailwind.css', 'vue3/themes/bootstrap5.scss'] as $pfad) {
            $this->assertFileExists($this->kit($pfad));
            $this->assertSame(
                [],
                $this->fehlendeRegeln((string) file_get_contents($this->kit($pfad)), self::TEILE, ['is-open', 'is-active', 'disabled']),
                "Fehlende Regeln in {$pfad}",
            );
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

    // AK14 (Gegenprobe des Abschnitt-Helfers)
    public function test_abschnitt_schneidet_bis_zur_naechsten_gleichrangigen_ueberschrift(): void
    {
        $text = "# A\nx\n## Vue 3 Kern\nin\n### Unter\nauch\n## Ende\naus";
        $this->assertSame("## Vue 3 Kern\nin\n### Unter\nauch", $this->abschnitt($text, '/vue\s?3/i'));
        $this->assertSame('', $this->abschnitt('# A\nx', '/vue\s?3/i'));
    }

    // AK14
    public function test_readme_beschreibt_vue3_ordner_klassenregel_und_einbinden(): void
    {
        $abschnitt = $this->abschnitt((string) file_get_contents($this->kit('README.md')), '/vue\s?3/i');
        $this->assertNotSame('', $abschnitt, 'README braucht einen Abschnitt mit Überschrift „Vue 3“ bzw. „vue3“');

        foreach (['vue3/', 'kit-', '__', 'is-open', 'is-active', 'disabled', 'KitVue3', 'kitKomponenten', 'mountInseln', 'themes/', 'npm install reka-ui'] as $muss) {
            $this->assertStringContainsString($muss, $abschnitt, "README-Abschnitt Vue 3 nennt „{$muss}“ nicht");
        }
    }

    // AK14
    public function test_vue_readme_nennt_nur_tailwind_als_vue3_und_changelog_den_neuen_ordner(): void
    {
        $vueReadme = (string) file_get_contents($this->kit('vue/README.md'));
        $this->assertStringContainsString('vue/tailwind/', $vueReadme);
        $this->assertMatchesRegularExpression('/nicht mehr gepflegt/i', $vueReadme);

        // „Unveröffentlicht“ und letzte veröffentlichte Version (nach einem Release stehen die Einträge dort)
        $erster = $this->changelogAktuell((string) file_get_contents($this->kit('CHANGELOG.md')));
        $this->assertStringContainsString('vue3/', $erster);
        $this->assertStringContainsString('reka-ui', $erster);
    }

    /** Rumpf der ersten Regel mit genau diesem Selektor (Klammern gezählt), sonst null. */
    private function regelRumpf(string $scss, string $selektor): ?string
    {
        $scss = $this->ohneKommentare((string) preg_replace('~//[^\n]*~', '', $scss));
        if (! preg_match('~(?:^|[}\s;])'.preg_quote($selektor, '~').'\s*\{~', $scss, $m, PREG_OFFSET_CAPTURE)) {
            return null;
        }
        $start = $m[0][1] + strlen($m[0][0]);
        $tiefe = 1;
        for ($i = $start, $n = strlen($scss); $i < $n; $i++) {
            $tiefe += match ($scss[$i]) {
                '{' => 1,
                '}' => -1,
                default => 0,
            };
            if ($tiefe === 0) {
                return substr($scss, $start, $i - $start);
            }
        }

        return null;
    }

    /** @return list<string> Verstöße: Dialog-Inhalt erbt Bootstrap-Variablen, die am fehlenden `.modal` hängen */
    private function dialogVerstoesse(string $scss): array
    {
        $inhalt = $this->regelRumpf($scss, '.kit-dialog__content');
        if ($inhalt === null) {
            return ['Regel .kit-dialog__content fehlt'];
        }
        $fehler = [];
        if (preg_match('~@extend\s+\.modal-content\b~', $inhalt)) {
            $fehler[] = '@extend .modal-content (liest --bs-modal-*, die nur .modal setzt)';
        }
        foreach (['background-color', 'border', 'border-radius'] as $eigenschaft) {
            if (! preg_match('~(?<![\w-])'.preg_quote($eigenschaft, '~').'\s*:~', $inhalt)) {
                $fehler[] = "{$eigenschaft} nicht gesetzt";
            }
        }
        $dialog = $this->regelRumpf($scss, '.kit-dialog__title') ?? '';
        foreach (array_unique(preg_match_all('~var\(\s*(--bs-modal-[\w-]+)~', $scss, $m) ? $m[1] : []) as $variable) {
            if (! str_contains($inhalt, $variable.':')) {
                $fehler[] = "{$variable} verwendet, aber nicht an .kit-dialog__content definiert";
            }
        }
        if (preg_match('~@extend\s+\.modal-title\b~', $dialog) && ! preg_match('~(?<![\w-])line-height\s*:~', $dialog)) {
            $fehler[] = '@extend .modal-title ohne eigene line-height (--bs-modal-title-line-height fehlt)';
        }

        return $fehler;
    }

    // AK13 Fix F2 (Gegenprobe des Prüfers)
    public function test_dialog_pruefer_erkennt_den_alten_fehlerhaften_stand(): void
    {
        $alt = ".kit-dialog__content {\n  @extend .modal-content;\n  width: 100%;\n}\n.kit-dialog__title { @extend .modal-title; }";
        $verstoesse = $this->dialogVerstoesse($alt);
        $this->assertNotSame([], $verstoesse);
        $this->assertStringContainsString('@extend .modal-content', implode(' ', $verstoesse));

        $gut = ".kit-dialog__content {\n  --bs-modal-bg: #fff;\n  background-color: var(--bs-modal-bg);\n  border: 1px solid #ccc;\n  border-radius: 4px;\n}\n.kit-dialog__title { line-height: 1.5; }";
        $this->assertSame([], $this->dialogVerstoesse($gut));
        $this->assertSame(['--bs-modal-bg verwendet, aber nicht an .kit-dialog__content definiert'], array_values(array_filter(
            $this->dialogVerstoesse(str_replace('--bs-modal-bg: #fff;', '', $gut)),
            fn (string $f) => str_contains($f, '--bs-modal-bg'),
        )));
    }

    // AK13 Fix F2: Dialog im Bootstrap-Theme hat Hintergrund, Rahmen und Radius ohne `.modal`-Elternelement
    public function test_bootstrap5_dialog_inhalt_hat_eigenen_hintergrund_rahmen_und_radius(): void
    {
        $scss = (string) file_get_contents($this->kit('vue3/themes/bootstrap5.scss'));
        $this->assertSame([], $this->dialogVerstoesse($scss));
    }

    // AK13 Fix F2: gleiches Muster bei Tabs und Accordion (Variablen hängen am Elternelement)
    public function test_bootstrap5_tabs_und_accordion_erben_die_elternklasse_mit_den_variablen(): void
    {
        $scss = (string) file_get_contents($this->kit('vue3/themes/bootstrap5.scss'));
        $tabs = $this->regelRumpf($scss, '.kit-tabs__list');
        $this->assertNotNull($tabs);
        $this->assertMatchesRegularExpression('~@extend\s+\.nav-tabs\b~', $tabs, '--bs-nav-tabs-* setzt nur .nav-tabs');
        $accordion = $this->regelRumpf($scss, '.kit-accordion');
        $this->assertNotNull($accordion);
        $this->assertMatchesRegularExpression('~@extend\s+\.accordion\b~', $accordion, '--bs-accordion-* setzt nur .accordion');
        $this->assertNull($this->regelRumpf('.kit-tabs__list { @extend .nav; }', '.kit-x'));
    }
}
