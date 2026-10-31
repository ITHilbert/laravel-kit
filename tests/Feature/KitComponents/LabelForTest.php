<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Facades\Blade;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * kit-label-for (Dashboard #375): `label for` zeigt auf die id des Eingabefeldes.
 */
final class LabelForTest extends TestCase
{
    /** @return array<string, array{string}> */
    public static function themes(): array
    {
        return ['tailwind' => ['tailwind'], 'bootstrap5' => ['bootstrap5']];
    }

    /**
     * Component mit Label und Feld je Theme (Ergebnis der Suche in src/Views/kit/{tailwind,bootstrap5}).
     *
     * @return array<string, array{string, string, string}>
     */
    public static function bausteineMitLabel(): array
    {
        return [
            'checkbox tailwind' => ['tailwind', 'checkbox', "//input[@type='checkbox']"],
            'checkbox bootstrap5' => ['bootstrap5', 'checkbox', "//input[@type='checkbox']"],
            'input-text tailwind' => ['tailwind', 'input-text', "//input[@type='text']"],
            'input-password tailwind' => ['tailwind', 'input-password', "//input[@type='password']"],
        ];
    }

    /** @return array<string, array{string, string, string}> */
    public static function eingabefelderMitLabelAttribut(): array
    {
        return array_filter(self::bausteineMitLabel(), static fn (array $z): bool => $z[1] !== 'checkbox');
    }

    private function markup(string $komponente, string $attribute): string
    {
        return in_array($komponente, ['input-text', 'input-password'], true)
            ? "<x-kit::{$komponente} {$attribute} label=\"Beschriftung\" />"
            : "<x-kit::{$komponente} {$attribute}>Beschriftung</x-kit::{$komponente}>";
    }

    private function dom(string $theme, string $markup): DOMXPath
    {
        config(['laravelkit.theme' => $theme]);
        $html = Blade::render($markup);

        $dom = new DOMDocument;
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8"><body>'.$html.'</body>');
        libxml_clear_errors();

        return new DOMXPath($dom);
    }

    private function element(DOMXPath $xp, string $query): ?DOMElement
    {
        $treffer = $xp->query($query);

        return $treffer !== false && $treffer->length > 0 ? $treffer->item(0) : null; // @phpstan-ignore return.type
    }

    // AK1
    #[DataProvider('themes')]
    public function test_checkbox_label_zeigt_auf_uebergebene_id(string $theme): void
    {
        $xp = $this->dom($theme, '<x-kit::checkbox name="x[1]" id="x_1">Text</x-kit::checkbox>');

        $label = $this->element($xp, '//label');
        $this->assertNotNull($label);
        $this->assertSame('x_1', $label->getAttribute('for'));
    }

    // AK1
    #[DataProvider('themes')]
    public function test_checkbox_feld_traegt_die_uebergebene_id(string $theme): void
    {
        $xp = $this->dom($theme, '<x-kit::checkbox name="x[1]" id="x_1">Text</x-kit::checkbox>');

        $input = $this->element($xp, "//input[@type='checkbox']");
        $this->assertNotNull($input);
        $this->assertSame('x_1', $input->getAttribute('id'));
        $this->assertSame('x[1]', $input->getAttribute('name'));
    }

    // AK2
    #[DataProvider('themes')]
    public function test_checkbox_ohne_id_feld_hat_id_gleich_name(string $theme): void
    {
        $xp = $this->dom($theme, '<x-kit::checkbox name="feld">Text</x-kit::checkbox>');

        $input = $this->element($xp, "//input[@type='checkbox']");
        $this->assertNotNull($input);
        $this->assertSame('feld', $input->getAttribute('id'));
    }

    // AK2
    #[DataProvider('themes')]
    public function test_checkbox_ohne_id_label_zeigt_auf_name(string $theme): void
    {
        $xp = $this->dom($theme, '<x-kit::checkbox name="feld">Text</x-kit::checkbox>');

        $label = $this->element($xp, '//label');
        $this->assertNotNull($label);
        $this->assertSame('feld', $label->getAttribute('for'));
    }

    // AK2 (Nicht-Ziel: Name mit Klammern bleibt Vorgabe-id)
    #[DataProvider('themes')]
    public function test_checkbox_name_mit_klammern_bleibt_vorgabe_id(string $theme): void
    {
        $xp = $this->dom($theme, '<x-kit::checkbox name="permission[9]">Text</x-kit::checkbox>');

        $input = $this->element($xp, "//input[@type='checkbox']");
        $label = $this->element($xp, '//label');
        $this->assertNotNull($input);
        $this->assertNotNull($label);
        $this->assertSame('permission[9]', $input->getAttribute('id'));
        $this->assertSame('permission[9]', $label->getAttribute('for'));
    }

    // AK3
    #[DataProvider('bausteineMitLabel')]
    public function test_baustein_label_zeigt_auf_uebergebene_id(string $theme, string $komponente, string $feldQuery): void
    {
        $xp = $this->dom($theme, $this->markup($komponente, 'name="a[1]" id="eigene_id"'));

        $feld = $this->element($xp, $feldQuery);
        $label = $this->element($xp, '//label');
        $this->assertNotNull($feld);
        $this->assertNotNull($label);
        $this->assertSame('eigene_id', $feld->getAttribute('id'));
        $this->assertSame('eigene_id', $label->getAttribute('for'));
    }

    // AK3
    #[DataProvider('bausteineMitLabel')]
    public function test_baustein_label_zeigt_ohne_id_auf_bisherige_vorgabe(string $theme, string $komponente, string $feldQuery): void
    {
        $xp = $this->dom($theme, $this->markup($komponente, 'name="vorgabe"'));

        $feld = $this->element($xp, $feldQuery);
        $label = $this->element($xp, '//label');
        $this->assertNotNull($feld);
        $this->assertNotNull($label);
        $this->assertSame('vorgabe', $feld->getAttribute('id'));
        $this->assertSame('vorgabe', $label->getAttribute('for'));
    }

    // AK3
    public function test_alle_bausteine_mit_label_sind_in_der_pruefliste(): void
    {
        $bekannt = array_map(
            static fn (array $zeile): string => $zeile[0].'/'.$zeile[1].'.blade.php',
            array_values(self::bausteineMitLabel())
        );
        sort($bekannt);

        $gefunden = [];
        foreach (['tailwind', 'bootstrap5'] as $theme) {
            foreach (glob(dirname(__DIR__, 3)."/src/Views/kit/{$theme}/*.blade.php") ?: [] as $datei) {
                if (preg_match('/<label\b[^>]*\bfor=/', (string) file_get_contents($datei)) === 1) {
                    $gefunden[] = $theme.'/'.basename($datei);
                }
            }
        }
        sort($gefunden);

        $this->assertNotEmpty($gefunden);
        $this->assertSame($bekannt, $gefunden);
    }

    // Randfall 1
    #[DataProvider('themes')]
    public function test_checkbox_ohne_label_text_wirft_nichts_und_rendert_das_feld(string $theme): void
    {
        $xp = $this->dom($theme, '<x-kit::checkbox name="leer" id="leer_id"></x-kit::checkbox>');

        $input = $this->element($xp, "//input[@type='checkbox']");
        $label = $this->element($xp, '//label');
        $this->assertNotNull($input);
        $this->assertSame('leer_id', $input->getAttribute('id'));
        $this->assertNotNull($label);
        $this->assertSame('', trim($label->textContent));
    }

    // Randfall 1
    #[DataProvider('themes')]
    public function test_checkbox_ohne_label_text_und_ohne_id_wie_bisher(string $theme): void
    {
        $xp = $this->dom($theme, '<x-kit::checkbox name="leer"></x-kit::checkbox>');

        $input = $this->element($xp, "//input[@type='checkbox']");
        $this->assertNotNull($input);
        $this->assertSame('leer', $input->getAttribute('id'));
    }

    // Randfall 1
    #[DataProvider('eingabefelderMitLabelAttribut')]
    public function test_eingabefeld_ohne_label_wirft_nichts(string $theme, string $komponente, string $feldQuery): void
    {
        $xp = $this->dom($theme, "<x-kit::{$komponente} name=\"n\" id=\"eigene_id\" />");

        $feld = $this->element($xp, $feldQuery);
        $this->assertNotNull($feld);
        $this->assertSame('eigene_id', $feld->getAttribute('id'));
        $this->assertNull($this->element($xp, '//label'));
    }
}
