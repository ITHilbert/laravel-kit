<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\Logs;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;
use ITHilbert\LaravelKit\Tests\Feature\ChangelogHilfe;
use ITHilbert\LaravelKit\Tests\TestCase;
use Orchestra\Testbench\Attributes\DefineEnvironment;

/**
 * log-taeglich (#1716): Eintrag im Scheduler (AK13) und Doku (AK14).
 */
final class LogZeitplanDokuTest extends TestCase
{
    use ChangelogHilfe;
    use ConfigMitEnv;

    /**
     * @param  Application  $app
     */
    protected function zeitplanAn($app): void
    {
        $app['config']->set('laravelkit.logs', ['taeglich' => true, 'tage' => 30, 'zeitplan' => true]);
    }

    /**
     * @param  Application  $app
     */
    protected function zeitplanAus($app): void
    {
        $app['config']->set('laravelkit.logs', ['taeglich' => true, 'tage' => 30, 'zeitplan' => false]);
    }

    /**
     * @return list<Event>
     */
    private function pruneEvents(): array
    {
        return array_values(array_filter(
            $this->app->make(Schedule::class)->events(),
            fn (Event $e): bool => str_contains((string) $e->command, 'log:prune'),
        ));
    }

    // AK13
    public function test_standard_traegt_log_prune_taeglich_im_scheduler_ein(): void
    {
        $events = $this->pruneEvents();

        $this->assertCount(1, $events);
        $this->assertMatchesRegularExpression('/^\d+ \d+ \* \* \*$/', $events[0]->expression);

        Artisan::call('schedule:list');
        $this->assertStringContainsString('log:prune', Artisan::output());
    }

    // AK13
    #[DefineEnvironment('zeitplanAn')]
    public function test_zeitplan_true_traegt_log_prune_ein(): void
    {
        $this->assertCount(1, $this->pruneEvents());
    }

    // AK13
    #[DefineEnvironment('zeitplanAus')]
    public function test_zeitplan_false_traegt_log_prune_nicht_ein(): void
    {
        // Gegenprobe: Der Scheduler ist les- und der Command registriert
        $this->assertContains('log:prune', array_keys(Artisan::all()));
        $this->assertSame([], $this->pruneEvents());

        Artisan::call('schedule:list');
        $this->assertStringNotContainsString('log:prune', Artisan::output());
    }

    // AK13 (Env-Name und Standard der Konfiguration)
    public function test_env_name_zeitplan_und_standard(): void
    {
        $this->assertFalse($this->configMitEnv('LARAVELKIT_LOGS_ZEITPLAN', 'false')['logs']['zeitplan'] ?? null);
        $this->assertTrue($this->configMitEnv('LARAVELKIT_LOGS_ZEITPLAN', null)['logs']['zeitplan'] ?? null);
    }

    /**
     * Text unter einer Überschrift bis zur nächsten gleich- oder höherrangigen (Überschriften in Codeblöcken zählen nicht).
     */
    private function abschnitt(string $datei, string $titel): string
    {
        $zeilen = preg_split('/\R/', (string) file_get_contents($datei)) ?: [];
        $aus = [];
        $ebene = null;
        $imCode = false;

        foreach ($zeilen as $zeile) {
            if (str_starts_with(ltrim($zeile), '```')) {
                $imCode = ! $imCode;
            }
            if (! $imCode && preg_match('/^(#{1,6})\s+(.*?)\s*$/u', $zeile, $m)) {
                if ($ebene !== null && strlen($m[1]) <= $ebene) {
                    break;
                }
                if ($ebene === null && $m[2] === $titel) {
                    $ebene = strlen($m[1]);

                    continue;
                }
            }
            if ($ebene !== null) {
                $aus[] = $zeile;
            }
        }

        return implode("\n", $aus);
    }

    // AK14
    public function test_readme_hat_abschnitt_log_dateien_mit_allen_angaben(): void
    {
        $text = $this->abschnitt(dirname(__DIR__, 3).'/README.md', 'Log-Dateien');

        $this->assertNotSame('', trim($text), 'Abschnitt „Log-Dateien“ fehlt oder ist leer');
        foreach ([
            'daily',
            'logs.taeglich', 'LARAVELKIT_LOGS_TAEGLICH',
            'logs.tage', 'LARAVELKIT_LOGS_TAGE',
            'logs.zeitplan', 'LARAVELKIT_LOGS_ZEITPLAN',
            'log:prune', '--days', '--dry-run', 'schedule:run',
        ] as $begriff) {
            $this->assertStringContainsString($begriff, $text, $begriff);
        }
    }

    // AK14
    public function test_changelog_nennt_umstellung_und_command(): void
    {
        // Einträge dieses Features stehen im Abschnitt der Version 1.10.0
        $text = $this->changelogVersion((string) file_get_contents(dirname(__DIR__, 3).'/CHANGELOG.md'), '1.10.0');

        // Gegenprobe: der Abschnitt ist der richtige (bestehender Eintrag) und endet vor der Version 1.7.0
        $this->assertStringContainsString('Deprecated', $text);
        // einzelwort: jquery-ui steht nur im Eintrag 1.7.0, nie in Version 1.10.0
        $this->assertStringNotContainsString('jquery-ui', $text);

        $this->assertStringContainsString('log:prune', $text);
        $this->assertStringContainsString('daily', $text);
    }
}
