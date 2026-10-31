<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use ITHilbert\LaravelKit\Helpers\HForm;
use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * Regression Dashboard #359: HForm::date() und HForm::int().
 */
final class HFormDateIntTest extends TestCase
{
    /**
     * Führt $aufruf mit eigenem Fehler-Handler aus und liefert [Ergebnis, Warnungen].
     * Gegenprobe: Der Handler erkennt eine echte Warnung.
     *
     * @return array{0: string, 1: list<string>}
     */
    private function ohneWarnung(callable $aufruf): array
    {
        $warnungen = [];
        $vorher = set_error_handler(function (int $nr, string $text) use (&$warnungen): bool {
            $warnungen[] = $text;

            return true;
        }, E_WARNING);

        try {
            // Gegenprobe: der Handler erkennt eine Warnung
            $leer = [];
            $unbenutzt = $leer['gibt-es-nicht'];
            $this->assertCount(1, $warnungen, 'Der Handler muss eine Warnung erkennen.');
            $warnungen = [];

            $html = (string) $aufruf();
        } finally {
            restore_error_handler();
        }

        return [$html, $warnungen];
    }

    /**
     * Entfernt alle Attributwerte samt Anführungszeichen; übrig bleibende `"` stehen außerhalb eines Attributwerts.
     */
    private function ohneAttributwerte(string $html): string
    {
        return (string) preg_replace('/="[^"]*"/', '=', $html);
    }

    // AK1
    public function test_date_liefert_input_mit_value_und_attribut(): void
    {
        $html = (string) HForm::date('lieferdatum', '2026-03-15 10:00:00', '', ['data-x' => '1']);

        $this->assertStringStartsWith('<input', $html);
        $this->assertStringContainsString('value="15.03.2026"', $html);
        $this->assertStringContainsString('data-x="1"', $html);
    }

    // AK1
    public function test_date_hat_kein_anfuehrungszeichen_ausserhalb_eines_attributwerts(): void
    {
        $html = (string) HForm::date('lieferdatum', '2026-03-15 10:00:00', '', ['data-x' => '1']);

        // Gegenprobe: die Attributwerte sind erkannt und entfernt worden
        $this->assertStringNotContainsString('15.03.2026', $this->ohneAttributwerte($html));
        $this->assertStringContainsString('data-x=', $this->ohneAttributwerte($html));

        $this->assertStringNotContainsString('"', $this->ohneAttributwerte($html));
    }

    // AK1
    public function test_date_endet_nicht_mit_einzelnem_anfuehrungszeichen(): void
    {
        $html = (string) HForm::date('lieferdatum', '2026-03-15 10:00:00', '', ['data-x' => '1']);

        $this->assertStringContainsString('data-x="1"', $html);
        $this->assertDoesNotMatchRegularExpression('/"\s*"\s*>$/', $html);
        $this->assertDoesNotMatchRegularExpression('/data-x="1"\s*"\s*>/', $html);
    }

    // Randfall 1
    public function test_date_leer_liefert_leeren_value(): void
    {
        $html = (string) HForm::date('d', '');

        $this->assertStringContainsString('value=""', $html);
    }

    // Randfall 1
    public function test_date_leer_hat_kein_ueberzaehliges_zeichen(): void
    {
        $html = (string) HForm::date('d', '');

        // Gegenprobe: leerer value ist erkannt und entfernt worden
        $this->assertStringContainsString('value=""', $html);
        $this->assertStringNotContainsString('"', $this->ohneAttributwerte($html));
    }

    // AK2
    public function test_int_liefert_value_5_fuer_zahl_5(): void
    {
        [$html] = $this->ohneWarnung(fn () => HForm::int('menge', 5));

        $this->assertStringContainsString('value="5"', $html);
    }

    // AK2
    public function test_int_liefert_value_12_345_fuer_zahl_12345(): void
    {
        [$html] = $this->ohneWarnung(fn () => HForm::int('menge', 12345));

        $this->assertStringContainsString('value="12.345"', $html);
    }

    // AK2
    public function test_int_mit_zahl_loest_keine_warnung_aus(): void
    {
        [$html, $warnungen] = $this->ohneWarnung(fn () => HForm::int('menge', 12345));

        $this->assertStringContainsString('name="menge"', $html);
        $this->assertSame([], $warnungen);
    }

    // AK3
    public function test_int_liefert_value_abc_fuer_text(): void
    {
        [$html] = $this->ohneWarnung(fn () => HForm::int('menge', 'abc'));

        $this->assertStringContainsString('value="abc"', $html);
    }

    // AK3
    public function test_int_mit_text_loest_keine_warnung_aus(): void
    {
        [$html, $warnungen] = $this->ohneWarnung(fn () => HForm::int('menge', 'abc'));

        $this->assertStringContainsString('name="menge"', $html);
        $this->assertSame([], $warnungen);
    }
}
