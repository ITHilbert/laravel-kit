<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use ITHilbert\LaravelKit\Helpers\HForm;
use ITHilbert\LaravelKit\Helpers\MyDateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Dashboard #479, #361 (Review 3): führendes Minus mit leerem ersten Teil.
 */
final class MyDateTimeFuehrendesMinusTest extends TestCase
{
    private string $zeitzoneVorher = '';

    protected function setUp(): void
    {
        parent::setUp();
        $this->zeitzoneVorher = date_default_timezone_get();
        date_default_timezone_set('UTC');
    }

    protected function tearDown(): void
    {
        date_default_timezone_set($this->zeitzoneVorher);
        parent::tearDown();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function mitFuehrendemMinus(): array
    {
        return [
            'Minus Monat-Tag' => ['-03-15'],
            'Minus Jahr-Monat' => ['-2026-03'],
        ];
    }

    /**
     * Führt $aufruf aus und liefert [Ergebnis, Meldungen]; jede PHP-Meldung und jede Throwable wird gesammelt.
     * Gegenprobe: der Handler erkennt eine echte Warnung.
     *
     * @return array{0: mixed, 1: list<string>}
     */
    private function ueberwacht(callable $aufruf): array
    {
        $meldungen = [];
        set_error_handler(function (int $nr, string $text) use (&$meldungen): bool {
            $meldungen[] = $text;

            return true;
        });

        try {
            $leer = [];
            $unbenutzt = $leer['gibt-es-nicht'];
            $this->assertCount(1, $meldungen, 'Der Handler muss eine Warnung erkennen.');
            $meldungen = [];

            try {
                $ergebnis = $aufruf();
            } catch (\Throwable $e) {
                $meldungen[] = get_class($e).': '.$e->getMessage();
                $ergebnis = null;
            }
        } finally {
            restore_error_handler();
        }

        return [$ergebnis, $meldungen];
    }

    // AK1
    #[DataProvider('mitFuehrendemMinus')]
    public function test_fuehrendes_minus_erzeugt_keine_warnung_und_keine_exception(string $angabe): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);
    }

    // AK1
    #[DataProvider('mitFuehrendemMinus')]
    public function test_fuehrendes_minus_ergibt_leeres_objekt(string $angabe): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        // Gegenprobe: das Objekt wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);

        $this->assertSame('Nothing', $objekt->getType());
        $this->assertEquals(0, $objekt->getTimestamp());
    }

    // AK1
    #[DataProvider('mitFuehrendemMinus')]
    public function test_get_date_de_wirft_bei_fuehrendem_minus_nicht(string $angabe): void
    {
        [$text, $meldungen] = $this->ueberwacht(fn () => (new MyDateTime($angabe))->getDateDE());

        $this->assertSame([], $meldungen);
        $this->assertIsString($text);
    }

    // AK2
    #[DataProvider('mitFuehrendemMinus')]
    public function test_form_date_mit_fuehrendem_minus_erzeugt_keine_exception(string $angabe): void
    {
        [$html, $meldungen] = $this->ueberwacht(fn () => (string) HForm::date('d', $angabe));

        $this->assertSame([], $meldungen);
        $this->assertStringContainsString('name="d"', (string) $html);
    }

    // AK2
    #[DataProvider('mitFuehrendemMinus')]
    public function test_form_date_zeigt_angabe_mit_fuehrendem_minus_unveraendert(string $angabe): void
    {
        [$html, $meldungen] = $this->ueberwacht(fn () => (string) HForm::date('d', $angabe));

        // Gegenprobe: das Feld wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertStringContainsString('name="d"', (string) $html);

        $this->assertStringContainsString('value="'.e($angabe).'"', $html);
    }

    /**
     * Erwartet laut Klasse vor der Änderung (UTC): Angabe, getTimestamp() samt Typ, Typ des Objekts.
     * Ein Zeitstempel als String liefert `getTimestamp()` bisher als String.
     *
     * @return array<string, array{string|int, string|int, string}>
     */
    public static function zeitstempel(): array
    {
        return [
            'negativ als String' => ['-86400', '-86400', 'DateTime'],
            'negativ als int' => [-86400, -86400, 'DateTime'],
            'Leerzeichen am Ende' => ['1537451550 ', '1537451550 ', 'DateTime'],
            'Null als String' => ['0', '0', 'DateTime'],
        ];
    }

    // AK3
    #[DataProvider('zeitstempel')]
    public function test_zeitstempel_bleiben_wie_vorher_zeitstempel(string|int $angabe, string|int $zeitstempel, string $typ): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        // Gegenprobe: das Objekt wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);

        $this->assertSame($typ, $objekt->getType());
        $this->assertEquals((int) $zeitstempel, $objekt->getTimestamp());
    }

    // AK3
    #[DataProvider('zeitstempel')]
    public function test_zeitstempel_liefern_get_timestamp_wie_vorher_mit_gleichem_typ(string|int $angabe, string|int $zeitstempel, string ...$rest): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);
        $this->assertSame($zeitstempel, $objekt->getTimestamp());
    }
}
