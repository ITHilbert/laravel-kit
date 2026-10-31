<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use Illuminate\Support\Facades\Blade;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

/**
 * Theme-Schalter der Kit-Components (laravelkit.theme).
 */
final class ThemeTest extends TestCase
{
    /** Ein Aufruf je Familie von Kit-Components, genügt für „jede Kit-Component“. */
    private const AUFRUFE = [
        'card-main' => '<x-kit::card-main>x</x-kit::card-main>',
        'card-main-header' => '<x-kit::card-main-header>x</x-kit::card-main-header>',
        'card-body' => '<x-kit::card-body>x</x-kit::card-body>',
        'card-footer' => '<x-kit::card-footer>x</x-kit::card-footer>',
        'button-create' => '<x-kit::button-create route="/a">x</x-kit::button-create>',
        'button-delete' => '<x-kit::button-delete>x</x-kit::button-delete>',
        'button-submit' => '<x-kit::button-submit>x</x-kit::button-submit>',
        'input-text' => '<x-kit::input-text name="n" />',
        'input-hidden' => '<x-kit::input-hidden name="n" value="1" />',
        'text-area' => '<x-kit::text-area name="n" />',
        'checkbox' => '<x-kit::checkbox name="n">x</x-kit::checkbox>',
        'tabs-header' => '<x-kit::tabs-header>x</x-kit::tabs-header>',
        'tab-header' => '<x-kit::tab-header name="t">x</x-kit::tab-header>',
        'tabs-body' => '<x-kit::tabs-body>x</x-kit::tabs-body>',
        'tab-body' => '<x-kit::tab-body name="t">x</x-kit::tab-body>',
        'breadcrumb' => '<x-kit::breadcrumb>x</x-kit::breadcrumb>',
        'show-euro' => '<x-kit::show-euro value="1" />',
        'show-date' => '<x-kit::show-date value="2026-10-31" />',
    ];

    /** @return array<string, array{string}> */
    public static function aufrufe(): array
    {
        $daten = [];
        foreach (self::AUFRUFE as $name => $_) {
            $daten[$name] = [$name];
        }

        return $daten;
    }

    private function rendern(string $theme, string $blade): string
    {
        config(['laravelkit.theme' => $theme]);

        return Blade::render($blade);
    }

    private function fehlerBeimRendern(string $blade): Throwable
    {
        try {
            Blade::render($blade);
        } catch (Throwable $e) {
            return $e;
        }

        $this->fail('Es wurde keine Exception geworfen.');
    }

    // AK1
    public function test_theme_default_ist_tailwind_wenn_env_nicht_gesetzt(): void
    {
        putenv('LARAVELKIT_THEME');
        unset($_ENV['LARAVELKIT_THEME'], $_SERVER['LARAVELKIT_THEME']);

        $konfig = require dirname(__DIR__, 3).'/src/Config/laravelkit.php';

        $this->assertSame('tailwind', $konfig['theme'] ?? null);
    }

    // AK1
    public function test_theme_kommt_aus_env_laravelkit_theme(): void
    {
        putenv('LARAVELKIT_THEME=bootstrap5');
        try {
            $konfig = require dirname(__DIR__, 3).'/src/Config/laravelkit.php';
        } finally {
            putenv('LARAVELKIT_THEME');
        }

        $this->assertSame('bootstrap5', $konfig['theme'] ?? null);
    }

    // AK1
    public function test_theme_in_der_laufenden_app_ist_per_default_tailwind(): void
    {
        $this->assertSame('tailwind', config('laravelkit.theme'));
    }

    // AK1
    #[DataProvider('aufrufe')]
    public function test_theme_bestimmt_das_markup_jeder_kit_component(string $name): void
    {
        $tailwind = $this->rendern('tailwind', self::AUFRUFE[$name]);
        $bootstrap = $this->rendern('bootstrap5', self::AUFRUFE[$name]);

        $this->assertNotSame('', trim($tailwind));
        $this->assertNotSame('', trim($bootstrap));
        // Diese Vue-Komponenten haben in beiden Themes dasselbe Template, alle anderen unterscheiden sich
        $gleich = ['input-hidden', 'card-body', 'card-footer', 'button-submit', 'breadcrumb', 'show-euro', 'show-date'];
        if (! in_array($name, $gleich, true)) {
            $this->assertNotSame($tailwind, $bootstrap, "{$name}: beide Themes erzeugen dasselbe Markup");
        }
    }

    // AK1
    public function test_wechsel_des_themes_wirkt_beim_naechsten_rendern(): void
    {
        $blade = '<x-kit::card-main>x</x-kit::card-main>';

        $erst = $this->rendern('bootstrap5', $blade);
        $dann = $this->rendern('tailwind', $blade);
        $wieder = $this->rendern('bootstrap5', $blade);

        $this->assertNotSame($erst, $dann);
        $this->assertSame($erst, $wieder);
    }

    // AK1
    public function test_unbekannter_wert_wirft_exception_mit_wert_und_erlaubten_werten(): void
    {
        // Gegenprobe: gültige Werte werfen nicht
        $this->assertNotSame('', trim($this->rendern('tailwind', '<x-kit::card-main>x</x-kit::card-main>')));
        $this->assertNotSame('', trim($this->rendern('bootstrap5', '<x-kit::card-main>x</x-kit::card-main>')));

        config(['laravelkit.theme' => 'neon-pink']);
        $fehler = $this->fehlerBeimRendern('<x-kit::card-main>x</x-kit::card-main>');

        $this->assertStringContainsString('neon-pink', $fehler->getMessage());
        $this->assertStringContainsString('tailwind', $fehler->getMessage());
        $this->assertStringContainsString('bootstrap5', $fehler->getMessage());
    }

    // AK1
    #[DataProvider('aufrufe')]
    public function test_unbekanntes_theme_wirft_bei_jeder_kit_component(string $name): void
    {
        config(['laravelkit.theme' => 'neon-pink']);

        $fehler = $this->fehlerBeimRendern(self::AUFRUFE[$name]);

        $this->assertStringContainsString('neon-pink', $fehler->getMessage());
    }

    // AK1
    public function test_bootstrap4_ist_kein_erlaubter_wert(): void
    {
        config(['laravelkit.theme' => 'bootstrap4']);

        $fehler = $this->fehlerBeimRendern('<x-kit::card-main>x</x-kit::card-main>');

        $this->assertStringContainsString('bootstrap4', $fehler->getMessage());
    }

    // AK1
    public function test_leer_gesetztes_theme_ist_unbekannt_und_faellt_nicht_still_zurueck(): void
    {
        config(['laravelkit.theme' => '']);

        $fehler = $this->fehlerBeimRendern('<x-kit::card-main>x</x-kit::card-main>');

        $this->assertStringContainsString('tailwind', $fehler->getMessage());
        $this->assertStringContainsString('bootstrap5', $fehler->getMessage());
    }

    // AK1
    public function test_fehlendes_theme_in_der_config_faellt_nicht_still_zurueck(): void
    {
        config(['laravelkit.theme' => null]);

        $fehler = $this->fehlerBeimRendern('<x-kit::card-main>x</x-kit::card-main>');

        $this->assertStringContainsString('tailwind', $fehler->getMessage());
        $this->assertStringContainsString('bootstrap5', $fehler->getMessage());
    }
}
