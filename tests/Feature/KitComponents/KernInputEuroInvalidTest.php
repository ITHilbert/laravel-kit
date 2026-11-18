<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * Bugfix #1617: Theme `kern`, input-euro verschluckt den Prop `invalid` wie bootstrap5
 * (Spec blade-kern, Vertrag: Props und Verhalten gleich wie im Theme bootstrap5).
 */
final class KernInputEuroInvalidTest extends TestCase
{
    use KernHilfe;

    private const EINGABEFELD = "//div".'[contains(concat(" ", normalize-space(@class), " "), " kit-input-euro ")]'
        .'//input[contains(concat(" ", normalize-space(@class), " "), " kit-input-euro__input ")][@name="p"]';

    // AK Vertrag blade-kern, #1617
    public function test_input_euro_verschluckt_prop_invalid_und_aendert_das_markup_nicht(): void
    {
        $ohne = $this->kernRendern('<x-kit::input-euro name="p" />');
        // Gegenprobe: Aufbau greift, Feld rendert normal
        $this->sieHat($ohne, self::EINGABEFELD);
        $this->sieHatNicht($ohne, self::EINGABEFELD.'[@invalid]');

        $mit = $this->kernRendern('<x-kit::input-euro name="p" invalid="true" />');
        $this->sieHat($mit, self::EINGABEFELD);
        $this->sieHatNicht($mit, self::EINGABEFELD.'[@invalid]');
        $this->assertSame($ohne, $mit, 'Prop invalid darf das Markup nicht ändern');
    }
}
