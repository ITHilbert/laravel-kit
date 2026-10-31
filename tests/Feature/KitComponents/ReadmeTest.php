<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * Die README von laravel-kit beschreibt die Kit-Components und den Mount-Helfer.
 */
final class ReadmeTest extends TestCase
{
    private const KOMPONENTEN = [
        'card-main', 'card-main-header', 'card-body', 'card-footer',
        'button-create', 'button-edit', 'button-show', 'button-delete',
        'button-save', 'button-back', 'button-submit', 'button-cancel',
        'input-text', 'input-email', 'input-password', 'input-date', 'input-euro',
        'input-number', 'input-hidden', 'input-file-img', 'text-area', 'checkbox',
        'tabs-header', 'tab-header', 'tabs-body', 'tab-body',
        'breadcrumb', 'show-euro', 'show-date',
    ];

    private function readme(): string
    {
        $pfad = dirname(__DIR__, 3).'/README.md';
        $this->assertFileExists($pfad);

        return (string) file_get_contents($pfad);
    }

    /** Text eines Abschnitts: ab der Überschrift bis zur nächsten gleich- oder höherrangigen. */
    private function abschnitt(string $readme, string $ueberschriftMuster): string
    {
        $zeilen = explode("\n", $readme);
        $text = [];
        $ebene = null;
        foreach ($zeilen as $zeile) {
            if (preg_match('/^(#{1,6})\s+(.*)$/', $zeile, $m)) {
                if ($ebene !== null && strlen($m[1]) <= $ebene) {
                    break;
                }
                if ($ebene === null && preg_match($ueberschriftMuster, $m[2])) {
                    $ebene = strlen($m[1]);
                }
            }
            if ($ebene !== null) {
                $text[] = $zeile;
            }
        }

        return implode("\n", $text);
    }

    // AK9
    public function test_readme_hat_abschnitt_zu_den_kit_components(): void
    {
        $abschnitt = $this->abschnitt($this->readme(), '/Kit-Components?|Blade-Components?/i');

        $this->assertNotSame('', $abschnitt, 'Kein Abschnitt „Kit-Components“ in der README');
        $this->assertStringContainsString('x-kit::', $abschnitt);
    }

    // AK9
    public function test_readme_listet_jede_kit_component(): void
    {
        $abschnitt = $this->abschnitt($this->readme(), '/Kit-Components?|Blade-Components?/i');
        $this->assertNotSame('', $abschnitt, 'Kein Abschnitt „Kit-Components“ in der README');

        $fehlen = array_values(array_filter(
            self::KOMPONENTEN,
            fn (string $name) => ! preg_match('/(?<![\w-])'.preg_quote($name, '/').'(?![\w-])/', $abschnitt),
        ));

        $this->assertSame([], $fehlen, 'In der Liste der Kit-Components fehlen: '.implode(', ', $fehlen));
    }

    // AK9
    public function test_readme_beschreibt_den_theme_schalter(): void
    {
        $readme = $this->readme();

        $this->assertStringContainsString('laravelkit.theme', $readme);
        $this->assertStringContainsString('LARAVELKIT_THEME', $readme);
        $this->assertStringContainsString('tailwind', $readme);
        $this->assertStringContainsString('bootstrap5', $readme);
    }

    // AK9
    public function test_readme_enthaelt_ein_beispiel_mit_kit_component(): void
    {
        $this->assertSame(
            1,
            preg_match('/```[a-z]*\n(?:(?!```).)*<x-kit::[a-z-]+(?:(?!```).)*```/s', $this->readme()),
            'Kein Code-Beispiel mit <x-kit::…> in der README',
        );
    }

    // AK9
    public function test_readme_beschreibt_den_mount_helfer_mit_insel_verzeichnis(): void
    {
        $readme = $this->readme();

        $this->assertStringContainsString('mountInseln', $readme);
        $this->assertStringContainsString('data-vue', $readme);
        $this->assertStringContainsString('data-props', $readme);
        $this->assertMatchesRegularExpression('/Insel-?Verzeichnis/iu', $readme);
        $this->assertSame(
            1,
            preg_match('/```[a-z]*\n(?:(?!```).)*mountInseln\((?:(?!```).)*```/s', $readme),
            'Kein Code-Beispiel mit mountInseln(verzeichnis) in der README',
        );
    }

    // AK9
    public function test_readme_kennzeichnet_den_whole_dom_mount_als_veraltet(): void
    {
        $zeilen = explode("\n", $this->readme());

        $stellen = [];
        foreach ($zeilen as $i => $zeile) {
            if (preg_match('/vueapp_|Whole-DOM/i', $zeile)) {
                $stellen[] = $i;
            }
        }
        $this->assertNotSame([], $stellen, 'Whole-DOM-Mount (vueapp_*.js) kommt in der README nicht vor');

        $gekennzeichnet = false;
        foreach ($stellen as $i) {
            $umfeld = implode("\n", array_slice($zeilen, max(0, $i - 2), 5));
            if (preg_match('/veraltet|deprecated/iu', $umfeld)) {
                $gekennzeichnet = true;
            }
        }

        $this->assertTrue($gekennzeichnet, 'Der Whole-DOM-Mount ist in der README nicht als veraltet gekennzeichnet');
    }
}
