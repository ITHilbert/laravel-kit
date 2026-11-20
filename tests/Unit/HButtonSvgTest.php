<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use Illuminate\Support\HtmlString;
use ITHilbert\LaravelKit\Helpers\HButton;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;

/**
 * HButton gibt Inline-SVG statt Font Awesome aus und vergibt bei leerem Text einen zugänglichen Namen.
 */
final class HButtonSvgTest extends TestCase
{
    /** @return array<string, array{0: callable}> */
    public static function alleButtons(): array
    {
        return [
            'edit' => [fn () => HButton::edit('/p')],
            'show' => [fn () => HButton::show('/p')],
            'delete' => [fn () => HButton::delete(1)],
            'create' => [fn () => HButton::create('/p')],
            'save' => [fn () => HButton::save()],
            'back' => [fn () => HButton::back('/p')],
            'project' => [fn () => HButton::project('/p')],
        ];
    }

    /** @return array<string, array{0: callable, 1: string}> */
    public static function mitVueDatei(): array
    {
        return [
            'edit' => [fn () => HButton::edit('/p'), 'button-edit.vue'],
            'show' => [fn () => HButton::show('/p'), 'button-show.vue'],
            'delete' => [fn () => HButton::delete(1), 'button-delete.vue'],
        ];
    }

    /** @return array<string, array{0: callable}> */
    public static function ohneText(): array
    {
        return [
            'edit' => [fn (mixed $t) => HButton::edit('/p', $t, 'Tipp "x" <b>')],
            'show' => [fn (mixed $t) => HButton::show('/p', $t, 'Tipp "x" <b>')],
            'delete' => [fn (mixed $t) => HButton::delete(1, $t, 'Tipp "x" <b>')],
            'create' => [fn (mixed $t) => HButton::create('/p', $t, 'Tipp "x" <b>')],
            'save' => [fn (mixed $t) => HButton::save($t, 'Tipp "x" <b>')],
            'back' => [fn (mixed $t) => HButton::back('/p', $t, 'Tipp "x" <b>')],
            'project' => [fn (mixed $t) => HButton::project('/p', $t, 'Tipp "x" <b>')],
        ];
    }

    // AK1
    #[DataProvider('alleButtons')]
    public function test_icon_ist_inline_svg_ohne_font_awesome(callable $aufruf): void
    {
        $html = (string) $aufruf();

        $this->assertMatchesRegularExpression('#<svg\b[^>]*viewBox="0 0 24 24"[^>]*>#', $html);
        $this->assertMatchesRegularExpression('#<svg\b[^>]*stroke="currentColor"[^>]*>#', $html);
        $this->assertMatchesRegularExpression('#<svg\b[^>]*aria-hidden="true"[^>]*>#', $html);
        $this->assertStringNotContainsString('<i', $html);
        $this->assertStringNotContainsString('fa-', $html);
    }

    // AK2
    #[DataProvider('mitVueDatei')]
    public function test_svg_elemente_stimmen_mit_der_vue_komponente_ueberein(callable $aufruf, string $datei): void
    {
        $vue = file_get_contents(dirname(__DIR__, 2).'/vue/tailwind/buttons/'.$datei);
        $this->assertNotFalse($vue);
        $this->assertSame(1, preg_match('#<svg\b[^>]*>(.*?)</svg>#s', $vue, $v), 'Gegenprobe: Vue-Datei enthält ein SVG');
        $this->assertSame(1, preg_match('#<svg\b[^>]*>(.*?)</svg>#s', (string) $aufruf(), $h), 'HButton gibt ein SVG aus');

        $norm = fn (string $s): string => trim((string) preg_replace('#\s*(/?>)\s*#', '$1', (string) preg_replace('#\s+#', ' ', $s)));

        $this->assertNotSame('', $norm($v[1]));
        $this->assertSame($norm($v[1]), $norm($h[1]));
    }

    // AK3
    #[DataProvider('ohneText')]
    public function test_leerer_text_setzt_aria_label_mit_escapetem_tooltip(callable $aufruf): void
    {
        $this->assertStringContainsString(' aria-label="Tipp &quot;x&quot; &lt;b&gt;"', (string) $aufruf(''));
        $this->assertSame(1, substr_count((string) $aufruf(''), 'aria-label='));
    }

    // AK4
    #[DataProvider('ohneText')]
    public function test_nichtleerer_text_hat_kein_aria_label(callable $aufruf): void
    {
        $this->assertStringContainsString('aria-label=', (string) $aufruf(''), 'Gegenprobe: mit leerem Text ist es da');
        // einzelwort: aria-label kommt in den Buttons mit Text nie vor (Gegenprobe davor)
        $this->assertStringNotContainsString('aria-label', (string) $aufruf('Beschriftung'));
    }

    // Randfall 1
    public function test_htmlstring_mit_leerer_zeichenkette_zaehlt_als_leer(): void
    {
        $this->assertStringContainsString('aria-label="Tipp"', (string) HButton::edit('/p', new HtmlString(' '), 'Tipp'));
        // einzelwort: aria-label kommt im Button mit Text nie vor (Gegenprobe davor)
        $this->assertStringNotContainsString('aria-label', (string) HButton::edit('/p', new HtmlString('Text'), 'Tipp'));
    }

    // Randfall 2
    public function test_eigenes_aria_label_erscheint_genau_einmal(): void
    {
        $html = (string) HButton::edit('/p', '', 'Tipp', '', ['aria-label' => 'Eigener Name']);

        $this->assertSame(1, substr_count($html, 'aria-label='));
        $this->assertStringContainsString('aria-label="Eigener Name"', $html);
        $this->assertStringNotContainsString('aria-label="Tipp"', $html);
    }

    // AK6
    #[Group('arch')]
    public function test_quelltext_enthaelt_kein_font_awesome(): void
    {
        $quelle = (string) file_get_contents(dirname(__DIR__, 2).'/src/Helpers/HButton.php');

        $this->assertStringContainsString('class HButton', $quelle, 'Gegenprobe: richtige Datei gelesen');
        $this->assertStringNotContainsString('fa-', $quelle);
    }
}
