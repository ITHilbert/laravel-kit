<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Schema;
use ITHilbert\LaravelKit\Tests\TestCase;
use Orchestra\Testbench\Attributes\DefineEnvironment;

/**
 * #1109: Log-Migration per Config `laravelkit.log_tabelle` abschaltbar.
 */
final class LogMigrationOptionalTest extends TestCase
{
    /**
     * @param  Application  $app
     */
    protected function logTabelleAus($app): void
    {
        $app['config']->set('laravelkit.log_tabelle', false);
    }

    /**
     * @param  Application  $app
     */
    protected function logTabelleAn($app): void
    {
        $app['config']->set('laravelkit.log_tabelle', true);
    }

    /**
     * Wertet die Config-Datei mit gesetzter bzw. entfernter Umgebungsvariable neu aus.
     *
     * @return array<string, mixed>
     */
    private function configMitEnv(?string $wert): array
    {
        $name = 'LARAVELKIT_LOG_TABELLE';
        $alt = [$_ENV[$name] ?? null, $_SERVER[$name] ?? null, getenv($name)];

        try {
            if ($wert === null) {
                unset($_ENV[$name], $_SERVER[$name]);
                putenv($name);
            } else {
                $_ENV[$name] = $_SERVER[$name] = $wert;
                putenv("{$name}={$wert}");
            }

            return require dirname(__DIR__, 2).'/src/Config/laravelkit.php';
        } finally {
            foreach (['_ENV', '_SERVER'] as $i => $global) {
                if ($alt[$i] === null) {
                    unset($GLOBALS[$global][$name]);
                } else {
                    $GLOBALS[$global][$name] = $alt[$i];
                }
            }
            $alt[2] === false ? putenv($name) : putenv("{$name}={$alt[2]}");
        }
    }

    // AK1
    public function test_standard_ist_log_tabelle_an(): void
    {
        $this->assertTrue(config('laravelkit.log_tabelle'));
    }

    // AK1
    #[DefineEnvironment('logTabelleAn')]
    public function test_migrate_legt_log_tabelle_mit_spalten_an(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__).'/Fixtures/Migrations');

        $this->artisan('migrate')->assertSuccessful();

        $this->assertTrue(Schema::hasTable('gegenprobe'));
        $this->assertTrue(Schema::hasTable('log'));
        $this->assertTrue(Schema::hasColumns('log', ['id', 'log', 'created_at', 'updated_at']));
    }

    // AK2
    #[DefineEnvironment('logTabelleAus')]
    public function test_migrate_legt_log_tabelle_nicht_an_wenn_abgeschaltet(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__).'/Fixtures/Migrations');

        $this->artisan('migrate')->assertSuccessful();

        // Gegenprobe: migrate lief und hat andere Migrationen ausgeführt
        $this->assertTrue(Schema::hasTable('gegenprobe'));
        $this->assertFalse(Schema::hasTable('log'));
    }

    // AK3
    public function test_env_false_schaltet_log_tabelle_ab(): void
    {
        $this->assertFalse($this->configMitEnv('false')['log_tabelle'] ?? null);
    }

    // AK3
    public function test_ohne_env_ist_log_tabelle_an(): void
    {
        $config = $this->configMitEnv(null);

        $this->assertArrayHasKey('log_tabelle', $config);
        $this->assertTrue($config['log_tabelle'] ?? null);
    }

    // AK3
    public function test_env_true_laesst_log_tabelle_an(): void
    {
        $this->assertTrue($this->configMitEnv('true')['log_tabelle'] ?? null);
    }
}
