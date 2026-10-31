<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ITHilbert\LaravelKit\Classes\TimeStop;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Regression Dashboard #93: TimeStop mit serialisierenden Cache-Stores
 * und cache.serializable_classes = false.
 */
final class TimeStopCacheTest extends TestCase
{
    private string $cachePfad;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cachePfad = sys_get_temp_dir().'/timestop-cache-'.uniqid('', true);
        File::ensureDirectoryExists($this->cachePfad);

        config()->set('cache.stores.file', ['driver' => 'file', 'path' => $this->cachePfad]);
        config()->set('cache.stores.database', [
            'driver' => 'database',
            'connection' => null,
            'table' => 'cache',
            'lock_table' => 'cache_locks',
        ]);
        config()->set('cache.serializable_classes', false);

        Schema::create('cache', function ($table): void {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->cachePfad);

        parent::tearDown();
    }

    /**
     * @return array<string, array{string}>
     */
    public static function serialisierendeStores(): array
    {
        return ['file' => ['file'], 'database' => ['database']];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function alleStores(): array
    {
        return ['array' => ['array'], 'file' => ['file'], 'database' => ['database']];
    }

    private function storeVerwenden(string $store): void
    {
        config()->set('cache.default', $store);
        app('cache')->forgetDriver($store);
    }

    /**
     * Legt den Eintrag so ab, wie ihn der alte Stand hinterlässt (serialisiertes Carbon).
     */
    private function altlastAnlegen(string $store): void
    {
        config()->set('cache.default', $store);
        config()->set('cache.serializable_classes', true);
        app('cache')->forgetDriver($store);
        Cache::store($store)->forever('TimeStop_start', now());

        config()->set('cache.serializable_classes', false);
        app('cache')->forgetDriver($store);
    }

    // AK1
    #[DataProvider('serialisierendeStores')]
    public function test_ende_liefert_fuenf_als_int_ohne_fehler(string $store): void
    {
        $this->storeVerwenden($store);
        $this->travelTo(now());

        TimeStop::start();
        $this->travel(5)->seconds();

        $dauer = TimeStop::ende();

        $this->assertIsInt($dauer);
        $this->assertSame(5, $dauer);
    }

    // AK1
    #[DataProvider('serialisierendeStores')]
    public function test_start_zeit_ist_nach_ende_nicht_mehr_im_cache(string $store): void
    {
        $this->storeVerwenden($store);
        $this->travelTo(now());

        TimeStop::start();
        $this->assertNotNull(Cache::get('TimeStop_start'));
        $this->travel(5)->seconds();

        TimeStop::ende();

        $this->assertNull(Cache::get('TimeStop_start'));
    }

    // AK2
    #[DataProvider('alleStores')]
    public function test_start_legt_int_zeitstempel_im_cache_ab(string $store): void
    {
        $this->storeVerwenden($store);
        $this->travelTo(now());
        $erwartet = now()->getTimestamp();

        TimeStop::start();

        $wert = Cache::get('TimeStop_start');
        $this->assertIsInt($wert);
        $this->assertSame($erwartet, $wert);
    }

    // AK3
    #[DataProvider('alleStores')]
    public function test_zweiter_start_behaelt_erste_startzeit(string $store): void
    {
        $this->storeVerwenden($store);
        $this->travelTo(now());
        $erste = now()->getTimestamp();

        TimeStop::start();
        $this->travel(3)->seconds();
        TimeStop::start();

        $this->assertSame($erste, Cache::get('TimeStop_start'));
    }

    // AK3
    #[DataProvider('alleStores')]
    public function test_ende_rechnet_ab_der_ersten_startzeit(string $store): void
    {
        $this->storeVerwenden($store);
        $this->travelTo(now());

        TimeStop::start();
        $this->travel(3)->seconds();
        TimeStop::start();
        $this->travel(2)->seconds();

        $this->assertSame(5, TimeStop::ende());
    }

    // Randfall 1
    #[DataProvider('alleStores')]
    public function test_ende_ohne_start_liefert_null(string $store): void
    {
        $this->storeVerwenden($store);

        $this->assertNull(Cache::get('TimeStop_start'));
        $this->assertSame(0, TimeStop::ende());
    }

    // Randfall 2
    #[DataProvider('serialisierendeStores')]
    public function test_ende_behandelt_altlast_im_cache_wie_fehlend(string $store): void
    {
        $this->altlastAnlegen($store);

        // Gegenprobe: Eintrag existiert und ist kein int
        $alt = Cache::get('TimeStop_start');
        $this->assertNotNull($alt);
        $this->assertFalse(is_int($alt));

        $this->assertSame(0, TimeStop::ende());
    }

    // Randfall 2
    #[DataProvider('serialisierendeStores')]
    public function test_ende_entfernt_altlast_aus_dem_cache(string $store): void
    {
        $this->altlastAnlegen($store);

        // Gegenprobe: Altlast ist vorhanden
        $this->assertNotNull(Cache::get('TimeStop_start'));

        TimeStop::ende();

        $this->assertNull(Cache::get('TimeStop_start'));
    }

    // Randfall 3
    public function test_zeit_folgt_der_laravel_uhr(): void
    {
        $this->travelTo(now());
        TimeStop::start();
        $this->travel(7)->seconds();

        $this->assertSame(7, TimeStop::ende());
    }
}
