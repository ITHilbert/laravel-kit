<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\Logs;

use Closure;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ITHilbert\LaravelKit\Tests\TestCase;
use Monolog\Handler\StreamHandler;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * log-taeglich (#1716): Das Kit stellt `single` zur Laufzeit auf `daily` um.
 * Aufbau wie ein Projekt mit älterer config/logging.php (stack fest auf ['single'], days 14).
 */
final class LogTaeglichTest extends TestCase
{
    use ConfigMitEnv;

    private ?string $verzeichnis = null;

    private ?Closure $umgebung = null;

    protected function tearDown(): void
    {
        parent::tearDown();

        if ($this->verzeichnis !== null) {
            File::deleteDirectory($this->verzeichnis);
        }
    }

    private function logDir(): string
    {
        if ($this->verzeichnis === null) {
            $this->verzeichnis = sys_get_temp_dir().'/kit-log-taeglich-'.bin2hex(random_bytes(6));
            mkdir($this->verzeichnis, 0777, true);
        }

        return $this->verzeichnis;
    }

    /**
     * @param  list<string>  $stack
     * @return array<string, mixed>
     */
    private function logging(array $stack = ['single'], string $default = 'stack', bool $mitDaily = true): array
    {
        $kanaele = [
            'stack' => ['driver' => 'stack', 'channels' => $stack, 'ignore_exceptions' => false],
            'single' => ['driver' => 'single', 'path' => $this->logDir().'/laravel.log', 'level' => 'notice'],
            'slack' => ['driver' => 'slack', 'url' => 'https://example.invalid/hook', 'level' => 'critical'],
            'stderr' => ['driver' => 'monolog', 'handler' => StreamHandler::class, 'with' => ['stream' => 'php://stderr']],
        ];

        if ($mitDaily) {
            $kanaele['daily'] = ['driver' => 'daily', 'path' => $this->logDir().'/laravel.log', 'level' => 'debug', 'days' => 14];
        }

        return ['default' => $default, 'channels' => $kanaele];
    }

    /**
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('logging', $this->logging());

        if ($this->umgebung !== null) {
            ($this->umgebung)($app);
        }
    }

    /**
     * Startet die App neu mit der Config eines Projekts (gilt vor dem Booten des Kit-ServiceProviders).
     *
     * @param  array<string, mixed>  $logging
     * @param  array<string, mixed>  $kit  Werte für laravelkit.logs.*
     */
    private function starte(array $logging, array $kit = []): void
    {
        $this->umgebung = function ($app) use ($logging, $kit): void {
            $app['config']->set('logging', $logging);
            if ($kit !== []) {
                $app['config']->set('laravelkit.logs', array_merge(['taeglich' => true, 'tage' => 30, 'zeitplan' => true], $kit));
            }
        };

        $this->refreshApplication();
    }

    /**
     * @return list<string>
     */
    private function tagesdateien(?string $dir = null): array
    {
        return glob(($dir ?? $this->logDir()).'/*-[0-9][0-9][0-9][0-9]-[0-9][0-9]-[0-9][0-9].log') ?: [];
    }

    // AK1
    public function test_stack_mit_single_schreibt_in_die_tagesdatei(): void
    {
        $this->starte($this->logging(['single'], 'stack'));
        $marker = 'kit-probe-'.bin2hex(random_bytes(4));

        Log::error($marker);

        $datei = $this->logDir().'/laravel-'.date('Y-m-d').'.log';
        $this->assertFileExists($datei);
        $this->assertStringContainsString($marker, (string) file_get_contents($datei));
        $this->assertFileDoesNotExist($this->logDir().'/laravel.log');
    }

    // AK2
    public function test_default_single_schreibt_in_die_tagesdatei(): void
    {
        $this->starte($this->logging(['single'], 'single'));
        $marker = 'kit-probe-'.bin2hex(random_bytes(4));

        Log::error($marker);

        $datei = $this->logDir().'/laravel-'.date('Y-m-d').'.log';
        $this->assertFileExists($datei);
        $this->assertStringContainsString($marker, (string) file_get_contents($datei));
        $this->assertFileDoesNotExist($this->logDir().'/laravel.log');
    }

    // AK3
    /**
     * @param  list<string>  $vorher
     * @param  list<string>  $nachher
     */
    #[DataProvider('stackFaelle')]
    public function test_nur_single_wird_im_stack_ersetzt(array $vorher, array $nachher): void
    {
        $logging = $this->logging($vorher);
        $this->starte($logging);

        $this->assertSame($nachher, config('logging.channels.stack.channels'));
        $this->assertSame($logging['channels']['slack'], config('logging.channels.slack'));
        $this->assertSame($logging['channels']['single'], config('logging.channels.single'));
    }

    /**
     * @return array<string, array{list<string>, list<string>}>
     */
    public static function stackFaelle(): array
    {
        return [
            'Beispiel der Spec' => [['single', 'slack'], ['daily', 'slack']],
            'Reihenfolge bleibt' => [['stderr', 'single', 'slack'], ['stderr', 'daily', 'slack']],
            'ohne single unverändert' => [['slack', 'stderr'], ['slack', 'stderr']],
        ];
    }

    // AK4
    public function test_tage_aus_config_landen_im_daily_kanal(): void
    {
        $this->starte($this->logging(), ['tage' => 45]);

        $this->assertEquals(45, config('logging.channels.daily.days'));
    }

    // AK4
    public function test_ohne_angabe_sind_es_30_tage(): void
    {
        $this->assertEquals(30, config('laravelkit.logs.tage'));
        $this->assertEquals(30, config('logging.channels.daily.days'));
    }

    // AK4 (leer gesetzt, Annahme: leer verhält sich wie nicht gesetzt = 30)
    #[DataProvider('leereTage')]
    public function test_leere_tage_ergeben_den_standard_30(mixed $leer): void
    {
        $this->starte($this->logging(), ['tage' => $leer]);

        $this->assertEquals(30, config('logging.channels.daily.days'));
    }

    /**
     * @return array<string, array{mixed}>
     */
    public static function leereTage(): array
    {
        return ['leerer Text' => [''], 'null' => [null]];
    }

    // AK4 (Env-Name und Standard der Konfiguration)
    public function test_env_name_der_tage_und_standard(): void
    {
        $this->assertEquals(45, $this->configMitEnv('LARAVELKIT_LOGS_TAGE', '45')['logs']['tage'] ?? null);
        $this->assertEquals(30, $this->configMitEnv('LARAVELKIT_LOGS_TAGE', null)['logs']['tage'] ?? null);
    }

    // AK1 (Env-Name und Standard der Konfiguration)
    public function test_env_name_taeglich_und_standard(): void
    {
        $this->assertFalse($this->configMitEnv('LARAVELKIT_LOGS_TAEGLICH', 'false')['logs']['taeglich'] ?? null);
        $this->assertTrue($this->configMitEnv('LARAVELKIT_LOGS_TAEGLICH', null)['logs']['taeglich'] ?? null);
    }

    // AK5
    public function test_taeglich_aus_laesst_die_config_unveraendert_und_schreibt_laravel_log(): void
    {
        $logging = $this->logging(['single'], 'stack');
        $this->starte($logging, ['taeglich' => false, 'tage' => 45]);
        $marker = 'kit-probe-'.bin2hex(random_bytes(4));

        $this->assertSame($logging['default'], config('logging.default'));
        $this->assertSame(['single'], config('logging.channels.stack.channels'));
        $this->assertSame($logging['channels']['daily'], config('logging.channels.daily'));

        Log::error($marker);

        $this->assertStringContainsString($marker, (string) @file_get_contents($this->logDir().'/laravel.log'));
        $this->assertSame([], $this->tagesdateien());
    }

    // AK5 (daily fehlt, Umstellung aus: es wird kein Kanal angelegt)
    public function test_taeglich_aus_legt_keinen_daily_kanal_an(): void
    {
        $this->starte($this->logging(['single'], 'stack', false), ['taeglich' => false]);

        $this->assertNull(config('logging.channels.daily'));
    }

    // AK6
    #[DataProvider('andereKanaele')]
    public function test_anderer_default_bleibt_und_nur_days_folgt(string $default): void
    {
        $logging = $this->logging(['single'], $default);
        $this->starte($logging, ['tage' => 45]);

        $this->assertSame($default, config('logging.default'));
        $this->assertEquals(45, config('logging.channels.daily.days'));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function andereKanaele(): array
    {
        return ['stderr' => ['stderr'], 'syslog' => ['syslog'], 'daily' => ['daily']];
    }

    // AK7
    public function test_fehlender_daily_kanal_wird_aus_single_angelegt(): void
    {
        $this->starte($this->logging(['single'], 'stack', false), ['tage' => 45]);
        $marker = 'kit-probe-'.bin2hex(random_bytes(4));

        $daily = config('logging.channels.daily');
        $this->assertSame('daily', $daily['driver'] ?? null);
        $this->assertSame($this->logDir().'/laravel.log', $daily['path'] ?? null);
        $this->assertSame('notice', $daily['level'] ?? null);
        $this->assertEquals(45, $daily['days'] ?? null);

        Log::error($marker);
        $this->assertStringContainsString($marker, (string) file_get_contents($this->logDir().'/laravel-'.date('Y-m-d').'.log'));
    }

    // Randfall 7
    public function test_eigener_daily_kanal_behaelt_seinen_pfad_und_nur_days_folgt(): void
    {
        $eigen = $this->logDir().'/eigen';
        mkdir($eigen);
        $logging = $this->logging(['single'], 'stack');
        $logging['channels']['daily']['path'] = $eigen.'/projekt.log';
        $this->starte($logging, ['tage' => 45]);
        $marker = 'kit-probe-'.bin2hex(random_bytes(4));

        $this->assertSame($eigen.'/projekt.log', config('logging.channels.daily.path'));
        $this->assertEquals(45, config('logging.channels.daily.days'));

        Log::error($marker);

        $datei = $eigen.'/projekt-'.date('Y-m-d').'.log';
        $this->assertFileExists($datei);
        $this->assertStringContainsString($marker, (string) file_get_contents($datei));
        $this->assertSame([], $this->tagesdateien());
    }

    // Randfall 6 — gecachte Config: Die Datei aus APP_CONFIG_CACHE ersetzt die Config-Dateien wie nach `config:cache`.
    public function test_umstellung_wirkt_bei_gecachter_config_ohne_logs_schluessel(): void
    {
        $alt = $this->logging(['single'], 'stack');
        $gecacht = config()->all();
        $gecacht['logging'] = $alt;
        unset($gecacht['laravelkit']['logs']);
        $cache = $this->logDir().'/config-cache.php';
        file_put_contents($cache, '<?php return '.var_export($gecacht, true).';');

        $this->umgebung = null;
        $vorher = [$_ENV['APP_CONFIG_CACHE'] ?? null, $_SERVER['APP_CONFIG_CACHE'] ?? null, getenv('APP_CONFIG_CACHE')];
        $_ENV['APP_CONFIG_CACHE'] = $_SERVER['APP_CONFIG_CACHE'] = $cache;
        putenv('APP_CONFIG_CACHE='.$cache);

        try {
            $this->umgebung = fn () => null;
            $this->refreshApplication();
        } finally {
            foreach (['_ENV', '_SERVER'] as $i => $global) {
                $vorher[$i] === null ? $GLOBALS[$global] = array_diff_key($GLOBALS[$global], ['APP_CONFIG_CACHE' => 1]) : $GLOBALS[$global]['APP_CONFIG_CACHE'] = $vorher[$i];
            }
            $vorher[2] === false ? putenv('APP_CONFIG_CACHE') : putenv('APP_CONFIG_CACHE='.$vorher[2]);
        }

        // Gegenprobe: Der Aufbau ist wirklich die gecachte Config
        $this->assertTrue($this->app->configurationIsCached());

        $marker = 'kit-probe-'.bin2hex(random_bytes(4));
        $this->assertSame(['daily'], config('logging.channels.stack.channels'));
        $this->assertEquals(30, config('logging.channels.daily.days'));

        Log::error($marker);

        $this->assertStringContainsString($marker, (string) file_get_contents($this->logDir().'/laravel-'.date('Y-m-d').'.log'));
        $this->assertFileDoesNotExist($this->logDir().'/laravel.log');
    }
}
