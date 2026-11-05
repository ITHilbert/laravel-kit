<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use ITHilbert\LaravelKit\Helpers\HForm;
use ITHilbert\LaravelKit\Helpers\MyDateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Dashboard #730, Klarstellung K5: relative Angaben ohne Datumstrenner sind kein Zeitstempel.
 */
final class MyDateTimeZeitstempelTest extends TestCase
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
    public static function relativeAngaben(): array
    {
        return [
            'Zahl mit Einheit' => ['1 day'],
            'Zahl mit Vorzeichen-Zusatz' => ['86400 +1'],
            'Zeitstempel mit Einheit' => ['1537451550 +1 day'],
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

    // K5
    #[DataProvider('relativeAngaben')]
    public function test_k5_relative_angabe_erzeugt_keine_warnung(string $angabe): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);
    }

    // K5
    #[DataProvider('relativeAngaben')]
    public function test_k5_relative_angabe_ergibt_leeres_objekt(string $angabe): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        // Gegenprobe: das Objekt wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);

        $this->assertSame('Nothing', $objekt->getType());
        $this->assertEquals(0, $objekt->getTimestamp());
    }

    // K5
    #[DataProvider('relativeAngaben')]
    public function test_k5_get_date_de_wirft_bei_relativer_angabe_nicht(string $angabe): void
    {
        [$text, $meldungen] = $this->ueberwacht(fn () => (new MyDateTime($angabe))->getDateDE());

        $this->assertSame([], $meldungen);
        $this->assertIsString($text);
    }

    // K5
    #[DataProvider('relativeAngaben')]
    public function test_k5_form_date_erzeugt_bei_relativer_angabe_keine_meldung(string $angabe): void
    {
        [$html, $meldungen] = $this->ueberwacht(fn () => (string) HForm::date('d', $angabe));

        $this->assertSame([], $meldungen);
        $this->assertStringContainsString('name="d"', (string) $html);
    }

    // K5
    #[DataProvider('relativeAngaben')]
    public function test_k5_form_date_zeigt_relative_angabe_escaped(string $angabe): void
    {
        [$html, $meldungen] = $this->ueberwacht(fn () => (string) HForm::date('d', $angabe));

        // Gegenprobe: das Feld wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertStringContainsString('name="d"', (string) $html);

        $this->assertStringContainsString('value="'.e($angabe).'"', $html);
    }

    /**
     * Erwartet wie vorher (UTC): Angabe, getTimestamp() samt PHP-Typ, Typ des Objekts.
     * Ein Zeitstempel als Text liefert `getTimestamp()` bisher als Text.
     *
     * @return array<string, array{string|int|float, string|int|float, string}>
     */
    public static function zeitstempel(): array
    {
        return [
            'Text' => ['1537451550', '1537451550', 'DateTime'],
            'Text mit Leerzeichen am Ende' => ['1537451550 ', '1537451550 ', 'DateTime'],
            'negativ als Text' => ['-86400', '-86400', 'DateTime'],
            'Null als Text' => ['0', '0', 'DateTime'],
            'int' => [1537451550, 1537451550, 'DateTime'],
            'float' => [1537451550.0, 1537451550.0, 'DateTime'],
        ];
    }

    // K5
    #[DataProvider('zeitstempel')]
    public function test_k5_zeitstempel_bleiben_zeitstempel_mit_typ_datetime(string|int|float $angabe, string|int|float $zeitstempel, string $typ): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        // Gegenprobe: das Objekt wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);

        $this->assertSame($typ, $objekt->getType());
    }

    // K5
    #[DataProvider('zeitstempel')]
    public function test_k5_zeitstempel_liefern_get_timestamp_mit_wert_und_php_typ_wie_vorher(string|int|float $angabe, string|int|float $zeitstempel, string ...$rest): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);
        $this->assertSame($zeitstempel, $objekt->getTimestamp());
    }

    // K5
    public function test_k5_now_bleibt_datetime_mit_aktuellem_zeitstempel(): void
    {
        $vorher = time();
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime('now'));
        $nachher = time();

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);

        $this->assertSame('DateTime', $objekt->getType());
        $this->assertGreaterThanOrEqual($vorher, (int) $objekt->getTimestamp());
        $this->assertLessThanOrEqual($nachher, (int) $objekt->getTimestamp());
    }
}
