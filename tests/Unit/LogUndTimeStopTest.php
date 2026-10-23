<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use ITHilbert\LaravelKit\Classes\TimeStop;
use ITHilbert\LaravelKit\Entities\Log;
use ITHilbert\LaravelKit\Tests\TestCase;

/**
 * Charakterisierung von Log-Entity und TimeStop (Ist-Stand).
 */
final class LogUndTimeStopTest extends TestCase
{
    use RefreshDatabase;

    // Charakterisierung
    public function test_log_write_speichert_text(): void
    {
        Log::write('Hallo Welt');

        $this->assertDatabaseHas('log', ['log' => 'Hallo Welt']);
        $this->assertSame(1, Log::count());
    }

    // Charakterisierung
    public function test_time_stop_start_legt_zeitpunkt_im_cache_ab(): void
    {
        TimeStop::start();

        $this->assertNotNull(Cache::get('TimeStop_start'));
    }

    // Charakterisierung
    public function test_time_stop_ende_liefert_sekunden_und_loescht_den_zeitpunkt(): void
    {
        $this->travelTo(now());
        TimeStop::start();
        $this->travel(5)->seconds();

        $this->assertSame(5, TimeStop::ende());
        $this->assertNull(Cache::get('TimeStop_start'));
    }
}
