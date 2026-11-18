<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * Architektur-Tests zum Theme `kern` (Spec blade-kern, T4): Quelltext der Templates, Theme-Dateien, README, CHANGELOG.
 */
#[Group('arch')]
final class KernQuelltextTest extends TestCase
{
    private const VARIANTEN = ['back', 'cancel', 'create', 'delete', 'edit', 'save', 'show', 'submit'];

    private const REGELN = [
        'kit-button', 'kit-card', 'kit-card__header', 'kit-card__body', 'kit-card__footer',
        'kit-tabs__body', 'kit-breadcrumb', 'kit-breadcrumb__list', 'kit-show-euro', 'kit-show-date',
    ];

    private function paket(string $pfad): string
    {
        return dirname(__DIR__, 3).'/'.$pfad;
    }

    private function lesen(string $pfad): string
    {
        $datei = $this->paket($pfad);
        $this->assertFileExists($datei);

        return (string) file_get_contents($datei);
    }

    /** @return list<string> Dateinamen (ohne Pfad) der Templates eines Themes, ohne Unterordner */
    private function templates(string $theme): array
    {
        $namen = array_map('basename', glob($this->paket("src/Views/kit/{$theme}/*.blade.php")) ?: []);
        sort($namen);

        return $namen;
    }

    /**
     * Klassennamen aus festen class-Werten (class="…", 'class' => '…', ->class([...]) mit Zeichenketten);
     * Blade-Ausdrücke {{ }} werden entfernt.
     *
     * @return list<string>
     */
    private function klassen(string $quelltext): array
    {
        $quelltext = preg_replace('/\{\{--.*?--\}\}/s', ' ', $quelltext) ?? '';
        $werte = [];
        if (preg_match_all('/\bclass="([^"]*)"/', $quelltext, $m)) {
            // Blade-Ausdrücke im festen Wert (class="kit-card {{ $x }}") sind keine Klassennamen
            $werte = array_merge($werte, array_map(static fn (string $w): string => preg_replace('/\{\{.*?\}\}/s', ' ', $w) ?? '', $m[1]));
        }
        if (preg_match_all('/[\'"]class[\'"]\s*=>\s*\'([^\']*)\'/', $quelltext, $m)) {
            $werte = array_merge($werte, $m[1]);
        }
        if (preg_match_all('/->class\(\[(.*?)\]\)/s', $quelltext, $m)) {
            foreach ($m[1] as $liste) {
                // je Element: 'klasse' oder 'klasse' => bedingung; Variablen und Ausdrücke sind keine Klassennamen
                foreach (explode(',', $liste) as $element) {
                    if (preg_match('/^\s*\'([^\']*)\'\s*(=>.*)?$/s', $element, $teil)) {
                        $werte[] = $teil[1];
                    }
                }
            }
        }

        $klassen = [];
        foreach ($werte as $wert) {
            foreach (preg_split('/\s+/', trim($wert), -1, PREG_SPLIT_NO_EMPTY) ?: [] as $klasse) {
                $klassen[] = $klasse;
            }
        }

        return $klassen;
    }

    /** @return list<string> */
    private function unerlaubteKlassen(string $quelltext): array
    {
        return array_values(array_filter(
            $this->klassen($quelltext),
            static fn (string $k): bool => ! str_starts_with($k, 'kit-') && ! in_array($k, ['is-invalid', 'is-active', 'is-open', 'disabled'], true),
        ));
    }

    /** @return array<string, array{string}> */
    public static function kernTemplates(): array
    {
        $namen = array_map('basename', glob(dirname(__DIR__, 3).'/src/Views/kit/kern/*.blade.php') ?: []);

        return array_combine($namen, array_map(static fn (string $n): array => [$n], $namen)) ?: [];
    }

    // AK14 (Gegenprobe des Prüfers)
    public function test_pruefer_findet_fremde_klassen_und_laesst_kit_klassen_und_zustaende_zu(): void
    {
        $this->assertSame(['btn', 'fa-plus'], $this->unerlaubteKlassen('<a class="kit-button btn is-active" data-x="y">'."<i {{ \$a->merge(['class' => 'fa-plus']) }}></i>"));
        $this->assertSame(['mb-3'], $this->unerlaubteKlassen("<div {{ \$attributes->class(['kit-card', 'is-invalid' => \$x, 'mb-3']) }}>"));
        $this->assertSame([], $this->unerlaubteKlassen("{{-- class=\"btn\" --}}<div {{ \$attributes->merge(['class' => 'kit-card']) }}>"));
        $this->assertSame([], $this->unerlaubteKlassen('<div class="kit-card {{ $classcard }}" class="disabled is-open">'));
    }

