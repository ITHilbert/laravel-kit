<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use ITHilbert\LaravelKit\Helpers\HForm;
use ITHilbert\LaravelKit\Helpers\MyDateTime;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Dashboard #479, #361: MyDateTime ohne Warnung bei unvollständigen Angaben, kein globales setlocale.
 */
final class MyDateTimeUnvollstaendigTest extends TestCase
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
    public static function unvollstaendige(): array
    {
        return [
            'Tag.Monat' => ['15.03'],
            'Jahr-Monat' => ['2026-03'],
            'Punkt am Ende' => ['15.03.'],
            'Strich am Ende' => ['2026-03-'],
            'Tag.Monat mit Uhrzeit' => ['15.03 10:00'],
            'Jahr-Monat mit Uhrzeit' => ['2026-03 10:00'],
            'leerer Monat Punkt' => ['15..2026'],
            'leerer Monat Strich' => ['2026--15'],
            'nur Punkte' => ['..'],
            'nur Striche' => ['--'],
            'leerer Tag' => ['.03.2026'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function nichtNumerische(): array
    {
        return [
            'Punkt' => ['aa.bb.cccc'],
            'Strich' => ['aa-bb-cccc'],
            'Punkt mit Uhrzeit' => ['aa.bb.cccc 10:00'],
            'Strich mit Uhrzeit' => ['aa-bb-cccc 10:00:00'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function alleUnvollstaendigen(): array
    {
        return self::unvollstaendige() + self::nichtNumerische();
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
    #[DataProvider('unvollstaendige')]
    public function test_unvollstaendige_angabe_erzeugt_keine_warnung_und_keine_exception(string $angabe): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);
    }

    // AK1
    #[DataProvider('unvollstaendige')]
    public function test_unvollstaendige_angabe_ergibt_leeres_objekt(string $angabe): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        // Gegenprobe: das Objekt wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);

        $this->assertSame('Nothing', $objekt->getType());
        $this->assertEquals(0, $objekt->getTimestamp());
    }

    // AK1
    public function test_set_date_time_mit_unvollstaendiger_angabe_macht_vorhandenes_objekt_leer(): void
    {
        $objekt = new MyDateTime('2018-05-25');
        $this->assertSame('Date', $objekt->getType());

        [, $meldungen] = $this->ueberwacht(fn () => $objekt->setDateTime('15.03'));

        $this->assertSame([], $meldungen);
        $this->assertSame('Nothing', $objekt->getType());
        $this->assertEquals(0, $objekt->getTimestamp());
    }

    // Randfall 2
    #[DataProvider('nichtNumerische')]
    public function test_nicht_numerische_teile_erzeugen_keine_warnung_und_kein_typeerror(string $angabe): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);
    }

    // Randfall 2
    #[DataProvider('nichtNumerische')]
    public function test_nicht_numerische_teile_ergeben_leeres_objekt(string $angabe): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        // Gegenprobe: das Objekt wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);

        $this->assertSame('Nothing', $objekt->getType());
        $this->assertEquals(0, $objekt->getTimestamp());
    }

    // AK2
    #[DataProvider('alleUnvollstaendigen')]
    public function test_form_date_mit_unvollstaendiger_angabe_erzeugt_keine_warnung(string $angabe): void
    {
        [$html, $meldungen] = $this->ueberwacht(fn () => (string) HForm::date('d', $angabe));

        $this->assertSame([], $meldungen);
        $this->assertStringContainsString('name="d"', (string) $html);
    }

    // AK2
    #[DataProvider('alleUnvollstaendigen')]
    public function test_form_date_zeigt_unvollstaendige_angabe_unveraendert(string $angabe): void
    {
        [$html, $meldungen] = $this->ueberwacht(fn () => (string) HForm::date('d', $angabe));

        // Gegenprobe: das Feld wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertStringContainsString('name="d"', (string) $html);

        $this->assertStringContainsString('value="'.e($angabe).'"', $html);
    }

    // AK2
    public function test_form_date_escaped_die_unvollstaendige_angabe(): void
    {
        $angabe = '15.03<b>"x\'&';

        [$html, $meldungen] = $this->ueberwacht(fn () => (string) HForm::date('d', $angabe));

        $this->assertSame([], $meldungen);
        $this->assertStringContainsString('value="'.e($angabe).'"', $html);
        $this->assertStringNotContainsString('<b>', $html);
        $this->assertStringNotContainsString('value="15.03<', $html);
    }

    // AK2
    public function test_form_date_mit_unvollstaendiger_angabe_behaelt_attribute_und_klasse(): void
    {
        [$html, $meldungen] = $this->ueberwacht(fn () => (string) HForm::date('d', '15.03', 'extra', ['data-x' => '1']));

        $this->assertSame([], $meldungen);
        $this->assertStringContainsString('value="15.03"', $html);
        $this->assertStringContainsString('data-x="1"', $html);
        $this->assertStringContainsString('input-date extra', $html);
    }

    /**
     * Erwartete Werte, auf `main` vor der Änderung ermittelt (Zeitzone UTC).
     * Reihenfolge: Angabe, Zeitstempel, Typ, getDateTimeISO, getDateDE, getDateDB, getTime, ISO-, DE-, DB-Ausgabe.
     *
     * @return array<string, array{string, int, string, string, string, string, string, string, string, string}>
     */
    public static function vollstaendige(): array
    {
        return [
            'ISO-Datum' => ['2018-05-25', 1527206400, 'Date', '2018-05-25 00:00:00', '25.05.2018', '20180525', '00:00:00', '2018-05-25', '25.05.2018', '20180525'],
            'DE-Datum' => ['25.05.2018', 1527206400, 'Date', '2018-05-25 00:00:00', '25.05.2018', '20180525', '00:00:00', '2018-05-25', '25.05.2018', '20180525'],
            'ISO mit Sekunden' => ['2018-05-25 15:25:06', 1527261906, 'DateTime', '2018-05-25 15:25:06', '25.05.2018', '20180525', '15:25:06', '2018-05-25 15:25:06', '25.05.2018 15:25:06', '20180525152506'],
            'ISO ohne Sekunden' => ['2018-05-25 15:25', 1527261900, 'DateTime', '2018-05-25 15:25:00', '25.05.2018', '20180525', '15:25:00', '2018-05-25 15:25:00', '25.05.2018 15:25:00', '20180525152500'],
            'DE mit Sekunden' => ['25.05.2018 15:25:06', 1527261906, 'DateTime', '2018-05-25 15:25:06', '25.05.2018', '20180525', '15:25:06', '2018-05-25 15:25:06', '25.05.2018 15:25:06', '20180525152506'],
            'DE ohne Sekunden' => ['25.05.2018 15:25', 1527261900, 'DateTime', '2018-05-25 15:25:00', '25.05.2018', '20180525', '15:25:00', '2018-05-25 15:25:00', '25.05.2018 15:25:00', '20180525152500'],
            'Uhrzeit mit Sekunden' => ['15:25:06', 943975506, 'Time', '1999-11-30 15:25:06', '30.11.1999', '19991130', '15:25:06', '15:25:06', '15:25:06', '15:25:06'],
            'Uhrzeit ohne Sekunden' => ['15:25', 943975500, 'Time', '1999-11-30 15:25:00', '30.11.1999', '19991130', '15:25:00', '15:25:00', '15:25:00', '15:25:00'],
            'Zeitstempel' => ['1537451550', 1537451550, 'DateTime', '2018-09-20 13:52:30', '20.09.2018', '20180920', '13:52:30', '2018-09-20 13:52:30', '20.09.2018 13:52:30', '20180920135230'],
            'NULL' => ['NULL', 0, 'DateTime', '1970-01-01 00:00:00', '', '0000-00-00', '00:00:00', '1970-01-01 00:00:00', '01.01.1970 00:00:00', '19700101000000'],
            'Nulldatum' => ['0000-00-00', 0, 'Date', '1970-01-01 00:00:00', '', '0000-00-00', '00:00:00', '1970-01-01', '', '0000-00-00'],
            'leer' => ['', 0, 'Nothing', '1970-01-01 00:00:00', '', '0000-00-00', '00:00:00', '', '', ''],
            'nicht erkannt' => ['abc', 0, 'Nothing', '1970-01-01 00:00:00', '', '0000-00-00', '00:00:00', '', '', ''],
        ];
    }

    // AK3
    #[DataProvider('vollstaendige')]
    public function test_bisher_erkannte_angaben_ergeben_wie_vorher_zeitstempel_und_typ(string $angabe, int $zeitstempel, string $typ, string ...$ausgaben): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        $this->assertSame([], $meldungen);
        $this->assertEquals($zeitstempel, $objekt->getTimestamp());
        $this->assertSame($typ, $objekt->getType());
    }

    // AK3
    #[DataProvider('vollstaendige')]
    public function test_bisher_erkannte_angaben_ergeben_wie_vorher_die_ausgaben(
        string $angabe, int $zeitstempel, string $typ,
        string $iso, string $de, string $db, string $zeit,
        string $ausgabeIso, string $ausgabeDe, string $ausgabeDb,
    ): void {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime($angabe));

        // Gegenprobe: das Objekt wurde erzeugt
        $this->assertSame([], $meldungen);
        $this->assertInstanceOf(MyDateTime::class, $objekt);

        $this->assertSame($iso, $objekt->getDateTimeISO());
        $this->assertSame($de, $objekt->getDateDE());
        $this->assertSame($db, $objekt->getDateDB());
        $this->assertSame($zeit, $objekt->getTime());
        $this->assertSame($ausgabeIso, (string) $objekt);
        $this->assertSame($ausgabeDe, (string) $objekt->setOutputFormat('DE'));
        $this->assertSame($ausgabeDb, (string) $objekt->setOutputFormat('DB'));
    }

    // AK3
    public function test_now_ergibt_wie_vorher_aktuellen_zeitstempel_vom_typ_datetime(): void
    {
        $vorher = time();
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime('now'));
        $nachher = time();

        $this->assertSame([], $meldungen);
        $this->assertSame('DateTime', $objekt->getType());
        $this->assertGreaterThanOrEqual($vorher, $objekt->getTimestamp());
        $this->assertLessThanOrEqual($nachher, $objekt->getTimestamp());
    }

    // AK3
    public function test_form_date_mit_vollstaendiger_angabe_zeigt_wie_vorher_das_deutsche_datum(): void
    {
        $this->assertStringContainsString('value="25.05.2018"', (string) HForm::date('d', '2018-05-25'));
        $this->assertStringContainsString('value="25.05.2018"', (string) HForm::date('d', '25.05.2018 15:25'));
    }

    // Randfall 1
    public function test_ungueltiges_aber_vollstaendiges_datum_wird_wie_bisher_normalisiert(): void
    {
        [$objekt, $meldungen] = $this->ueberwacht(fn () => new MyDateTime('31.02.2026'));

        $this->assertSame([], $meldungen);
        $this->assertSame('Date', $objekt->getType());
        $this->assertEquals(1772496000, $objekt->getTimestamp());
        $this->assertSame('03.03.2026', $objekt->getDateDE());
    }

    /**
     * Führt PHP-Code in einem frischen Prozess mit LC_TIME=C aus und liefert die JSON-Ausgabe.
     *
     * @return array{vorher: string|false, nachher: string|false}
     */
    private function prozessMitLcTimeC(string $code): array
    {
        $autoload = dirname(__DIR__, 2).'/vendor/autoload.php';
        $skript = 'require '.var_export($autoload, true).'; '
            .'$vorher = setlocale(LC_TIME, "C"); '
            .$code
            .' echo json_encode(["vorher" => $vorher, "nachher" => setlocale(LC_TIME, 0)]);';

        $prozess = proc_open(
            [PHP_BINARY, '-d', 'display_errors=stderr', '-r', $skript],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            null,
            array_merge(getenv(), ['LC_TIME' => 'C', 'LC_ALL' => 'C']),
        );
        $this->assertIsResource($prozess);
        $ausgabe = stream_get_contents($pipes[1]);
        $fehler = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($prozess);

        $daten = json_decode((string) $ausgabe, true);
        $this->assertIsArray($daten, 'Prozess lieferte kein JSON: '.$ausgabe.' '.$fehler);

        return $daten;
    }

    /**
     * @return array<string, array{string}>
     */
    public static function ladewege(): array
    {
        return [
            'MyDateTime laden' => ['class_exists(ITHilbert\LaravelKit\Helpers\MyDateTime::class);'],
            'MyDateTime benutzen' => ['$m = new ITHilbert\LaravelKit\Helpers\MyDateTime("2018-05-25"); $m->getDateDE();'],
            'HForm benutzen' => ['(string) ITHilbert\LaravelKit\Helpers\HForm::date("d", "2018-05-25");'],
        ];
    }

    // AK4
    #[DataProvider('ladewege')]
    public function test_laden_und_benutzen_aendert_lc_time_nicht(string $code): void
    {
        $daten = $this->prozessMitLcTimeC($code);

        // Gegenprobe: der Prozess startete mit LC_TIME = C
        $this->assertSame('C', $daten['vorher']);

        $this->assertSame('C', $daten['nachher']);
    }

    // AK5
    public function test_readme_nennt_dass_lc_time_nicht_mehr_gesetzt_wird(): void
    {
        $readme = (string) file_get_contents(dirname(__DIR__, 2).'/README.md');

        // Gegenprobe: die README ist gelesen
        $this->assertStringContainsString('MyDateTime', $readme);

        $this->assertMatchesRegularExpression('/LC_TIME[^\n]*nicht mehr|nicht mehr[^\n]*LC_TIME/u', $readme);
    }

    // AK5
    public function test_readme_nennt_die_behandlung_unvollstaendiger_datumsangaben(): void
    {
        $readme = (string) file_get_contents(dirname(__DIR__, 2).'/README.md');

        // Gegenprobe: die README ist gelesen
        $this->assertStringContainsString('MyDateTime', $readme);

        $this->assertMatchesRegularExpression('/unvollständig/iu', $readme);
    }
}
