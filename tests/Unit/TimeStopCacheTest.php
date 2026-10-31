<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use Illuminate\Contracts\Cache\Store;
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

    // Randfall „start() findet eine Altlast aus dem alten Stand (kein int)“
    #[DataProvider('serialisierendeStores')]
    public function test_start_ueberschreibt_altlast_mit_int_zeitstempel(string $store): void
    {
        $this->altlastAnlegen($store);
        $this->travelTo(now());
        $erwartet = now()->getTimestamp();

        // Gegenprobe: Altlast liegt wirklich im Cache und ist kein int
        $alt = Cache::get('TimeStop_start');
        $this->assertNotNull($alt);
        $this->assertFalse(is_int($alt));

        TimeStop::start();

        $wert = Cache::get('TimeStop_start');
        $this->assertIsInt($wert);
        $this->assertSame($erwartet, $wert);
    }

    // Randfall „start() findet eine Altlast aus dem alten Stand (kein int)“
    #[DataProvider('serialisierendeStores')]
    public function test_ende_nach_start_ueber_altlast_liefert_dauer_ab_start(string $store): void
    {
        $this->altlastAnlegen($store);
        $this->travelTo(now());

        // Gegenprobe: Altlast liegt wirklich im Cache und ist kein int
        $alt = Cache::get('TimeStop_start');
        $this->assertNotNull($alt);
        $this->assertFalse(is_int($alt));

        TimeStop::start();
        $this->travel(5)->seconds();

        $this->assertSame(5, TimeStop::ende());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function redisartigUndArray(): array
    {
        return ['redisartig' => ['redisartig'], 'array' => ['array']];
    }

    /**
     * Store, der wie RedisStore ohne Serializer arbeitet: Zahlen kommen als String zurück.
     */
    private function redisartigVerwenden(): void
    {
        config()->set('cache.stores.redisartig', ['driver' => 'redisartig']);
        Cache::extend('redisartig', fn () => Cache::repository(new ZahlenAlsStringStore));
        $this->storeVerwenden('redisartig');
    }

    private function storeNutzen(string $store): void
    {
        if ($store === 'redisartig') {
            $this->redisartigVerwenden();

            return;
        }

        $this->storeVerwenden($store);
    }

    // AK2
    public function test_redisartiger_store_liefert_zahlen_als_string(): void
    {
        $this->redisartigVerwenden();
        $this->travelTo(now());

        TimeStop::start();

        // Gegenprobe: der Aufbau greift, der Store gibt die Zahl als String zurück
        $this->assertIsString(Cache::get('TimeStop_start'));
        $this->assertSame((string) now()->getTimestamp(), Cache::get('TimeStop_start'));
    }

    // AK2
    #[DataProvider('redisartigUndArray')]
    public function test_start_legt_zahl_statt_objekt_ab_auch_bei_stringrueckgabe(string $store): void
    {
        $this->storeNutzen($store);
        $this->travelTo(now());
        $erwartet = now()->getTimestamp();

        TimeStop::start();

        $wert = Cache::get('TimeStop_start');
        $this->assertTrue(is_int($wert) || is_string($wert));
        $this->assertSame($erwartet, (int) $wert);
        $this->assertSame((string) $erwartet, (string) $wert);
    }

    // AK3
    #[DataProvider('redisartigUndArray')]
    public function test_zweiter_start_behaelt_erste_startzeit_bei_stringrueckgabe(string $store): void
    {
        $this->storeNutzen($store);
        $this->travelTo(now());
        $erste = now()->getTimestamp();

        TimeStop::start();
        $this->travel(3)->seconds();
        TimeStop::start();

        $this->assertSame($erste, (int) Cache::get('TimeStop_start'));
    }

    // AK3
    #[DataProvider('redisartigUndArray')]
    public function test_ende_rechnet_ab_erster_startzeit_bei_stringrueckgabe(string $store): void
    {
        $this->storeNutzen($store);
        $this->travelTo(now());

        TimeStop::start();
        $this->travel(3)->seconds();
        TimeStop::start();
        $this->travel(2)->seconds();

        $dauer = TimeStop::ende();

        $this->assertIsInt($dauer);
        $this->assertSame(5, $dauer);
    }

    // AK2
    #[DataProvider('redisartigUndArray')]
    public function test_ende_liefert_dauer_als_int_und_entfernt_eintrag_bei_stringrueckgabe(string $store): void
    {
        $this->storeNutzen($store);
        $this->travelTo(now());

        TimeStop::start();
        $this->assertNotNull(Cache::get('TimeStop_start'));
        $this->travel(5)->seconds();

        $dauer = TimeStop::ende();

        $this->assertIsInt($dauer);
        $this->assertSame(5, $dauer);
        $this->assertNull(Cache::get('TimeStop_start'));
    }

    // Randfall 1
    public function test_ende_ohne_start_liefert_null_bei_redisartigem_store(): void
    {
        $this->redisartigVerwenden();

        $this->assertNull(Cache::get('TimeStop_start'));
        $this->assertSame(0, TimeStop::ende());
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

/**
 * Verhält sich wie Illuminate\Cache\RedisStore ohne Serializer: Zahlen werden roh abgelegt
 * und kommen als String zurück, alles andere wird serialisiert.
 */
final class ZahlenAlsStringStore implements Store
{
    /** @var array<string, string> */
    private array $daten = [];

    public function get($key)
    {
        if (! array_key_exists($key, $this->daten)) {
            return null;
        }

        $wert = $this->daten[$key];

        return is_numeric($wert) ? $wert : unserialize($wert, ['allowed_classes' => false]);
    }

    public function many(array $keys)
    {
        $ergebnis = [];
        foreach ($keys as $key) {
            $ergebnis[$key] = $this->get($key);
        }

        return $ergebnis;
    }

    public function put($key, $value, $seconds)
    {
        $this->daten[$key] = is_int($value) || is_float($value) ? (string) $value : serialize($value);

        return true;
    }

    public function putMany(array $values, $seconds)
    {
        foreach ($values as $key => $value) {
            $this->put($key, $value, $seconds);
        }

        return true;
    }

    public function increment($key, $value = 1)
    {
        return $this->daten[$key] = (string) ((int) ($this->daten[$key] ?? 0) + $value);
    }

    public function decrement($key, $value = 1)
    {
        return $this->increment($key, -$value);
    }

    public function touch($key, $seconds)
    {
        return array_key_exists($key, $this->daten);
    }

    public function forever($key, $value)
    {
        return $this->put($key, $value, 0);
    }

    public function forget($key)
    {
        unset($this->daten[$key]);

        return true;
    }

    public function flush()
    {
        $this->daten = [];

        return true;
    }

    public function getPrefix()
    {
        return '';
    }
}
