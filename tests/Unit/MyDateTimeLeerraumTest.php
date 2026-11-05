<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use ITHilbert\LaravelKit\Helpers\HForm;
use ITHilbert\LaravelKit\Helpers\MyDateTime;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Dashboard #479, #361, Klarstellung K4: Leerraum und Vorzeichen in Teilen, float-Zeitstempel,
 * Punkt ohne Ziffern; ausdrücklich nicht mehr erkannt: Nachkommastellen im Teil, bool.
 */
final class MyDateTimeLeerraumTest extends TestCase
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
     * Angabe, Schreibweise ohne Leerraum/Vorzeichen als Vergleich, erwarteter Typ, erwarteter Zeitstempel (fest).
     *
     * @return array<string, array{string, string, string, int}>
     */
    public static function mitLeerraumOderVorzeichen(): array
    {
        return [
            'Leerraum nach Punkten' => ['25. 05. 2018', '25.05.2018', 'Date', 1527206400],
            'Leerraum nach zweitem Punkt' => ['25.05. 2018', '25.05.2018', 'Date', 1527206400],
            'Leerraum nach Strichen' => ['2018- 05- 25', '2018-05-25', 'Date', 1527206400],
            'Plus am Tag' => ['+25.05.2018', '25.05.2018', 'Date', 1527206400],
            'Plus am Jahr' => ['25.05.+2018', '25.05.2018', 'Date', 1527206400],
            'Leerraum in der Uhrzeit' => ['15: 25', '15:25', 'Time', 943975500],
            'Plus in der Uhrzeit' => ['15:+25', '15:25', 'Time', 943975500],
        ];
    }

    /**
     * Führt $aufruf aus und liefert [Ergebnis, Meldungen]; jede PHP-Meldung (auch Deprecation) und jede Throwable wird gesammelt.
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

    // AK3 (K4)
    #[DataProvider('mitLeerraumOderVorzeichen')]
    public function test_k4_leerraum_oder_vorzeichen_im_teil_erzeugt_keine_warnung(string $angabe, string $ohne, string $typ, int $zeitstempel): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);
    }

    // AK3 (K4)
    #[DataProvider('mitLeerraumOderVorzeichen')]
    public function test_k4_leerraum_oder_vorzeichen_im_teil_ergibt_typ_und_zeitstempel_wie_ohne(string $angabe, string $ohne, string $typ, int $zeitstempel): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));
        $vergleich = new MyDateTime($ohne);

        // Gegenprobe: Vergleichswert ist erkannt, Objekt wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertSame($typ, $vergleich->getType());
        $this->assertEquals($zeitstempel, $vergleich->getTimestamp());

        $this->assertSame($typ, $objekt->getType());
        $this->assertEquals($zeitstempel, $objekt->getTimestamp());
        $this->assertSame($vergleich->getTimestamp(), $objekt->getTimestamp());
    }

    // AK3 (K4)
    #[DataProvider('mitLeerraumOderVorzeichen')]
    public function test_k4_leerraum_oder_vorzeichen_im_teil_ergibt_ausgaben_wie_ohne(string $angabe, string $ohne, string $typ, int $zeitstempel): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));
        $vergleich = new MyDateTime($ohne);

        // Gegenprobe: Vergleichswert liefert echte Ausgaben, Objekt wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertNotSame('', $vergleich->getTime());
        $this->assertInstanceOf(MyDateTime::class, $objekt);

        $this->assertSame($vergleich->getDateDE(), $objekt->getDateDE());
        $this->assertSame($vergleich->getDateISO(), $objekt->getDateISO());
        $this->assertSame($vergleich->getDateDB(), $objekt->getDateDB());
        $this->assertSame($vergleich->getTime(), $objekt->getTime());
        $this->assertSame((string) $vergleich, (string) $objekt);
    }

    // AK3 (K4)
    public function test_k4_form_date_mit_leerraum_im_teil_zeigt_das_deutsche_datum(): void
    {
        [$html, $meldungen] = $this->ueberwacht(fn () => (string) HForm::date('d', '25. 05. 2018'));

        $this->assertSame([], $meldungen);
        $this->assertStringContainsString('value="25.05.2018"', $html);
    }

    // AK3 (K4)
    public function test_k4_punkt_ohne_ziffern_am_ende_erzeugt_keine_warnung_und_keine_deprecation(): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime('2018-05-25 15:25:06.'));

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);
    }

    // AK3 (K4)
    public function test_k4_punkt_ohne_ziffern_am_ende_ergibt_typ_datetime_und_zeitstempel_wie_ohne(): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime('2018-05-25 15:25:06.'));
        $vergleich = new MyDateTime('2018-05-25 15:25:06');

        // Gegenprobe: Vergleichswert ist erkannt, Objekt wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertSame('DateTime', $vergleich->getType());
        $this->assertEquals(1527261906, $vergleich->getTimestamp());

        $this->assertSame('DateTime', $objekt->getType());
        $this->assertEquals(1527261906, $objekt->getTimestamp());
        $this->assertSame($vergleich->getTimestamp(), $objekt->getTimestamp());
    }

    // AK3 (K4)
    public function test_k4_punkt_ohne_ziffern_am_ende_ergibt_ausgaben_wie_ohne(): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime('2018-05-25 15:25:06.'));
        $vergleich = new MyDateTime('2018-05-25 15:25:06');

        $this->assertSame([], $meldungen);
        $this->assertSame('2018-05-25 15:25:06', $vergleich->getDateTimeISO());
        $this->assertInstanceOf(MyDateTime::class, $objekt);

        $this->assertSame('2018-05-25 15:25:06', $objekt->getDateTimeISO());
        $this->assertSame($vergleich->getDateDE(), $objekt->getDateDE());
        $this->assertSame($vergleich->getDateDB(), $objekt->getDateDB());
        $this->assertSame($vergleich->getTime(), $objekt->getTime());
        $this->assertSame((string) $vergleich, (string) $objekt);
    }

    // AK3 (K4)
    public function test_k4_form_date_mit_punkt_ohne_ziffern_am_ende_zeigt_das_deutsche_datum(): void
    {
        $this->assertStringContainsString('value="25.05.2018"', (string) HForm::date('d', '2018-05-25 15:25:06.'));
    }

    // AK3 (K4)
    public function test_k4_zeitstempel_als_float_erzeugt_keine_warnung_und_bleibt_datetime(): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime(1537451550.0));

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);
        $this->assertSame('DateTime', $objekt->getType());
    }

    // AK3 (K4)
    public function test_k4_zeitstempel_als_float_liefert_get_timestamp_als_float(): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime(1537451550.0));

        // Gegenprobe: das Objekt wurde erzeugt und ist ein DateTime; int bleibt int
        $this->assertSame([], $meldungen);
        $this->assertSame('DateTime', $objekt->getType());
        $this->assertSame(1537451550, (new MyDateTime(1537451550))->getTimestamp());

        $this->assertSame(1537451550.0, $objekt->getTimestamp());
    }

    // AK3 (K4)
    public function test_k4_teil_mit_nachkommastellen_wird_nicht_mehr_erkannt(): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime('2018-05-25 15:25.5'));

        // Gegenprobe: dieselbe Angabe ohne Nachkomma ist erkannt
        $erkannt = new MyDateTime('2018-05-25 15:25');
        $this->assertSame('DateTime', $erkannt->getType());

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);
        $this->assertSame('Nothing', $objekt->getType());
        $this->assertEquals(0, $objekt->getTimestamp());
    }

    /**
     * @return array<string, array{bool}>
     */
    public static function bools(): array
    {
        return ['true' => [true], 'false' => [false]];
    }

    // AK3 (K4)
    #[DataProvider('bools')]
    public function test_k4_bool_wird_nicht_mehr_erkannt(bool $wert): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($wert));

        // Gegenprobe: ein Zeitstempel als int wird weiter erkannt
        $erkannt = new MyDateTime(1537451550);
        $this->assertSame('DateTime', $erkannt->getType());

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);
        $this->assertSame('Nothing', $objekt->getType());
        $this->assertEquals(0, $objekt->getTimestamp());
    }
}
