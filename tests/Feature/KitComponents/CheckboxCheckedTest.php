<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Blade;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * AK10 (userauth-admin-views): `<x-kit::checkbox>` nimmt `checked` in beiden Themes an.
 * Die Regel ohne `checked` (tailwind: value 1/true) steht in BausteineTest (Randfall 8).
 */
final class CheckboxCheckedTest extends TestCase
{
    /** @return array<string, array{string}> */
    public static function themes(): array
    {
        return ['tailwind' => ['tailwind'], 'bootstrap5' => ['bootstrap5']];
    }

    private function kaestchen(string $theme, string $markup): ?\DOMElement
    {
        config(['laravelkit.theme' => $theme]);
        $html = Blade::render($markup);

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><body>'.$html.'</body>');
        libxml_clear_errors();
        $treffer = (new DOMXPath($dom))->query("//input[@type='checkbox'][@name='p']");

        return $treffer !== false && $treffer->length > 0 ? $treffer->item(0) : null; // @phpstan-ignore return.type
    }

    // AK10
    #[DataProvider('themes')]
    public function test_checked_als_attribut_hakt_an(string $theme): void
    {
        $el = $this->kaestchen($theme, '<x-kit::checkbox name="p" value="5" checked>Text</x-kit::checkbox>');

        $this->assertNotNull($el);
        $this->assertTrue($el->hasAttribute('checked'));
        $this->assertSame('5', $el->getAttribute('value'));
    }

    // AK10
    #[DataProvider('themes')]
    public function test_checked_true_hakt_an_auch_bei_value_ungleich_eins(string $theme): void
    {
        $el = $this->kaestchen($theme, '<x-kit::checkbox name="p" value="7" :checked="true">Text</x-kit::checkbox>');

        $this->assertNotNull($el);
        $this->assertTrue($el->hasAttribute('checked'));
    }

    // AK10
    #[DataProvider('themes')]
    public function test_checked_false_hakt_nicht_an_auch_bei_value_eins(string $theme): void
    {
        $el = $this->kaestchen($theme, '<x-kit::checkbox name="p" value="1" :checked="false">Text</x-kit::checkbox>');

        $this->assertNotNull($el);
        $this->assertFalse($el->hasAttribute('checked'));
    }

    // AK10
    #[DataProvider('themes')]
    public function test_checked_aus_einer_variablen_wird_beachtet(string $theme): void
    {
        $markup = '<x-kit::checkbox name="p" value="3" :checked="$an">Text</x-kit::checkbox>';
        config(['laravelkit.theme' => $theme]);

        $an = Blade::render($markup, ['an' => true]);
        $aus = Blade::render($markup, ['an' => false]);

        $this->assertMatchesRegularExpression('/<input\b[^>]*\bchecked\b/', $an);
        $this->assertDoesNotMatchRegularExpression('/<input\b[^>]*\bchecked\b/', $aus);
    }

    // AK10
    #[DataProvider('themes')]
    public function test_checked_steht_genau_einmal_am_input(string $theme): void
    {
        config(['laravelkit.theme' => $theme]);
        $html = Blade::render('<x-kit::checkbox name="p" value="1" :checked="true">Text</x-kit::checkbox>');

        $this->assertSame(1, preg_match_all('/\bchecked\b/', $html));
    }

    // AK10
    public function test_tailwind_ohne_checked_gilt_value_eins_oder_true(): void
    {
        $this->assertTrue($this->kaestchen('tailwind', '<x-kit::checkbox name="p" value="1">T</x-kit::checkbox>')?->hasAttribute('checked'));
        $this->assertTrue($this->kaestchen('tailwind', '<x-kit::checkbox name="p" :value="true">T</x-kit::checkbox>')?->hasAttribute('checked'));
        $this->assertFalse($this->kaestchen('tailwind', '<x-kit::checkbox name="p" value="0">T</x-kit::checkbox>')?->hasAttribute('checked'));
    }

    // AK10
    public function test_tailwind_checked_false_schlaegt_die_value_regel_aber_checked_true_braucht_sie_nicht(): void
    {
        // Gegenprobe: ohne checked ist value=1 angehakt, mit checked=false nicht
        $this->assertTrue($this->kaestchen('tailwind', '<x-kit::checkbox name="p" value="1">T</x-kit::checkbox>')?->hasAttribute('checked'));
        $this->assertFalse($this->kaestchen('tailwind', '<x-kit::checkbox name="p" value="1" :checked="false">T</x-kit::checkbox>')?->hasAttribute('checked'));
        $this->assertTrue($this->kaestchen('tailwind', '<x-kit::checkbox name="p" value="0" :checked="true">T</x-kit::checkbox>')?->hasAttribute('checked'));
    }
}