    // AK14
    public function test_kern_templates_haben_nur_kit_klassen_und_keine_icon_schrift(): void
    {
        $this->assertNotEmpty($this->templates('kern'), 'src/Views/kit/kern/ fehlt oder ist leer');

        foreach (glob($this->paket('src/Views/kit/kern/{*,*/*}.blade.php'), GLOB_BRACE) ?: [] as $datei) {
            $quelltext = (string) file_get_contents($datei);
            $this->assertSame([], $this->unerlaubteKlassen($quelltext), basename($datei));
            $this->assertDoesNotMatchRegularExpression('/\b(fa-|fas |bi-)/', $quelltext, basename($datei).': Icon-Schrift');
        }
    }

    // AK1 (Vertrag: gleiche 34 Templates wie bootstrap5)
    public function test_kern_hat_zu_jedem_bootstrap5_template_ein_gleichnamiges(): void
    {
        $bootstrap = $this->templates('bootstrap5');
        $this->assertCount(34, $bootstrap, 'Gegenprobe: bootstrap5 hat 34 Templates');

        $this->assertSame($bootstrap, $this->templates('kern'));
    }

    // AK15
    #[DataProvider('themeDateien')]
    public function test_themes_haben_regeln_fuer_neue_bausteine_und_alle_button_varianten(string $datei): void
    {
        $css = $this->lesen($datei);

        foreach (self::REGELN as $klasse) {
            $this->assertMatchesRegularExpression('/\.'.preg_quote($klasse, '/').'(?![\w-])/', $css, "{$datei}: keine Regel für {$klasse}");
        }
        foreach (self::VARIANTEN as $variante) {
            $this->assertMatchesRegularExpression('/\.kit-button\[data-variant=["\']?'.$variante.'["\']?\]/', $css, "{$datei}: keine Regel für data-variant {$variante}");
        }
    }

    /** @return array<string, array{string}> */
    public static function themeDateien(): array
    {
        return ['bootstrap5.scss' => ['vue3/themes/bootstrap5.scss'], 'tailwind.css' => ['vue3/themes/tailwind.css']];
    }

    // AK15
    public function test_bootstrap5_theme_nutzt_nur_sass_variablen_aus_bootstrap_5_2(): void
    {
        $bootstrap = dirname(__DIR__, 5).'/node_modules/bootstrap/scss/_variables.scss';
        if (! is_file($bootstrap)) {
            $this->markTestSkipped('node_modules/bootstrap fehlt im Host');
        }
        $scss = $this->lesen('vue3/themes/bootstrap5.scss');
        $this->assertStringContainsString('.kit-button[data-variant', $scss, 'Regeln für kit-button fehlen noch');
        preg_match_all('/^\$([\w-]+)\s*:/m', (string) file_get_contents($bootstrap), $definiert);
        preg_match_all('/^\$([\w-]+)\s*:/m', $scss, $eigene);
        preg_match_all('/\$([a-z][\w-]*)/i', preg_replace('~//.*$~m', '', $scss) ?? '', $benutzt);

        $unbekannt = array_values(array_diff(array_unique($benutzt[1]), $definiert[1], $eigene[1]));

        $this->assertNotEmpty($benutzt[1], 'Gegenprobe: die Datei nutzt Variablen');
        $this->assertSame([], $unbekannt, 'Variablen, die Bootstrap 5.2 nicht kennt: '.implode(', ', $unbekannt));
    }

    // AK16
    public function test_readme_nennt_kern_mit_css_dateien_und_friert_die_alten_themes_ein(): void
    {
        $readme = $this->lesen('README.md');
        $this->assertMatchesRegularExpression('/^###\s+Theme-Schalter\s*$/m', $readme);
        $teile = preg_split('/^###\s+Theme-Schalter\s*$/m', $readme);
        $abschnitt = preg_split('/^#{1,3}\s/m', (string) ($teile[1] ?? ''))[0];

        $this->assertMatchesRegularExpression('/`kern`/', $abschnitt);
        $this->assertStringContainsString('vue3/kit.css', $abschnitt);
        $this->assertMatchesRegularExpression('#vue3/themes/(bootstrap5\.scss|tailwind\.css)#', $abschnitt);
        $this->assertMatchesRegularExpression('/`bootstrap5`[^\n]*eingefroren, wird nicht mehr gepflegt \(Entscheidung 0013\)/', $abschnitt);
        $this->assertMatchesRegularExpression('/`tailwind`[^\n]*eingefroren, wird nicht mehr gepflegt \(Entscheidung 0013\)/', $abschnitt);
    }

    // AK16
    public function test_changelog_nennt_das_theme_kern_unter_unveroeffentlicht(): void
    {
        $changelog = $this->lesen('CHANGELOG.md');
        $teile = preg_split('/^## Unveröffentlicht\s*$/m', $changelog);
        $this->assertCount(2, $teile, 'Abschnitt „Unveröffentlicht“ fehlt');
        $abschnitt = preg_split('/^## /m', $teile[1])[0];

        $this->assertStringContainsString('kern', $abschnitt);
        // Gegenprobe: der Abschnitt ist nicht der ganze Changelog
        $this->assertStringNotContainsString('## 1.7.0', $abschnitt);
    }
}
