<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use ITHilbert\LaravelKit\Helpers\HForm;
use ITHilbert\LaravelKit\Helpers\MyDateTime;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;

/**
 * Regression Dashboard #92: MyDateTime darf die PHP-Zeitzone der App nicht ändern.
 *
 * Jeder Test läuft in einem eigenen Prozess, weil MyDateTime beim ersten Laden
 * der Datei einen Seiteneffekt hatte. Ist die Klasse schon geladen, würde er
 * sonst nicht mehr greifen.
 */
final class MyDateTimeZeitzoneTest extends TestCase
{
    /**
     * @return array<string, array{string}>
     */
    public static function zeitzonen(): array
    {
        return [
            'UTC' => ['UTC'],
            'New York' => ['America/New_York'],
        ];
    }

    /**
     * Setzt Config und PHP-Zeitzone der App und zeigt per Gegenprobe, dass der Aufbau greift.
     */
    private function appZeitzoneSetzen(string $zeitzone): void
    {
        config()->set('app.timezone', $zeitzone);
        date_default_timezone_set($zeitzone);

        // Gegenprobe: Aufbau greift, die Klasse ist noch nicht geladen
        $this->assertSame($zeitzone, date_default_timezone_get());
        $this->assertSame($zeitzone, config('app.timezone'));
        $this->assertFalse(class_exists(MyDateTime::class, false), 'MyDateTime darf vor dem Test nicht geladen sein.');
    }

    // AK1
    #[DataProvider('zeitzonen')]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_laden_der_klasse_aendert_die_zeitzone_der_app_nicht(string $zeitzone): void
    {
        $this->appZeitzoneSetzen($zeitzone);

        $this->assertTrue(class_exists(MyDateTime::class));

        $this->assertSame(config('app.timezone'), date_default_timezone_get());
    }

    // AK1
    #[DataProvider('zeitzonen')]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_objekt_mit_now_aendert_die_zeitzone_der_app_nicht(string $zeitzone): void
    {
        $this->appZeitzoneSetzen($zeitzone);

        new MyDateTime('now');

        $this->assertSame(config('app.timezone'), date_default_timezone_get());
    }

    // AK1
    #[DataProvider('zeitzonen')]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_objekt_mit_datum_aendert_die_zeitzone_der_app_nicht(string $zeitzone): void
    {
        $this->appZeitzoneSetzen($zeitzone);

        new MyDateTime('2026-01-01 10:00:00');

        $this->assertSame(config('app.timezone'), date_default_timezone_get());
    }

    // AK1
    #[DataProvider('zeitzonen')]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_hform_date_mit_wert_aendert_die_zeitzone_der_app_nicht(string $zeitzone): void
    {
        $this->appZeitzoneSetzen($zeitzone);

        HForm::date('lieferdatum', '2026-03-15 10:00:00');

        $this->assertSame(config('app.timezone'), date_default_timezone_get());
    }

    // AK2
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_timestamp_in_utc(): void
    {
        $this->appZeitzoneSetzen('UTC');

        $d = new MyDateTime('2026-01-01 00:00:00');

        $this->assertEquals(1767225600, $d->getTimestamp());
    }

    // AK2
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_timestamp_in_berlin(): void
    {
        $this->appZeitzoneSetzen('Europe/Berlin');

        $d = new MyDateTime('2026-01-01 00:00:00');

        $this->assertEquals(1767222000, $d->getTimestamp());
    }

    // AK2 (Spec nennt "2026-01-01 00:00:00" für getDateTimeDB; die Methode liefert
    // laut MyDateTimeTest das Format YmdHis, das ISO-Format steht in getDateTimeISO)
    #[DataProvider('zeitzonen')]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_datum_zeit_bleibt_in_jeder_zeitzone_gleich(string $zeitzone): void
    {
        $this->appZeitzoneSetzen($zeitzone);

        $d = new MyDateTime('2026-01-01 00:00:00');

        $this->assertSame('2026-01-01 00:00:00', $d->getDateTimeISO());
        $this->assertSame('20260101000000', $d->getDateTimeDB());
    }

    // AK2
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_datum_zeit_bleibt_in_berlin_gleich(): void
    {
        $this->appZeitzoneSetzen('Europe/Berlin');

        $d = new MyDateTime('2026-01-01 00:00:00');

        $this->assertSame('2026-01-01 00:00:00', $d->getDateTimeISO());
        $this->assertSame('20260101000000', $d->getDateTimeDB());
    }

    // AK3
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_hform_date_gibt_wert_im_deutschen_format_aus(): void
    {
        $html = (string) HForm::date('lieferdatum', '2026-03-15 10:00:00');

        $this->assertStringContainsString('value="15.03.2026"', $html);
    }

    // AK3
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_hform_date_gibt_feldnamen_aus(): void
    {
        $html = (string) HForm::date('lieferdatum', '2026-03-15 10:00:00');

        $this->assertStringContainsString('name="lieferdatum"', $html);
    }

    // Randfall 1 (zu AK3)
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_hform_date_mit_leerem_wert_erzeugt_kein_mydatetime(): void
    {
        $this->assertFalse(class_exists(MyDateTime::class, false), 'Gegenprobe: MyDateTime noch nicht geladen.');

        $html = (string) HForm::date('lieferdatum', '');

        $this->assertStringContainsString('value=""', $html);
        $this->assertStringContainsString('name="lieferdatum"', $html);
        $this->assertFalse(class_exists(MyDateTime::class, false), 'MyDateTime darf nicht geladen werden.');
    }
}
