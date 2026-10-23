<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use ITHilbert\LaravelKit\Helpers\MyDateTime;
use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * Charakterisierung von MyDateTime (Ist-Stand). Die Klasse setzt beim Laden
 * die Zeitzone Europe/Berlin global.
 */
final class MyDateTimeTest extends TestCase
{
    // Charakterisierung
    public function test_iso_datum_mit_zeit_wird_erkannt(): void
    {
        $d = new MyDateTime('2018-05-25 15:25:06');

        $this->assertSame('DateTime', $d->getType());
        $this->assertSame('2018-05-25 15:25:06', $d->getDateTimeISO());
        $this->assertSame('25.05.2018 15:25:06', $d->getDateTimeDE());
        $this->assertSame('20180525152506', $d->getDateTimeDB());
    }

    // Charakterisierung
    public function test_deutsches_datum_mit_zeit_ohne_sekunden(): void
    {
        $d = new MyDateTime('25.05.2018 15:25');

        $this->assertSame('DateTime', $d->getType());
        $this->assertSame('2018-05-25 15:25:00', $d->getDateTimeISO());
    }

    // Charakterisierung
    public function test_nur_datum_iso_und_de(): void
    {
        $iso = new MyDateTime('2018-05-25');
        $de = new MyDateTime('25.05.2018');

        $this->assertSame('Date', $iso->getType());
        $this->assertSame('Date', $de->getType());
        $this->assertSame($iso->getTimestamp(), $de->getTimestamp());
        $this->assertSame('25.05.2018', $iso->getDateDE());
        $this->assertSame('2018-05-25', $de->getDateISO());
    }

    // Charakterisierung
    public function test_nur_uhrzeit(): void
    {
        $d = new MyDateTime('15:25');

        $this->assertSame('Time', $d->getType());
        $this->assertSame('15:25:00', $d->getTime());
        $this->assertSame('15:25', $d->getTimeSmall());
    }

    // Charakterisierung
    public function test_nullwerte_ergeben_timestamp_null(): void
    {
        $this->assertSame(0, (new MyDateTime('NULL'))->getTimestamp());
        $this->assertSame(0, (new MyDateTime('0000-00-00'))->getTimestamp());
        $this->assertSame('Date', (new MyDateTime('0000-00-00'))->getType());
        $this->assertSame('', (new MyDateTime('0000-00-00'))->getDateDE());
    }

    // Charakterisierung
    public function test_unbekannte_eingabe_ist_typ_nothing(): void
    {
        $d = new MyDateTime('kein datum');

        $this->assertSame('Nothing', $d->getType());
        $this->assertSame(0, $d->getTimestamp());
    }

    // Charakterisierung
    public function test_now_liefert_aktuellen_timestamp(): void
    {
        $vorher = time();
        $d = new MyDateTime('now');

        $this->assertGreaterThanOrEqual($vorher, $d->getTimestamp());
        $this->assertLessThanOrEqual(time(), $d->getTimestamp());
    }

    // Charakterisierung
    public function test_to_string_folgt_dem_ausgabeformat(): void
    {
        $d = new MyDateTime('2018-05-25 15:25:06');

        $this->assertSame('ISO', $d->getOutputFormat());
        $this->assertSame('2018-05-25 15:25:06', (string) $d);
        $this->assertSame('25.05.2018 15:25:06', (string) $d->setOutputFormat('DE'));
        $this->assertSame('20180525152506', (string) $d->setOutputFormat('DB'));
    }

    // Charakterisierung
    public function test_wochentag_und_monat_und_kalenderwoche(): void
    {
        $d = new MyDateTime('2018-05-25');   // Freitag

        $this->assertSame('5', (string) $d->getDayOfWeek());
        $this->assertSame('Freitag', $d->getDayOfWeekName());
        $this->assertSame('Fr', $d->getDayOfWeekNameSmall());
        $this->assertSame('21', $d->getKW());
        $this->assertSame('05', $d->getMonth());
        $this->assertSame('May', $d->getMonthName());
        $this->assertSame('2018', $d->getYear());
        $this->assertSame('25', $d->getDay());
    }

    // Charakterisierung
    public function test_addieren_von_zeiteinheiten(): void
    {
        $d = new MyDateTime('2018-05-25 10:00:00');

        $d->addMin(30)->addHour(2)->addDay(1)->addWeek(1);

        $this->assertSame('2018-06-02 12:30:00', $d->getDateTimeISO());
    }

    // Charakterisierung
    public function test_add_year_beachtet_schaltjahr(): void
    {
        $d = new MyDateTime('2019-03-01');
        $d->addYear(1);   // 2020 ist Schaltjahr, Ziel nach Februar

        $this->assertSame('2020-03-01', $d->getDateISO());
    }

    // Charakterisierung
    public function test_schaltjahr_pruefung(): void
    {
        $this->assertSame('1', (string) (new MyDateTime('2020-01-01'))->isSchaltJahr());
        $this->assertSame('0', (string) (new MyDateTime('2019-01-01'))->isSchaltJahr());
    }

    // Charakterisierung
    public function test_differenz_in_tagen_und_mit_format(): void
    {
        $a = new MyDateTime('2018-05-01');
        $b = new MyDateTime('2018-05-11');

        $this->assertSame('10', $a->diff($b));
        $this->assertSame('10 Tage', $a->diff($b, '%d Tage'));
        $this->assertSame('10', $a->diff2('11.05.2018'));
    }

    // Charakterisierung
    public function test_kalenderwoche_montag_und_sonntag(): void
    {
        $d = new MyDateTime;

        $this->assertSame('2018-05-21', $d->getKWMo(21, 2018));
        $this->assertSame('2018-05-27', $d->getKWSo(21, 2018));
    }

    // Charakterisierung
    public function test_is_date_und_is_timestamp(): void
    {
        $d = new MyDateTime;

        $this->assertTrue($d->is_Date('2018/05/25'));
        $this->assertFalse($d->is_Date('kein datum'));
        $this->assertTrue($d->isTimestamp('1537451550'));
    }

    // Charakterisierung
    public function test_timestamp_als_eingabe(): void
    {
        $d = new MyDateTime('1537451550');

        $this->assertSame('DateTime', $d->getType());
        $this->assertSame(1537451550, (int) $d->getTimestamp());
        $this->assertSame(date('Y-m-d H:i:s', 1537451550), $d->getDateTimeISO());
    }

    // Charakterisierung
    public function test_alter_in_jahren(): void
    {
        $geburt = (new MyDateTime('now'))->addYear(-30)->addDay(-2);

        $this->assertSame('30', $geburt->getAlter());
    }

    // Charakterisierung
    public function test_format_nutzt_php_date_format(): void
    {
        $this->assertSame('25/05/18', (new MyDateTime('2018-05-25'))->format('d/m/y'));
    }
}
